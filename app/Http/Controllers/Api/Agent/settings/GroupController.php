<?php

namespace App\Http\Controllers\Api\Agent\settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\api\agent\settings\group\GroupRequest;

use App\Models\Group;
use App\Models\Nationality;

class GroupController extends Controller
{
    public function __construct(private Group $groups,
    private Nationality $nationalities){}
    
    public function view(){
        $groups = $this->groups
        ->with('nationalities')
        ->get();
        $nationalities = $this->nationalities
        ->get();

        return response()->json([
            'groups' => $groups,
            'nationalities' => $nationalities,
        ]);
    }

    public function create(GroupRequest $request){
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
        $nationalities = is_string($request->nationalities) ? json_decode($request->nationalities): $request->nationalities;
        $group = $this->groups
        ->create([
            'name' => $request->name,
            $role => $agent_id,
        ]);
        $group->nationalities()->attach($nationalities);

        return response()->json([
            'success' => $group
        ]);
    }

    public function modify(){
        
    }

    public function delete(Request $request, $id){
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
        $this->groups
        ->where('id', $id)
        ->where($role, $agent_id)
        ->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
