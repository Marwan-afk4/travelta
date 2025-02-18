<?php

namespace App\Http\Controllers\Api\Agent\accounting\OE;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Owner;
use App\Models\CurrencyAgent;

class OwnerTransactionController extends Controller
{
    public function __construct(private Owner $owners, private CurrencyAgent $currencies){}

}
