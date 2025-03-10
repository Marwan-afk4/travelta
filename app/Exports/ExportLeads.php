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
            'source_id', 'service_id', 'nationality_id', 'country_id',
            'city_id', 'image'], 
        ]);
    }

    public function headings(): array
    {
        return ['name', 'phone', 'email', 'watts', 'emergency_phone', 'gender',
            'source_id', 'service_id', 'nationality_id', 'country_id',
            'city_id', 'image'];
    }
}
