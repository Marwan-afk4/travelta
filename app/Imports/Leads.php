<?php

namespace App\Imports;

use App\Models\CustomerData;
use App\Models\Customer;

use App\Models\Service;
use App\Models\Nationality;
use App\Models\Country;
use App\Models\City;
use App\Models\CustomerSource;
use Maatwebsite\Excel\Concerns\ToModel;

use App\trait\image;

class Leads implements ToModel
{
    use image;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $image = null;
        if (isset($row['image']) && !empty($row['image']) && !is_string($row['image'])) {
            $image = $this->uploadFile($row['image'], 'agent/lead/image');
        }
        $customer = Customer::
        where('phone', $row['phone'])
        ->first();
        $service = is_numeric($row['service_id']) ??
        Service::where('service_name', 'like', "%{$row['service_id']}%")
        ->first()?->id ?? null;
        $nationality = is_numeric($row['nationality_id']) ??
        Nationality::where('name', 'like', "%{$row['nationality_id']}%")
        ->first()?->id ?? null;
        $country = is_numeric($row['country_id']) ??
        Country::where('name', 'like', "%{$row['country_id']}%")
        ->first()?->id ?? null;
        $city = is_numeric($row['city_id']) ??
        City::where('name', 'like', "%{$row['city_id']}%")
        ->first()?->id ?? null;
        $source = is_numeric($row['source_id']) ??
        CustomerSource::where('name', 'like', "%{$row['source_id']}%")
        ->first()?->id ?? null;
        // service_id, nationality_id, country_id, city_id
        if (empty($customer)) {
            $customer = Customer::create([
                'name'  => $row['name'],
                'image'  => $image,
                'phone'  => $row['phone'],
                'email'  => $row['email'],
                'gender'  => $row['gender'],
                'watts'  => $row['watts'],
                'status'  => 1,
                'emergency_phone'  => $row['emergency_phone'],
                'role' => 'lead',
            ]);
        }
        // , , ,
        // , , ,  
        $customer_data = CustomerData::create([
            'name'  => $row['name'],
            'image'  => $image,
            'phone'  => $row['phone'],
            'email'  => $row['email'],
            'gender'  => $row['gender'],
            'watts'  => $row['watts'],
            'status'  => 1,
            'emergency_phone'  => $row['emergency_phone'],
            'customer_id' => $customer->id,
            'source_id'  => $source,
            'service_id'  => $service,
            'nationality_id'  => $nationality,
            'country_id'  => $country,
            'city_id'  => $city, 
        ]);
        return new $customer_data;
    }
}
