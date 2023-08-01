<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    /**
     * Show the application welcome page.
     *
     * @return \Illuminate\Http\Response
     */
    public function welcome()
    {
        return view('welcome');
    }

    /**
     * Show the application privacy page.
     *
     * @return \Illuminate\Http\Response
     */
    public function privacy()
    {
        return view('frontend.privacy');
    }

    /**
     * Show the application terms and condiion page.
     *
     * @return \Illuminate\Http\Response
     */
    public function terms()
    {
        return view('frontend.tac');
    }

    /**
     * Show the application legal page.
     *
     * @return \Illuminate\Http\Response
     */
    public function legal()
    {
        return view('frontend.legal');
    }

}