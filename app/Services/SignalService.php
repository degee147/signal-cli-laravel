<?php

namespace App\Services;

use App\Models\Message;
use Illuminate\Support\Facades\Http;


class SignalService
{
    public $phone;

    public function __construct()
    {
        $this->phone = env('SIGNAL_PHONE');
    }

    public function version()
    {
        return $this->exec("signal-cli version");
    }

    public function sendMessage($number, $message)
    {
        return $this->exec('signal-cli -a ' . $this->phone . ' send -m "' . $message . '" ' . $number);
    }

    public function receiveMessages()
    {

        $response = $this->exec("signal-cli -a " . $this->phone . " receive");

        $output = $response['output'];

        $lines = explode("\r\n", $output);

        if (!is_array($lines)) {
            // $response['messages'] = [];
            return $response;
        }
        $messages = [];

        $sender = null;
        $receiver = null;
        $receive_timestamp = null;
        $delivered_timestamp = null;
        $message_timestamp = null;
        $body = null;


        foreach ($lines as $line) {
            if (str_contains($line, "Envelope from: ")) {
                $sender = $this->get_string_between($line, "Envelope from:", "(");
                $receiver = $this->get_string_after($line, ") to");
            }
            if (str_contains($line, "Server timestamps:")) {
                $receive_timestamp = $this->get_string_between($line, "received:", "delivered:");
                $delivered_timestamp = $this->get_string_after($line, "delivered:");
            }
            if (str_contains($line, "Message timestamp:")) {
                $message_timestamp = $this->get_string_after($line, "Message timestamp:");
            }
            if (str_contains($line, "Body:")) {
                $body = $this->get_string_after($line, "Body:");
            }

            if (!empty($body) and !empty($sender) and !empty($receiver)) {

                $message = [
                    'sender' => $sender,
                    'receiver' => $receiver,
                    'receive_timestamp' => $receive_timestamp,
                    'delivered_timestamp' => $delivered_timestamp,
                    'message_timestamp' => $message_timestamp,
                    'body' => $body,
                ];
                Message::create($message);
                $messages[] = $message;

                $sender = null;
                $receiver = null;
                $receive_timestamp = null;
                $delivered_timestamp = null;
                $message_timestamp = null;
                $body = null;
            }
        }
        $response['output'] = $messages;
        return $response;
    }

    private function get_string_between($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0)
            return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return trim(substr($string, $ini, $len));
    }
    private function get_string_after($string, $needle)
    {
        $position = strpos($string, $needle);
        if ($position !== false) {
            $result = substr($string, $position + strlen($needle));
            return trim($result);
        }

        return "";
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
    public function updateProfileName($name)
    {
        // signal-cli -a +2348058582828 updateProfile --given-name "The Mace Name"
        return $this->exec("signal-cli -a " . $this->phone . ' updateProfile --given-name "' . $name . '"');
    }

    public function command($command)
    {
        //list of available commands
        //addDevice,block,daemon,deleteLocalAccountData,getAttachment,getUserStatus,joinGroup,jsonRpc,link,listAccounts,listContacts,listDevices,listGroups,listIdentities,listStickerPacks,quitGroup,receive,register,remoteDelete,removeContact,removeDevice,removePin,send,sendContacts,sendPaymentNotification,sendReaction,sendReceipt,sendSyncRequest,sendTyping,setPin,submitRateLimitChallenge,trust,unblock,unregister,updateAccount,updateConfiguration,updateContact,updateGroup,updateProfile,uploadStickerPack,verify,version
        return $this->exec("signal-cli -a " . $this->phone . " " . $command);
    }

    private function exec($command = 'signal-cli')
    {

        $os = PHP_OS;
        $prefix = "";

        if (strpos($os, 'Linux') !== false) {
            $prefix = "sudo ";
        } elseif (strpos($os, 'Windows') !== false) {
            // echo 'This is a Windows system.';
        } elseif (strpos($os, 'Darwin') !== false) {
            // echo 'This is a Mac system.';
        } else {
            echo 'This is an unknown operating system.';
        }

        $path = storage_path() . "/output.txt";
        $command = $command . " > " . $path . " 2>&1";
        $returnValue = null;
        exec($prefix . $command, $output, $returnValue);

        $response = [];
        if ($returnValue === 0) {
            // echo "Command ran successfully";
            $response['success'] = true;
        } else {
            $response['success'] = false;
            // echo "Command failed to run";
        }
        $response['output'] = file_get_contents($path);
        return $response;
    }
}
