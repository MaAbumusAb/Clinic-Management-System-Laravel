<?php

namespace App\Http\Controllers;

use App\Models\SportsTherapy;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SportsTherapyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin|doctor|patient')->except(['index', 'show']);
        $this->middleware('role:admin|doctor')->only(['index', 'show', 'edit', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->role === 'patient') {
            $sportsTherapies = SportsTherapy::where('patient_id', Auth::id())->paginate(10);
        } else {
            $sportsTherapies = SportsTherapy::paginate(10);
        }
        return view('therapy.index', compact('sportsTherapies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $patients = User::where('role', 'patient')->get();
        return view('therapy.create', compact('patients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'injury_type' => 'required|string|max:255',
            'therapy_type' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        SportsTherapy::create($request->all());

        return redirect()->route('therapy.index')->with('success', 'Sports Therapy record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SportsTherapy $therapy)
    {
        if (Auth::user()->role === 'patient' && $therapy->patient_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }
        return view('therapy.show', compact('therapy'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SportsTherapy $therapy)
    {
        $patients = User::where('role', 'patient')->get();
        return view('therapy.edit', compact('therapy', 'patients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SportsTherapy $therapy)
    {
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'injury_type' => 'required|string|max:255',
            'therapy_type' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        $therapy->update($request->all());

        return redirect()->route('therapy.index')->with('success', 'Sports Therapy record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SportsTherapy $therapy)
    {
        $therapy->delete();
        return redirect()->route('therapy.index')->with('success', 'Sports Therapy record deleted successfully.');
    }
}