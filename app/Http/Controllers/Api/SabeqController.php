<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use App\Services\SabeqService;
class SabeqController extends Controller
{
public function areas()
{
    $siteUser = \App\Models\User::find(env('DEFAULT_USER_ID'));
    $sabeq = new SabeqService($siteUser);
    return response()->json($sabeq->getAreas());
}
}