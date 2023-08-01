<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ReportUser;
use App\Models\User;
use App\Repositories\Contracts\ReportUserRepository;
use App\Repositories\Contracts\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class ReportUserController extends Controller
{
    private $userRepository;
    private $reportUserRepository;

    public function __construct(UserRepository $userRepository, ReportUserRepository $reportUserRepository)
    {
        $this->userRepository       = $userRepository;
        $this->reportUserRepository = $reportUserRepository;

    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $users = ReportUser::all();

        return view('backend.reports.users.index');
    }

    /**
     * @param Datatables $datatables
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function readAjax(Datatables $datatables)
    {
        return $datatables->eloquent(ReportUser::query())->toJson();
    }

    public function show($id)
    {
        $userReportedBy = DB::table('report_users')->join('users', function ($join) {
            $join->on('users.id', '=', 'report_users.reported_by');
        })->leftjoin('images', function ($join) {
            $join->on('images.user_id', '=', 'report_users.reported_by')->where('images.number', '=', 1);
        })->where('report_users.id', '=', $id)->select('users.id as reported_by_id', 'users.name as reported_by_name',
            'users.gender as reported_by_gender', 'users.email as reported_by_email',
            'users.country_code as reported_by_country_code', 'users.phone as reported_by_phone',
            'images.name as reported_by_image_name', 'images.path as reported_by_image_path',
            'images.number as reported_by_image_number', 'images.link as reported_by_image_link',
            'report_users.reason as reported_by_reason', 'report_users.created_at as reported_date')->first();


        $userReportedTo = DB::table('report_users')->join('users', function ($join) {
            $join->on('users.id', '=', 'report_users.reported_to');
        })->leftjoin('images', function ($join) {
            $join->on('images.user_id', '=', 'report_users.reported_to')->where('images.number', '=', 1);
        })->where('report_users.id', '=', $id)->select('users.id as reported_to_id', 'users.name as reported_to_name',
            'users.gender as reported_to_gender', 'users.email as reported_to_email',
            'users.country_code as reported_to_country_code', 'users.phone as reported_to_phone',
            'images.name as reported_to_image_name', 'images.path as reported_to_image_path',
            'images.number as reported_to_image_number', 'images.link as reported_to_image_link',
            'report_users.id as reported_id', 'report_users.reason as reported_to_reason',
            'report_users.created_at as reported_date')->first();

        $userReportedBy->number_of_times = DB::table('report_users')->where('reported_by', '=',
            $userReportedBy->reported_by_id)->count();
        $userReportedTo->number_of_times = DB::table('report_users')->where('reported_to', '=',
            $userReportedTo->reported_to_id)->count();

        return view('backend.reports.users.show',
            ['userReportedBy' => $userReportedBy, 'userReportedTo' => $userReportedTo]);
    }

    /**
     * if admin select deactivation, them the user status will be changed.
     *
     * @param Request $request
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        $validator = $this->validateRequest($request, $this->updateRequestValidationRules());

        if ($validator !== true) {
            return redirect(route('report.user.show', $id))->withErrors($validator)->withInput();
        }

        $reportUser = $this->reportUserRepository->findOneById($id);

        if ( ! $reportUser instanceof ReportUser) {
            return redirect(route('report.user.index'))->with('fail', trans('backend/reports.users.reported_id_not_found'));
        }

        $user = $this->userRepository->findOneByIdWithTrashed($reportUser->reported_to);

        if ( ! $user instanceof User) {
            return redirect(route('report.user.index'))->with('fail', trans('backend/reports.users.reported_user_not_found'));
        }

        $this->reportUserRepository->delete($reportUser);

        if ($request->input('action') == 1) {
            $input      = [
                'is_active' => 0
            ];
            $reportUser = $this->userRepository->update($user, $input);

            return redirect(route('report.user.index'))->with('status', trans('backend/reports.users.user_has_been_deactivated'));
        }

        return redirect(route('report.user.index'))->with('status', trans('backend/reports.users.user_has_been_ignored'));
    }

    public function updateRequestValidationRules()
    {
        $rules = [
            'action' => 'required',
        ];

        return $rules;
    }

}
