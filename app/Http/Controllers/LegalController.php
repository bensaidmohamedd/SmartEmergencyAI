<?php

namespace App\Http\Controllers;

class LegalController extends Controller
{
    public function privacy()
    {
        return view('pages.legal.privacy', ['layout' => 'guest']);
    }

    public function terms()
    {
        return view('pages.legal.terms', ['layout' => 'guest']);
    }
}
