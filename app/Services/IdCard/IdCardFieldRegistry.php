<?php

namespace App\Services\IdCard;

class IdCardFieldRegistry
{
    /**
     * Define dynamic variables that can be used inside text fields.
     */
    public static function getVariables(): array
    {
        return [
            ['tag' => '{{student_name}}', 'label' => 'Student Name', 'type' => 'student'],
            ['tag' => '{{father_name}}', 'label' => 'Father Name', 'type' => 'student'],
            ['tag' => '{{mother_name}}', 'label' => 'Mother Name', 'type' => 'student'],
            ['tag' => '{{admission_number}}', 'label' => 'Admission Number', 'type' => 'student'],
            ['tag' => '{{roll_number}}', 'label' => 'Roll Number', 'type' => 'student'],
            ['tag' => '{{class}}', 'label' => 'Class', 'type' => 'student'],
            ['tag' => '{{section}}', 'label' => 'Section', 'type' => 'student'],
            ['tag' => '{{blood_group}}', 'label' => 'Blood Group', 'type' => 'both'],
            ['tag' => '{{dob}}', 'label' => 'Date of Birth', 'type' => 'both'],
            ['tag' => '{{phone}}', 'label' => 'Phone', 'type' => 'both'],
            ['tag' => '{{address}}', 'label' => 'Address', 'type' => 'both'],
            
            ['tag' => '{{employee_id}}', 'label' => 'Employee ID', 'type' => 'staff'],
            ['tag' => '{{staff_name}}', 'label' => 'Staff Name', 'type' => 'staff'],
            ['tag' => '{{designation}}', 'label' => 'Designation', 'type' => 'staff'],
            ['tag' => '{{department}}', 'label' => 'Department', 'type' => 'staff'],
            
            ['tag' => '{{institute_name}}', 'label' => 'Institute Name', 'type' => 'both'],
            ['tag' => '{{academic_year}}', 'label' => 'Academic Year', 'type' => 'both'],
            ['tag' => '{{issue_date}}', 'label' => 'Issue Date', 'type' => 'both'],
            ['tag' => '{{expiry_date}}', 'label' => 'Expiry Date', 'type' => 'both'],
        ];
    }

    /**
     * Defines the structure for designer fields.
     */
    public static function getFieldTypes(): array
    {
        return [
            'text' => [
                'name' => 'Text',
                'properties' => ['text', 'fontFamily', 'fontSize', 'fill', 'fontWeight', 'fontStyle', 'textAlign', 'underline', 'lineHeight']
            ],
            'photo' => [
                'name' => 'User Photo',
                'properties' => ['width', 'height', 'radius', 'stroke', 'strokeWidth']
            ],
            'qr_code' => [
                'name' => 'QR Code',
                'properties' => ['width', 'height', 'color']
            ],
            'barcode' => [
                'name' => 'Barcode',
                'properties' => ['width', 'height', 'color', 'format']
            ],
            'logo' => [
                'name' => 'Institute Logo',
                'properties' => ['width', 'height']
            ],
            'signature' => [
                'name' => 'Authority Signature',
                'properties' => ['width', 'height']
            ],
            'image' => [
                'name' => 'Static Image',
                'properties' => ['src', 'width', 'height', 'radius']
            ],
            'rectangle' => [
                'name' => 'Rectangle Shape',
                'properties' => ['width', 'height', 'fill', 'radius', 'stroke', 'strokeWidth']
            ],
            'circle' => [
                'name' => 'Circle Shape',
                'properties' => ['radius', 'fill', 'stroke', 'strokeWidth']
            ],
            'line' => [
                'name' => 'Line',
                'properties' => ['width', 'stroke', 'strokeWidth']
            ],
        ];
    }
}
