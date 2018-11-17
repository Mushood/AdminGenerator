<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except('setup');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin(Request $request)
    {
        $user = Auth::user();
        $token = $user->api_token;

        $initialData = [
            'user' => [
                'name'  => $user->name,
                'email' => $user->email,
                'token' => $token,
            ],
            'locale'    => App::getLocale(),
            'url'       => config('app.url'),
        ];

        $initial = json_encode($initialData);

        return view('admin', compact('initial'));
    }

    /**
     * Get Initial Data
     *
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function setup ()
    {
        $initialData = [];
        $apiResponse = new ApiResponse();

        $user = Auth::user();

        $initialData['user'] = $user;

        $apiResponse->setData($initialData);

        return response()->json(
            $initialData,
            Response::HTTP_OK
        );
    }
}
