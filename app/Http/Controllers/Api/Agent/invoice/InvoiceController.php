<?php

namespace App\Http\Controllers\Api\Agent\invoice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\Plan;
use App\Models\Setting;

class InvoiceController extends Controller
{
    public function __construct(private Plan $plans, private Setting $settings){}

    public function invoice(Request $request){
        // /agent/invoice
        $plan = $this->plans
        ->where('id', $request->user()->plan_id)
        ->with('currancy')
        ->first();
        $start_date = $request->user()->start_date;
        $end_date = $request->user()->end_date;
        $allow_time = $this->settings
        ->where('name', 'allow_time')
        ->first();
        if (empty($allow_time)) {
            $data = [
                'days' => 0,
                'fine' => 0
            ];
            $data = json_encode($data);
            $allow_time = $this->settings
            ->create([
                'name' => 'allow_time',
                'value' => $data,
            ]);
        }
        $allow_time = json_decode($allow_time->value);
        $days = $allow_time->days ?? 0;
        $fine = $allow_time->fine ?? 0;
        $allow_date = null;
        if (!empty($end_date)) {
            $end_date =  Carbon::parse($end_date);
            $allow_date = (clone $end_date)->addDays(intval($days));
            $end_date = $end_date->format('Y-m-d');
            $allow_date = $allow_date->format('Y-m-d');
        } 

        return response()->json([
            'plan' => $plan,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'allow_date' => $allow_date,
            'fine' => $fine,
        ]);
    }
}
