<?php

namespace App\Http\Controllers\Api\Agent\inventory\tour\tour;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\trait\image;
use App\Http\Requests\api\agent\inventory\tour\TourRequest;

use App\Models\Tour;
use App\Models\TourAvailability;
use App\Models\TourCancelation;
use App\Models\TourExclude;
use App\Models\TourInclude;
use App\Models\TourItinerary;
use App\Models\TourDestination;

class CreateTourController extends Controller
{
    public function __construct(private Tour $tour, private TourAvailability $availability,
    private TourCancelation $cancelation, private TourExclude $exclude,
    private TourInclude $include, private TourItinerary $itinerary,
    private TourDestination $destinations){}
    use image;
    protected $tourRequest = [
        'name', 
        'description', 
        'video_link', 
        'tour_type', 
        'status', 
        'days', 
        'nights', 
        'tour_type_id', 
        'featured', 
        'featured_from', 
        'featured_to', 
        'deposit', 
        'deposit_type', 
        'tax', 
        'tax_type', 
        'pick_up_country_id', 
        'pick_up_city_id', 
        'pick_up_map', 
        'destination_type',
        'tour_email', 
        'tour_website', 
        'tour_phone', 
        'tour_address', 
        'payments_options', 
        'policy', 
        'cancelation', 
    ]; 

    public function create(TourRequest $request){
        // Keys
        // name, description, video_link, tour_type[private, group], status, days, 
        // nights, tour_type_id, featured[yes, no], featured_from, featured_to, 
        // deposit, deposit_type[precentage, fixed], tax, tax_type[precentage, fixed], pick_up_country_id, 
        // pick_up_city_id, pick_up_map, destination_type[single, multiple], 
        // tour_email, tour_website, tour_phone, tour_address, payments_options, 
        // policy, cancelation,
        // destinations [{country_id, city_id, arrival_map}]
        // availability [{date, last_booking, quantity}]
        // cancelation_items [{type[precentage, fixed], amount, days}] 
        // excludes [{name}] 
        // includes [{name}]
        // itinerary [{image, day_name, day_description, content}]
        // {"name": "Tour1","description": "Tour Description1","video_link": "Link1","status": "0","days": "11","nights": "11","tour_type_id": "1","featured": "no","featured_from": "2025-09-11","featured_to": "2025-10-11","deposit": "111","deposit_type": "precentage","tax": "11","tax_type": "precentage","pick_up_country_id": "1","pick_up_city_id": "1","pick_up_map": "dfdf11","destination_type": "single","tour_email": "ahmed11@gmail.com","tour_website": "Web11","tour_phone": "0111111","tour_address": "address111","payments_options": "fdgdf111","policy": "sdfsd111","cancelation": "0","destinations": [{  "country_id": "1", "city_id": "1", "arrival_map": "dsfsd111" } ],"availability": [ {"date": "2025-09-11", "last_booking": "11", "quantity": "11"}],"cancelation_items": [    {"type": "precentage","amount": "11111","days": "11"    }],"excludes": [    {"name": "Exclude11"    }],"includes": [    {"name": "Include11"    }],"itinerary": [    {"image":"base64""day_name": "First11","day_description": "Description11","content": "Content11"    }],"tour_type": "private"}
        
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
            $agent_type = 'affilate_id';
        }
        else{
            $agent_type = 'agent_id';
        }
        $tourRequest = $request->only($this->tourRequest);
        $tourRequest[$agent_type] = $agent_id;
        $tour = $this->tour
        ->create($tourRequest);
        try{
            if ($request->destinations) {
                foreach ($request->destinations as $item) {
                    $this->destinations
                    ->create([
                        'tour_id' => $tour->id,
                        'country_id' => $item['country_id'],
                        'city_id' => $item['city_id'],
                        'arrival_map' => $item['arrival_map'],
                    ]);
                }
            }
            if ($request->availability) {
                foreach ($request->availability as $item) {
                    $this->availability
                    ->create([
                        'tour_id' => $tour->id,
                        'date' => $item['date'],
                        'last_booking' => $item['last_booking'],
                        'quantity' => $item['quantity'],
                    ]);
                }
            }
            if ($request->cancelation_items) {
                foreach ($request->cancelation_items as $item) {
                    $this->cancelation
                    ->create([
                        'tour_id' => $tour->id,
                        'type' => $item['type'],
                        'amount' => $item['amount'],
                        'days' => $item['days'],
                    ]);
                }
            }
            if ($request->excludes) {
                foreach ($request->excludes as $item) {
                    $this->exclude
                    ->create([
                        'tour_id' => $tour->id,
                        'name' => $item['name'], 
                    ]);
                }
            }
            if ($request->includes) {
                foreach ($request->includes as $item) {
                    $this->include
                    ->create([
                        'tour_id' => $tour->id,
                        'name' => $item['name'], 
                    ]);
                }
            }
            if ($request->itinerary) {
                foreach ($request->itinerary as $item) {
                    $image = null;
                    if (!empty($item['image'])) {
                        $image = $this->storeBase64Image($item['image'], 'agent/inventory/tour/itinerary');
                    }
                    $this->itinerary
                    ->create([
                        'tour_id' => $tour->id,
                        'day_name' => $item['day_name'], 
                        'day_description' => $item['day_description'] ?? null, 
                        'content' => $item['content'] ?? null, 
                        'image' => $image ?? null, 
                    ]);
                }
            }
            return response()->json([
                "success" => "You add data success"
            ]);
        } 
        catch (\Throwable $th) {
            $tour->delete();
            return response()->json([
                "errors" => "Something error"
            ], 400);
        }
    }

    public function modify(TourRequest $request, $id){
        // هتبعت id itinerary لو قديم
        // Keys
        // name, description, video_link, tour_type[private, group], status, days, 
        // nights, tour_type_id, featured[yes, no], featured_from, featured_to, 
        // deposit, deposit_type[precentage, fixed], tax, tax_type[precentage, fixed], pick_up_country_id, 
        // pick_up_city_id, pick_up_map, destination_type[single, multiple], 
        // tour_email, tour_website, tour_phone, tour_address, payments_options, 
        // policy, cancelation,
        // destinations [{country_id, city_id, arrival_map}]
        // availability [{date, last_booking, quantity}]
        // cancelation_items [{type[precentage, fixed], amount, days}] 
        // excludes [{name}] 
        // includes [{name}]
        // itinerary [{id, image, day_name, day_description, content}]
        // {"name": "Tour1","description": "Tour Description1","video_link": "Link1","status": "0","days": "11","nights": "11","tour_type_id": "1","featured": "no","featured_from": "2025-09-11","featured_to": "2025-10-11","deposit": "111","deposit_type": "precentage","tax": "11","tax_type": "precentage","pick_up_country_id": "1","pick_up_city_id": "1","pick_up_map": "dfdf11","destination_type": "single","tour_email": "ahmed11@gmail.com","tour_website": "Web11","tour_phone": "0111111","tour_address": "address111","payments_options": "fdgdf111","policy": "sdfsd111","cancelation": "0","destinations": [{  "country_id": "1", "city_id": "1", "arrival_map": "dsfsd111" } ],"availability": [ {"date": "2025-09-11", "last_booking": "11", "quantity": "11"}],"cancelation_items": [    {"type": "precentage","amount": "11111","days": "11"    }],"excludes": [    {"name": "Exclude11"    }],"includes": [    {"name": "Include11"    }],"itinerary": [    {"image":"base64""day_name": "First11","day_description": "Description11","content": "Content11"    }],"tour_type": "private"}
        $tourRequest = $request->only($this->tourRequest);
        $tour = $this->tour
        ->where('id', $id)
        ->first();
        $tour->update($tourRequest);
        $itinerary_ids = [];
        try{
            $this->destinations
            ->where('tour_id', $tour->id)
            ->delete();
            if ($request->destinations) {
                foreach ($request->destinations as $item) {
                    $this->destinations
                    ->create([ 
                        'tour_id' => $tour->id,
                        'country_id' => $item['country_id'],
                        'city_id' => $item['city_id'],
                        'arrival_map' => $item['arrival_map'],
                    ]);
                }
            }
            $this->availability
            ->where('tour_id', $tour->id)
            ->delete();
            if ($request->availability) {
                foreach ($request->availability as $item) {
                    $this->availability
                    ->create([
                        'tour_id' => $tour->id,
                        'date' => $item['date'],
                        'last_booking' => $item['last_booking'],
                        'quantity' => $item['quantity'],
                    ]);
                }
            }
            $this->cancelation
            ->where('tour_id', $tour->id)
            ->delete();
            if ($request->cancelation_items) {
                foreach ($request->cancelation_items as $item) {
                    $this->cancelation
                    ->create([
                        'tour_id' => $tour->id,
                        'type' => $item['type'],
                        'amount' => $item['amount'],
                        'days' => $item['days'],
                    ]);
                }
            }
            $this->exclude
            ->where('tour_id', $tour->id)
            ->delete();
            if ($request->excludes) {
                foreach ($request->excludes as $item) {
                    $this->exclude
                    ->create([
                        'tour_id' => $tour->id,
                        'name' => $item['name'], 
                    ]);
                }
            }
            $this->include
            ->where('tour_id', $tour->id)
            ->delete();
            if ($request->includes) {
                foreach ($request->includes as $item) {
                    $this->include
                    ->create([
                        'tour_id' => $tour->id,
                        'name' => $item['name'], 
                    ]);
                }
            }
            if ($request->itinerary) {
                foreach ($request->itinerary as $item) {
                    if (!empty($item['image'])) {
                        $image = $this->storeBase64Image($item['image'], 'agent/inventory/tour/itinerary');
                    }
                    if (isset($item['id']) && is_numeric($item['id'])) {
                        $itinerary = $this->itinerary
                        ->where('id', $item['id'])
                        ->first();
                        if (!empty($item['image'])) {
                            $this->deleteImage($itinerary->image);
                        }
                        $itinerary->update([
                            'tour_id' => $tour->id,
                            'day_name' => $item['day_name'], 
                            'day_description' => $item['day_description'] ?? null, 
                            'content' => $item['content'] ?? null, 
                            'image' => $image ?? null, 
                        ]);
                        $itinerary_ids[] = $item['id'];
                    } 
                    else {
                        $itinerary_item = $this->itinerary
                        ->create([
                            'tour_id' => $tour->id,
                            'day_name' => $item['day_name'], 
                            'day_description' => $item['day_description'] ?? null, 
                            'content' => $item['content'] ?? null, 
                            'image' => $image ?? null, 
                        ]);
                        $itinerary_ids[] = $itinerary_item->id;
                    }
                }
                $this->itinerary
                ->where('tour_id', $tour->id)
                ->whereNotIn('id', $itinerary_ids)
                ->delete();
            }
            return response()->json([
                "success" => "You update data success",
            ]);
        } 
        catch (\Throwable $th) { 
            return response()->json([
                "errors" => "Something error"
            ], 400);
        }
    }

    public function delete($id){
        $tour = $this->tour
        ->where('id', $id)
        ->first();
        $itinerary = $this->itinerary
        ->where('tour_id', $id)
        ->get();
        foreach ($itinerary as $item) { 
            $this->deleteImage($item->image);
        }

        $tour->delete();

        return response()->json([
            'success' => 'You delete success'
        ]);
    }
}
