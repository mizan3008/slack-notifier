<?php

namespace HipchatNotifier;

class HipchatNotifier {

    // property declaration
    private $domain;
    private $room;
    private $token;
    private $version = 'v2';
    private $botName = 'HIP-BOT';
    private $url;

    public function __construct() {

        if (empty(env('HIPCHAT_DOMAIN')) || empty(env('HIPCHAT_ROOM_ID')) || empty(env('HIPCHAT_TOKEN'))) {
            throw new Exception("Invalid configuration");
        }

        $this->domain = env('HIPCHAT_DOMAIN');
        $this->room = env('HIPCHAT_ROOM_ID');
        $this->token = env('HIPCHAT_TOKEN');
        if (!empty(env('HIPCHAT_BOT_NAME'))) {
            $this->botName = env('HIPCHAT_BOT_NAME');
        }
        
        //building url
        $this->url = $this->domain . "/" . $this->version . "/room/" . $this->room . "/notification?auth_token=" . $this->token;
    }

    public function notifyException($exception, $color = 'red') {

        if (!empty($exception->getMessage())) {

            $error = [
                'error' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine()
            ];

            $data = [
                'from' => env('HIPCHAT_BOT_NAME'),
                'color' => $color,
                'message' => json_encode($error),
                'notify' => true,
                'message_format' => 'html'
            ];

            $this->curl($data);
        }
    }

    public function notifyError($message, $color = 'red') {

        $data = [
            'from' => env('HIPCHAT_BOT_NAME'),
            'color' => $color,
            'message' => $message,
            'notify' => true,
            'message_format' => 'html'
        ];

        $this->curl($data);
    }

    public function notifyInfo($message, $color = 'green') {

        $data = [
            'from' => env('HIPCHAT_BOT_NAME'),
            'color' => $color,
            'message' => $message,
            'notify' => true,
            'message_format' => 'html'
        ];

        $this->curl($data);
    }

    private function curl($data) {
        $ch = curl_init();
        $headers = ['Content-type: application/json'];
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $result = curl_exec($ch); // Getting jSON result string
        if ($result === false) {
            echo '{"errors":{"message":"' . curl_error($ch) . '"}}';
        }
        curl_close($ch);
        return $result;
    }

}
