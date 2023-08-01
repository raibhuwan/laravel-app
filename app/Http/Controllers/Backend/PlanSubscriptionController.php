<?php

namespace App\Http\Controllers\Backend;

use App\Models\PlanSubscription;
use Gerardojbaez\Laraplans\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Repositories\Contracts\UserRepository;

class PlanSubscriptionController extends Controller
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;

        parent::__construct();
    }

    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int $uid
     *
     * @return \Illuminate\Http\Response
     */
    public function create($uid)
    {
        $userDetails = $this->loadUser($uid);

        $data['plan'] = Plan::pluck('name', 'id');

        $data['user_id'] = $uid;

        return view('backend.users.subscriptions.create', ['userDetails' => $userDetails, 'data' => $data]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $uid
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $uid)
    {
        $user = $this->userRepository->findOneByIdWithTrashed($uid);

        $plan = Plan::find($request->plan_id);

        $user->newSubscription('main', $plan)->create();

        return redirect(route('user.index'))->with('status',
            trans('strings.backend.plan_subscription.create.subscription_has_been_added_successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $uid
     * * @param  int $sid
     *
     * @return \Illuminate\Http\Response
     */
    public function show($uid, $sid)
    {
        $userDetails = $this->loadUser($uid);

        //Fetching data from database
        $data['subscription'] = PlanSubscription::findorFail($sid);

        //loading show view
        return view('backend.users.subscriptions.show', ['userDetails' => $userDetails, 'data' => $data]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $uid
     * * @param  int $sid
     *
     * @return \Illuminate\Http\Response
     */

    public function edit($uid, $sid)
    {
        $userDetails = $this->loadUser($uid);

        $data['plan'] = Plan::pluck('name', 'id');

        $data['user_id'] = $uid;

        $data['plan_subscription'] = PlanSubscription::findorFail($sid);

        return view('backend.users.subscriptions.edit', ['userDetails' => $userDetails, 'data' => $data]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $sid
     * @param  int $uid
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $uid, $sid)
    {
        $validator = $this->validateRequest($request, $this->requestValidationRules());

        if ($validator !== true) {
            return redirect(route('user.subscription.edit', $uid, $sid))->withErrors($validator)->withInput();
        }

        $subscriptions = PlanSubscription::findorFail($sid);

        $subscriptions->update($request->all());

        return redirect()->route('user.index')->with('status',
            trans('strings.backend.plan_subscription.edit.subscription_has_been_edited_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $subscription = PlanSubscription::findorFail($id);
        $status       = $subscription->delete();
        $jsonData     = [
            'status'  => $status,
            'message' => trans('strings.backend.plan_subscription.delete.subscription_delete')
        ];

        return json_encode($jsonData);
    }

    public function readSubscription(Request $request)
    {
        $user_id = $request->user_id;
        return DataTables::eloquent(PlanSubscription::where('subscribable_id', $user_id))->make(true);
    }

    private function loadUser($uid)
    {
        $user = $this->userRepository->findOneByIdWithTrashed($uid);

        if ( ! $user instanceof User) {
            return redirect(route('user.index'))->with('fail', trans('strings.backend.users.edit.user_not_found'));
        }

        return $this->editUserDetails($uid);
    }

    private function requestValidationRules()
    {
        $rules = [
            'plan_id'   => 'required',
            'starts_at' => 'required',
            'ends_at'   => 'required|after:starts_at'
        ];

        return $rules;
    }
}
