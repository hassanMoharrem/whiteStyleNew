<?php

namespace App\Http\Controllers\Api\Site;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;

class CitiesController extends Controller
{
    /**
     * Get all cities
     */
    public function index(request $request)
    {
        $per_page = $request->query('per_page', 10);
        $cities = City::orderBy('name', 'asc')->paginate($per_page);

        return response()->json([
            'status' => true,
            'data' => ['cities' => $cities],
            'message' => 'Cities retrieved successfully'
        ]);
    }
}
