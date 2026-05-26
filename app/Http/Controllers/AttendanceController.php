<?php
    
namespace App\Http\Controllers;
    
use App\Models\Attendance;
use Illuminate\Http\Request;
    
class AttendanceController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:attendance-list|attendance-create|attendance-edit|attendance-delete', ['only' => ['index','show']]);
         $this->middleware('permission:attendance-create', ['only' => ['create','store']]);
         $this->middleware('permission:attendance-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:attendance-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     
      public function markAttendance(Request $request)
{
    // Validate request
    $request->validate([
        'companyName' => 'required|string',
        'remark'      => 'nullable|string',
        'status'      => 'required|string',
        'created_by'  => 'required|integer', // Ensure created_by is provided
    ]);

    // Store attendance data
    $attendance = Attendance::create([
        'companyName' => $request->companyName,
        'remark'      => $request->remark,
        'status'      => $request->status,
        'created_by'  => $request->created_by, // Use user ID from request
        'created_at'  => now(),
        'updated_by'  => $request->created_by, // Use user ID from request
        'updated_at'  => now(),
    ]);

    return response()->json([
        'message' => 'Attendance recorded successfully!',
        'data'    => $attendance
    ], 201);
}

    public function index()
    {
        $departments = Attendance::latest()->paginate(100);
        return view('attendances.index',compact('departments'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
            return view('attendances.index');
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('attendances.create');
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate([
            'name' => 'required',
        ]);
    
        Attendance::create($request->all());
    
        return redirect()->route('attendances.index')
                        ->with('success','Department created successfully.');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function show($department)
    {

    	$data = [];
    	if(isset($_GET['filter'])){
		foreach ($data as $key => $value) {
		  $value->reports = Booking::select('bookings.*', 'booking_event_details.*', \DB::raw('SUM(receipts.amount) as receipt_amount'))
		                  ->leftJoin('booking_event_details', 'bookings.id', '=', 'booking_event_details.booking_id')
		                  ->leftJoin('receipts', 'bookings.id', '=', 'receipts.booking_id')
		                  ->where('booking_event_details.event_area', $value->event_area)
		                  ->whereAnd([
		                      ['receipts.deleted_at', 'NULL'],
		                      ['bookings.deleted_at', 'null'],
		                      // ['booking_event_details.event_area', $value]
		                    ])
		                  ->whereBetween('booking_date', [date($request->from_date),date($request->to_date)])
		                  ->whereBetween('event_date', [date($request->event_from_date),date($request->event_to_date)])
		                  ->orderBy('booking_event_details.event_date')
		                  ->groupBy('bookings.id')
		                  ->get(); 
        	}
        }
        return view('attendances.show',compact('data'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function edit(Department $department)
    {
        return view('attendances.edit',compact('department'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Department $department)
    {
         request()->validate([
            'name' => 'required',
            // 'detail' => 'required',
        ]);
    
        $department->update($request->all());
    
        return redirect()->route('attendances.index')
                        ->with('success','Department updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function destroy(Department $department)
    {
        $department->delete();
    
        return redirect()->route('attendances.index')
                        ->with('success','Department deleted successfully');
    }
    
    public function reportByDate(Request $request)
    {
      $data = BookingEventDetail::select('event_area')->groupBy('event_area')->get();

      if(isset($_GET['filter'])){
        foreach ($data as $key => $value) {
          $value->reports = Booking::select('bookings.*', 'booking_event_details.*', \DB::raw('SUM(receipts.amount) as receipt_amount'))
                          ->leftJoin('booking_event_details', 'bookings.id', '=', 'booking_event_details.booking_id')
                          ->leftJoin('receipts', 'bookings.id', '=', 'receipts.booking_id')
                          ->where('booking_event_details.event_area', $value->event_area)
                          ->whereAnd([
                              ['receipts.deleted_at', 'NULL'],
                              ['bookings.deleted_at', 'null'],
                              // ['booking_event_details.event_area', $value]
                            ])
                          ->whereBetween('booking_date', [date($request->from_date),date($request->to_date)])
                          ->whereBetween('event_date', [date($request->event_from_date),date($request->event_to_date)])
                          ->orderBy('booking_event_details.event_date')
                          ->groupBy('bookings.id')
                          ->get(); 
        }
        return view('bookings.report-created-at')->with(['data'=> $data]);
      }
      return view('bookings.report-created-at'); //->with(['data'=> $data]);    
    }
}
