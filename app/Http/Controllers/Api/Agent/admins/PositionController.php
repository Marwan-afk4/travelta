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
            'booking_payment' => [
                'view', 'add',
            ],
            'expenses' => [
                'view', 'add','update','delete',
            ],
            'expenses_category' => [
                'view', 'add','update','delete',
            ],
            'general_ledger' => [
                'view',
            ],
            'OE_owner' => [
                'view', 'add','update','delete',
            ],
            'OE_transaction' => [
                'view', 'add',
            ],
            'payment_receivable' => [
                'view',
            ],
            'revenue' => [
                'view', 'add','update','delete',
            ],
            'revenue_category' => [
                'view', 'add','update','delete',
            ],
            'supplier_payment_paid' => [
                'view',
            ],
            'supplier_payment_payable' => [
                'view', 'add',
            ],
            'supplier_payment_due' => [
                'view', 'add',
            ],
            'financial' => [
                'view', 'transfer', 'add','update','delete',
            ],
            'wallet' => [
                'view', 'charge', 'add','update','delete',
            ],
            'admin' => [
                'view', 'add','update','delete',
            ],
            'admin_position' => [
                'view', 'add','update','delete',
            ],
            'manuel_booking' => [
                'view',
            ],
            'booking_engine' => [
                'view',
            ],
            'bookings' => [
                'view', 'status'
            ],
            'customer' => [
                'view',
            ],
            'department' => [
                'view',
            ],
            'inventory_room' => [
                'view', 'add','update','delete', 'duplicated', 'availability', 'gallary', 'pricing',
                'type', 'amenity', 'extra'
            ],
            'inventory_tour' => [
                'view', 'add','update','delete', 'gallary',
            ],
            'lead' => [
                'view', 'add','update','delete',
            ],
            'request' => [
                'view', 'add','priority','delete', 'stages', 'notes'
            ],
            'setting_currency' => [
                'view', 'add','update','delete',
            ],
            'setting_group' => [
                'view', 'add','update','delete',
            ],
            'setting_tax' => [
                'view', 'add','update','delete',
            ],
            'supplier' => [
                'view', 'add','update','delete',
            ],
            'HRM_department' => [
                'view', 'add','update','delete',
            ],
            'HRM_agent' => [
                'view', 'add', 'update', 'delete',
            ],
            'HRM_employee' => [
                'view', 'add','update','delete',
            ],
        ];

        return response()->json([
            'modules' => $modules,
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

        $modules = [
            'booking_payment' => [
                'view', 'add',
            ],
            'expenses' => [
                'view', 'add','update','delete',
            ],
            'expenses_category' => [
                'view', 'add','update','delete',
            ],
            'general_ledger' => [
                'view',
            ],
            'OE_owner' => [
                'view', 'add','update','delete',
            ],
            'OE_transaction' => [
                'view', 'add',
            ],
            'payment_receivable' => [
                'view',
            ],
            'revenue' => [
                'view', 'add','update','delete',
            ],
            'revenue_category' => [
                'view', 'add','update','delete',
            ],
            'supplier_payment_paid' => [
                'view',
            ],
            'supplier_payment_payable' => [
                'view', 'add',
            ],
            'supplier_payment_due' => [
                'view', 'add',
            ],
            'financial' => [
                'view', 'transfer', 'add','update','delete',
            ],
            'wallet' => [
                'view', 'charge', 'add','update','delete',
            ],
            'admin' => [
                'view', 'add','update','delete',
            ],
            'admin_position' => [
                'view', 'add','update','delete',
            ],
            'manuel_booking' => [
                'view',
            ],
            'booking_engine' => [
                'view',
            ],
            'bookings' => [
                'view', 'status'
            ],
            'customer' => [
                'view',
            ],
            'department' => [
                'view',
            ],
            'inventory_room' => [
                'view', 'add','update','delete', 'duplicated', 'availability', 'gallary', 'pricing',
                'type', 'amenity', 'extra'
            ],
            'inventory_tour' => [
                'view', 'add','update','delete', 'gallary',
            ],
            'lead' => [
                'view', 'add','update','delete',
            ],
            'request' => [
                'view', 'add','priority','delete', 'stages', 'notes'
            ],
            'setting_currency' => [
                'view', 'add','update','delete',
            ],
            'setting_group' => [
                'view', 'add','update','delete',
            ],
            'setting_tax' => [
                'view', 'add','update','delete',
            ],
            'supplier' => [
                'view', 'add','update','delete',
            ],
            'HRM_department' => [
                'view', 'add','update','delete',
            ],
            'HRM_agent' => [
                'view', 'add', 'update', 'delete',
            ],
            'HRM_employee' => [
                'view', 'add','update','delete',
            ],
        ];
        $position = $this->position
        ->with('perimitions')
        ->where($role, $agent_id)
        ->where('id', $id)
        ->first();

        return response()->json([
            'position' => $position,
            'module' => $position?->perimitions?->module,
            'action' => $modules[$position?->perimitions?->module],
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
