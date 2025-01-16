<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\LegalPaper;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\trait\image;

class UserController extends Controller
{
    use image;
    public function users(){
        $user = Customer::
        with(['manuel' => function($query){
            $query->with([
                'hotel', 'bus', 'flight', 'tour.hotel', 'visa', 'agent'
            ]);
        }])
        ->get();
        $data = collect([]);
        foreach ($user as $item) {
            $element = collect([]);
            $element['id'] = $item->id;
            $element['name'] = $item->name;
            $element['email'] = $item->email;
            $element['phone'] = $item->phone;
            $element['emergency_phone'] = $item->emergency_phone;
            $element['gender'] = $item->gender;
            $booking = [];
            foreach ($item->manuel as $key => $value) {
                $hotel = $value->hotel;
                $bus = $value->bus;
                $flight = $value->flight;
                $tour = $value->tour;
                $visa = $value->visa;
                $agent = $value->agent;
                $booking[$key]['agent'] = $agent;
                $booking[$key]['agent'] = !empty($agent) ? $agent->name : null;
                $booking[$key]['price'] = $value->total_price;
                if (!empty($hotel)) {
                    $booking[$key]['start_date'] = $hotel->check_in;
                    $booking[$key]['end_date'] = $hotel->check_out;
                    $booking[$key]['service'] = 'hotel';
                    $booking[$key]['destination'] = $value->city->name;
                }
                if (!empty($bus)) {
                    $booking[$key]['start_date'] = $bus->departure;
                    $booking[$key]['end_date'] = $bus->arrival;
                    $booking[$key]['service'] = 'bus';
                    $booking[$key]['destination'] = $bus->to;
                }
                if (!empty($flight)) {
                    $booking[$key]['start_date'] = $flight->departure;
                    $booking[$key]['end_date'] = $flight->arrival;
                    $booking[$key]['service'] = 'flight';
                    $booking[$key]['destination'] = $flight->from_to[count($flight->from_to) - 1]->to;
                }
                if (!empty($tour)) {
                    $booking[$key]['start_date'] = $tour->hotel->sortBy('check_in')->first()->check_in ?? null;
                    $booking[$key]['end_date'] = $tour->hotel->sortByDesc('check_out')->first()->check_out ?? null;
                    $booking[$key]['service'] = 'tour';
                    $booking[$key]['destination'] = $tour->hotel->sortByDesc('check_out')->first()->destination ?? null;
                }
                if (!empty($visa)) {
                    $booking[$key]['start_date'] = $visa->travel_date;
                    $booking[$key]['end_date'] = $visa->travel_date;
                    $booking[$key]['service'] = 'visa';
                    $booking[$key]['destination'] = $visa->country;
                }
            }
            $element['booking'] = $booking;
            $data[] = $element;
        }
        return response()->json([
            'data' => $data,
        ], 200);
    }

    public function adduser(Request $request){
        $validation = Validator::make($request->all(), [
            'name'=>'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'phone' => 'required|unique:users,phone',
            'emergency_phone' => 'required|unique:users,emergency_phone',
            'image' => 'required|array',
            'image.*' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 401);
        }
        $user = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'phone' => $request->phone,
            'emergency_phone' => $request->emergency_phone,
        ]);
        foreach($request->image as $image){
            $imag_path = $this->storeBase64Image($image['image'], 'admin/legal_paper/user');
            LegalPaper::create([
                'customer_id' => $user->id,
                'image' => $imag_path
            ]);
        }
        return response()->json([
            'message' => 'User added successfully',
            'user' => $user
        ]);
    }

    public function deleteuser($id){
        $user=User::find($id);
        $legal_papers = LegalPaper::where('user_id', $user->id)
        ->get();
        foreach ($legal_papers as $item) {
            $this->deleteImage($item->image);
        }
        $user->delete();
        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }


}
