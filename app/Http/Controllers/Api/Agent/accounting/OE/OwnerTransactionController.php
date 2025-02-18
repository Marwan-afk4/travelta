<?php

namespace App\Http\Controllers\Api\Agent\accounting\OE;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\OwnerTransaction;

class OwnerTransactionController extends Controller
{
    public function __construct(private OwnerTransaction $owner_transactions){}

}
