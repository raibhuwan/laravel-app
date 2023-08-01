<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ChatMessageRecipient;
use App\Models\SwipeMatch;
use App\Models\User;
use App\Repositories\Contracts\ChatMessageRecipientRepository;
use App\Repositories\Contracts\ChatMessageRepository;
use App\Repositories\Contracts\UserRepository;
use App\Repositories\EloquentSwipeMatchRepository;
use App\Transformers\MessageSentTransformer;
use App\Transformers\MessageTransformer;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use LaravelFCM\Facades\FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use Ramsey\Uuid\Uuid;

class MessageController extends Controller
{
    /**
     * Instance of UserRepository
     *
     * @var UserRepository
     */
    private $userRepository;
    private $chatMessageRepository;
    private $chatMessageRecipientRepository;
    private $eloquentSwipeMatchRepository;
    private $messageTransformer;
    private $messageSentTransformer;

    public function __construct(
        UserRepository $userRepository,
        ChatMessageRepository $chatMessageRepository,
        ChatMessageRecipientRepository $chatMessageRecipientRepository,
        MessageTransformer $messageTransformer,
        MessageSentTransformer $messageSentTransformer
    ) {
        $this->userRepository                 = $userRepository;
        $this->chatMessageRepository          = $chatMessageRepository;
        $this->chatMessageRecipientRepository = $chatMessageRecipientRepository;
        $this->eloquentSwipeMatchRepository   = new EloquentSwipeMatchRepository(new SwipeMatch());
        $this->messageTransformer             = $messageTransformer;
        $this->messageSentTransformer         = $messageSentTransformer;

        parent::__construct();
    }

    /**
     * Update FCM registration id in users table
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function updateFcmID(Request $request)
    {
        // Validation
        $validatorResponse = $this->validateRequest($request, [
            'fcm_registration_id' => 'required'
        ]);

        // Send failed response if validation fails
        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }

        $currentUser = $this->getCurrentUserDetails();
        $user        = $this->userRepository->update($currentUser, $request->all());

        return $this->sendCustomResponse("200",
            "Fcm Registration id has been updated for user id : {$currentUser->uid}");

    }

    /**
     * Send message to single user
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function messageSingleUser(Request $request)
    {
        // Validation
        $validatorResponse = $this->validateRequest($request, $this->storeRequestValidationRules($request));

        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }

        $currentUser = $this->getCurrentUserDetails();

        if ($currentUser->fcm_registration_id == '') {
            return $this->sendNotFoundResponse("Your fcm registration id doesn't exist.");
        }

        $recipient = $this->userRepository->findOne($request->input('recipient_id'));

        if ( ! $recipient instanceof User) {
            return $this->sendNotFoundResponse("The user with id {$request->input('recipient_id')} doesn't exist.");
        }

        if ($recipient->fcm_registration_id == '') {
            return $this->sendNotFoundResponse("The user with id {$request->input('recipient_id')} doesn't have fcm registration id.");
        }

        $match = $this->eloquentSwipeMatchRepository->getMatch($currentUser, $recipient);

        if ($match->count == 0) {
            return $this->sendNotFoundResponse("You are not matched with recipient id: {$request->input('recipient_id')}");
        }

        switch ($request->input('type')) {
            case 'VOICE':
                $voiceName   = $this->getNewFileName($request->file('message'), 'voice');
                $voicePath   = $this->getVoicePath($currentUser->uid);
                $messageBody = $voicePath . $voiceName;
                break;
            case 'FILE':
                $fileName    = $this->getNewFileName($request->file('message'), 'file');
                $filePath    = $this->getFilePath($currentUser->uid);
                $messageBody = $filePath . $fileName;
                break;
            default:
                $messageBody = $request->input('message');
        }

        $inputMessage = [
            'creator_id'   => $currentUser->id,
            'message_body' => $messageBody,
            'type'         => $request->input('type')
        ];

        switch ($request->input('type')) {
            case 'VOICE':
            case 'FILE':
                $inputMessage['meta'] = $request->input('meta');
                break;
        };

        $message = $this->chatMessageRepository->save($inputMessage);

        if ( ! $message instanceof ChatMessage) {
            return $this->sendCustomResponse(500, 'Error occurred on saving message.');
        }

        switch ($request->input('type')) {
            case 'VOICE':
                Storage::putFileAs('public/' . $voicePath, $request->file('message'), $voiceName);
                break;
            case 'FILE':
                Storage::putFileAs('public/' . $filePath, $request->file('message'), $fileName);
                break;
        };

        $inputMessageRecipient = [
            'recipient_id'    => $recipient->id,
            'chat_message_id' => $message->id
        ];

        if ($request->has('device_message_id')) {
            $inputMessageRecipient['device_message_id'] = $request->input('device_message_id');
        } else {
            $inputMessageRecipient['device_message_id'] = 'n-' . Uuid::uuid4();
        }

        $messageRecipient = $this->chatMessageRecipientRepository->save($inputMessageRecipient);

        if ( ! $messageRecipient instanceof ChatMessageRecipient) {
            return $this->sendCustomResponse(500, 'Error occurred on saving message.');
        }

        switch ($request->input('type')) {
            case 'VOICE':
            case 'FILE':
                $newMessageBody = url('/') . '/storage/' . $messageBody;
                break;
            default:
                $newMessageBody = $messageBody;
        }

        $fields = array(
            'to'   => $recipient->fcm_registration_id,
            'data' => $newMessageBody,
        );

        $notificationBuilder = new PayloadNotificationBuilder("{$currentUser->name} has sent you a message.");
        $notificationBuilder->setBody($fields['data'])->setClickAction(config('fcm.click_action'))->setSound('notification_sound.mp3');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['recipient_id' => $currentUser->uid]);
        $dataBuilder->addData(['recipient_name' => $currentUser->name]);
        $dataBuilder->addData(['message' => $newMessageBody]);
        $dataBuilder->addData(['user_creator_id' => $request->input('recipient_id')]);
        $dataBuilder->addData(['device_message_id' => $messageRecipient->device_message_id]);
        $dataBuilder->addData(['type' => $message->type]);
        $dataBuilder->addData(['meta' => (string)$message->meta]);
        $dataBuilder->addData(['created_at' => (string)$message->created_at]);
        $dataBuilder->addData(['updated_at' => (string)$message->updated_at]);


        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setContentAvailable(true);
        $optionBuilder->setMutableContent(true);

        $data         = $dataBuilder->build();
        $notification = $notificationBuilder->build();
        $option       = $optionBuilder->build();

        // Here the platform ANDROID or IOS is identified.
        $client = new Client();

        $requestContent = [
            'headers' => [
                'Authorization' => 'key=' . config('fcm.http.server_key'),
                'Content-Type'  => 'application/json'
            ]
        ];


        try {
            $responseGuzzle = $client->request('POST', "https://iid.googleapis.com/iid/info/{$fields['to']}",
                $requestContent);
        } catch (ClientException $e) {
            $responseGuzzle = $e->getResponse()->getReasonPhrase();

            return $this->sendNotFoundResponse("The message could not be sent. Platform not identified. Reason = {$responseGuzzle}");
        }

        $responseBody = $responseGuzzle->getBody()->getContents();

        $platform = json_decode($responseBody)->platform;

        if ($platform == 'ANDROID') {
            $messageSent = FCM::sendTo($fields['to'], $option, null, $data);
        } elseif ($platform == 'IOS') {
            $messageSent = FCM::sendTo($fields['to'], $option, $notification, $data);
        }

        if ($messageSent->numberSuccess() >= 1) {
            $input = app();

            $messageObject = $input->make('stdClass');

            $messageObject->chat_message_id   = $message->uid;
            $messageObject->user_creator_id   = $currentUser->uid;
            $messageObject->user_recipient_id = $request->input('recipient_id');
            $messageObject->message_body      = $message->message_body;
            $messageObject->created_at        = $message->created_at;
            $messageObject->updated_at        = $message->updated_at;
            $messageObject->device_message_id = $messageRecipient->device_message_id;
            $messageObject->type              = $request->input('type');
            $messageObject->meta              = $message->meta;

            return $this->respondWithItem($messageObject, $this->messageSentTransformer);
        }

        return $this->sendNotFoundResponse("The message could not be sent");
    }

    /**
     * Define a path using the user id
     *
     * @param $user_uid
     *
     * @return string
     */
    public function getVoicePath($user_uid)
    {
        $soundPath = 'users/user_' . $user_uid . '/sounds/';

        return $soundPath;
    }

    /**
     * Define a path using the user id
     *
     * @param $user_uid
     *
     * @return string
     */
    public function getFilePath($user_uid)
    {
        $filePath = 'users/user_' . $user_uid . '/files/';

        return $filePath;
    }

    /**
     * Generate new filename according to timestamp
     *
     * @param $type
     *
     * @return bool
     */
    public function getNewFileName($file, $name)
    {
        $file_name = $name . time() . '.' . $file->getClientOriginalExtension();

        return $file_name;
    }

    private function storeRequestValidationRules(Request $request)
    {
        $rules = [
            'recipient_id' => 'required',
//            'device_message_id' => 'required|string',
            'type'         => 'required|in:TEXT,VOICE,FILE',
        ];

        switch ($request->input('type')) {
            case 'VOICE':
                $rules['message'] = 'required|mimetypes:application/octet-stream,audio/x-m4a|max:5000';
                $rules['meta']    = 'required';
                break;
            case 'FILE':
                $rules['message'] = 'required|max:15000';
                $rules['meta']    = 'required';
                break;
            default:
                $rules['message'] = 'required|max:255';
        }


        return $rules;
    }

    /**
     *  Get messages
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function getMessage(Request $request, $id)
    {
        $recipientId = $this->userRepository->findOne($id);

        if ( ! $recipientId instanceof User) {
            return $this->sendNotFoundResponse("The user with id {$id} doesn't exist");
        }

        $currentUser = $this->getCurrentUserDetails();
        DB::connection()->enableQueryLog();
        $messages = DB::table('chat_messages');

        $query = $messages->join('chat_message_recipients',
            function ($join) use ($currentUser, $recipientId, $request) {
                $join->on('chat_messages.id', '=', 'chat_message_recipients.chat_message_id')->where(function ($message
                ) use (
                    $currentUser,
                    $recipientId,
                    $request

                ) {
                    $chatMessages = $message->where(function ($chat) use ($currentUser, $recipientId) {
                        $chat->where(function ($chat_creator) use ($currentUser, $recipientId) {
                            $chat_creator->where('chat_messages.creator_id', '=',
                                $currentUser->id)->where('chat_message_recipients.recipient_id', '=', $recipientId->id);
                        })->orWhere(function ($chat_recipient) use ($currentUser, $recipientId) {
                            $chat_recipient->where('chat_messages.creator_id', '=',
                                $recipientId->id)->where('chat_message_recipients.recipient_id', '=', $currentUser->id);
                        });
                    });

                    if ($request->has('updatedAt')) {
                        $chatMessages->where('chat_messages.updated_at', '>', $request->input('updatedAt'));
                    }

                });
            })->select('chat_messages.uid as chat_message_id', 'chat_messages.message_body', 'chat_messages.created_at',
            'chat_messages.updated_at', 'chat_messages.creator_id', 'chat_messages.type', 'chat_messages.meta',
            'chat_message_recipients.recipient_id', 'chat_message_recipients.device_message_id');

        $rawQuery = $this->getSql($query);

        $messages = DB::table('users')->join(DB::raw("($rawQuery) as messages"), function ($join) {
            $join->on('users.id', '=', 'messages.creator_id');
        })->select('messages.chat_message_id', 'users.uid as user_creator_id', 'messages.message_body',
            'messages.created_at', 'messages.type', 'messages.meta', 'messages.updated_at',
            'messages.device_message_id')->orderBy('messages.created_at', 'DESC')->paginate();
        $results  = DB::getQueryLog();

        return $this->respondWithCollection($messages, $this->messageTransformer);
    }

    /**
     * This function return recipient_id as well but we don't need now so we are keeping backup
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function backup($id)
    {
        {
            $recipientId = $this->userRepository->findOne($id);

            if ( ! $recipientId instanceof User) {
                return $this->sendNotFoundResponse("The user with id {$id} doesn't exist");
            }

            $currentUser = $this->getCurrentUserDetails();
            DB::connection()->enableQueryLog();
            $messages = DB::table('chat_messages');

            $query = $messages->join('chat_message_recipients', function ($join) use ($currentUser, $recipientId) {
                $join->on('chat_messages.id', '=', 'chat_message_recipients.id')->where(function ($message) use (
                    $currentUser,
                    $recipientId
                ) {
                    $message->where(function ($chat_creator) use ($currentUser, $recipientId) {
                        $chat_creator->where('chat_messages.creator_id', '=',
                            $currentUser->id)->where('chat_message_recipients.recipient_id', '=', $recipientId->id);
                    })->orWhere(function ($chat_recipient) use ($currentUser, $recipientId) {
                        $chat_recipient->where('chat_messages.creator_id', '=',
                            $recipientId->id)->where('chat_message_recipients.recipient_id', '=', $currentUser->id);
                    });
                });
            })->select('chat_messages.uid as chat_message_id', 'chat_messages.message_body', 'chat_messages.created_at',
                'chat_messages.creator_id', 'chat_message_recipients.recipient_id');

            $rawQuery = $this->getSql($query);

            $messages = DB::table('users')->join(DB::raw("($rawQuery) as messages"), function ($join) {
                $join->on('users.id', '=', 'messages.creator_id');
            })->select('messages.chat_message_id', 'users.uid as user_creator_id', 'messages.recipient_id',
                'messages.message_body', 'messages.created_at');

            $rawQuery2 = $this->getSql($messages);

            $messages2 = DB::table('users')->join(DB::raw("($rawQuery2) as messages2"), function ($join) {
                $join->on('users.id', '=', 'messages2.recipient_id');
            })->select('messages2.chat_message_id', 'messages2.user_creator_id as user_creator_id',
                'users.uid as user_recipient_id', 'messages2.message_body',
                'messages2.created_at')->orderBy('messages2.created_at', 'DESC')->paginate();
            $results   = DB::getQueryLog();

            return $this->respondWithCollection($messages2, $this->messageTransformer);
        }
    }
}
