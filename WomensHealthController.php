<?php

namespace App\Http\Controllers;

use App\Models\WomensHealth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WomensHealthController extends Controller
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
            $womensHealthRecords = WomensHealth::where('patient_id', Auth::id())->paginate(10);
        } else {
            $womensHealthRecords = WomensHealth::paginate(10);
        }
        return view('womens.index', compact('womensHealthRecords'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $patients = User::where('role', 'patient')->get();
        return view('womens.create', compact('patients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'record_type' => 'required|string|max:255',
            'record_date' => 'required|date',
            'details' => 'required|string',
        ]);

        WomensHealth::create($request->all());

        return redirect()->route('womens.index')->with('success', 'Women\'s Health record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(WomensHealth $women)
    {
        if (Auth::user()->role === 'patient' && $women->patient_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }
        return view('womens.show', compact('women'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WomensHealth $women)
    {
        $patients = User::where('role', 'patient')->get();
        return view('womens.edit', compact('women', 'patients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WomensHealth $women)
    {
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'record_type' => 'required|string|max:255',
            'record_date' => 'required|date',
            'details' => 'required|string',
        ]);

        $women->update($request->all());

        return redirect()->route('womens.index')->with('success', 'Women\'s Health record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WomensHealth $women)
    {
        $women->delete();
        return redirect()->route('womens.index')->with('success', 'Women\'s Health record deleted successfully.');
    }
}