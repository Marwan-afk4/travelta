<?php

namespace App\Imports;

use App\Models\CustomerData;
use App\Models\Customer;

use App\Models\Service;
use App\Models\Nationality;
use App\Models\Country;
use App\Models\City;
use App\Models\CustomerSource;
use App\Models\HrmEmployee;
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
            $service =
            Service::where('service_name', 'like', "%{$row['service']}%")
            ->first();
            $nationality =
            Nationality::where('name', 'like', "%{$row['nationality']}%")
            ->first();
            $agent = HrmEmployee::where('name', 'like', "%{$row['agent']}%")
            ->where('agent', 1)
            ->first();
            $country =
            Country::where('name', 'like', "%{$row['country']}%")
            ->first();
            $city =
            City::where('name', 'like', "%{$row['city']}%")
            ->first();
            $source =
            CustomerSource::where('source', 'like', "%{$row['source']}%")
            ->first();
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
                'source_id'  => empty($source->id) ? null: $source->id,
                'service_id'  => empty($service->id) ? null: $service->id,
                'nationality_id'  => empty($nationality->id) ? null: $nationality->id,
                'country_id'  => empty($country->id) ? null: $country->id,
                'city_id'  => empty($city->id) ? null: $city->id,
                'agent_sales_id'  => empty($agent->id) ? null: $agent->id,
                $role => $agent_id,
            ]);
            return new $customer_data;
        }
    }
}
