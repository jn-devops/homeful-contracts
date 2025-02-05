<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class BookingController extends Controller
{
    public function authentication(){
        return Inertia::render('Booking/Authentication');
    }
    
    public function payment(){
        return Inertia::render('Booking/Payment');
    }

    public function complete_form(){
        return Inertia::render('Booking/CompleteProfile');
    }
}
