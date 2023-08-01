<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SwipeMatch;
use App\Models\User;
use App\Repositories\Contracts\RightLeftSwipeRepository;
use App\Repositories\Contracts\SwipeMatchRepository;
use App\Repositories\Contracts\UserRepository;
use App\Transformers\SwipeMatchTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SwipeMatchController extends Controller
{
    private $userRepository;
    private $swipeMatchRepository;
    private $rightLeftSwipeRepository;
    private $swipeMatchTransformer;

    public function __construct(
        UserRepository $userRepository,
        SwipeMatchRepository $swipeMatchRepository,
        RightLeftSwipeRepository $rightLeftSwipeRepository,
        SwipeMatchTransformer $swipeMatchTransformer
    ) {
        $this->userRepository           = $userRepository;
        $this->swipeMatchRepository     = $swipeMatchRepository;
        $this->rightLeftSwipeRepository = $rightLeftSwipeRepository;
        $this->swipeMatchTransformer    = $swipeMatchTransformer;

        parent::__construct();
    }

    public function store(Request $request)
    {
        DB::connection()->enableQueryLog();

        // Validation
        $validatorResponse = $this->validateRequest($request, $this->storeRequestValidationRules());

        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }

        $matchedUser = $this->userRepository->findOne($request->input('matched_user_id'));

        if ( ! $matchedUser instanceof User) {
            return $this->sendNotFoundResponse("The user with id {$request->input('matched_user_id')} doesn't exist.");
        }

        $currentUser = $this->getCurrentUserDetails();

        // We check if both the users have liked or super liked each other

        $swipe1 = DB::table('right_left_swipes')->where('a', '=', $currentUser->id)->where('b', '=',
            $matchedUser->id)->where(function ($swipe_type) {
            return $swipe_type->where('swipe_type', 'LIKE')->orWhere('swipe_type', 'SUPER_LIKE');
        })->select(DB::raw('count(*) as count'), 'id')->get()->first();


        $swipe2 = DB::table('right_left_swipes')->where('a', '=', $matchedUser->id)->where('b', '=',
            $currentUser->id)->where(function ($swipe_type) {
            return $swipe_type->where('swipe_type', 'LIKE')->orWhere('swipe_type', 'SUPER_LIKE');
        })->select(DB::raw('count(*) as count1'), 'id')->get()->first();

        if (($swipe1->count == 1) && ($swipe2->count1 == 1)) {
            DB::connection()->enableQueryLog();

            // Now we check if the match exists
            $fullQuery = $this->swipeMatchRepository->getMatch($currentUser, $matchedUser);

            $results = DB::getQueryLog();

            // Check if match already exists.
            if ($fullQuery->count > 0) {
                return $this->sendCustomResponse(200, "Match already exists.");
            }

            $input = [
                'a' => $currentUser->id,
                'b' => $matchedUser->id
            ];

            $matches = $this->swipeMatchRepository->save($input);

            if ( ! $matches instanceof SwipeMatch) {
                return $this->sendCustomResponse(500, 'Error occurred on creating Match.');
            }

            DB::table('right_left_swipes')->whereIn('id', [$swipe1->id, $swipe2->id])->delete();

            return $this->sendCustomResponse(200, "User Id: $currentUser->id and $matchedUser->id has been matched.");

        } else {
            return $this->sendNotFoundResponse('The match is invalid.');
        }

    }

    /**
     * Store Request Validation Rules
     *
     * @param Request $request
     *
     * @return array
     */
    private function storeRequestValidationRules()
    {
        $rules = [
            'matched_user_id' => 'required'
        ];

        return $rules;
    }

    /**
     * Remove matches
     *
     * @param $id
     *
     * @return string
     */
    public function destroy($id)
    {
        $currentUser = $this->getCurrentUserDetails();

        $matchedUser = $this->userRepository->findOne($id);

        if ( ! $matchedUser instanceof User) {
            return $this->sendNotFoundResponse("The user with id {$id} doesn't exist.");
        }

        // Now we check if the match exists
        $first = DB::table('swipe_matches')->where('a', $currentUser->id)->where('b', $matchedUser->id);

        $query = DB::table('swipe_matches')->where('a', $matchedUser->id)->where('b',
            $currentUser->id)->union($first)->first();

        // Check if match is empty.
        if ($query === null) {
            return $this->sendNotFoundResponse("Match not found.");
        }

        DB::table('swipe_matches')->whereIn('id', [$query->id])->delete();

        return $this->sendCustomResponse("200", "The match for user id  {$id} has been removed.");
    }

    /**
     * Get users that has been matched
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMatchUser()
    {
        $currentUser = $this->getCurrentUserDetails();

        // We get the matched id from two columns a and b
        $first = DB::table('swipe_matches')->where('a', $currentUser->id)->select('swipe_matches.b',
            'swipe_matches.created_at');

        $matchedId = DB::table('swipe_matches')->where('b', $currentUser->id)->select('swipe_matches.a as matches',
            'swipe_matches.created_at')->union($first)->orderby('matches');

        $rawQuery = $this->getSql($matchedId);

        $matchedUserDetails = DB::table('users')->join(DB::raw("($rawQuery) as swipe_matches"), function ($join) {
            $join->on('users.id', '=', 'swipe_matches.matches');
        })->join('images', function ($join) {
            $join->on('users.id', '=', 'images.user_id')->where('images.number', '=', 1);
        })->select('users.id as user_id', 'users.uid as user_uid', 'users.name as user_name',
            'images.name as image_name', 'images.path as image_path', 'images.link as image_link',
            'swipe_matches.created_at as swipe_match_created_at')->orderBy('swipe_match_created_at',
            'DESC')->orderBy('user_id', 'DESC')->paginate();

        return $this->respondWithCollection($matchedUserDetails, $this->swipeMatchTransformer);

    }
}
