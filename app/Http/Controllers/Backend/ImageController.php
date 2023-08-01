<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\User;
use App\Repositories\Contracts\ImageRepository;
use App\Repositories\Contracts\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    private $userRepository;
    private $imageRepository;

    public function __construct(UserRepository $userRepository, ImageRepository $imageRepository)
    {
        $this->imageRepository = $imageRepository;
        $this->userRepository  = $userRepository;

        parent::__construct();
    }

    public function edit($id)
    {
        $user = $this->userRepository->findOneByIdWithTrashed($id);

        if ( ! $user instanceof User) {
            return redirect(route('user.index'))->with('fail', trans('strings.backend.users.edit.user_not_found'));
        }

        $userDetails = $this->editUserDetails($id);

        $input = [
            'user_id' => $id
        ];

        $image = Image::where('user_id', $id)->orderBy('number')->get();

        return view('backend.users.images.edit', ['userDetails' => $userDetails, 'image' => $image]);
    }

    public function tempStore(Request $request)
    {
        if ( ! empty($_FILES)) {

            $tmpImagePath = '/app/public/uploads/tmpImages';
            $path         = storage_path($tmpImagePath);

            if ( ! file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $file = $request->file('file');

            $name = trim($file->getClientOriginalName());

            $file->move($path, $name);
        }
    }

    public function store(Request $request, $id)
    {

        $validatorResponse = $this->validateRequest($request, $this->storeRequestValidationRules());

        if ($validatorResponse !== true) {
            $jsonData = [
                'status'  => false,
                'message' => trans('strings.backend.images.edit.image_changed_failed')
            ];

            return json_encode($jsonData);
        }

        $fileName = $this->getNewFileName();

        $user = $this->userRepository->findOneById($id);

        if ( ! $user instanceof User) {
            $jsonData = [
                'status'  => false,
                'message' => trans('strings.backend.images.edit.image_changed_failed')
            ];

            return json_encode($jsonData);
        }

//  get current user uid
        $userUid = $user->uid;

// get current user id
        $userId = $user->id;

        $imagePath = $this->getImagePath($userUid);

        $input['name']    = $fileName;
        $input['path']    = $imagePath;
        $input['user_id'] = $userId;
        $input['number']  = $request->input('number');
        $input['link']    = 0;

        $imageExists = $this->imageRepository->findOneBy([
            'user_id' => $userId,
            'number'  => $input['number']
        ]);

        if ( ! $imageExists instanceof Image) {
            $image = $this->imageRepository->save($input);

        } else {
            $oldImageName = $imageExists->name;
            $image        = $this->imageRepository->update($imageExists, $input);

// Delete old image
            Storage::delete('public/' . $imagePath . $oldImageName);
        }

        Storage::putFileAs('public/' . $imagePath, $request->file('croppedImage'), $fileName);

        $jsonData = [
            'status'    => true,
            'message'   => trans('strings.backend.images.edit.image_changed_successfully'),
            'image_url' => asset('/storage/' . $imagePath . $fileName)
        ];


        $path = Storage::delete('public/uploads/tmpImages/' . $request->input('tmpImageName'));

        return json_encode($jsonData);
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
     * Generate new filename according to timestamp
     *
     * @param $type
     *
     * @return bool
     */
    public function getNewFileName()
    {
        $fileName = 'image_' . time() . '.png';

        return $fileName;

    }

    /**
     * Store Request Validation Rules
     *
     * @return array
     */

    private function storeRequestValidationRules()
    {
        $rules = [
            'croppedImage' => 'required',
            'number'       => 'required|numeric|min:1|max:6',
            'tmpImageName' => 'required'
        ];

        return $rules;
    }

    public function destroy(Request $request, $id)
    {
        $validatorResponse = $this->validateRequest($request, [
            'number' => 'required|numeric|min:2|max:6'
        ]);

        if ($validatorResponse !== true) {
            $jsonData = [
                'status'  => false,
                'message' => trans('strings.backend.images.delete.image_delete_failed')
            ];

            return json_encode($jsonData);
        }

        $currentUser = $this->getCurrentUserDetails();

        // get current user id

        $imageNumber = $request->input('number');

        $imageExists = $this->imageRepository->findOneBy([
            'user_id' => $id,
            'number'  => $imageNumber
        ]);

        if ( ! $imageExists instanceof Image) {
            $jsonData = [
                'status'  => false,
                'message' => trans('strings.backend.images.delete.image_delete_failed')
            ];

            return json_encode($jsonData);
        }

        // Delete image

        Storage::delete('public/' . $imageExists->path . $imageExists->name);

        $this->imageRepository->delete($imageExists);

        $jsonData = [
            'status'       => true,
            'message'      => trans('strings.backend.images.delete.image_deleted_successfully'),
            'image_number' => $imageNumber
        ];

        return json_encode($jsonData);
    }
}
