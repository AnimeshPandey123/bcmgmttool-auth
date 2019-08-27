<?php

namespace App\Http\Controllers\v1;

use App\User;
use Google_client;
use App\Models\SocialAuth;
use Illuminate\Http\Request;
use App\Models\UserMasterList;
use App\Http\Controllers\Controller;

class GoogleLoginController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function login(Request $request)
    {
        $user     = $request->all();
        if (!isset($user['token'])) {
            return response()->json(['message'=> 'Token is invalid or not present', 'Code'=> 400], 400);
        }
        $id_token = $user['token'];

        //Create an instance of a class Google_client
        $client = new \Google_Client();

        //Verify if the token provide is valid
        $payload = $client->verifyIdToken($id_token);

        if (!$payload)
        {
            // Invalid ID token
            return response()->json(['error' => 'Invalid Token', 'code' => 400], 400);
        }
        if (!array_key_exists("hd", $payload))
        {
            //Check if domain of email is karkhana
            return response()->json(['error' => 'Cannot login with personal account', 'code' => 404], 404);
        }

        $checkDomain = $this->checkDomain($payload);
        if (!$checkDomain)
        {
            return response()->json(['error' => 'Invalid Domain', 'code' => 403], 403);
        }

        $checkuserid = $this->checkuserid($checkDomain);
        if (!$checkuserid)
        {
            return response()->json(['error' => 'This User Doesnot Exist', 'code' => 403], 403);
        }
        if ($checkuserid)
        {
            $checkuser = $checkuserid->user()->first();
            // $user = User::find(3);
            // dd($user);
            //$this->content['token'] =  $checkuser->createToken('NewToken')->accessToken;
            //Create a Personal Access Token
            $dataToken = $checkuser->createToken('UserAuth');

            $expires = $dataToken->toArray()['token']->expires_at;

            $date = $expires->format('Y-m-d');

            $phone1 = $checkuser->userInfo->Phone1;

            $phone2 = $checkuser->userInfo->Phone2;

            $address = $checkuser->userInfo->HomeLocation;

            $bio = $checkuser->userInfo->Bio;
            // $bio=$user->userInfo->Bio;

            $userid = $payload['sub'];

            $tempData = [
                'user_name'  => $payload['name'],
                'email'      => $payload['email'],
                // 'photo'      => $checkuser->avatar(),
                'token'      => $dataToken->accessToken,
                'expires_at' => $date,
                'phone1'     => $phone1,
                'phone2'     => $phone2,
                'address'    => $address,
                'bio'        => strip_tags($bio),
            ];

            //return the data
            return response()->json(['data' => $tempData, 'code' => 200], 200);

        }

    }

    // If request specified a G Suite domain:
    public function checkDomain($checkuser)
    {
        if ($checkuser['hd'] == "karkhana.asia")
        {
            return $checkuser;
        }
        else
        {
            return [];
        }
    }

    //Check if userid exist in database
    public function checkuserid($checkuser)
    {
        $socialuser = SocialAuth::where('oauth_id', $checkuser['sub'])->first();
        if ($socialuser)
        {
            return $socialuser;
        }
        else
        {
            return [];

        }
    }
}
