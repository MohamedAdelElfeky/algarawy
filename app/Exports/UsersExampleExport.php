<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExampleExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'first_name', 'last_name', 'email', 'phone', 'national_id',
            // 'password',
             'birth_date', 
            // 'location', 'region_id', 'city_id', 
            // 'neighborhood_id', 'role'
        ];
    }

    public function array(): array
    {
        return [
            [
                'John', 'Doe', 'johndoe@example.com', '123456789', '987654321',
                // 'password123', 
                '01-01-1990', 
                // 'New York', '1', '10', '100', 'user'
            ],
            [
                'Jane', 'Smith', 'janesmith@example.com', '987654321', '123456789',
                // 'securepass', 
                '15-06-1995', 
                // 'Los Angeles', '2', '20', '200', 'admin'
            ]
        ];
    }
}
