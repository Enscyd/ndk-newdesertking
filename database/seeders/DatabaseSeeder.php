<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed dashboardusers instead of the unused users table
        if (DB::table('dashboardusers')->count() === 0) {
            DB::table('dashboardusers')->insert([
                'password' => Hash::make('password'), // default password: password
                'isActive' => true,
                'createdAt' => now(),
                'updatedAt' => now(),
            ]);
        }

        // Truncate tables to seed clean data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('billingitems')->truncate();
        DB::table('billings')->truncate();
        DB::table('trips')->truncate();
        DB::table('companies')->truncate();
        DB::table('destinations')->truncate();
        DB::table('employees')->truncate();
        DB::table('trucks')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Seed Companies
        $companies = [
            ['name' => 'Al-Maha Petroleum', 'address' => 'Muscat, Oman'],
            ['name' => 'Oman Oil Company', 'address' => 'Sohar, Oman'],
            ['name' => 'Shell Oman Marketing', 'address' => 'Salalah, Oman'],
            ['name' => 'Mazoon Electricity', 'address' => 'Nizwa, Oman'],
        ];
        foreach ($companies as $c) {
            DB::table('companies')->updateOrInsert(['name' => $c['name']], $c);
        }
        $companyIds = DB::table('companies')->pluck('id')->toArray();

        // 3. Seed Destinations
        $destinations = [
            ['name' => 'Muscat Port'],
            ['name' => 'Sohar Port'],
            ['name' => 'Salalah Freezone'],
            ['name' => 'Nizwa Industrial Area'],
            ['name' => 'Duqm Port'],
        ];
        foreach ($destinations as $d) {
            DB::table('destinations')->updateOrInsert(['name' => $d['name']], $d);
        }
        $destinationIds = DB::table('destinations')->pluck('id')->toArray();

        // 4. Seed Employees (Drivers)
        $employees = [
            ['employeeName' => 'Ahmed Al-Balushi', 'employeePhoneNo' => '+96891234567'],
            ['employeeName' => 'Said Al-Harthy', 'employeePhoneNo' => '+96892345678'],
            ['employeeName' => 'Mohammed Al-Sadi', 'employeePhoneNo' => '+96893456789'],
            ['employeeName' => 'Ali Al-Abri', 'employeePhoneNo' => '+96894567890'],
            ['employeeName' => 'Salim Al-Jabri', 'employeePhoneNo' => '+96895678901'],
        ];
        foreach ($employees as $e) {
            DB::table('employees')->updateOrInsert(['employeeName' => $e['employeeName']], $e);
        }
        $employeeIds = DB::table('employees')->pluck('id')->toArray();

        // 5. Seed Trucks
        $trucks = [
            ['truckNumber' => 'OB-8890-A'],
            ['truckNumber' => 'OB-1234-B'],
            ['truckNumber' => 'OB-5678-C'],
            ['truckNumber' => 'OB-4321-D'],
            ['truckNumber' => 'OB-9876-E'],
        ];
        foreach ($trucks as $t) {
            DB::table('trucks')->updateOrInsert(['truckNumber' => $t['truckNumber']], $t);
        }
        $truckIds = DB::table('trucks')->pluck('id')->toArray();
        $truckNumbers = DB::table('trucks')->pluck('truckNumber', 'id')->toArray();

        // 6. Seed Trips (60 trips)
        $tripIds = [];
        for ($i = 0; $i < 60; $i++) {
            $companyId = $companyIds[array_rand($companyIds)];
            $destId = $destinationIds[array_rand($destinationIds)];
            $empId = $employeeIds[array_rand($employeeIds)];
            $truckId = $truckIds[array_rand($truckIds)];
            
            $tripDate = Carbon::now(); // Keep all trips in the current month
            $tripAmount = rand(150, 450);
            $driverAmount = rand(20, 60);

            $tripId = DB::table('trips')->insertGetId([
                'companyId' => $companyId,
                'destinationId' => $destId,
                'employeeId' => $empId,
                'truckId' => $truckId,
                'tripType' => rand(0, 1) ? 'Go Trip' : 'Return Trip',
                'driverAmount' => $driverAmount,
                'tripDate' => $tripDate,
                'tripAmount' => $tripAmount,
                'isOmani' => rand(0, 1) ? 'Yes' : 'No',
                'OmaniName' => rand(0, 1) ? 'Salim' : null,
                'OmaniAmount' => rand(0, 1) ? 15.00 : null,
                'image' => null,
            ]);
            $tripIds[] = $tripId;
        }

        // 7. Seed Billings / Invoices (25 invoices for current month to ensure pagination works)
        for ($i = 1; $i <= 25; $i++) {
            $companyId = $companyIds[array_rand($companyIds)];
            $date = Carbon::now(); // Seed for today / current month
            $invoiceNo = sprintf('INV-2026-%04d', $i);
            
            $companyTrips = DB::table('trips')
                ->where('companyId', $companyId)
                ->get();
            
            if ($companyTrips->isEmpty()) {
                continue;
            }

            $billingId = DB::table('billings')->insertGetId([
                'invoiceNo' => $invoiceNo,
                'companyId' => $companyId,
                'date' => $date,
                'billImage' => null,
                'grandTotal' => 0.0,
                'paymentStatus' => rand(0, 1) ? 'PAID' : 'UNPAID',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $numItems = min(rand(1, 3), $companyTrips->count());
            $shuffled = $companyTrips->shuffle();
            $grandTotal = 0.0;
            
            for ($j = 0; $j < $numItems; $j++) {
                $trip = $shuffled[$j];
                $qty = 1;
                $rent = $trip->tripAmount;
                $taxable = $qty * $rent;
                $vat = $taxable * 0.05;
                $total = $taxable + $vat;
                $grandTotal += $total;

                $truckNo = $truckNumbers[$trip->truckId] ?? 'OB-8890-A';
                $destName = DB::table('destinations')->where('id', $trip->destinationId)->value('name') ?? 'Muscat Port';

                DB::table('billingitems')->insert([
                    'billingId' => $billingId,
                    'tripId' => $trip->id,
                    'tripDate' => $trip->tripDate,
                    'description' => 'Transportation of Goods to ' . $destName,
                    'vehicleNo' => $truckNo,
                    'quantity' => $qty,
                    'rent' => $rent,
                    'taxableAmount' => $taxable,
                    'vat' => $vat,
                    'totalAmount' => $total,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('billings')
                ->where('id', $billingId)
                ->update(['grandTotal' => $grandTotal]);
        }
    }
}
