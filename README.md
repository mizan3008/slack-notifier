Slack Notifier
================

Super-simple, minimum abstraction SlackNotifier v1, in PHP.

Requires PHP 5.3 and a pulse.

Installation
------------

You can install the slack-notifier using Composer:

```
composer require mizanur/slack-notifier
```
or
```
composer require mizanur/slack-notifier
```

HOW TO USER
-----------

You can start with .env or without .env

With .env

We need setup some configuration variable in .env
```
SLACK-WEBHOOK=https://hooks.slack.com/services/---/---/---
```

Initialize slack notifier
```
$slackNotifier = new \SlackNotifier\SlackNotifier();
```

Without .env

Initialize slack notifier
```

$slackNotifier = new \SlackNotifier\SlackNotifier('SLACK-WEBHOOK');
```

Send error notification to your slack channel
```
$slackNotifier->notifyError('YOUR ERROR MESSAGE');
```

Send success or info notification to your slack channel
```
$slackNotifier->notifyInfo('YOUR MESSAGE');
```

Send exception notification to your slack channel
```

try{
    #Your code...
}catch(\Exception $ex){
    $slackNotifier->notifyException($ex);
}
```
