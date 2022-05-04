<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\About;
use App\Models\Village;

class APIController extends Controller
{
    public function getAbout(){
        $about = About::first();
        return response()->json([
            'status' => 'Success',
            'data' => $about,
        ]);
    }

    public function getVillages(){
        $villages = Village::paginate(3);
        return response()->json([
            'status' => 'Success',
            'size' => sizeof($villages),
            'data' => $villages,
        ]);
    }

    public function getDetailVillage($id){
        $village = Village::find($id);
        return response()->json([
            'status' => 'Success',
            'data' => $village,
        ]);
    }
}
