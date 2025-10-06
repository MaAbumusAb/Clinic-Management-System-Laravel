<?php

namespace App\Http\Controllers;

use App\Models\LabTest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LabTestController extends Controller
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
            $labTests = LabTest::where('patient_id', Auth::id())->paginate(10);
        } else {
            $labTests = LabTest::paginate(10);
        }
        return view('lab.index', compact('labTests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $patients = User::where('role', 'patient')->get();
        return view('lab.create', compact('patients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'test_name' => 'required|string|max:255',
            'test_date' => 'required|date',
            'results' => 'nullable|string',
            'status' => 'required|in:pending,completed,cancelled',
        ]);

        LabTest::create($request->all());

        return redirect()->route('lab.index')->with('success', 'Lab Test created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(LabTest $lab)
    {
        if (Auth::user()->role === 'patient' && $lab->patient_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }
        return view('lab.show', compact('lab'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LabTest $lab)
    {
        $patients = User::where('role', 'patient')->get();
        return view('lab.edit', compact('lab', 'patients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LabTest $lab)
    {
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'test_name' => 'required|string|max:255',
            'test_date' => 'required|date',
            'results' => 'nullable|string',
            'status' => 'required|in:pending,completed,cancelled',
        ]);

        $lab->update($request->all());

        return redirect()->route('lab.index')->with('success', 'Lab Test updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LabTest $lab)
    {
        $lab->delete();
        return redirect()->route('lab.index')->with('success', 'Lab Test deleted successfully.');
    }
}