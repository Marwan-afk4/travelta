<?php

namespace App\Exports;

use App\Models\CustomerData;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExportLeads implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect([
            ['name', 'phone', 'email', 'watts', 'emergency_phone', 'gender',
            'source', 'service', 'nationality', 'country',
            'city', 'image'], 
        ]);
    }

    public function headings(): array
    {
        return ['name', 'phone', 'email', 'watts', 'emergency_phone', 'gender',
            'source', 'service', 'nationality', 'country',
            'city', 'image'];
    }
}
