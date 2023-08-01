<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Gerardojbaez\Laraplans\Models\Plan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PlanController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        return view('backend.plans.index');
    }

    public function show($id)
    {
        $data['plans'] = Plan::findorFail($id);

        return view('backend.plans.show', compact('data'));
    }

    public function create()
    {
        $data['select'] = [
            ''      => 'Select Interval',
            'day'   => 'Day',
            'week'  => 'Week',
            'month' => 'Month',
            'year'  => 'Year'
        ];

        return view('backend.plans.create', compact('data'));
    }

    public function store(Request $request)
    {
        $validator = $this->validateRequest($request, $this->requestValidationRules());
        
        if ($validator !== true) {
            return redirect(route('plan.create'))->withErrors($validator)->withInput();
        }

        Plan::create($request->all());

        return redirect(route('plan.index'))->with('status',
            trans('strings.backend.plans.create.plan_has_been_created_successfully'));

    }

    public function edit($id)
    {
        $data['select'] = [
            ''      => 'Select Interval',
            'day'   => 'Day',
            'week'  => 'Week',
            'month' => 'Month',
            'year'  => 'Year'
        ];
        $data['plan']   = Plan::findorFail($id);

        if ($data['plan']) {
            return view('backend.plans.edit', compact('data'));
        }
    }

    public function update(Request $request, $id)
    {
        $validator = $this->validateRequest($request, $this->requestValidationRules());

        if ($validator !== true) {
            return redirect(route('plan.edit', $id))->withErrors($validator)->withInput();
        }

        $plan = Plan::findorFail($id);

        $plan->update($request->all());

        return redirect()->route('plan.index')->with('status',
            trans('strings.backend.plans.edit.plan_has_been_edited_successfully'));
    }

    public function destroy(Request $request, $id)
    {

        $plan     = Plan::findorFail($id);
        $status   = $plan->delete();
        $jsonData = [
            'status'  => $status,
            'message' => trans('strings.backend.plans.delete.plan_delete')
        ];

        return json_encode($jsonData);
    }

    private function requestValidationRules()
    {
        $rules = [
            'name'              => 'required|max:100',
            'plan_code'         => 'required|max:100',
            'price'             => 'required|numeric|min:0',
            'interval_count'    => 'required|numeric|min:0',
            'interval'          => 'required',
            'google_product_id' => 'max:100',
            'apple_product_id'  => 'max:100',
            'description'       => 'max:255',
        ];

        return $rules;
    }

    public function readPlan()
    {
        return DataTables::eloquent(Plan::query())->make(true);
    }
}
