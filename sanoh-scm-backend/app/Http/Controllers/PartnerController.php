<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index(){
        $users = Partner::all();

        return view('index', compact('users'));
    }
}
