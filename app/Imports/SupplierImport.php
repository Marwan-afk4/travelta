<?php

namespace App\Imports;

use App\Models\SupplierAgent;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SupplierImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if (auth()->user()->affilate_id && !empty(auth()->user()->affilate_id)) {
            $agent_id = auth()->user()->affilate_id;
        }
        elseif (auth()->user()->agent_id && !empty(auth()->user()->agent_id)) {
            $agent_id = auth()->user()->agent_id;
        }
        else{
            $agent_id = auth()->user()->id;
        }
        if (auth()->user()->role == 'affilate' || auth()->user()->role == 'freelancer') {
            $role = 'affilate_id';
        }
        else{
            $role = 'agent_id';
        }
        $emails = is_array(json_decode($row['emails'])) ? $row['emails'] : json_encode([$row['emails']]);
        $phones = is_array(json_decode($row['phones'])) ? $row['phones'] : json_encode([$row['phones']]);
        return new SupplierAgent([
            'agent' => $row['agent'],
            'admin_name' => $row['admin_name'],
            'admin_phone' => $row['admin_phone'],
            'admin_email' => $row['admin_email'],
            'emails' => $emails,
            'phones' => $phones,
            'emergency_phone' => $row['emergency_phone'],
            $role => $agent_id,
        ]);
    }
}
