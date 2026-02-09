<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\BucketList;


class TripController extends Controller
{
    public function index()
    {
        // Get trips sorted by newest date first
        $trips = Trip::orderBy('date', 'desc')->get();
        return view('travel_journal', compact('trips'));
    }

    public function store(Request $request)
    {
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

    // Update Trip
    public function update(Request $request, $id)
    {
        $request->validate([
            'destination' => 'required',
            'latitude' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $trip = Trip::findOrFail($id);
        $input = $request->all();

        // Handle image upload if provided
        if ($image = $request->file('image')) {
            $destinationPath = 'uploads/trips/';
            $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move(public_path($destinationPath), $profileImage);
            $input['image'] = "$destinationPath$profileImage";
        }

        $trip->update($input);
        return back()->with('success', 'Trip Updated Successfully!');
    }

    // Simple Delete for CRUD completeness
    public function destroy($id)
    {
        Trip::destroy($id);
        return back();
    }

    public function dashboard()
    {
        $trips = Trip::orderBy('date', 'desc')->get();
        return view('dashboard', compact('trips'));
    }

    public function gallery()
    {
        $trips = Trip::whereNotNull('image')->orderBy('date', 'desc')->get();
        return view('gallery', compact('trips'));
    }

    public function bucketList()
    {
        $buckets = BucketList::orderBy('priority', 'desc')->orderBy('created_at', 'desc')->get();
        return view('bucketlist', compact('buckets'));
    }

    public function storeBucket(Request $request)
    {
        $request->validate([
            'destination' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high'
        ]);

        BucketList::create($request->all());
        return back()->with('success', 'Added to bucket list!');
    }

    public function destroyBucket($id)
    {
        BucketList::destroy($id);
        return back()->with('success', 'Removed from bucket list!');
    }
    public function updateBucket(Request $request, $id)
{
    $request->validate([
        'destination' => 'required|string|max:255',
        'description' => 'nullable|string',
        'priority' => 'required|in:low,medium,high'
    ]);

    $bucket = BucketList::findOrFail($id);
    $bucket->update($request->all());

    return back()->with('success', 'Bucket updated!');
}
}
