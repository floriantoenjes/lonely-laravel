<?php


namespace App\Http\Controllers;


use App\Models\Activity;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivityController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'verified']);
    }

    public function newActivity()
    {
        return Inertia::render('ActivityForm');
    }

    public function createActivity(Request $request)
    {
        $activity = new Activity();
        // name, description, city, postcode, address
    }

}
