<?php

namespace App\Http\Controllers;

use App\Models\Truck;
use Illuminate\Http\Request;

class TruckController extends Controller
{

    public function index()
    {
        $trucks = Truck::orderBy('id','desc')->get();

        return view('truck.truck',compact('trucks'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'truckNumber'=>'required|string|max:255'
        ]);

        Truck::create([
            'truckNumber'=>$request->truckNumber
        ]);

        return response()->json(['success'=>true]);
    }


    public function update(Request $request,$id)
    {
        $request->validate([
            'truckNumber'=>'required|string|max:255'
        ]);

        $truck = Truck::findOrFail($id);

        $truck->update([
            'truckNumber'=>$request->truckNumber
        ]);

        return response()->json(['success'=>true]);
    }


    public function destroy($id)
    {
        Truck::findOrFail($id)->delete();

        return response()->json(['success'=>true]);
    }

}