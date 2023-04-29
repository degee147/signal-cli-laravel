<?php

namespace App\Services;

use App\Models\Message;
use Illuminate\Support\Facades\Http;


class SignalService
{
    public $phone;

    public function __construct($phone = "")
    {
        $this->phone = $phone ?? env('SIGNAL_PHONE');
    }
    public function version()
    {
        // return $this->exec("signal-cli version");
        return ['success' => true, 'output' => "signal-cli 0.11.9.1"];
    }


    public function get_path($path_name)
    {
        // return storage_path() . "/bg/" . $path_name;
        return "/home/ubuntu/laravel/storage/bg/" . $path_name;
        // return "../../storage/bg/" . $path_name;
    }
    public function get_status_file_path($file_name)
    {
        return "/home/ubuntu/laravel/storage/bg/" . $file_name;
    }
    public function make_status_file($file_name)
    {
        $filepath = "/home/ubuntu/laravel/storage/bg/" . $file_name;
        $file = fopen($filepath, 'w');
        // / Write some text to the file
        fwrite($file, $file_name . " status..");
        // Close the file
        fclose($file);
        return $filepath;
    }
    public function make_path($path_name)
    {
        // $dirPath = storage_path() . "/bg/" . $path_name;
        $dirPath = "/home/ubuntu/laravel/storage/bg/" . $path_name;
        // $dirPath = "../../storage/bg/" . $path_name;

        // Check if the directory exists
        if (!file_exists($dirPath)) {
            // Create the directory
            mkdir($dirPath, 0777, true);
        }

        $save_path = $dirPath . "/output" . mt_rand(11111, 99999) . ".txt";

        // Open a file for reading and writing
        // $file = fopen($save_path, "w+");

        return $save_path;
    }



    public function queueMessageSend($number, $message)
    {
        $save_path = $this->make_path('send');
        $file = fopen($save_path, 'w');
        // / Write some text to the file
        fwrite($file, json_encode(['number' => $number, 'message' => $message]));
        // Close the file
        fclose($file);
        return ['success' => true, 'output' => "message sent to " . $number];
        // return ['success' => true, 'output' => "message queued for sending to " . $number];
    }


    public function sendMessage($number, $message, $save_path = "")
    {
        return $this->exec('signal-cli -a ' . $this->phone . ' send -m "' . $message . '" ' . $number, $save_path);
    }

    public function saveMessages($file_path)
    {
        $response = ["success" => true];
        $saveCount = 0;

        // Open the file for reading
        $file = fopen($file_path, "r");

        $sender = null;
        $sender_name = null;
        $receiver = null;
        $receive_timestamp = null;
        $delivered_timestamp = null;
        $message_timestamp = null;
        $body = null;

        // Loop through the file line by line
        while (!feof($file)) {
            $line = fgets($file);
            // Do something with the line, such as print it
            // echo $line;

            if (str_contains($line, "Envelope from: ")) {
                $sender = $this->get_string_between($line, "Envelope from:", "(");

                if (str_contains($line, "Timestamp")) {
                    $receiver = $this->get_string_between($line, ") to", "Timestamp");
                } else {
                    $receiver = $this->get_string_after($line, ") to");
                }
            }
            if (str_contains($line, "Server timestamps:")) {
                $receive_timestamp = $this->get_string_between($line, "received:", "delivered:");

                if (str_contains($line, "Sent by")) {
                    $delivered_timestamp = $this->get_string_between($line, "delivered:", "Sent by");
                } else {
                    $delivered_timestamp = $this->get_string_after($line, "delivered:");
                }

            }
            if (str_contains($line, "Message timestamp:")) {

                if (str_contains($line, "Body:")) {
                    $message_timestamp = $this->get_string_between($line, "Message timestamp:", "Body:");
                } else {
                    $message_timestamp = $this->get_string_after($line, "Message timestamp:");
                }

            }
            if (str_contains($line, "Body:")) {
                $body = $this->get_string_after($line, "Body:");
            }

            if (!empty($body) and !empty($sender) and !empty($receiver)) {

                $sender_name = $this->get_string_between($sender, '“', '”');
                $sender = trim(str_replace('“' . $sender_name . '”', '', $sender));

                $message = [
                    'sender' => $sender,
                    'sender_name' => $sender_name,
                    'receiver' => $receiver,
                    'receive_timestamp' => $receive_timestamp,
                    'delivered_timestamp' => $delivered_timestamp,
                    'message_timestamp' => $message_timestamp,
                    'body' => $body,
                ];
                Message::create($message);
                $saveCount++;
                // $messages[] = $message;

                $sender = null;
                $sender_name = null;
                $receiver = null;
                $receive_timestamp = null;
                $delivered_timestamp = null;
                $message_timestamp = null;
                $body = null;
            }
        }

        // Close the file
        fclose($file);
        $response['output'] = "saved " . $saveCount . " messages";
        $response['count'] = $saveCount;
        // $response['output'] = $messages;
        // $response['success'] = $messages;
        return $response;
    }

    public function receiveMessages($save_path)
    {
        $response = $this->exec("signal-cli -a " . $this->phone . " receive", $save_path);
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
        $str = trim(substr($string, $ini, $len));
        return str_replace("With profile key", "", $str);
    }
    private function get_string_after($string, $needle)
    {
        $position = strpos($string, $needle);
        if ($position !== false) {
            $result = substr($string, $position + strlen($needle));
            $str = trim($result);
            return str_replace("With profile key", "", $str);
        }

        return "";
    }


    public function verify($code)
    {
        return $this->exec("signal-cli -a " . $this->phone . " verify " . $code);

    }
    public function deleteReplied($phone)
    {
        return Message::where('sender', 'like', '%' . $phone)->where(['replied' => true])->delete();
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

    private function exec($command = 'signal-cli', $save_path = '')
    {

        // $path = __DIR__ . "/output.txt";
        if (!empty($save_path)) {
            $path = $save_path;
        } else {
            $path = $this->make_path('default');
        }
        $command = $command . " > " . $path . " 2>&1";
        $returnValue = null;
        exec($command, $output, $returnValue);

        $response = [];
        if ($returnValue === 0) {
            //  "Command ran successfully";
            $response['success'] = true;
        } else {
            $response['success'] = false;
            //  "Command failed to run";
        }

        try {
            if (file_exists($path)) {
                $response['output'] = file_get_contents($path);
            }
        } catch (\Exception $e) {
            $response['output'] = $e->getMessage();
        }
        return $response;
    }


}
