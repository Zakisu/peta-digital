<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Village;


class DesaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(){
        return view('store.desa.index');
    }

    public function listVillages(){
        $about = Village::all();
        return $about;
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'coordinate' => 'required',
            'image' => 'required',
            'description' => 'required',
        ]);

        $village = New Village;
        $village->title = $request->title;
        $village->coordinate = $request->coordinate; 

        $village->save();

        return $village;
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'coordinate' => 'required',
            'description' => 'required',
        ]);

        return Village::find($id)->update($request->all());
    }

    public function destroy($id)
    {
       return Village::find($id)->delete();

    }


}
