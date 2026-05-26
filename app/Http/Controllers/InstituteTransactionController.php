<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InstituteTransactionController extends Controller
{
    /* ==========================
       AUTH HELPER
    =========================== */
    private function authInstitute(Request $request)
    {
        $token = $request->bearerToken();
        if (!$token) {
            abort(401, 'Unauthorized');
        }

        $user = User::where('api_token', hash('sha256', $token))->first();
        if (!$user) {
            abort(401, 'Unauthorized');
        }

        return $user;
    }

    /* ==========================
       CREATE TRANSACTION (ADMIN)
    =========================== */
    public function store(Request $request)
    {
        $authUser = $this->authInstitute($request);

        // Only institute admin
        if ($authUser->user_type !== 1) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // Validation
        $request->validate([
            'created_for'        => 'required|exists:users,id',
            'transaction_type'    => 'required|in:credit,debit',
            'transaction_amount' => 'required|numeric|min:1',
            'transaction_method' => 'required|in:CASH,ONLINE,UPI,BANK_TRANSFER,CHEQUE',
            'transaction_date'   => 'required|date',
            'transaction_duration'=> 'required|in:yearly,quarterly,monthly',
            'remarks'            => 'nullable|string',
        ]);

        // Fetch target user
        $targetUser = User::find($request->created_for);

        // 🔒 Institute isolation check
        if ($targetUser->institute_id !== $authUser->institute_id) {
            return response()->json([
                'message' => 'User does not belong to your institute'
            ], 403);
        }

        
        // Create transaction
        $transaction = Transaction::create([
            'institute_id'       => $authUser->institute_id,
            'created_for'        => $targetUser->id,
            'created_by'         => $authUser->id,
            'transaction_type'    => $request->transaction_type,
            'transaction_amount' => $request->transaction_amount,
            'transaction_method' => $request->transaction_method,
            'transaction_date'   => $request->transaction_date,
            'transaction_duration'=>$request->transaction_duration,
            'remarks'            => $request->remarks,
            'status'             => 'paid',
        ]);

        return response()->json([
            'message' => 'Transaction created successfully',
            'data'    => $transaction
        ], 201);
    }

    /* ==========================
       ADMIN: ALL TRANSACTIONS
    =========================== */
    public function index(Request $request)
    {
        $authUser = $this->authInstitute($request);

        if ($authUser->user_type !== 1) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $query = Transaction::where('institute_id', $authUser->institute_id)
            ->with('user');

        if ($request->filled('type')) {
            $query->where('transaction_type', $request->type);
        }

        if ($request->filled('user_id')) {
            $query->where('created_for', $request->user_id);
        }

        $transactions = $query->latest()->get();

        return response()->json([
            'count' => $transactions->count(),
            'data'  => $transactions
        ]);
    }

    /* ==========================
       STUDENT / STAFF: MY TRANSACTIONS
    =========================== */
    public function myTransactions(Request $request)
    {
        $authUser = $this->authInstitute($request);

        $type = $authUser->user_type === 3 ? 'credit' : 'debit';

        $transactions = Transaction::where('institute_id', $authUser->institute_id)
            ->where('created_for', $authUser->id)
            ->where('transaction_type', $type)
            ->latest()
            ->get();

        return response()->json([
            'count' => $transactions->count(),
            'data'  => $transactions
        ]);
    }

    /* ==========================
       SHOW SINGLE TRANSACTION
    =========================== */
    public function show(Request $request, $id)
    {
        $authUser = $this->authInstitute($request);

        $query = Transaction::where('id', $id)
            ->where('institute_id', $authUser->institute_id);

        // Student / Staff can see only their own transaction
        if ($authUser->user_type !== 1) {
            $query->where('created_for', $authUser->id);
        }

        $transaction = $query->firstOrFail();

        return response()->json($transaction);
    }
}
