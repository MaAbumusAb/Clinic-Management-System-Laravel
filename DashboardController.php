<?php

namespace App\Http\Controllers;

use App\Models\LabTest;
use App\Models\DisabilitySupport;
use App\Models\WomensHealth;
use App\Models\SportsTherapy;
use App\Models\HealthArticle;
use App\Models\Prescription;
use App\Models\Immunization;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Fetch counts for the dashboard cards
        $labTestCount = LabTest::count();
        $disabilitySupportCount = DisabilitySupport::count();
        $womensHealthCount = WomensHealth::count();
        $sportsTherapyCount = SportsTherapy::count();
        $healthArticleCount = HealthArticle::count();
        $prescriptionCount = Prescription::count();
        $immunizationCount = Immunization::count();

        // You can also fetch recent items or summaries here
        // Example: $recentLabTests = LabTest::latest()->take(5)->get();

        return view('dashboard', compact(
            'labTestCount',
            'disabilitySupportCount',
            'womensHealthCount',
            'sportsTherapyCount',
            'healthArticleCount',
            'prescriptionCount',
            'immunizationCount'
        ));
    }
}}