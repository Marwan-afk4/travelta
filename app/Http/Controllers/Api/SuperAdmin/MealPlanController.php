<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\HotelMeal;
use App\Models\Hotel;

class MealPlanController extends Controller
{
    public function __construct(private HotelMeal $hotel_meal,
    private Hotel $hotels){}

    public function view(Request $request){
        // /meal_plan

        $hotel_meal_planss = $this->hotel_meal
        ->with('hotel:id,hotel_name')
        ->get();
        $hotels = $this->hotels
        ->get();

        return response()->json([
            'hotel_meal_planss' => $hotel_meal_planss,
            'hotels' => $hotels,
        ]);
    }

    public function meal(Request $request, $id){
        // /agent/admin/item/{id}
        $hotel_meal_planss = $this->hotel_meal
        ->where('id', $id)
        ->with('hotel:id,hotel_name')
        ->first();

        return response()->json([
            'hotel_meal_plan' => $hotel_meal_plans, 
        ]);
    }

    public function status(Request $request, $id){ 
        // /agent/admin/status/{id}
        // Key
        // status
        $validation = Validator::make($request->all(), [
            'status' => 'required|boolean',
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
        $hotel_meal_plan = $this->hotel_meal
        ->where('id', $id)
        ->with('hotel:id,hotel_name')
        ->update([
            'status' => $request->status
        ]);

        return response()->json([
            'hotel_meal_plan' => $hotel_meal_plan
        ]);
    }

    public function create(Request $request){ 
        // /agent/admin/add
        // Keys
        // hotel_id, meal_name
        $validation = Validator::make($request->all(), [
            'hotel_id' => 'required|exists:hotels,id', 
            'meal_name' => 'required', 
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
        $mealPlanRequest = $validation->validated();
        $hotel_meal = $this->hotel_meal
        ->create($mealPlanRequest);

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function modify(AdminRequest $request, $id){ 
        // /agent/admin/update/{id}
        // Keys
        // hotel_id, meal_name
        $validation = Validator::make($request->all(), [
            'hotel_id' => 'required|exists:hotels,id', 
            'meal_name' => 'required', 
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }

        $mealPlanRequest = $validation->validated();
        $hotel_meal = $this->hotel_meal
        ->where('id', $id)
        ->update($mealPlanRequest);

        return response()->json([
            'success' => 'You update data success'
        ]);
    }

    public function delete(Request $request, $id){ 
        // /agent/admin/delete/{id}
        $this->hotel_meal
        ->where('id', $id)
        ->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
