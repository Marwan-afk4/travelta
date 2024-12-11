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

class AgentAuthController extends Controller
{
    public function __construct(private Agent $agent, private AffilateAgent $affilate,
    private LegalPaper $legal_paper){}

    public function signup_affilate(SginUpAffilateRequest $request){
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
        
        return response()->json([
            'success' => 'You signup success',
            'user' => $affilate,
            'token' => $affilate->token,
            'legal_papers' => $affilate->legal_papers
        ]);
    }

    public function signup_agent(SginUpAgentRequest $request){
        $agentRequest = $request->validated();
        if ($request->role == 'agent') {
            $agent = $this->agent
            ->create($agentRequest);
        } 
        else {
            $agentRequest['services'] = json_encode($request->services);
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
        
        return response()->json([
            'success' => 'You signup success',
            'user' => $agent,
            'token' => $agent->token,
            'legal_papers' => $agent->legal_papers
        ]);
    }

    public function login(LoginRequest $request){
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
            return response()->json([
                'user' => $user,
                'token' => $user->token,
            ], 200);
        }
        else { 
            return response()->json(['faield'=>'creational not Valid'],403);
        }
    }
}
