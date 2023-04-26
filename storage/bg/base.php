<?php

use App\Services\SignalService;
require  '../../app/Services/SignalService.php';

// require 'Signal.php';
// require __DIR__ . '..Signal.php';


// $phone = "+2348058582828";
$phone = "+2348067058555";
// $signal = new Signal($phone);
$signal = new SignalService($phone);




function make_path($path_name)
{

    $dirPath = __DIR__ . $path_name;

    // Check if the directory exists
    if (!file_exists($dirPath)) {
        // Create the directory
        mkdir($dirPath, 0777, true);
    }

    $save_path = $dirPath . "/output" . mt_rand(11111, 99999) . ".txt";

    // Open a file for reading and writing
    $file = fopen($save_path, "w+");

    return $save_path;
}




//  $output = $signal->version();
// $output = $signal->sendMessage("+2348030910338", "Hi there, how are you?");
//  $output = $signal->receiveMessages();
//  $output = $signal->register($captcha);
//  $output = $signal->verify("224506");
//  echo json_encode($output);

// $output = $signal->version();

//always the first step

// $output = $signal->receiveMessages();

//second step



//send custom command
//output = $signal->command("command string")

//list of available commands
//addDevice,block,daemon,deleteLocalAccountData,getAttachment,getUserStatus,joinGroup,jsonRpc,link,listAccounts,listContacts,listDevices,listGroups,listIdentities,listStickerPacks,quitGroup,receive,register,remoteDelete,removeContact,removeDevice,removePin,send,sendContacts,sendPaymentNotification,sendReaction,sendReceipt,sendSyncRequest,sendTyping,setPin,submitRateLimitChallenge,trust,unblock,unregister,updateAccount,updateConfiguration,updateContact,updateGroup,updateProfile,uploadStickerPack,verify,version
