<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class PageController extends Controller
{
    /**
     * Show terms and conditions page
     */
    public function terms()
    {
        return view('pages.terms');
    }

    /**
     * Show privacy policy page
     */
    public function privacy()
    {
        return view('pages.privacy');
    }
} 