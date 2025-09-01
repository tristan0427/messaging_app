<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Inertia\Inertia;

class Frontend_Controller extends Controller
{
    public function index()
    {
        return redirect()->route('login');
    }
}
