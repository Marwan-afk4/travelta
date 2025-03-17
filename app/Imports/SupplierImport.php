<?php

namespace App\Imports;

use App\Models\SupplierAgent;
use Maatwebsite\Excel\Concerns\ToModel;

class SupplierImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new SupplierAgent([
            //
        ]);
    }
}
