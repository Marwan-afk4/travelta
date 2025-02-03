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
        // /settings/group
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
    
    public function group($id){
        // /settings/group/item/{id}
        $group = $this->groups
        ->with('nationalities')
        ->where('id', $id)
        ->get(); 

        return response()->json([
            'group' => $group,
        ]);
    }

    public function create(GroupRequest $request){
        // /settings/group/add
        // Keys
        // name, nationalities[]
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

    public function modify(GroupRequest $request, $id){
        // /settings/group/update/{id}
        // Keys
        // name, nationalities[]
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
        ->where('id', $id)
        ->where($role, $agent_id)
        ->first();
        $group->update([
            'name' => $request->name,
        ]);
        $group->nationalities()->sync($nationalities);

        return response()->json([
            'success' => $group
        ]);
    }

    public function delete(Request $request, $id){
        // /settings/group/delete/{id}
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
