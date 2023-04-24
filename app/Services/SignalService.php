<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;


class SignalService
{
    public $phone;

    public function __construct()
    {
        $this->phone = env('SIGNAL_PHONE');
        ;
    }

    public function version()
    {
        return $this->exec("signal-cli version");
    }

    public function sendMessage($number, $message)
    {
        return $this->exec("signal-cli -u " . $this->phone . " send -e -m " . $message . " " . $number);
    }

    public function receiveMessages()
    {
        return $this->exec("signal-cli -a " . $this->phone . " receive");

    }
    public function verify($code)
    {
        return $this->exec("signal-cli -a " . $this->phone . " verify " . $code);

    }
    public function listDevices()
    {
        return $this->exec("signal-cli -o json listDevices");
        // return $this->exec("signal-cli -a " . $this->phone . " -o json listDevices");
    }
    public function register($captcha)
    {
        return $this->exec("signal-cli -u " . $this->phone . " register --captcha " . $captcha);
    }

    public function unregister()
    {
        return $this->exec("signal-cli -a " . $this->phone . " unregister");
    }

    public function command($command)
    {
        //list of available commands
        //addDevice,block,daemon,deleteLocalAccountData,getAttachment,getUserStatus,joinGroup,jsonRpc,link,listAccounts,listContacts,listDevices,listGroups,listIdentities,listStickerPacks,quitGroup,receive,register,remoteDelete,removeContact,removeDevice,removePin,send,sendContacts,sendPaymentNotification,sendReaction,sendReceipt,sendSyncRequest,sendTyping,setPin,submitRateLimitChallenge,trust,unblock,unregister,updateAccount,updateConfiguration,updateContact,updateGroup,updateProfile,uploadStickerPack,verify,version
        return $this->exec("signal-cli " . $command);
    }

    private function exec($command = 'signal-cli')
    {
        // $command = $command . " > /path/to/output.txt 2>&1";
        $path = storage_path() . "/output.txt";
        $command = $command . " > " . $path . " 2>&1";
        $returnValue = null;
        exec($command, $output, $returnValue);
        if ($returnValue === 0) {
            // echo "Command ran successfully";
        } else {
            // echo "Command failed to run";
        }

        return file_get_contents($path);
    }
}
