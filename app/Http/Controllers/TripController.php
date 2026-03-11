<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\Company;
use App\Models\Destination;
use App\Models\Employee;
use App\Models\Truck;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use NumberToWords\NumberToWords;
use App\Helpers\CurrencyHelper;

class TripController extends Controller
{

    private function queryTrips($request = null)
    {

        $query = Trip::with(['company','destination','employee','truck'])
            ->orderBy('id','desc');

        if($request){

            /* SEARCH */
            if(!empty($request->search)){

                $search = trim($request->search);

                $query->where(function($q) use ($search){

                    $q->where('tripType','like','%'.$search.'%')
                    ->orWhere('tripAmount','like','%'.$search.'%')
                    ->orWhere('driverAmount','like','%'.$search.'%')
                    ->orWhere('omaniName','like','%'.$search.'%')
                    ->orWhere('omaniAmount','like','%'.$search.'%')

                    ->orWhereHas('company',function($q2) use ($search){
                        $q2->where('name','like','%'.$search.'%');
                    })

                    ->orWhereHas('destination',function($q3) use ($search){
                        $q3->where('name','like','%'.$search.'%');
                    })

                    ->orWhereHas('employee',function($q4) use ($search){
                        $q4->where('employeeName','like','%'.$search.'%');
                    })

                    ->orWhereHas('truck',function($q5) use ($search){
                        $q5->where('truckNumber','like','%'.$search.'%');
                    });

                });

            }


            /* COMPANY FILTER */
            if(!empty($request->company)){
                $query->where('companyId',$request->company);
            }


            /* MONTH FILTER */
            if(!empty($request->month)){

                $month = Carbon::parse($request->month)->month;
                $year  = Carbon::parse($request->month)->year;

                $query->whereMonth('tripDate',$month)
                    ->whereYear('tripDate',$year);

            }

            /* DATE FILTER */
            elseif(!empty($request->date)){

                $query->whereDate('tripDate',$request->date);

            }

            /* DEFAULT = TODAY */
            else{

                $query->whereDate('tripDate',Carbon::today());

            }

        }
        else{

            $query->whereDate('tripDate',Carbon::today());

        }

        return $query;
    }


    public function index()
    {

        $companies = Company::all();
        $destinations = Destination::all();
        $employees = Employee::all();
        $trucks = Truck::all();

        $trips = $this->queryTrips()->paginate(20);

        return view('trip.trip',compact(
            'companies',
            'destinations',
            'employees',
            'trucks',
            'trips'
        ));
    }



    public function fetchTrips(Request $request)
    {

        $trips = $this->queryTrips($request)->paginate(20);

        return view('partials.trip_rows',compact('trips'))->render();
    }



    public function store(Request $request)
    {

        $request->validate([
            'companyId'=>'required|exists:companies,id',
            'destinationId'=>'required|exists:destinations,id',
            'employeeId'=>'required|exists:employees,id',
            'truckId'=>'required|exists:trucks,id',
            'tripType'=>'required|in:Go Trip,Return Trip',
            'tripDate'=>'required|date',
            'tripAmount'=>'required|numeric|min:0',
            'driverAmount'=>'nullable|numeric|min:0',
            'isOmani'=>'required|in:Yes,No',
            'omaniName'=>'nullable|string|max:255',
            'omaniAmount'=>'nullable|numeric|min:0',
            'image'=>'nullable|image|max:2048'
        ]);


        $imagePath = null;

        if($request->hasFile('image')){

            $image = $request->file('image');

            $imageName = time().'_'.$image->getClientOriginalName();

            $image->storeAs('trips',$imageName,'public');

            $imagePath = 'trips/'.$imageName;
        }


        if($request->isOmani=="No"){
            $request->merge([
                'omaniName'=>null,
                'omaniAmount'=>null
            ]);
        }


        if($request->tripType=="Return Trip"){
            $request->merge([
                'driverAmount'=>null
            ]);
        }


        Trip::create([
            'companyId'=>$request->companyId,
            'destinationId'=>$request->destinationId,
            'employeeId'=>$request->employeeId,
            'truckId'=>$request->truckId,
            'tripType'=>$request->tripType,
            'driverAmount'=>$request->driverAmount ?? null,
            'tripDate'=>$request->tripDate,
            'tripAmount'=>$request->tripAmount,
            'isOmani'=>$request->isOmani,
            'omaniName'=>$request->omaniName ?? null,
            'omaniAmount'=>$request->omaniAmount ?? null,
            'image'=>$imagePath
        ]);

        return $this->fetchTrips($request);
    }



    public function edit($id)
    {

        $trip = Trip::find($id);

        if(!$trip){
            return response()->json(['error'=>'Trip not found'],404);
        }

        return response()->json($trip);
    }



    public function update(Request $request,$id)
    {

        $trip = Trip::findOrFail($id);

        $request->validate([
            'tripDate'=>'required|date',
            'tripAmount'=>'required|numeric|min:0'
        ]);


        $imagePath = $trip->image;

        if($request->hasFile('image')){

            $image = $request->file('image');

            $imageName = time().'_'.$image->getClientOriginalName();

            $image->storeAs('trips',$imageName,'public');

            $imagePath = 'trips/'.$imageName;
        }


        if($request->isOmani=="No"){
            $request->merge([
                'omaniName'=>null,
                'omaniAmount'=>null
            ]);
        }


        if($request->tripType=="Return Trip"){
            $request->merge([
                'driverAmount'=>null
            ]);
        }


        $trip->update([
            'companyId'=>$request->companyId,
            'destinationId'=>$request->destinationId,
            'employeeId'=>$request->employeeId,
            'truckId'=>$request->truckId,
            'tripType'=>$request->tripType,
            'driverAmount'=>$request->driverAmount ?? null,
            'tripDate'=>$request->tripDate,
            'tripAmount'=>$request->tripAmount,
            'isOmani'=>$request->isOmani,
            'omaniName'=>$request->omaniName ?? null,
            'omaniAmount'=>$request->omaniAmount ?? null,
            'image'=>$imagePath
        ]);

        return $this->fetchTrips($request);
    }



    public function destroy($id)
    {

        $trip = Trip::findOrFail($id);

        $trip->delete();

        return $this->fetchTrips(request());
    }



    /* ===============================
       PROFESSIONAL PDF EXPORT
    =============================== */

    public function downloadPDF(Request $request)
    {

        $trips = $this->queryTrips($request)->get();

        $totalDriver = $trips->sum('driverAmount');
        $totalTrip   = $trips->sum('tripAmount');
        $totalOmani  = $trips->sum('omaniAmount');


        /* NUMBER TO WORDS */

        $numberToWords = new NumberToWords();
        $transformer = $numberToWords->getNumberTransformer('en');

      $totalTripWords = CurrencyHelper::omrToWords($totalTrip);


            $pdf = Pdf::loadView('trip.trip_pdf',[
                'trips'=>$trips,
                'totalDriver'=>$totalDriver,
                'totalTrip'=>$totalTrip,
                'totalOmani'=>$totalOmani,
                'totalTripWords'=>$totalTripWords
            ]);

        $pdf->setPaper('A4','landscape');

        return $pdf->download('trip-report-'.date('Y-m-d').'.pdf');
    }

}