<?php

namespace App\Http\Controllers\Api\Agent\booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\BookingTask;

class ConfirmationTaskController extends Controller
{
    public function __construct(private BookingTask $tasks){}

    public function manuel_tasks(Request $request, $id){
        // http://localhost/travelta/public/agent/booking/task/manuel/{id}
        if ($request->user()->affilate_id && !empty($request->user()->affilate_id)) {
            $agent_id = $request->user()->affilate_id;
        }
        elseif ($request->user()->agent_id && !empty($request->user()->agent_id)) {
            $agent_id = $request->user()->agent_id;
        }
        else{
            $agent_id = $request->user()->id;
        }
        if ($request->user()->role == 'affilate' || $request->user()->role == 'freelancer') {
            $role = 'affilate_id';
        } 
        else {
            $role = 'agent_id';
        }

        $tasks = $this->tasks
        ->where('manuel_booking_id', $id)
        ->where($role, $agent_id)
        ->get();

        return response()->json([
            'tasks' => $tasks,
        ]);
    }
    
    public function engine_tasks(Request $request, $id){
        // http://localhost/travelta/public/agent/booking/task/engine/{id}
        if ($request->user()->affilate_id && !empty($request->user()->affilate_id)) {
            $agent_id = $request->user()->affilate_id;
        }
        elseif ($request->user()->agent_id && !empty($request->user()->agent_id)) {
            $agent_id = $request->user()->agent_id;
        }
        else{
            $agent_id = $request->user()->id;
        }
        if ($request->user()->role == 'affilate' || $request->user()->role == 'freelancer') {
            $role = 'affilate_id';
        } 
        else {
            $role = 'agent_id';
        }

        $tasks = $this->tasks
        ->where('booking_engine_id', $id)
        ->where($role, $agent_id)
        ->get();

        return response()->json([
            'tasks' => $tasks,
        ]);
    }
    
    public function engine_tour_tasks(Request $request, $id){
        // http://localhost/travelta/public/agent/booking/task/tour_engine/{id}
        if ($request->user()->affilate_id && !empty($request->user()->affilate_id)) {
            $agent_id = $request->user()->affilate_id;
        }
        elseif ($request->user()->agent_id && !empty($request->user()->agent_id)) {
            $agent_id = $request->user()->agent_id;
        }
        else{
            $agent_id = $request->user()->id;
        }
        if ($request->user()->role == 'affilate' || $request->user()->role == 'freelancer') {
            $role = 'affilate_id';
        } 
        else {
            $role = 'agent_id';
        }

        $tasks = $this->tasks
        ->where('engine_tour_id', $id)
        ->where($role, $agent_id)
        ->get();

        return response()->json([
            'tasks' => $tasks,
        ]);
    }

    public function task(Request $request, $id){
        // http://localhost/travelta/public/agent/booking/task/item/{id}
        if ($request->user()->affilate_id && !empty($request->user()->affilate_id)) {
            $agent_id = $request->user()->affilate_id;
        }
        elseif ($request->user()->agent_id && !empty($request->user()->agent_id)) {
            $agent_id = $request->user()->agent_id;
        }
        else{
            $agent_id = $request->user()->id;
        }
        if ($request->user()->role == 'affilate' || $request->user()->role == 'freelancer') {
            $role = 'affilate_id';
        } 
        else {
            $role = 'agent_id';
        }

        $task = $this->tasks
        ->where('id', $id)
        ->where($role, $agent_id)
        ->first();

        return response()->json([
            'task' => $task,
        ]);
    }

    public function create(Request $request){
        // http://localhost/travelta/public/agent/booking/task/add
        // Keys
        // manuel_booking_id, booking_engine_id, notes, confirmation_number, notification,
        // engine_tour_id
        $validation = Validator::make($request->all(), [
            'manuel_booking_id' => 'exists:manuel_bookings,id|nullable',
            'booking_engine_id' => 'exists:bookingengine_lists,id|nullable',
            'engine_tour_id' => 'exists:book_tourengines,id|nullable',
            'notes' => 'sometimes',
            'confirmation_number' => 'required',
            'notification' => 'date|required',
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
        if ($request->user()->affilate_id && !empty($request->user()->affilate_id)) {
            $agent_id = $request->user()->affilate_id;
        }
        elseif ($request->user()->agent_id && !empty($request->user()->agent_id)) {
            $agent_id = $request->user()->agent_id;
        }
        else{
            $agent_id = $request->user()->id;
        }
        if ($request->user()->role == 'affilate' || $request->user()->role == 'freelancer') {
            $role = 'affilate_id';
        } 
        else {
            $role = 'agent_id';
        }

        $taskRequest = $validation->validated();
        $taskRequest[$role] = $agent_id;
        $this->tasks
        ->create($taskRequest);

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function modify(Request $request, $id){
        // http://localhost/travelta/public/agent/booking/task/update/{id}
        // Keys
        // manuel_booking_id, booking_engine_id, notes, confirmation_number, notification
        // engine_tour_id
        $validation = Validator::make($request->all(), [
            'manuel_booking_id' => 'exists:manuel_bookings,id|nullable',
            'booking_engine_id' => 'exists:bookingengine_lists,id|nullable',
            'engine_tour_id' => 'exists:book_tourengines,id|nullable',
            'notes' => 'sometimes',
            'confirmation_number' => 'required',
            'notification' => 'date|required',
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
        if ($request->user()->affilate_id && !empty($request->user()->affilate_id)) {
            $agent_id = $request->user()->affilate_id;
        }
        elseif ($request->user()->agent_id && !empty($request->user()->agent_id)) {
            $agent_id = $request->user()->agent_id;
        }
        else{
            $agent_id = $request->user()->id;
        }
        if ($request->user()->role == 'affilate' || $request->user()->role == 'freelancer') {
            $role = 'affilate_id';
        } 
        else {
            $role = 'agent_id';
        }

        $taskRequest = $validation->validated();
        $this->tasks
        ->where('id', $id)
        ->where($role, $agent_id)
        ->update($taskRequest);

        return response()->json([
            'success' => 'You update data success'
        ]);
    }

    public function delete(Request $request, $id){
        // http://localhost/travelta/public/agent/booking/task/delete/{id}
        if ($request->user()->affilate_id && !empty($request->user()->affilate_id)) {
            $agent_id = $request->user()->affilate_id;
        }
        elseif ($request->user()->agent_id && !empty($request->user()->agent_id)) {
            $agent_id = $request->user()->agent_id;
        }
        else{
            $agent_id = $request->user()->id;
        }
        if ($request->user()->role == 'affilate' || $request->user()->role == 'freelancer') {
            $role = 'affilate_id';
        } 
        else {
            $role = 'agent_id';
        }

        $this->tasks
        ->where('id', $id)
        ->where($role, $agent_id)
        ->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
