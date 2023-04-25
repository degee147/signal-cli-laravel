<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Services\SignalService;
use App\Ultainfinity\Ultainfinity;
use App\Http\Requests\CommandRequest;
use App\Http\Requests\ProfileNameRequest;
use App\Http\Requests\SendMessageRequest;
use App\Http\Requests\VerificationRequest;
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

    /**
     * @OA\Get(
     *     path="/command",
     *     summary="Send a custom command to the Signal CLI",
     *     description="List of available commands: addDevice,block,daemon,deleteLocalAccountData,getAttachment,getUserStatus,joinGroup,jsonRpc,link,listAccounts,listContacts,listDevices,listGroups,listIdentities,listStickerPacks,quitGroup,receive,register,remoteDelete,removeContact,removeDevice,removePin,send,sendContacts,sendPaymentNotification,sendReaction,sendReceipt,sendSyncRequest,sendTyping,setPin,submitRateLimitChallenge,trust,unblock,unregister,updateAccount,updateConfiguration,updateContact,updateGroup,updateProfile,uploadStickerPack,verify,version",
     *     @OA\Response(
     *         response=200,
     *         description="Everything OK"
     *     )
     * )
     */
    public function command(CommandRequest $request)
    {
        $input = $request->validated();
        $updates = (new SignalService())->command($input['command']);
        return response()->json($updates, 200);
    }

    /**
     * @OA\Get(
     *     path="/receive",
     *     summary="Receieve unread messages",
     *     @OA\Response(
     *         response=200,
     *         description="Everything OK"
     *     )
     * )
     */
    public function receive()
    {
        // $updates = (new SignalService())->receiveMessages();
        // return response()->json($updates, 200);

        $unreadMessages = Message::where('replied', false)->orderBy('id', 'asc')->paginate(10);
        return response()->json($unreadMessages, 200);

    }

    /**
     * @OA\Post(
     *     path="/verify",
     *     summary="Verify a registered phone number using otp from sms",
     *     operationId="verify",
     *     description="Send params in form-data. Add Accept:application/json in header",
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     @OA\Parameter(
     *         name="code",
     *         in="query",
     *         description="otp code",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     * )
     */
    public function verify(VerificationRequest $request)
    {

        $input = $request->validated();
        $updates = (new SignalService())->verify($input['code']);
        if ($updates['success']) {
            return $this->AppResponse('success', 'verification successfull', 200);
        }
        return response()->json($updates, 400);
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
     *         description="receipient phone number in international format e.g +23480312345678",
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
        if ($updates['success']) {
            //mark all sender's messages as replied
            Message::where('sender', 'like', '%' . $input['phone'])->update(['replied' => true]);
        }
        return response()->json($updates, 200);
    }


    /**
     * @OA\Post(
     *     path="/profilename",
     *     summary="Update profile name of registered phone number",
     *     operationId="profilename",
     *     description="Send params in form-data. Add Accept:application/json in header",
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="name string",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     )
     * )
     */
    public function profilename(ProfileNameRequest $request)
    {
        $input = $request->validated();
        $updates = (new SignalService())->updateProfileName($input['name']);
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
     *     )
     * )
     */
    public function register(RegisterPhoneRequest $request)
    {
        $input = $request->validated();
        $updates = (new SignalService())->register($input['captcha']);
        if ($updates['success']) {
            return $this->AppResponse('success', 'check your phone for otp verfication number', 200);
        }
        return response()->json($updates, 400);
    }
    public function unregister()
    {
        $updates = (new SignalService())->unregister();
        if ($updates['success']) {
            return $this->AppResponse('success', 'unregistered', 200);
        }
        return response()->json($updates, 400);
    }

}
