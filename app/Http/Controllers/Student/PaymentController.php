<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use App\Models\Transaction;

class PaymentController extends Controller
{
    /**
     * Initiate a Razorpay payment for due fees.
     */
    public function initiatePayment(Request $request)
    {
        $user = auth('student')->user();
        if (!$user) {
            $user = auth()->user();
        }

        // Calculate due amount
        $totalFeesAmount = \App\Models\StudentFee::where('student_id', $user->id)->sum('total_amount');
        $totalPaidAmount = \App\Models\Transaction::where('created_for', $user->id)
            ->where('transaction_type', 'Fee Payment')
            ->sum('transaction_amount');
            
        $dueAmount = $totalFeesAmount - $totalPaidAmount;

        if ($dueAmount <= 0) {
            return redirect()->back()->with('success', 'You have no due fees.');
        }

        // Determine amount to pay
        $amountToPay = $request->input('amount_to_pay', $dueAmount);
        
        $feeStructureId = $request->input('fee_structure_id');

        // Ensure we don't pay more than what's due globally if fee_structure_id is not passed
        if (!$feeStructureId && $amountToPay > $dueAmount) {
            $amountToPay = $dueAmount;
        }

        // Generate Razorpay Order
        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
        
        $orderData = [
            'receipt'         => 'rcptid_' . time() . '_' . $user->id,
            'amount'          => $amountToPay * 100, // Razorpay uses paisa
            'currency'        => 'INR',
            'payment_capture' => 1 // auto capture
        ];

        try {
            $razorpayOrder = $api->order->create($orderData);
            
            // Pass to checkout view
            return view('student.razorpay_checkout', [
                'orderId' => $razorpayOrder['id'],
                'amount' => $amountToPay,
                'user' => $user,
                'key' => config('services.razorpay.key'),
                'feeStructureId' => $feeStructureId
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error generating payment order: ' . $e->getMessage());
        }
    }

    /**
     * Handle the Razorpay callback signature verification.
     */
    public function verifyPayment(Request $request)
    {
        $user = auth('student')->user();
        if (!$user) {
            $user = auth()->user();
        }

        $signatureStatus = false;
        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

        try {
            $attributes = array(
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            );

            $api->utility->verifyPaymentSignature($attributes);
            $signatureStatus = true;
        } catch (\Exception $e) {
            return redirect()->to(route('student.workspace.profile') . '#fees')->with('error', 'Payment verification failed: ' . $e->getMessage());
        }

        if ($signatureStatus) {
            // Fetch the payment to get the actual amount paid
            $payment = $api->payment->fetch($request->razorpay_payment_id);
            $amountPaid = $payment['amount'] / 100; // Convert back to rupees

            // Record the transaction
            Transaction::create([
                'institute_id' => $user->institute_id,
                'created_for' => $user->id,
                'created_by' => $user->id,
                'transaction_type' => 'Fee Payment',
                'transaction_amount' => $amountPaid,
                'transaction_method' => 'ONLINE',
                'transaction_date' => now(),
                'transaction_duration' => 'One Time',
                'remarks' => 'Online payment via Razorpay. Order ID: ' . $request->razorpay_order_id . ' Payment ID: ' . $request->razorpay_payment_id,
                'status' => 'paid',
            ]);

            // Update StudentFee if fee_structure_id is provided
            $feeStructureId = $request->input('fee_structure_id');
            if ($feeStructureId) {
                $studentFee = \App\Models\StudentFee::where('student_id', $user->id)
                    ->where('fee_structure_id', $feeStructureId)
                    ->first();
                if ($studentFee) {
                    $studentFee->paid_amount += $amountPaid;
                    if ($studentFee->total_amount > 0) {
                        $studentFee->percentage_paid = min(100, round(($studentFee->paid_amount / $studentFee->total_amount) * 100));
                    }
                    $studentFee->save();
                }
            }

            return redirect()->to(route('student.workspace.profile') . '#fees')->with('success', 'Fee payment successful! Thank you.');
        }

        return redirect()->to(route('student.workspace.profile') . '#fees')->with('error', 'Payment verification failed.');
    }

    /**
     * Handle payment cancellation by user
     */
    public function cancelPayment()
    {
        return redirect()->to(route('student.workspace.profile') . '#fees')->with('error', 'Payment cancelled by user.');
    }
}
