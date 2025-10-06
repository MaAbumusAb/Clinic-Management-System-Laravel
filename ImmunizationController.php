<?php

namespace App\Http\Controllers;

use App\Models\Immunization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImmunizationController extends Controller
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
            $immunizations = Immunization::where('patient_id', Auth::id())->paginate(10);
        } else {
            $immunizations = Immunization::paginate(10);
        }
        return view('immunizations.index', compact('immunizations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $patients = User::where('role', 'patient')->get();
        return view('immunizations.create', compact('patients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'vaccine_name' => 'required|string|max:255',
            'scheduled_date' => 'required|date',
            'administered_date' => 'nullable|date|before_or_equal:scheduled_date',
            'notes' => 'nullable|string',
            'status' => 'required|in:scheduled,administered,cancelled',
        ]);

        Immunization::create($request->all());

        return redirect()->route('immunizations.index')->with('success', 'Immunization record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Immunization $immunization)
    {
        if (Auth::user()->role === 'patient' && $immunization->patient_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }
        return view('immunizations.show', compact('immunization'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Immunization $immunization)
    {
        $patients = User::where('role', 'patient')->get();
        return view('immunizations.edit', compact('immunization', 'patients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Immunization $immunization)
    {
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'vaccine_name' => 'required|string|max:255',
            'scheduled_date' => 'required|date',
            'administered_date' => 'nullable|date|before_or_equal:scheduled_date',
            'notes' => 'nullable|string',
            'status' => 'required|in:scheduled,administered,cancelled',
        ]);

        $immunization->update($request->all());

        return redirect()->route('immunizations.index')->with('success', 'Immunization record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Immunization $immunization)
    {
        $immunization->delete();
        return redirect()->route('immunizations.index')->with('success', 'Immunization record deleted successfully.');
    }
}