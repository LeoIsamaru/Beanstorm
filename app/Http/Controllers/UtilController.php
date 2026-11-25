<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UtilController extends Controller
{
    /**
     * Load the welcome view.
     */
    public function loadWelcome()
    {
        return view('welcome');
    }

    /**
     * Load the homepage view.
     */
    public function loadHomepage()
    {
        $courseName = 'SD';

        return view('home');
    }
}
