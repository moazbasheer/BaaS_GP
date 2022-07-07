<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Trip;
class TripsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $trips = Trip::get();
        foreach($trips as $trip) {
            $temp = Trip::find($trip->id);
            $temp->datetime = $temp->date . ' ' . $temp->time;
            $temp->save();
        }
    }
}
