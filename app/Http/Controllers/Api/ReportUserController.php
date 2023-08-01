<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\Contracts\ReportUserRepository;
use App\Repositories\Contracts\UserRepository;
use App\Transformers\ReportUserTransformer;
use Illuminate\Http\Request;


class ReportUserController extends Controller
{
    private $userRepository;
    private $reportUserRepository;
    private $reportUserTransformer;

    public function __construct(
        UserRepository $userRepository,
        ReportUserRepository $reportUserRepository,
        ReportUserTransformer $reportUserTransformer
    ) {
        $this->userRepository        = $userRepository;
        $this->reportUserRepository  = $reportUserRepository;
        $this->reportUserTransformer = $reportUserTransformer;

        parent::__construct();
    }

    public function store(Request $request)
    {
        $validatorResponse = $this->validateRequest($request, $this->storeRequestValidationRules());

        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }

        $user = $this->userRepository->findOne($request->input('reported_to'));

        if ( ! $user instanceof User) {
            return $this->sendNotFoundResponse("The user with id {$request->input('user_id')} doesn't exist");
        }

        $currentUser = $this->getCurrentUserDetails();

        $input = [
            'reported_by' => $currentUser->id,
            'reported_to' => $user->id,
            'reason'      => $request->input('reason')
        ];

        $report = $this->reportUserRepository->save($input);

        $reportDetails                    = app();

        $reportDetailsObject              = $reportDetails->make('stdClass');
        $reportDetailsObject->reported_by = $currentUser->uid;
        $reportDetailsObject->reported_to = $request->input('reported_to');
        $reportDetailsObject->reason      = $request->input('reason');

        return $this->respondWithItem($reportDetailsObject, $this->reportUserTransformer);
    }

    private function storeRequestValidationRules()
    {
        $rules = [
            'reported_to' => 'required',
            'reason'      => 'required|max:255'
        ];

        return $rules;
    }
}
