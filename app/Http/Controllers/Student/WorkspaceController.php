<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $todayHomework = null;
        $overallPercentage = 0;

        if ($user->class_id) {
            $todayHomework = \App\Models\Homework::where('class_id', $user->class_id)
                ->where('for_date', today()->toDateString())
                ->first();

            $feeStructures = \App\Models\FeeStructure::where('class_id', $user->class_id)->get();
            $totalFeesAmount = 0;
            foreach ($feeStructures as $fee) {
                $multiplier = match($fee->frequency) {
                    'monthly' => 12, 'quarterly' => 4, 'half_yearly' => 2, 'yearly' => 1, 'one_time' => 1, default => 1,
                };
                $totalFeesAmount += $fee->amount * $multiplier;
            }
            
            if ($totalFeesAmount > 0) {
                $totalPaidAmount = \App\Models\Transaction::where('created_for', $user->id)
                    ->where('transaction_type', 'Fee Payment')
                    ->where('status', 'paid')
                    ->sum('transaction_amount');
                $overallPercentage = min(100, round(($totalPaidAmount / $totalFeesAmount) * 100));
            }
        }

        return view('student.workspace', compact('user', 'todayHomework', 'overallPercentage'));
    }

    public function homeworkHistory()
    {
        $user = auth()->user();
        $homeworks = collect();
        
        if ($user->class_id) {
            $homeworks = \App\Models\Homework::where('class_id', $user->class_id)
                ->orderBy('for_date', 'desc')
                ->get();
        }

        return view('student.homework_history', compact('user', 'homeworks'));
    }

    public function profile()
    {
        $user = auth()->user();
        
        $attendanceData = \App\Models\Attendance::where('created_for', $user->id)
            ->get(['attendance_date', 'status'])
            ->mapWithKeys(function ($att) {
                return [\Carbon\Carbon::parse($att->attendance_date)->toDateString() => $att->status];
            });

        $feeStructures = collect();
        $studentFees = collect();
        $totalFeesAmount = 0;
        $totalPaidAmount = 0;

        if ($user->class_id) {
            $feeStructures = \App\Models\FeeStructure::where('class_id', $user->class_id)->get();
            
            // Generate or fetch StudentFee records
            foreach ($feeStructures as $fee) {
                // Calculate multiplier based on frequency
                $multiplier = match($fee->frequency) {
                    'monthly' => 12,
                    'quarterly' => 4,
                    'half_yearly' => 2,
                    'yearly' => 1,
                    'one_time' => 1,
                    default => 1,
                };
                
                $totalAmount = $fee->amount * $multiplier;
                
                $studentFee = \App\Models\StudentFee::firstOrCreate(
                    [
                        'student_id' => $user->id,
                        'fee_structure_id' => $fee->id,
                    ],
                    [
                        'total_amount' => $totalAmount,
                        'paid_amount' => 0,
                        'percentage_paid' => 0,
                    ]
                );
                
                $studentFees->push($studentFee);
                $totalFeesAmount += $studentFee->total_amount;
            }

            // Calculate paid amount from Transactions
            $totalPaidAmount = \App\Models\Transaction::where('created_for', $user->id)
                ->where('transaction_type', 'Fee Payment')
                ->where('status', 'paid')
                ->sum('transaction_amount');
                
            // Sync logic for legacy/unallocated transactions
            $currentAllocatedPaidAmount = $studentFees->sum('paid_amount');
            
            if ($totalPaidAmount > $currentAllocatedPaidAmount) {
                $unallocatedAmount = $totalPaidAmount - $currentAllocatedPaidAmount;
                foreach ($studentFees as $sFee) {
                    if ($unallocatedAmount <= 0) break;
                    
                    $due = $sFee->total_amount - $sFee->paid_amount;
                    if ($due > 0) {
                        $payAmount = min($due, $unallocatedAmount);
                        $sFee->paid_amount += $payAmount;
                        if ($sFee->total_amount > 0) {
                            $sFee->percentage_paid = min(100, round(($sFee->paid_amount / $sFee->total_amount) * 100));
                        }
                        $sFee->save();
                        $unallocatedAmount -= $payAmount;
                    }
                }
            }
                
            // Update percentage and total paid on the frontend
            $dueAmount = $totalFeesAmount - $totalPaidAmount;
            $overallPercentage = $totalFeesAmount > 0 ? min(100, round(($totalPaidAmount / $totalFeesAmount) * 100)) : 0;
        } else {
            $dueAmount = 0;
            $overallPercentage = 0;
        }

        return view('student.profile', compact(
            'user', 'attendanceData', 'feeStructures', 'studentFees',
            'totalFeesAmount', 'totalPaidAmount', 'dueAmount', 'overallPercentage'
        ));
    }
}
