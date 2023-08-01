<?php

namespace App\Http\Controllers\Api;

use App\Helpers\FcmHelperFunctions;
use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\SwipeMatch;
use App\Models\User;
use App\Repositories\Contracts\ImageRepository;
use App\Repositories\Contracts\UserRepository;
use App\Repositories\EloquentSwipeMatchRepository;
use App\Transformers\CallRecipientTransformer;
use App\Transformers\CallTransformer;
use Illuminate\Http\Request;
use LaravelFCM\Message\PayloadDataBuilder;
use OpenTok\OpenTok;

class CallController extends Controller
{
    protected $apiKey;
    protected $apiSecret;
    protected $callTransformer;
    protected $userRepository;
    private $eloquentSwipeMatchRepository;
    private $callRecipientTransformer;
    private $imageRepository;

    public function __construct(
        CallTransformer $callTransformer,
        UserRepository $userRepository,
        CallRecipientTransformer $callRecipientTransformer,
        ImageRepository $imageRepository
    ) {

        parent::__construct();

        $this->callTransformer              = $callTransformer;
        $this->userRepository               = $userRepository;
        $this->eloquentSwipeMatchRepository = new EloquentSwipeMatchRepository(new SwipeMatch());
        $this->callRecipientTransformer     = $callRecipientTransformer;
        $this->imageRepository              = $imageRepository;

        $this->apiKey    = config('call.api_key');
        $this->apiSecret = config('call.api_secret');
    }

    public function requestCall(Request $request)
    {
        $validatorResponse = $this->validateRequest($request, $this->storeRequestValidationRules());

        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }

        $recipient_user_id = $request->input('recipient_user_id');

        $currentUser = $this->getCurrentUserDetails();

        if ($currentUser->uid == $recipient_user_id) {
            return $this->sendNotFoundResponse("You cannot send message to yourself.");
        }

        if ($currentUser->fcm_registration_id == '') {
            return $this->sendNotFoundResponse("Your fcm registration id doesn't exist.");
        }

        $recipient = $this->userRepository->findOne($recipient_user_id);

        if ( ! $recipient instanceof User) {
            return $this->sendNotFoundResponse("The user with id {$recipient_user_id} doesn't exist.");
        }

        if ($recipient->fcm_registration_id == '') {
            return $this->sendNotFoundResponse("The user with id {$recipient_user_id} doesn't have fcm registration id.");
        }

        $match = $this->eloquentSwipeMatchRepository->getMatch($currentUser, $recipient);

        if ($match->count == 0) {
            return $this->sendNotFoundResponse("You are not matched with recipient id: {$recipient_user_id}");
        }

        $image = $this->imageRepository->findOneBy([
            'user_id' => $currentUser->id,
            'number'  => 1
        ]);

        if ($image instanceof Image) {
            $imagePath = ($image->link == 1) ? $image->path : url('/') . '/storage/' . $image->path . $image->name;
        } else {
            $imagePath = '';
        }

        $opentok = new OpenTok($this->apiKey, $this->apiSecret);

        // Create a session that attempts to use peer-to-peer streaming:
        $session = $opentok->createSession();

        // Store this sessionId in the database for later use
        $sessionId = $session->getSessionId();

        // Generate a Token from just a sessionId (fetched from a database)
        $token = $opentok->generateToken($sessionId);

        $message = "{$currentUser->name} is calling.";

        $fields = array(
            'to'   => $recipient->fcm_registration_id,
            'data' => $message,
        );

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['recipient_id' => $recipient_user_id]);
        $dataBuilder->addData(['user_creator_id' => $currentUser->uid]);
        $dataBuilder->addData(['user_creator_name' => $currentUser->name]);
        $dataBuilder->addData(['type' => $request->input('type')]);
        $dataBuilder->addData(['session_id' => $sessionId]);
        $dataBuilder->addData(['token_id' => $token]);
        $dataBuilder->addData(['image_path' => $imagePath]);

        if ($fields['to'] != null) {
            $fcm = FcmHelperFunctions::sendFcm($message, $fields, $dataBuilder);

            if ($fcm) {
                $input = app();

                $callObject                    = $input->make('stdClass');
                $callObject->session_id        = $sessionId;
                $callObject->token             = $token;
                $callObject->recipient_user_id = $recipient_user_id;

                return $this->respondWithItem($callObject, $this->callTransformer);
            }
        }

        return $this->sendNotFoundResponse("The message could not be sent");
    }

    private function storeRequestValidationRules()
    {
        $rules = [
            'recipient_user_id' => 'required',
            'type'              => 'required|in:VOICE_CALL,VIDEO_CALL',
        ];

        return $rules;
    }

    public function respond(Request $request)
    {
        $validatorResponse = $this->validateRequest($request, [
            'status'            => 'required|in:ACCEPTED,REJECTED,CANCELLED,HANG_UP',
            'recipient_user_id' => 'required',
            'type'              => 'required|in:VOICE_CALL,VIDEO_CALL',
            'session_id'        => 'required',
        ]);

        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }

        $currentUser       = $this->getCurrentUserDetails();
        $recipient_user_id = $request->input('recipient_user_id');

        if ($currentUser->uid == $recipient_user_id) {
            return $this->sendNotFoundResponse("You cannot send message to yourself.");
        }

        $messageRecipientUserDetails = $this->userRepository->findOne($recipient_user_id);

        if ( ! $messageRecipientUserDetails instanceof User) {
            return $this->sendNotFoundResponse("The user with id {$recipient_user_id} doesn't exist");
        }

        switch ($request->input('status')) {
            case 'ACCEPTED':
                $accepted = $this->accepted($request, $currentUser, $messageRecipientUserDetails);

                return $accepted;

            case 'REJECTED':
                $rejected = $this->rejected($request, $currentUser, $messageRecipientUserDetails);

                return $rejected;

            case 'CANCELLED':
                $cancelled = $this->cancelled($request, $currentUser, $messageRecipientUserDetails);

                return $cancelled;

            case 'HANG_UP':
                $hangup = $this->hangUp($request, $currentUser, $messageRecipientUserDetails);

                return $hangup;

            case 'BUSY':
                $busy = $this->busy($request, $currentUser, $messageRecipientUserDetails);

                return $busy;
        }
    }

    public function busy(
        $request,
        $currentUser,
        $messageRecipientUserDetails
    ) {

        $input                         = app();
        $callObject                    = $input->make('stdClass');
        $callObject->recipient_user_id = $messageRecipientUserDetails->uid;
        $callObject->user_creator_id   = $currentUser->uid;
        $callObject->status            = 'BUSY';

        return $this->respondWithItem($callObject, $this->callRecipientTransformer);
    }

    public function hangUp(
        $request,
        $currentUser,
        $messageRecipientUserDetails
    ) {
        $sendUserDetails = $messageRecipientUserDetails;

        $this->sendFcm('HANG_UP', $sendUserDetails, $request, $currentUser);

        $input                         = app();
        $callObject                    = $input->make('stdClass');
        $callObject->recipient_user_id = $messageRecipientUserDetails->uid;
        $callObject->user_creator_id   = $currentUser->uid;
        $callObject->status            = 'HANG_UP';

        return $this->respondWithItem($callObject, $this->callRecipientTransformer);
    }

    public function cancelled(
        $request,
        $currentUser,
        $messageRecipientUserDetails
    ) {
        $sendUserDetails = $messageRecipientUserDetails;
        $this->sendFcm('CANCELLED', $sendUserDetails, $request, $currentUser);

        $input                         = app();
        $callObject                    = $input->make('stdClass');
        $callObject->recipient_user_id = $messageRecipientUserDetails->uid;
        $callObject->user_creator_id   = $currentUser->uid;
        $callObject->status            = 'CANCELLED';

        return $this->respondWithItem($callObject, $this->callRecipientTransformer);
    }

    public function rejected(
        $request,
        $currentUser,
        $messageRecipientUserDetails
    ) {
        $sendUserDetails = $messageRecipientUserDetails;
        $this->sendFcm('REJECTED', $sendUserDetails, $request, $currentUser);

        $input                         = app();
        $callObject                    = $input->make('stdClass');
        $callObject->recipient_user_id = $messageRecipientUserDetails->uid;
        $callObject->user_creator_id   = $currentUser->uid;
        $callObject->status            = 'REJECTED';

        return $this->respondWithItem($callObject, $this->callRecipientTransformer);
    }

    public function accepted(
        $request,
        $currentUser,
        $messageRecipientUserDetails
    ) {

    }

    public function sendFcm($status, $sendUserDetails, $request, $currentUser)
    {
        $fields = array(
            'to'   => $sendUserDetails->fcm_registration_id,
            'data' => $status,
        );

        $message = $status;

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['status' => $status]);
        $dataBuilder->addData(['user_creator_id' => $currentUser->uid]);
        $dataBuilder->addData(['type' => $request->input('type')]);
        $dataBuilder->addData(['session_id' => $request->input('session_id')]);

        $fcm = FcmHelperFunctions::sendFcm($message, $fields, $dataBuilder);

        return true;
    }
}
