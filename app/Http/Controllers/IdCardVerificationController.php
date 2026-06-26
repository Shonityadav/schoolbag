<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserIdentityCard;

class IdCardVerificationController extends Controller
{
    /**
     * Verify an ID card securely via token.
     * Accessible to the public.
     */
    public function verify($token)
    {
        $card = UserIdentityCard::with(['user.studentDetail', 'user.staffDetail', 'template.institute'])
            ->where('token', $token)
            ->first();

        if (!$card) {
            return view('id_cards.verification', [
                'status' => 'Invalid',
                'message' => 'No ID card found matching this secure token. This card may be forged or tampered with.'
            ]);
        }

        // Determine user type and details
        $user = $card->user;
        $institute = $card->template->institute;
        
        $details = [];
        $photo = null;
        $name = null;
        
        if ($user->hasRole('Student')) {
            $sd = $user->studentDetail;
            $name = $sd->student_name ?? 'N/A';
            $photo = $sd->photo;
            $details = [
                'Type' => 'Student',
                'Admission No' => $sd->admission_number,
                'Class' => ($sd->class->name ?? '') . ' ' . ($sd->section ?? ''),
                'Blood Group' => $sd->blood_group,
                'Emergency Phone' => $sd->phone
            ];
        } else {
            $sd = $user->staffDetail;
            $name = $sd->staff_name ?? 'N/A';
            $photo = $sd->photo;
            $details = [
                'Type' => 'Staff',
                'Employee ID' => $sd->employee_id,
                'Designation' => $sd->designation,
                'Department' => $sd->department,
                'Blood Group' => $sd->blood_group,
                'Emergency Phone' => $sd->phone
            ];
        }

        // Determine real-time status based on dates
        $status = $card->status; // Active, Revoked, Lost, etc.
        if ($status === 'Active' && $card->expires_on && $card->expires_on->isPast()) {
            $status = 'Expired';
        }

        return view('id_cards.verification', [
            'status' => $status,
            'card' => $card,
            'institute' => $institute,
            'name' => $name,
            'photo' => $photo,
            'details' => $details
        ]);
    }
}
