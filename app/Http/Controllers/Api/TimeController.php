<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;

class TimeController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $currentTime = Carbon::now();

        $message = [
            'current_time' => (string)$currentTime
        ];


        return $this->sendCustomResponse("200", $message);

    }
}
