<?php

namespace NotificationChannels\ExpoPushNotifications\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use NotificationChannels\ExpoPushNotifications\ExpoChannel;

class ExpoController extends Controller
{
    /**
     * @var ExpoChannel
     */
    private $expoChannel;

    /**
     * ExpoController constructor.
     *
     * @param  ExpoChannel  $expoChannel
     */
    public function __construct(ExpoChannel $expoChannel)
    {
        $this->expoChannel = $expoChannel;
    }

    /**
     * Handles subscription endpoint for an expo token.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'expo_token' => 'required|string',
            'experience_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return new JsonResponse([
                'status' => 'failed',
                'error' => [
                    'message' => 'Expo Token is required',
                ],
            ], 422);
        }

        $token = $request->get('expo_token');
        $experienceId = $request->get('experience_id');

        $interest = $this->expoChannel->interestName(Auth::user());

        try {
            $this->expoChannel->expo
                ->subscribe($interest, $token, $experienceId);
        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => 'failed',
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ], 500);
        }

        return new JsonResponse([
            'status' => 'succeeded',
            'expo_token' => $token,
        ], 200);
    }

    /**
     * Handles removing subscription endpoint for the authenticated interest.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function unsubscribe(Request $request)
    {
        $interest = $this->expoChannel->interestName(Auth::user());

        $validator = Validator::make($request->all(), [
            'expo_token'    =>  'sometimes|string',
        ]);

        if ($validator->fails()) {
            return new JsonResponse([
                'status' => 'failed',
                'error' => [
                    'message' => 'Expo Token is invalid',
                ],
            ], 422);
        }

        $token = $request->get('expo_token') ?: null;

        try {
            $deleted = $this->expoChannel->expo->unsubscribe($interest, $token);
        } catch (\Exception $e) {
            return new JsonResponse([
                'status'    => 'failed',
                'error'     =>  [
                    'message' => $e->getMessage(),
                ],
            ], 500);
        }

        return new JsonResponse(['deleted' => $deleted]);
    }
}
