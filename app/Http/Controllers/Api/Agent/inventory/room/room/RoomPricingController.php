<?php

namespace App\Http\Controllers\Api\Agent\inventory\room\room;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\api\agent\inventory\room\room\RoomPricingRequest;

use App\Models\RoomPricing;
use App\Models\CurrencyAgent;
use App\Models\RoomPricingData;
use App\Models\Group;
use App\Models\Nationality;

class RoomPricingController extends Controller
{
    public function __construct(private RoomPricing $pricing, private Group $groups,
    private CurrencyAgent $currency, private RoomPricingData $pricing_data,
    private Nationality $nationalities){}
    protected $pricingRequest =[
        'pricing_data_id',
        'room_id',
        'currency_id',
        'name',
        'from',
        'to',
        'price', 
    ];

    public function view(Request $request, $id){
        // room/pricing/{id}
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
        else{
            $role = 'agent_id';
        } 
        $currencies = $this->currency
        ->select('id', 'name')
        ->where($role, $agent_id)
        ->get();
        $pricing_data = $this->pricing_data
        ->get();
        $pricing = $this->pricing
        ->where('room_id', $id)
        ->with('currency', 'pricing_data', 'groups', 'nationality')
        ->get();
        $groups = $this->groups
        ->where($role, $agent_id)
        ->get();
        $nationalities = $this->nationalities
        ->get();

        return response()->json([
            'currencies' => $currencies,
            'pricing_data' => $pricing_data,
            'pricing' => $pricing,
            'groups' => $groups,
            'nationalities' => $nationalities,
        ]);
    }

    public function pricing($id){
        // room/pricing/item/{id}
        $pricing = $this->pricing
        ->where('id', $id)
        ->with('currency', 'pricing_data')
        ->get();

        return response()->json([
            'pricing' => $pricing,
        ]);
    }

    public function duplicate(Request $request, $id){
        // room/pricing/duplicate/{id}
        $pricing = $this->pricing
        ->where('id', $id) 
        ->first();
        if (empty($pricing)) {
            return response()->json([
                'errors' => 'id is wrong'
            ], 400);
        }
        $new_pricing = $this->pricing
        ->create($pricing->toArray());
        $new_pricing->groups()->attach($pricing->groups->pluck('id')->toArray());
        $new_pricing->nationality()->attach($pricing->nationality->pluck('id')->toArray());

        return response()->json([
            'success' => 'You duplicated room success'
        ]);
    }

    public function create(RoomPricingRequest $request){
        // room/pricing/add 
        // Keys
        // pricing_data_id, room_id, currency_id, name, from, to, price, groups_id[], nationality_id[]
        $room_pricing = $request->only($this->pricingRequest);
        $pricing = $this->pricing
        ->create($room_pricing);
        $groups_id = $request->groups_id;
        $nationality_id = $request->nationality_id;
        $pricing->groups()->attach($groups_id);
        $pricing->nationality()->attach($nationality_id);

        return response()->json([
            'success' => $pricing
        ]);
    }

    public function modify(RoomPricingRequest $request, $id){
        // room/pricing/update/{id}
        // Keys
        // pricing_data_id, room_id, currency_id, name, from, to, price, groups_id[], nationality_id[]
        $room_pricing = $request->only($this->pricingRequest);
        $pricing = $this->pricing
        ->where('room_id', $request->room_id)
        ->where('id', $id)
        ->first();
        $pricing->update($room_pricing);
        $groups_id = $request->groups_id;
        $nationality_id = $request->nationality_id;
        $pricing->groups()->sync($groups_id);
        $pricing->nationality()->sync($nationality_id);

        return response()->json([
            'success' => $pricing
        ]);
    }

    public function delete($id){
        // room/pricing/delete/{id}
        $this->pricing
        ->where('id', $id)
        ->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
