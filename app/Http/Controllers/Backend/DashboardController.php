<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

/**
 * Class DashboardController.
 */
class DashboardController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {

        $users = DB::table('users')->join('images', function ($join) {
            $join->on('users.id', '=', 'images.user_id')->where('images.number', '=', 1);
        })->select('users.id as user_id', 'users.uid as user_uid', 'users.name as user_name',
            'users.gender as user_gender', 'users.created_at as user_created_at', 'images.name as image_name',
            'images.path as image_path', 'images.link as image_link')->orderBy('user_created_at',
            'desc')->limit(7)->get();

        $totalUsers         = DB::table('users')->count();
        $totalMatches       = DB::table('swipe_matches')->count();
        $totalReportedUsers = DB::table('report_users')->count();
        $totalFailedJobs    = DB::table('failed_jobs')->count();

        return view('backend.dashboard', [
            'users'              => $users,
            'totalUsers'         => $totalUsers,
            'totalMatches'       => $totalMatches,
            'totalReportedUsers' => $totalReportedUsers,
            'totalFailedJobs'    => $totalFailedJobs
        ]);
    }
}
