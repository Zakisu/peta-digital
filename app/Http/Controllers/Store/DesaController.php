<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Village;
use File;


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
        $villages = Village::all();
        return $villages;
    }

    public function create(){
        return view('store.desa.create');
    }

    public function store(Request $request)
    {

        $request->validate([
            'title' => 'required',
            'coordinate' => 'required',
            'images' => 'required',
            'description' => 'required',
        ]);
        $files = [];
        if($request->hasfile('images')){
            foreach ($request->file('images') as $imagefile) {
                $extension = $imagefile->getClientOriginalExtension();
                $filename = time().rand(1,100) . '.' . $extension;
                $imagefile->move('uploads/villages/', $filename);
                $files[] = $filename;
            }
        }

        $village = New Village;
        $village->title = $request->title; 
        $village->coordinate = $request->coordinate; 
        $village->description = $request->description; 
        $village->image = json_encode($files); 
        $village->save();

        return $village;
    }

    public function edit(){
        return view('store.desa.edit');
    }

    public function detail($id){
        $village = Village::find($id);
        return $village;
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'coordinate' => 'required',
            'description' => 'required',
        ]);

        $village = Village::find($id);

        $items = array();
        $images = json_decode($village->image);
        if($request->hasfile('images')){
            foreach ($images as $imagefile) {
                $image = public_path('uploads/villages/'.$imagefile);
                $items[] = $image;
            }
            File::delete($items);

            foreach ($request->file('images') as $imagefile) {
                $extension = $imagefile->getClientOriginalExtension();
                $filename = time().rand(1,100) . '.' . $extension;
                $imagefile->move('uploads/villages/', $filename);
                $files[] = $filename;
            }
            $village->save();
        }

        $village->update($request->all());
        return $village;
    }

    public function destroy($id)
    {
        $village = Village::find($id);
        $images = json_decode($village->image);

        $items = array();
        foreach ($images as $imagefile) {
            $image = public_path('uploads/villages/'.$imagefile);
            $items[] = $image;
        }
        File::delete($items);
        return $village->delete();
    }
}
