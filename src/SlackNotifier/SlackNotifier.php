<?php

namespace SlackNotifier;

use Auth;

class SlackNotifier {

    // property declaration
    private $slackWebhook;
    private $appName;
    private $appEnv;
    private $protocol;
    private $color;

    public function __construct($slackWebhook = "") {

        if (empty($slackWebhook)) {
            $this->slackWebhook = !empty(env('SLACK_WEBHOOK')) ? env('SLACK_WEBHOOK') : "";
        } else {
            $this->slackWebhook = $slackWebhook;
        }

        if (empty($this->slackWebhook)) {
            throw new \Exception("Webhook url is required to send notification to slack");
        }

        $this->appEnv = !empty(env('APP_ENV')) ? strtoupper(env('APP_ENV')) : 'APP ENV NOT DEFINED';
        $this->appName = !empty(env('APP_NAME')) ? env('APP_NAME') : 'APP NAME NOT DEFINED';

        if (empty($_SERVER["HTTPS"]) || $_SERVER['HTTPS'] == 'off' || filter_var($_SERVER['HTTP_HOST'], FILTER_VALIDATE_IP)) {
            $this->protocol = "http://";
        } else {
            $this->protocol = "https://";
        }

        $this->color = $this->appEnv == 'PRODUCTION' ? '#D00000' : '#FD9149';

        $this->actionButton = env('SLACK_ACTION_BUTTON', false);
    }

    public function notifyException($exception) {

        if (!empty($exception->getMessage())) {

            $data = [
                'attachments' => [
                    [
                        'attachment_type' => 'default',
                        'fallback' => $this->appName . ' - Error',
                        'pretext' => '*Event* in `' . $this->appEnv . '` from `' . $this->appName . '`',
                        'mrkdwn_in' => ['text', 'pretext'],
                        'color' => $this->color,
                        'callback_id' => time(),
                        'fields' => [
                            [
                                'title' => 'Error',
                                'value' => $exception->getMessage()
                            ],
                            [
                                'title' => 'Location',
                                'value' => $exception->getFile() . ':' . $exception->getLine()
                            ],
                            [
                                'title' => 'Requested Url',
                                'value' => $this->protocol . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]
                            ]
                        ]
                    ]
                ]
            ];

            //if user loggedin, appending user payload
            if (Auth::check()) {

                $user = Auth::user();

                $userData = [
                    'id' => $user->id,
                    'username' => !empty($user->username) ? $user->username : '',
                    'email' => !empty($user->email) ? $user->email : '',
                    'role' => !empty($user->role->title) ? $user->role->title : ''
                ];

                $data['attachments'][0]['fields'][3] = [
                    'title' => 'User Payload',
                    'value' => json_encode($userData)
                ];
            }

            //controling action button
            if ($this->actionButton == true) {
                $data['attachments'][0]['actions'] = [
                    [
                        "name" => "fixed",
                        "text" => "Mark as fixed",
                        "type" => "button",
                        "value" => "fixed"
                    ],
                    [
                        "name" => "ignore",
                        "text" => "Ignore",
                        "type" => "button",
                        "value" => "ignore"
                    ]
                ];
            }

            $this->curl($data);
        }
    }

    public function notifyError($message, $pretext = true) {

        $data = [
            'attachments' => [
                [
                    'pretext' => '*Event* in `' . $this->appEnv . '` from `' . $this->appName . '`',
                    'text' => $message,
                    'mrkdwn_in' => ['text', 'pretext'],
                    'color' => '#FD9149',
                ]
            ]
        ];

        if ($pretext) {
            $data['attachments'][0]['pretext'] = '*Event* in `' . $this->appEnv . '` from `' . $this->appName . '`';
        }

        $this->curl($data);
    }

    public function notifyInfo($message, $pretext = true) {

        $data = [
            'attachments' => [
                [
                    'text' => $message,
                    'mrkdwn_in' => ['text', 'pretext'],
                    'color' => '#36A64F',
                ]
            ]
        ];

        if ($pretext) {
            $data['attachments'][0]['pretext'] = '*Event* in `' . $this->appEnv . '` from `' . $this->appName . '`';
        }

        $this->curl($data);
    }

    private function curl($data) {
        $ch = curl_init();
        $headers = ['Content-type: application/json'];
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $this->slackWebhook);
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
