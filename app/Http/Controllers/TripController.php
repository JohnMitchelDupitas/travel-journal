<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trip;


class TripController extends Controller
{
    public function index() {
        // Get trips sorted by newest date first
        $trips = Trip::orderBy('date', 'desc')->get();
        return view('travel_journal', compact('trips'));
    }

    public function store(Request $request) {
        $request->validate([
            'destination' => 'required',
            'latitude' => 'required', // Enforced to ensure map works
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $input = $request->all();

        // MANUAL IMAGE UPLOAD TO PUBLIC FOLDER
        if ($image = $request->file('image')) {
            $destinationPath = 'uploads/trips/'; // Inside public/
            $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            
            // Move the file directly to public/uploads/trips/
            $image->move(public_path($destinationPath), $profileImage);
            
            // Save the path in database
            $input['image'] = "$destinationPath$profileImage";
        }

        Trip::create($input);
        return back()->with('success', 'Trip Logged Successfully!');
    }
    
    // Simple Delete for CRUD completeness
    public function destroy($id) {
        Trip::destroy($id);
        return back();
    }
}
