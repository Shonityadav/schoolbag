<?php
    
namespace App\Http\Controllers;
    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;
use Hash;
use Auth;
use Illuminate\Support\Arr;
use App\Models\StaffDetails;
use App\Models\StudentDetailss;
    
class UserController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index','store']]);
        $this->middleware('permission:user-create', ['only' => ['create','store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        // 🔹 show users of same school only (except self)
        $data = User::where('id', '!=', Auth::id())
                    ->where('institute_id', Auth::user()->institute_id)
                    ->get();
        //dd("hh");

        return view('users.index')->with('data', $data);
    }
    
    public function userDelete()
    {
        return view('users.user-delete');
    }
    
    public function create()
    {
        $roles = Role::pluck('name','name')->all();
        return view('users.create', compact('roles'));
    }
    
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'     => 'required',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles'    => 'required'
        ]);
    
        $input = $request->all();

        // 🔹 attach school automatically
        $input['institution_id'] = Auth::user()->institution_id;

        $input['password'] = Hash::make($input['password']);
    
        $user = User::create($input);
        $user->assignRole($request->input('roles'));
    
        return redirect()->route('users.index')
                         ->with('success','User created successfully');
    }
    
    public function show($id)
    {
        $user = User::where('id', $id)
                    ->where('institution_id', Auth::user()->institution_id)
                    ->firstOrFail();

        return view('users.show', compact('user'));
    }
    
    public function edit($id)
    {
        $user = User::where('id', $id)
                    ->where('institution_id', Auth::user()->institution_id)
                    ->firstOrFail();

        $roles = Role::pluck('name','name')->all();
        $userRole = $user->roles->pluck('name','name')->all();
    
        return view('users.edit', compact('user','roles','userRole'));
    }
    
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name'     => 'required',
            'email'    => 'required|email|unique:users,email,'.$id,
            'password' => 'same:confirm-password',
            'roles'    => 'required'
        ]);
    
        $input = $request->all();

        if(!empty($input['password'])){ 
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, ['password']);    
        }
    
        $user = User::where('id', $id)
                    ->where('institution_id', Auth::user()->institution_id)
                    ->firstOrFail();

        $user->update($input);

        DB::table('model_has_roles')->where('model_id', $id)->delete();
        $user->assignRole($request->input('roles'));
    
        return redirect()->route('users.index')
                         ->with('success','User updated successfully');
    }
    
    public function destroy($id)
    {
        User::where('id', $id)
            ->where('institution_id', Auth::user()->institution_id)
            ->delete();

        return redirect()->route('users.index')
                         ->with('success','User deleted successfully');
    }
}
