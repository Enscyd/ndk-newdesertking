<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use Illuminate\Http\Request;

class DestinationController extends Controller
{

    public function index()
    {
        $destinations = Destination::orderBy('id','desc')->get();

        return view('destination.destination', compact('destinations'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required|string|max:255'
        ]);

        Destination::create([
            'name'=>$request->name
        ]);

        return response()->json(['success'=>true]);
    }


    public function update(Request $request,$id)
    {
        $request->validate([
            'name'=>'required|string|max:255'
        ]);

        $destination = Destination::findOrFail($id);

        $destination->update([
            'name'=>$request->name
        ]);

        return response()->json(['success'=>true]);
    }


    public function destroy($id)
    {
        Destination::findOrFail($id)->delete();

        return response()->json(['success'=>true]);
    }

}