<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;

class ViewUserLocationMapController extends Controller
{
    public function index(){
        return view('userLocation');
    }

    public function jsonDataOfAllUsers(){
        $users = User::all();
        $data='{ "data":[';
        foreach ($users as $user){
            $loc = Location::where('user_id',$user->id)->first();
            $image = Image::where('user_id',$user->id)->first();
            if($loc && $image){
                $data.='{ "name":"'.$user->name
                    .'", "longitude":"'
                    .$loc->longitude
                    .'", "latitude":"'
                    .$loc->latitude
                    .'", "uimage":"'
                    .(substr($image->path,0,4) == 'http' ? $image->path : 'storage/'.$image->path.$image->name)
                    .'", "userid":"'
                    .$user->id
                    .'"},';
            }else if($loc){
                    $data.='{ "name":"'.$user->name
                        .'", "longitude":"'
                        .$loc->longitude
                        .'", "latitude":"'
                        .$loc->latitude
                        .'", "uimage":"'
                        .'https://cdn.shopify.com/s/files/1/1061/1924/products/Thinking_Face_Emoji_large.png'
                        .'", "userid":"'
                        .$user->id
                        .'"},';
            }
        }
        $data=substr($data,0,strlen($data)-1);
        $data.=']}';
        return $data;
    }

    public function getUserDataForMap(Request $request){
        $user = User::find($request->input('id'));
        return json_encode($user);
    }
}
