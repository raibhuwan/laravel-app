<?php

namespace App\Http\Controllers\Api;

use App\Helpers\FacebookHelperFunctions;
use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\User;
use App\Repositories\EloquentImageRepository;
use App\Repositories\EloquentUserRepository;
use App\Transformers\ImageTransformer;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ImageController extends Controller
{

    /**
     * @var EloquentImageRepository
     */
    protected $eloquentImageRepository;

    /**
     * @var ImageTransformer
     */
    private $imageTransformer;

    /**
     * @var EloquentUserRepository
     */
    private $userRepository;

    public function __construct()
    {
        $this->eloquentImageRepository = new EloquentImageRepository(new Image());
        $this->imageTransformer        = new ImageTransformer();
        $this->userRepository          = new EloquentUserRepository(new User());

        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $images = $this->eloquentImageRepository->findBy($request->all());

        return $this->respondWithCollection($images, $this->imageTransformer);
    }

    /**
     * Store new image in storage directory or update the existing ones
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function store(Request $request)
    {
        $validatorResponse = $this->validateRequest($request, $this->storeRequestValidationRules());

        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }

        $fileData = $request->input('image');
        $type      = $request->input('type');

        $fileName = $this->getNewFileName($type);

        $currentUser = $this->getCurrentUserDetails();

        //  get current user uid
        $userUid = $currentUser ->uid;

        // get current user id
        $userId = $currentUser->id;

        $imagePath = $this->getImagePath($userUid);

        $input['name']   = $fileName;
        $input['path']   = $imagePath;
        $input['number'] = $request->input('number');
        $input['link']   = 0;

        $imageExists = $this->eloquentImageRepository->findOneBy([
            'user_id' => $userId,
            'number'  => $input['number']
        ]);

        if ( ! $imageExists instanceof Image) {
            $image = $this->eloquentImageRepository->save($input);
        } else {
            $oldImageName = $imageExists->name;
            $image          = $this->eloquentImageRepository->update($imageExists, $input);

            // Delete old image
            Storage::delete('public/' . $imagePath . $oldImageName);
        }

        if ( ! $image instanceof Image) {
            return $this->sendCustomResponse(500, 'Error occurred on creating Image');
        }

        $this->createImageFromBase64($fileData, $fileName, $imagePath);

        return $this->setStatusCode(201)->respondWithItem($image, $this->imageTransformer);
    }

    public function update(Request $request, $id)
    {
        // Validation
        $validatorResponse = $this->validateRequest($request, $this->storeRequestValidationRules());

        // Send failed response if validation fails
        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }

        $image = $this->eloquentImageRepository->findOne($id);

        if ( ! $image instanceof Image) {
            return $this->sendNotFoundResponse("The image with id {$id} doesn't exist");
        }

        // Authorization
        $this->authorize('update', $image);

        $fileData = $request->input('image');
        $type      = $request->input('type');

        $fileName = $this->getNewFileName($type);

        $currentUser = $this->getCurrentUserDetails();

        //  get current user uid
        $userUid = $currentUser->uid;

        // get current user id
        $userId = $currentUser->id;

        $imagePath = $this->getImagePath($userUid);

        $input['name']   = $fileName;
        $input['path']   = $imagePath;
        $input['number'] = $request->input('number');
        $input['link']   = 0;

        $oldImageName = $image->name;

        $image = $this->eloquentImageRepository->update($image, $input);


        if ( ! $image instanceof Image) {
            return $this->sendCustomResponse(500, 'Error occurred on creating Image');
        }

        $this->createImageFromBase64($fileData, $fileName, $imagePath);
        // Delete old image
        Storage::delete('public/' . $imagePath . $oldImageName);

        return $this->respondWithItem($image, $this->imageTransformer);
    }

    /**
     * Create image from base 64
     *
     * @param $fileData
     * @param $fileName
     */
    public function createImageFromBase64($fileData, $fileName, $imagePath)
    {
        if ($fileData != "") { // storing image in storage/app/public Folder

            Storage::put('public/' . $imagePath . $fileName, base64_decode($fileData));
        }

        return;

    }

    /**
     * Store Request Validation Rules
     *
     * @return array
     */

    private function storeRequestValidationRules()
    {
        $rules = [
            'image'  => 'required|imageBase64',
            'type'   => [
                'required',
                Rule::in(['image/png', 'image/jpg', 'image/jpeg'])
            ],
            'number' => 'required|numeric|min:1|max:6'
        ];

        return $rules;
    }

    /**
     * Generate new filename according to timestamp
     *
     * @param $type
     *
     * @return bool
     */
    public function getNewFileName($type)
    {
        switch ($type) {
            case 'image/jpg':
                $fileName = 'image_' . time() . '.jpg';

                return $fileName;

            case 'image/jpeg':
                $fileName = 'image_' . time() . '.jpeg';

                return $fileName;

            case 'image/png':
                $fileName = 'image_' . time() . '.png';

                return $fileName;

            default:
                return false;
        }
    }

    /**
     * Insert profile image while registering
     *
     * @param $user
     * @param $image
     *
     * @return bool|mixed
     */
    public function insertProfileImage($user, $image)
    {
        $fileData = $image['image'];
        $type      = $image['type'];

        $fileName = $this->getNewFileName($type);

        $userId  = $user['id'];
        $userUid = $user['uid'];

        $imagePath = $this->getImagePath($userUid);

        $input['user_id'] = $userId;
        $input['name']    = $fileName;
        $input['path']    = $imagePath;
        $input['number']  = $image['number'];
        $input['link']    = 0;

        $imageSave = $this->eloquentImageRepository->save($input);

        if ( ! $imageSave instanceof Image) {
            return false;
        }

        $this->createImageFromBase64($fileData, $fileName, $imagePath);

        return true;
    }

    /**
     * Define a path using the user id
     *
     * @param $userUid
     *
     * @return string
     */
    public function getImagePath($userUid)
    {
        $imagePath = 'users/user_' . $userUid . '/photos/';

        return $imagePath;
    }

    /**
     * Get all images  of current user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getImagesUser()
    {

        $currentUser = $this->getCurrentUserDetails();

        $input = [
            'user_id' => $currentUser->id
        ];

        $images = $this->eloquentImageRepository->findBy($input);

        return $this->respondWithCollection($images, $this->imageTransformer);
    }

    /**
     * Remove image
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|string
     */
    public function delete(Request $request)
    {
        $validatorResponse = $this->validateRequest($request, $this->deleteImageValidationRules());

        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }

        if ($request->input('number') == 1) {
            return $this->sendNotFoundResponse("You are not allowed to delete image number 1.");
        }

        $currentUser = $this->getCurrentUserDetails();

        // get current user id
        $userId = $currentUser->id;

        $imageNumber = $request->input('number');

        $imageExists = $this->eloquentImageRepository->findOneBy([
            'user_id' => $userId,
            'number'  => $imageNumber
        ]);

        if ( ! $imageExists instanceof Image) {
            return $this->sendNotFoundResponse("The image with number {$imageNumber} doesn't exist.");
        }

        // Delete image

        Storage::delete('public/' . $imageExists->path . $imageExists->name);


        $this->eloquentImageRepository->delete($imageExists);

        $message = "The image number {$imageNumber} has been deleted successfully.";

        return $this->sendCustomResponse('200', $message);
    }

    /**
     * Delete image by providing number
     *
     * @return array
     */
    public function deleteImageValidationRules()
    {
        $rule = [
            'number' => 'required|numeric|min:1|max:6'
        ];

        return $rule;
    }


    /**
     * Save 4 image link from facebook
     *
     * @param $user
     *
     * @return bool
     */
    public function insertOtherProfileImagesFacebook($user)
    {
        $fb = FacebookHelperFunctions::facebook();

        $profilePictureAlbumId = $this->searchProfilePictureAlbum($user->access_token);

        // Receive the list of album names and id

        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $fb->get("/{$profilePictureAlbumId}/photos?fields=source&limit=4", $user->access_token);
        } catch (FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();

            return false;
        } catch (FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();

            return false;
        }

        // Taking 4 photos , source (link ) and id
        $albumPhotos = $response->getGraphEdge()->asArray();

        $images = [];
        $i      = 0;

        foreach ($albumPhotos as $key => $value) {
            $images[$i]['user_id'] = $user->id;
            $images[$i]['id']      = $value['id'];
            $images[$i]['source']  = $value['source'];
            $images[$i]['number']  = $i + 1;
            $i++;
        }

        $maxKeys = max(array_keys($images));

        for ($i = 0; $i <= $maxKeys; $i++) {
            $input = [
                'user_id' => $user->id,
                'name'    => $images[$i]['id'],
                'path'    => $images[$i]['source'],
                'number'  => $images[$i]['number'],
                'link'    => 1
            ];
            $this->eloquentImageRepository->save($input);
        }
        return true;
    }

    /**
     * Check and return the album id in an array
     *
     * @param $albumNamesArray
     *
     * @return bool
     */
    public function getProfilePictureAlbumId($albumNamesArray)
    {
        // Taking the id of Profile Picture album
        foreach ($albumNamesArray as $key) {
            if ($key['name'] == 'Profile Pictures') {
                $albumId = $key['id'];

                return $albumId;
            }
        }

        return false;
    }

    /** Recursively checks for Profile Picture album in facebook within multiple pages
     *
     * @param $accessToken
     *
     * @return bool
     */
    public function searchProfilePictureAlbum($accessToken)
    {
        $nextPage = '/me/albums?limit=6';
        $fb       = FacebookHelperFunctions::facebook();

        do {
            try {
                $response = $fb->get($nextPage, $accessToken);
            } catch (FacebookResponseException $e) {
                echo 'Graph returned an error: ' . $e->getMessage();

                return false;
            } catch (FacebookSDKException $e) {
                echo 'Facebook SDK returned an error: ' . $e->getMessage();

                return false;
            }
            $albumsId              = $response->getGraphEdge();
            $albumsIdArray         = $albumsId->asArray();
            $profilePictureAlbumId = $this->getProfilePictureAlbumId($albumsIdArray);
            $nextPage              = $albumsId->getPaginationUrl('next');
        } while ($profilePictureAlbumId == false && $nextPage != null);

        return $profilePictureAlbumId;
    }
}
