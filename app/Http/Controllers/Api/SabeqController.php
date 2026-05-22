<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use App\Services\SabeqService;
class SabeqController extends Controller
{
    public function areas(SabeqService $sabeq)
    {
        return response()->json(
            $sabeq->getAreas()
        );
    }
}