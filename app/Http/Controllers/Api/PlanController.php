<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\PlanRepository;
use App\Transformers\PlanTransformer;

class PlanController extends Controller
{
    private $planRepository;
    private $planTransformer;

    public function __construct(PlanRepository $planRepository, PlanTransformer $planTransformer)
    {
        $this->planRepository = $planRepository;
        $this->planTransformer = $planTransformer;

        parent::__construct();
    }

    /**
     * Display a listing of the all plans.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function index()
    {
        $plans = $this->planRepository->findBy();
        return $this->respondWithCollection($plans, $this->planTransformer);
    }
}
