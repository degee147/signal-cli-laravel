<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SignalService;
use App\Ultainfinity\Ultainfinity;
use App\Http\Requests\SendMessageRequest;
use App\Http\Requests\RegisterPhoneRequest;

class SignalController extends Controller
{
    use Ultainfinity;
    /**
     * @OA\Get(
     *     path="/version",
     *     summary="View installed version of Signal CLI",
     *     @OA\Response(
     *         response=200,
     *         description="Everything OK"
     *     )
     * )
     */
    public function version()
    {
        $updates = (new SignalService())->version();
        return response()->json($updates, 200);
    }
    public function receive()
    {
        $updates = (new SignalService())->receiveMessages();
        return response()->json($updates, 200);
    }
    public function verify($code)
    {
        $updates = (new SignalService())->verify($code);
        return response()->json($updates, 200);
    }


    /**
     * @OA\Post(
     *     path="/sendmessage",
     *     summary="Send a message to user",
     *     operationId="sendmessage",
     *     description="Send params in form-data. Add Accept:application/json in header",
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     @OA\Parameter(
     *         name="phone",
     *         in="query",
     *         description="receipient phone number",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="message",
     *         in="query",
     *         description="message",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     * )
     */
    public function sendmessage(SendMessageRequest $request)
    {
        $input = $request->validated();
        $updates = (new SignalService())->sendMessage($input['phone'], $input['message']);
        return response()->json($updates, 200);
    }

    /**
     * @OA\Post(
     *     path="/register",
     *     summary="Endpoint to register phone number. Number has to be set in .env because it's better to use one number at a time with signal",
     *     operationId="register",
     *     description="Send params in form-data. Add Accept:application/json in header",
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     @OA\Parameter(
     *         name="captcha",
     *         in="query",
     *         description="captcha gottent from https://signalcaptchas.org/registration/generate.html or https://signalcaptchas.org/challenge/generate.html",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="captcha",
     *         in="query",
     *         description="captcha",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     * )
     */
    public function register(RegisterPhoneRequest $request)
    {
        $input = $request->validated();
        $updates = (new SignalService())->register($input['captcha']);
        if ($updates == null) {
            return $this->AppResponse('success', 'check your phone for otp verfication number', 200);
        }
        return response()->json($updates, 400);
    }

}
