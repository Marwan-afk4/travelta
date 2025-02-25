<?php

namespace App\Http\Controllers\Api\Agent\admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\api\agent\admin\RoleRequest;

use App\Models\AdminAgentPosition;
use App\Models\AdminAgentRole;

class PositionController extends Controller
{
    public function __construct(private AdminAgentPosition $position,
    private AdminAgentRole $role){}

    public function view(Request $request){
        // /agent/admin/position
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

        $positions = $this->position
        ->with(['perimitions'])
        ->where($role, $agent_id)
        ->get();

        return response()->json([
            'positions' => $positions
        ]);
    }

    public function lists(){
        // /agent/admin/position/lists
        $modules = [
            'booking_payment',
            'expenses',
            'expenses_category',
            'general_ledger',
            'OE_owner',
            'OE_transaction',
            'payment_receivable',
            'revenue',
            'revenue_category',
            'supplier_payment_paid',
            'supplier_payment_payable',
            'supplier_payment_due',
            'financial',
            'wallet',
            'admin',
            'admin_position',
            'manuel_booking',
            'booking_engine',
            'bookings',
            'customer',
            'department',
            'inventory_room',
            'inventory_tour',
            'invoice',
            'lead',
            'request',
            'setting_currency',
            'setting_group',
            'setting_tax',
            'supplier',
            'HRM_department',
            'HRM_agent',
            'HRM_employee',
        ];
        $actions = [
            'view',
            'transfer', // financial
            'charge', // wallet
            'add',
            'update',
            'delete',
        ];

        return response()->json([
            'modules' => $modules,
            'actions' => $actions,
        ]);
    }

    public function position(Request $request, $id){
        // /agent/admin/position/item/{id}
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

        $position = $this->position
        ->with('perimitions')
        ->where($role, $agent_id)
        ->where('id', $id)
        ->first();

        return response()->json([
            'position' => $position
        ]);
    }

    public function create(RoleRequest $request){
        // /agent/admin/position/add
        // keys
        // name, premisions[module, action]
        // {"name":"Sub Admin","premisions":[{"module":"OE","action":"add"}]}
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

        $premisions = $request->premisions;
        $position = $this->position
        ->create([
            'name' => $request->name,
            $role => $agent_id,
        ]);
        foreach ($premisions as $item) {
            $this->role
            ->create([
                'module' => $item['module'],
                'action' => $item['action'],
                'position_id' => $position->id,
            ]);
        }

        return response()->json([
            'success' => 'You add data success',
        ]);
    }

    public function modify(RoleRequest $request, $id){
        // /agent/admin/position/update/{id}
        // keys
        // name, premisions[module, action]
        // {"name":"Sub Admin","premisions":[{"module":"OE","action":"add"}]}
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

        $premisions = $request->premisions;
        $position = $this->position
        ->where('id', $id)
        ->where($role, $agent_id)
        ->first();
        if (empty($position)) {
            return response()->json([
                'errors' => 'position is not found'
            ]);
        }
        $position->update([
            'name' => $request->name,
        ]);
        $this->role
        ->where('position_id', $position->id)
        ->delete();
        foreach ($premisions as $item) {
            $this->role
            ->create([
                'module' => $item['module'],
                'action' => $item['action'],
                'position_id' => $position->id,
            ]);
        }

        return response()->json([
            'success' => 'You update data success',
        ]);
    }

    public function delete(Request $request, $id){
        // /agent/admin/position/delete/{id}
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

        $premisions = $request->premisions;
        $position = $this->position
        ->where('id', $id)
        ->where($role, $agent_id)
        ->delete();

        return response()->json([
            'success' => 'You delete data success',
        ]);
    }
}
