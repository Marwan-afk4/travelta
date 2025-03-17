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
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use App\trait\image;

class Leads implements ToModel, WithHeadingRow
{
    use image;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if (!empty($row['phone'])) {
            $customer = Customer::
            where('phone', $row['phone'])
            ->first();
            $service = is_numeric($row['service']) ??
            Service::where('service_name', 'like', "%{$row['service']}%")
            ->first()?->id ?? null;
            $nationality = is_numeric($row['nationality']) ??
            Nationality::where('name', 'like', "%{$row['nationality']}%")
            ->first()?->id ?? null;
            $country = is_numeric($row['country']) ??
            Country::where('name', 'like', "%{$row['country']}%")
            ->first()?->id ?? null;
            $city = is_numeric($row['city']) ??
            City::where('name', 'like', "%{$row['city']}%")
            ->first()?->id ?? null;
            $source = is_numeric($row['source']) ??
            CustomerSource::where('name', 'like', "%{$row['source']}%")
            ->first()?->id ?? null;
            // service_id, nationality_id, country_id, city_id
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

            
            if (empty($customer)) {
                $customer = Customer::create([
                    'name'  => $row['name'],
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
                'phone'  => $row['phone'],
                'email'  => $row['email'],
                'gender'  => $row['gender'],
                'watts'  => $row['watts'],
                'status'  => 1,
                'emergency_phone'  => $row['emergency_phone'],
                'customer_id' => $customer->id,
                'source_id'  => $source == 0 ? null: $source,
                'service_id'  => $service == 0 ? null: $service,
                'nationality_id'  => $nationality == 0 ? null: $nationality,
                'country_id'  => $country == 0 ? null: $country,
                'city_id'  => $city == 0 ? null: $city,
                $role => $agent_id,
            ]);
            return new $customer_data;
        }
    }
}
