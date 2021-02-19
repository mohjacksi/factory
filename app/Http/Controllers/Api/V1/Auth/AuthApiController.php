<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginApiRequest;
use App\Http\Requests\RegisterApiRequest;
use App\Models\User;
use App\Traits\FirebaseFCM;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

//use Auth;
class AuthApiController extends Controller
{
    use FirebaseFCM;



    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterApiRequest $request)
    {
        $user = User::firstOrCreate([
            'phone_number' => $request->phone_number,
            'name' => $request->name,
        ]);
// create firebase token
        if ($request->firebase_token) {
            $this->checkFirebaseToken($user->id, $request->firebase_token);
        }
        $token = $user->createToken($user->phone_number . '-' . now());
        Auth::login($user);

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginApiRequest $request)
    {
        $user = User::where('phone_number', $request->phone_number)->whereNull('deleted_at')->first();

        if ($user) {
            $token = $user->createToken($user->phone_number . '-' . now());
            Auth::login($user);
//            dd(Auth::user()->id);
//            $token = Auth::user()->AauthAcessToken()->first();

            // create firebase token
            if ($request->firebase_token) {
                $this->checkFirebaseToken($user->id, $request->firebase_token);
            }

            return response()->json([
                'token' => $token->accessToken
            ]);
        }

        return response()->json([
            'message' => 'لم تستطيع إيجاد المستخدم!'
        ]);
    }


    public function logout()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $user->AauthAcessToken()->delete();

            if ($user->firebaseToken()->count() > 0) {
                $user->firebaseToken()->delete();
            }
        }

        return 'logged out successfully.';
    }
}
