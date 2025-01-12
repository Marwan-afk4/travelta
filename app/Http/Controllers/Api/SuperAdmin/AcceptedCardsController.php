<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AcceptedCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AcceptedCardsController extends Controller
{

    protected $updateCard =['card_name'];

    public function getAllCards(){
        $cards = AcceptedCard::all();
        $data = [
            'cards' => $cards
        ];
        return response()->json($data);
    }

    public function addCard(Request $request){
        $validation = Validator::make($request->all(), [
            'card_name'=>'required|unique:accepted_cards,card_name',
        ]);
        if($validation->fails()){
            return response()->json(['errors' => $validation->errors()], 401);
        }
        $card = AcceptedCard::create([
            'card_name' => $request->card_name,
        ]);
        return response()->json([
            'message' => 'Card name added successfully',
        ]);
    }

    public function deleteCard($id){
        $card = AcceptedCard::find($id);
        $card->delete();
        return response()->json([
            'message' => 'Card name deleted successfully',
        ]);
    }

    public function updateCard(Request $request,$id){
        $card = AcceptedCard::find($id);
        $card->update($request->only($this->updateCard));
        return response()->json([
            'message' => 'Card name updated successfully',
        ]);
    }


}
