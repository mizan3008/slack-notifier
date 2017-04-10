<?php

namespace HipchatNotifier;

class HipchatNotifier {

    // property declaration
    private $domain;
    private $room;
    private $token;
    private $version = 'v2';
    private $botName;
    private $url;

    public function __construct($domain = "", $room = "", $token = "", $botName = "") {

        if (empty($domain)) {
            $this->domain = !empty(env('HIPCHAT_DOMAIN')) ? env('HIPCHAT_DOMAIN') : "";
        }

        if (empty($room)) {
            $this->room = !empty(env('HIPCHAT_ROOM_ID')) ? env('HIPCHAT_ROOM_ID') : "";
        }

        if (empty($token)) {
            $this->token = !empty(env('HIPCHAT_TOKEN')) ? env('HIPCHAT_TOKEN') : "";
        }


        if (empty($this->domain) || empty($this->room) || empty($this->token)) {
            throw new \Exception("Invalid configuration");
        }

        if (empty($botName)) {
            $this->botName = !empty(env('HIPCHAT_BOT_NAME')) ? env('HIPCHAT_BOT_NAME') : "HIP-BOT";
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
