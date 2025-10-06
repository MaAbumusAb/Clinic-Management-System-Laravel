<?php

namespace App\Http\Controllers;

use App\Models\DisabilitySupport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisabilitySupportController extends Controller
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
            $disabilitySupports = DisabilitySupport::where('patient_id', Auth::id())->paginate(10);
        } else {
            $disabilitySupports = DisabilitySupport::paginate(10);
        }
        return view('disability.index', compact('disabilitySupports'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $patients = User::where('role', 'patient')->get();
        return view('disability.create', compact('patients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'disability_type' => 'required|string|max:255',
            'support_needed' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        DisabilitySupport::create($request->all());

        return redirect()->route('disability.index')->with('success', 'Disability Support record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DisabilitySupport $disability)
    {
        if (Auth::user()->role === 'patient' && $disability->patient_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }
        return view('disability.show', compact('disability'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DisabilitySupport $disability)
    {
        $patients = User::where('role', 'patient')->get();
        return view('disability.edit', compact('disability', 'patients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DisabilitySupport $disability)
    {
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'disability_type' => 'required|string|max:255',
            'support_needed' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $disability->update($request->all());

        return redirect()->route('disability.index')->with('success', 'Disability Support record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DisabilitySupport $disability)
    {
        $disability->delete();
        return redirect()->route('disability.index')->with('success', 'Disability Support record deleted successfully.');
    }
}