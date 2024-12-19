<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CountryController extends Controller
{
    protected $updateCouuntry=['name'];

    public function getCountries(){
        $country = Country::all();
        $data = [
            'country' => $country];
        return response()->json($data);
    }

    public function addContry(Request $request){
        $validation = Validator::make($request->all(), [
            'name'=>'required|unique:countries,name'
        ]);
        if($validation->fails()){
            return response()->json(['errors' => $validation->errors()], 401);
        }
        $country = Country::create([
            'name' => $request->name,
        ]);
        return response()->json([
            'message' => 'Country added successfully',
        ]);
    }

    public function deleteCountry($id){
        $country=Country::find($id);
        $country->delete();
        return response()->json([
            'message' => 'Country deleted successfully',
        ]);

    }

    public function updateCountry(Request $request, $id){
        $country=Country::find($id);
        $country->update($request->only($this->updateCouuntry));
        return response()->json([
            'message' => 'Country updated successfully',
        ]);
    }
}
