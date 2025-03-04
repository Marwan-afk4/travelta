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

use App\Models\TourDiscount;
use App\Models\TourExtra;
use App\Models\TourHotel;
use App\Models\TourPricing;
use App\Models\TourPricingItems;
use App\Models\TourRoom; 

class CreateTourController extends Controller
{
    public function __construct(private Tour $tour, private TourAvailability $availability,
    private TourCancelation $cancelation, private TourExclude $exclude,
    private TourInclude $include, private TourItinerary $itinerary,
    private TourDestination $destinations,
    private TourDiscount $discounts, private TourExtra $extra, private TourHotel $hotels, 
    private TourPricing $pricing,private TourRoom $tour_room,
    private TourPricingItems $pricing_item){}
    use image;
    protected $tourRequest = [
        'name',
        'description',
        'video_link',
        'tour_type',
        'status',
        'days',
        'price',
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
        'arrival',
        'enable_person_type',
        'with_accomodation',
        'enabled_extra_price',
    ];

    public function create(TourRequest $request){
        // /agent/tour/add
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
        
        // arrival
        // ----- Pricing -----
        // enable_person_type, with_accomodation, enabled_extra_price,
        // discounts[{from, to, discount, type => [precentage, fixed]}]
        // extra[{name, price, currency_id, type => [one_time, person, night]}]
        // hotels[{name}]
        // price, currency_id
        // pricing[{person_type => [adult,child,infant], min_age, max_age, pricing_item[{currency_id, price, type => [precentage,fixed]}]}]
        // tour_room[{adult_single, adult_double, adult_triple, adult_quadruple, 
        // children_single, children_double, children_triple, children_quadruple}]
        // {"name": "Tour1","description": "Tour Description1","video_link": "Link1","status": "0","days": "11","nights": "11","tour_type_id": "1","featured": "no","featured_from": "2025-09-11","featured_to": "2025-10-11","deposit": "111","deposit_type": "precentage","tax": "11","tax_type": "precentage","pick_up_country_id": "1","pick_up_city_id": "1","pick_up_map": "dfdf11","destination_type": "single","tour_email": "ahmed11@gmail.com","tour_website": "Web11","tour_phone": "0111111","tour_address": "address111","payments_options": "fdgdf111","policy": "sdfsd111","cancelation": "0","destinations": [    {"country_id": "1","city_id": "1","arrival_map": "dsfsd111"    }],"availability": [    {"date": "2025-09-11","last_booking": "2025-11-09","quantity": "11"    }],"cancelation_items": [    {"type": "precentage","amount": "11111","days": "11"    }],"excludes": [    {"name": "Exclude11"    }],"includes": [    {"name": "Include11"    }],"itinerary": [    {"image": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAQMAAADCCAMAAAB6zFdcAAAAz1BMVEX///8AMIUBnN4CIGkAEXzl6PABnd8AIYAAldwAmd0Amt0CIWsBoeMAltwAKIIALoQAJYEAGX0CEGCUnr8AHn8AFX0ACXrT2OUAK4MBKnoBJHACFmMBfb4CGmXy9PgbPYsABXrGy93F4vUBkNKi0e NmLzm8/sCEmEupeHQ6Pez2fJxf60qR5BabKODj7ers801T5S4v9VldahKX5sAS41SsOQBa6xmuOcBVpcBYKGCw oCJW0CBl2yutEBcrMBUJECPoECM3cGUp2NyOwDUJuWIrmTAAAEzklEQVR4nO3cW1caSRQFYGgRu5u ICLhLooKxmsSNUYzZmYy//83TXMRGdza1dU4tU65v7eslYeqY9U5bLq1UCAiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiv1N9/W75te4Tvrn9YbpRSNRvXs/OLyk m1vpe9raKKZlSt7w6ujmysw5ddpRI8VWKr1Dyy7macVrPUIBFvDU4tq8JenLEGiWpjx/Sy16qRvQTJWdi sugofMrUDp5F0abppa/NH3W9GiTd0ZqTcJS1JT6fhD3Ta1 X80i3BsX6henFr0lTYyw8GdjREvpaY2Eu mp6 WuhOxbmB8GKtnipOxam6pem178OF9pjYSI6N73 dfjWzFODYt30 tehmmMsJBoWNIRcYyGxa8G3CfnGQrG4/cX0DvLTTgsW1eBI7Xs0q2vwVT8tzGqwb3oH b3yJVKrXVnYmFn8s9Jut1qt X 0oSfisdDeUFCZFKIkPzVtlmANVEow1SqZ3kF  9vwJijXYKP92fQWcsOJSekqTPWuwxvTe8gLJyb1Y9C5Dbyx6U3khBOTeg1qw7IjvQjwGGRoB5XAcRzvu lt5IETk3oNeneTGjjugemN5IATk3pLrHXLkxr4gemN5IATk3oNpldB EHAiUm5BMlUmNXAF9wRcGJSvwp 2ZkfBNM70XeGEpNyS zczY9BUoMT01vRBtOCcg2ej4ETHpreii6cmFRr0PmxOAaOd296L7pwYlIeC4tTILkp4sRUSd/99CZ0g6UaiE1O G0sxZtwvVQCxxNbgxyJqfdQXroKgs9BjsQ0CYzLNZDaD3Ikpp/LzUDyXNBPTKslcDypnw 0E1NttQROKDU0aSamTmW4WgLHPTa9GU16ienn9X8mwvwumN6LLviMKaUljh66wcsSyB2NmcdCr/bwCCogODJlTEy9Ue26G7zoBLLbgXJi6vU6o1rl16MPz8DkKoj9dv0SjoVK8gMf1ZaMHv78ddsdBsFrFRB8FV5JTL3R3ePwmVcOJrsHo2D5Koh9MQsmpvgvf7rlhbf2Pif2g3KhAK9C5KvseuUYSO2IODE1f DGb kxgIkp6mY/BqHpneiDiSkaZq6BK/gtDJiYqpnbgeiHzigxxcWs7UD2ywcoMcX/ZKyBJ/mJM37GlHUsyD4FODE1bzPVwBUbmWdgYso0Gn1XbEyY20FjIcNo9N2x2I HT2BiilRL4IdlwR8LnlyBxBTvKbUD33PHFlQAJ6bm32/XwPd9zwvd4F7uGxfLsiSm5OeeCEM/GN/cHx6I/bJgFU5Mj6gGXvnwxJp9L1NPTH7Z9FrfC/yrBzAxiX2UmAr91YM4RldB7KPEVDAxnaEayP2mLA0aCzEcjYJfv3ybemKS /wkjXpiEvuWTSr4Vl4VjUbvt m1vhf4e0xV EjZimSAfIOvX8DRaEc2AGLlLxPlPktMM1AdC3LfskmFagDHgtyXcFOdDrZe2IVjQe7DxFT7Oy956CpYm5gwF40FaxMTdBKisWBtYoI wxqYXtX/6zfoB6J/g1XDdx/UwNrEhI1BDexNTBhqB/YmJugYjUbBv7 q4wTU4KO1xGPX81d4H wYFAoH48XLqcHMzUcrARERERERERERERERERERERERERER0UfxL37waieue1ChAAAAAElFTkSuQmCC","day_name": "First11","day_description": "Description11","content": "Content11"    }],"tour_type": "private","enable_person_type": "1","with_accomodation": "1","enabled_extra_price": "1","arrival": "2025-05-05","discounts": [    {"from": "10","to": "20","discount": "20","type": "precentage"    }],"extra": [    {"name": "Extra","price": "245","currency_id": "1","type": "one_time"    }],"hotels": [    {"name": "Hilton"    }],"tour_price": [    {"price": "255","currency_id": "1"    }],"pricing": [    {"person_type": "adult","min_age": "22","max_age": "66","pricing_item": [    {"currency_id": "1","price": "455","type": "fixed"    }]    }],"tour_room": [    {"adult_single": "1","adult_double": "2","adult_triple": "3","adult_quadruple": "4","children_single": "2","children_double": "4","children_triple": "6","children_quadruple": "8"    }]}
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
        if (!empty($request->image)) {
            $image = $this->storeBase64Image($request->image, 'agent/inventory/tour/images');
            $tourRequest['image'] = $image;
        }
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
                        'remaining' => $item['quantity'],
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
            // Pricing
             if ($request->discounts) {
                $discounts = $request->discounts;
                foreach ($discounts as $item) {
                    $this->discounts
                    ->create([
                        'tour_id' => $tour->id,
                        'from' => $item['from'],
                        'to' => $item['to'],
                        'discount' => $item['discount'],
                        'type' => $item['type'],
                    ]);
                }
             }
             if ($request->extra) {
                $extra = $request->extra;
                foreach ($extra as $item) {
                    $this->extra
                    ->create([
                        'tour_id' => $tour->id,
                        'name' => $item['name'],
                        'price' => $item['price'],
                        'currency_id' => $item['currency_id'],
                        'type' => $item['type'],
                    ]);
                }
             }
             if ($request->hotels) {
                $hotels = $request->hotels;
                foreach ($hotels as $item) {
                    $this->hotels
                    ->create([
                        'tour_id' => $tour->id,
                        'name' => $item['name'],
                    ]);
                }
             } 
             if ($request->pricing) {
                $pricing = $request->pricing;
                foreach ($pricing as $item) {
                    $pricing_data = $this->pricing
                    ->create([
                        'tour_id' => $tour->id,
                        'person_type' => $item['person_type'],
                        'min_age' => $item['min_age'],
                        'max_age' => $item['max_age'],
                    ]);
                    if (isset($item['pricing_item'])) {
                        $pricing_item = $item['pricing_item'];
                        foreach ($pricing_item as $element) {
                            $this->pricing_item
                            ->create([        
                                'tour_pricing_id' => $pricing_data->id,
                                'currency_id' => $element['currency_id'],
                                'price' => $element['price'],
                                'type' => $element['type'],
                                'tour_pricing_items' => $pricing_data->id,
                            ]);
                        }
                    }
                }
             }
             if ($request->tour_room) {
                $tour_room = $request->tour_room;
                foreach ($tour_room as $item) {
                    $this->tour_room
                    ->create([
                        'tour_id' => $tour->id,
                        'adult_single' => $item['adult_single'],
                        'adult_double' => $item['adult_double'],
                        'adult_triple' => $item['adult_triple'],
                        'adult_quadruple' => $item['adult_quadruple'], 
                        'children_single' => $item['children_single'],
                        'children_double' => $item['children_double'],
                        'children_triple' => $item['children_triple'],
                        'children_quadruple' => $item['children_quadruple'], 
                    ]);
                }
             }
            return response()->json([
                "success" => "You add data success", 
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
        // /agent/tour/update/{id}
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
        
        // arrival
        // ----- Pricing -----
        // enable_person_type, with_accomodation, enabled_extra_price,
        // discounts[{from, to, discount, type => [precentage, fixed]}]
        // extra[{name, price, currency_id, type => [one_time, person, night]}]
        // hotels[{name}]
        // tour_price[{price, currency_id}]
        // pricing[{person_type => [adult,child,infant], min_age, max_age, pricing_item[{currency_id, price, type => [precentage,fixed]}]}]
        // tour_room[{adult_single, adult_double, adult_triple, adult_quadruple, 
        // children_single, children_double, children_triple, children_quadruple}]
        // {"name": "Tour1","description": "Tour Description1","video_link": "Link1","status": "0","days": "11","nights": "11","tour_type_id": "1","featured": "no","featured_from": "2025-09-11","featured_to": "2025-10-11","deposit": "111","deposit_type": "precentage","tax": "11","tax_type": "precentage","pick_up_country_id": "1","pick_up_city_id": "1","pick_up_map": "dfdf11","destination_type": "single","tour_email": "ahmed11@gmail.com","tour_website": "Web11","tour_phone": "0111111","tour_address": "address111","payments_options": "fdgdf111","policy": "sdfsd111","cancelation": "0","destinations": [    {"country_id": "1","city_id": "1","arrival_map": "dsfsd111"    }],"availability": [    {"date": "2025-09-11","last_booking": "2025-11-09","quantity": "11"    }],"cancelation_items": [    {"type": "precentage","amount": "11111","days": "11"    }],"excludes": [    {"name": "Exclude11"    }],"includes": [    {"name": "Include11"    }],"itinerary": [    {"image": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAQMAAADCCAMAAAB6zFdcAAAAz1BMVEX///8AMIUBnN4CIGkAEXzl6PABnd8AIYAAldwAmd0Amt0CIWsBoeMAltwAKIIALoQAJYEAGX0CEGCUnr8AHn8AFX0ACXrT2OUAK4MBKnoBJHACFmMBfb4CGmXy9PgbPYsABXrGy93F4vUBkNKi0e NmLzm8/sCEmEupeHQ6Pez2fJxf60qR5BabKODj7ers801T5S4v9VldahKX5sAS41SsOQBa6xmuOcBVpcBYKGCw oCJW0CBl2yutEBcrMBUJECPoECM3cGUp2NyOwDUJuWIrmTAAAEzklEQVR4nO3cW1caSRQFYGgRu5u ICLhLooKxmsSNUYzZmYy//83TXMRGdza1dU4tU65v7eslYeqY9U5bLq1UCAiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiv1N9/W75te4Tvrn9YbpRSNRvXs/OLyk m1vpe9raKKZlSt7w6ujmysw5ddpRI8VWKr1Dyy7macVrPUIBFvDU4tq8JenLEGiWpjx/Sy16qRvQTJWdi sugofMrUDp5F0abppa/NH3W9GiTd0ZqTcJS1JT6fhD3Ta1 X80i3BsX6henFr0lTYyw8GdjREvpaY2Eu mp6 WuhOxbmB8GKtnipOxam6pem178OF9pjYSI6N73 dfjWzFODYt30 tehmmMsJBoWNIRcYyGxa8G3CfnGQrG4/cX0DvLTTgsW1eBI7Xs0q2vwVT8tzGqwb3oH b3yJVKrXVnYmFn8s9Jut1qt X 0oSfisdDeUFCZFKIkPzVtlmANVEow1SqZ3kF  9vwJijXYKP92fQWcsOJSekqTPWuwxvTe8gLJyb1Y9C5Dbyx6U3khBOTeg1qw7IjvQjwGGRoB5XAcRzvu lt5IETk3oNeneTGjjugemN5IATk3pLrHXLkxr4gemN5IATk3oNpldB EHAiUm5BMlUmNXAF9wRcGJSvwp 2ZkfBNM70XeGEpNyS zczY9BUoMT01vRBtOCcg2ej4ETHpreii6cmFRr0PmxOAaOd296L7pwYlIeC4tTILkp4sRUSd/99CZ0g6UaiE1O G0sxZtwvVQCxxNbgxyJqfdQXroKgs9BjsQ0CYzLNZDaD3Ikpp/LzUDyXNBPTKslcDypnw 0E1NttQROKDU0aSamTmW4WgLHPTa9GU16ienn9X8mwvwumN6LLviMKaUljh66wcsSyB2NmcdCr/bwCCogODJlTEy9Ue26G7zoBLLbgXJi6vU6o1rl16MPz8DkKoj9dv0SjoVK8gMf1ZaMHv78ddsdBsFrFRB8FV5JTL3R3ePwmVcOJrsHo2D5Koh9MQsmpvgvf7rlhbf2Pif2g3KhAK9C5KvseuUYSO2IODE1f DGb kxgIkp6mY/BqHpneiDiSkaZq6BK/gtDJiYqpnbgeiHzigxxcWs7UD2ywcoMcX/ZKyBJ/mJM37GlHUsyD4FODE1bzPVwBUbmWdgYso0Gn1XbEyY20FjIcNo9N2x2I HT2BiilRL4IdlwR8LnlyBxBTvKbUD33PHFlQAJ6bm32/XwPd9zwvd4F7uGxfLsiSm5OeeCEM/GN/cHx6I/bJgFU5Mj6gGXvnwxJp9L1NPTH7Z9FrfC/yrBzAxiX2UmAr91YM4RldB7KPEVDAxnaEayP2mLA0aCzEcjYJfv3ybemKS /wkjXpiEvuWTSr4Vl4VjUbvt m1vhf4e0xV EjZimSAfIOvX8DRaEc2AGLlLxPlPktMM1AdC3LfskmFagDHgtyXcFOdDrZe2IVjQe7DxFT7Oy956CpYm5gwF40FaxMTdBKisWBtYoI wxqYXtX/6zfoB6J/g1XDdx/UwNrEhI1BDexNTBhqB/YmJugYjUbBv7 q4wTU4KO1xGPX81d4H wYFAoH48XLqcHMzUcrARERERERERERERERERERERERERER0UfxL37waieue1ChAAAAAElFTkSuQmCC","day_name": "First11","day_description": "Description11","content": "Content11"    }],"tour_type": "private","enable_person_type": "1","with_accomodation": "1","enabled_extra_price": "1","arrival": "2025-05-05","discounts": [    {"from": "10","to": "20","discount": "20","type": "precentage"    }],"extra": [    {"name": "Extra","price": "245","currency_id": "1","type": "one_time"    }],"hotels": [    {"name": "Hilton"    }],"tour_price": [    {"price": "255","currency_id": "1"    }],"pricing": [    {"person_type": "adult","min_age": "22","max_age": "66","pricing_item": [    {"currency_id": "1","price": "455","type": "fixed"    }]    }],"tour_room": [    {"adult_single": "1","adult_double": "2","adult_triple": "3","adult_quadruple": "4","children_single": "2","children_double": "4","children_triple": "6","children_quadruple": "8"    }]}
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
        $tour = $this->tour
        ->where('id', $id)
        ->where($agent_type, $agent_id)
        ->first();
        if (!empty($request->image)) {
            $image = $this->storeBase64Image($request->image, 'agent/inventory/tour/images');
            $tourRequest['image'] = $image;
            $this->deleteImage($tour->image);
        }
        if (empty($tour)) {
            return response()->json([
                'faild' => 'tour is not found'
            ], 400);
        }
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
                        'remaining' => $item['quantity'],
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
            // Pricing
            $this->discounts
            ->where('tour_id', $tour->id)
            ->delete();
            if ($request->discounts) {
                $discounts = $request->discounts;
                foreach ($discounts as $item) {
                    $this->discounts
                    ->create([
                        'tour_id' => $tour->id,
                        'from' => $item['from'],
                        'to' => $item['to'],
                        'discount' => $item['discount'],
                        'type' => $item['type'],
                    ]);
                }
             }
             $this->extra
             ->where('tour_id', $tour->id)
             ->delete();
             if ($request->extra) {
                $extra = $request->extra;
                foreach ($extra as $item) {
                    $this->extra
                    ->create([
                        'tour_id' => $tour->id,
                        'name' => $item['name'],
                        'price' => $item['price'],
                        'currency_id' => $item['currency_id'],
                        'type' => $item['type'],
                    ]);
                }
             }
             $this->hotels
             ->where('tour_id', $tour->id)
             ->delete();
             if ($request->hotels) {
                $hotels = $request->hotels;
                foreach ($hotels as $item) {
                    $this->hotels
                    ->create([
                        'tour_id' => $tour->id,
                        'name' => $item['name'],
                    ]);
                }
             } 
             $this->pricing
             ->where('tour_id', $tour->id)
             ->delete();
             if ($request->pricing) {
                $pricing = $request->pricing;
                foreach ($pricing as $item) {
                    $pricing_data = $this->pricing
                    ->create([
                        'tour_id' => $tour->id,
                        'person_type' => $item['person_type'],
                        'min_age' => $item['min_age'],
                        'max_age' => $item['max_age'],
                    ]);
                    if (isset($item['pricing_item'])) {
                        $pricing_item = $item['pricing_item'];
                        foreach ($pricing_item as $element) {
                            $this->pricing_item
                            ->create([        
                                'tour_pricing_id' => $pricing_data->id,
                                'currency_id' => $element['currency_id'],
                                'price' => $element['price'],
                                'type' => $element['type'],
                                'tour_pricing_items' => $pricing_data->id,
                            ]);
                        }
                    }
                }
             }
             $this->tour_room
             ->where('tour_id', $tour->id)
             ->delete();
             if ($request->tour_room) {
                $tour_room = $request->tour_room;
                foreach ($tour_room as $item) {
                    $this->tour_room
                    ->create([
                        'tour_id' => $tour->id,
                        'adult_single' => $item['adult_single'],
                        'adult_double' => $item['adult_double'],
                        'adult_triple' => $item['adult_triple'],
                        'adult_quadruple' => $item['adult_quadruple'], 
                        'children_single' => $item['children_single'],
                        'children_double' => $item['children_double'],
                        'children_triple' => $item['children_triple'],
                        'children_quadruple' => $item['children_quadruple'], 
                    ]);
                }
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

    public function delete(Request $request, $id){
        // /agent/tour/delete/{id}
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
        $tour = $this->tour
        ->where('id', $id)
        ->where($role, $agent_id)
        ->first();
        if (empty($tour)) {
            return response()->json([
                'errors' => 'tour is not found'
            ], 400);
        }
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
