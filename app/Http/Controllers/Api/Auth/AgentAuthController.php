<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\api\auth\agent\LoginRequest;
use App\Http\Requests\api\auth\agent\SginUpAffilateRequest;
use App\Http\Requests\api\auth\agent\SginUpAgentRequest;

use App\Models\Agent;
use App\Models\LegalPaper;
use App\Models\AffilateAgent;
use App\Models\Plan;
use App\Models\CustomerSource;
use App\Models\City;
use App\Models\Country;

class AgentAuthController extends Controller
{
    public function __construct(private Agent $agent, private AffilateAgent $affilate,
    private LegalPaper $legal_paper, private Plan $plans, private CustomerSource $sources,
    private City $cities, private Country $countries){}

    public function lists(){
        $sources = $this->sources
        ->get();
        $cities = $this->cities
        ->get();
        $countries = $this->countries
        ->get();
        $services = [
            [
                'name' => 'hotels'
            ],
            [
                'name' => 'tours'
            ],
            [
                'name' => 'flight'
            ],
            [
                'name' => 'visas'
            ],
            [
                'name' => 'service'
            ],
            [
                'name' => 'umrah'
            ],
            [
                'name' => 'activities'
            ],
        ];

        return response()->json([
            'sources' => $sources,
            'cities' => $cities,
            'countries' => $countries,
            'services' => $services,
        ]);
    }

    public function signup_affilate(SginUpAffilateRequest $request){
        // Keys
        // f_name, l_name, email, phone, password, image_type => [passport, national], 
        // passport_image OR national_image1, national_image2, role => [affilate, freelancer]
        $affilateRequest = $request->validated();
        $affilate = $this->affilate
        ->create($affilateRequest);
        if ($request->image_type == 'passport') {
            $this->legal_paper
            ->create([
                'image' => $request->passport_image,
                'type' => $request->image_type,
                'affilate_id' => $affilate->id, 
            ]);
        } 
        else {
            $this->legal_paper
            ->create([
                'image' => $request->national_image1,
                'type' => $request->image_type,
                'affilate_id' => $affilate->id, 
            ]);
            $this->legal_paper
            ->create([
                'image' => $request->national_image2,
                'type' => $request->image_type,
                'affilate_id' => $affilate->id, 
            ]);
        }
        $affilate->token = $affilate->createToken($affilate->role)->plainTextToken;
        
        if ($affilate->role == 'freelancer') {
            $plans = $this->plans
            ->where('type', 'freelancer')
            ->get();
            return response()->json([
                'success' => 'You signup success',
                'user' => $affilate,
                'token' => $affilate->token,
                'legal_papers' => $affilate->legal_papers,
                'plans' => $plans
            ]);
        } 
        else {
            return response()->json([
                'success' => 'You signup success',
                'user' => $affilate,
                'token' => $affilate->token,
                'legal_papers' => $affilate->legal_papers
            ]);
        }
    }

    public function signup_agent(SginUpAgentRequest $request){
        // Keys
        // name, phone, email, address, password, role => [agent, supplier],
        // country_id, city_id, source_id, owner_name, owner_phone, owner_email,
        // tax_card_image, tourism_license_image, commercial_register_image
        // services => [hotels,tours,flight,visas,service,umrah,activities]
        $agentRequest = $request->validated();
        if ($request->role == 'agent') {
            $agent = $this->agent
            ->create($agentRequest);
        } 
        else {
            $agent = $this->agent
            ->create($agentRequest);
        }

        $this->legal_paper
        ->create([
            'image' => $request->tax_card_image,
            'type' => 'Tax Card',
            'agent_id' => $agent->id, 
        ]);
        $this->legal_paper
        ->create([
            'image' => $request->tourism_license_image,
            'type' => 'Tourism License',
            'agent_id' => $agent->id, 
        ]);
        $this->legal_paper
        ->create([
            'image' => $request->commercial_register_image,
            'type' => 'Commercial Register',
            'agent_id' => $agent->id, 
        ]);
        $agent->token = $agent->createToken($agent->role)->plainTextToken;
        
        if ($agent->role == 'agent') {
            $plans = $this->plans
            ->where('type', 'agency')
            ->get();
            return response()->json([
                'success' => 'You signup success',
                'user' => $agent,
                'token' => $agent->token,
                'legal_papers' => $agent->legal_papers,
                'plans' => $plans,
            ]);
        } 
        else {
            $plans = $this->plans
            ->where('type', 'suplier')
            ->get();
            return response()->json([
                'success' => 'You signup success',
                'user' => $agent,
                'token' => $agent->token,
                'legal_papers' => $agent->legal_papers,
                'plans' => $plans,
            ]);
        }
    }

    public function login(LoginRequest $request){
        // https://travelta.online/agent/login
        // Keys
        // email, password
        $user = $this->agent
        ->where('email', $request->email)
        ->orWhere('phone', $request->email)
        ->first();
        if (empty($user)) {
            $user = $this->affilate
            ->where('email', $request->email)
            ->orWhere('phone', $request->email)
            ->first();
        }
        
        if (password_verify($request->input('password'), $user->password)) {
            $user->token = $user->createToken($user->role)->plainTextToken;
            if ((!empty($user->end_date) && $user->end_date > date('Y-m-d')) || 
            $user->role == 'affilate') {
                return response()->json([
                    'user' => $user,
                    'token' => $user->token,
                ], 200);
            }
            else{
                if ($user->role == 'freelancer') {
                    $plans = $this->plans
                    ->where('type', 'freelancer')
                    ->get();
                    return response()->json([
                        'user' => $user,
                        'token' => $user->token,
                        'plans' => $plans,
                    ], 200);
                } 
                elseif ($user->role == 'agent') {
                    $plans = $this->plans
                    ->where('type', 'agency')
                    ->get();
                    return response()->json([
                        'user' => $user,
                        'token' => $user->token,
                        'plans' => $plans,
                    ], 200);
                }
                elseif ($user->role == 'supplier') {
                    $plans = $this->plans
                    ->where('type', 'suplier')
                    ->get(); 
                    return response()->json([
                        'user' => $user,
                        'token' => $user->token,
                        'plans' => $plans,
                    ], 200);
                }
            }
        }
        else { 
            return response()->json(['faield'=>'creational not Valid'],403);
        }
    }
}
