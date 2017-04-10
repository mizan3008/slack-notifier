Hipchat Notifier
================

Super-simple, minimum abstraction HipchatNotifier v2, in PHP.

Requires PHP 5.3 and a pulse.

Installation
------------

You can install the hipchat-notifier using Composer:

```
composer require mizanur/hipchat-notifier
```
or
```
composer require mizanur/hipchat-notifier
```

HOW TO USER
-----------

You can start with .env or without .env

With .env

We need setup some configuration variable in .env
```
HIPCHAT_DOMAIN=https://example.hipchat.com
HIPCHAT_TOKEN=your-room-token
HIPCHAT_ROOM_ID=your-room-id
HIPCHAT_BOT_NAME=your-bot-name (this one is optional, default bot name is HIP-BOT)
```

Initialize hipchat notifier
```
$hipchatNotifier = new \HipchatNotifier\HipchatNotifier();
```

Without .env

Initialize hipchat notifier
```
//the last param bot-name is optional, default bot name is HIP-BOT
$hipchatNotifier = new \HipchatNotifier\HipchatNotifier('YOUR-DOMAIN','YOUR-ROOM-ID','YOUR-ROOM-TOKEN', 'BOT-NAME');
```

//send error notification to your hipchat room
```
//you can pass your color as second param (red or green or yellow) default is red
$hipchatNotifier->notifyError('YOUR ERROR MESSAGE');

//or

$hipchatNotifier->notifyError('YOUR ERROR MESSAGE', 'green');
```

//send success or info notification to your hipchat room
```
//you can pass your color as second param (red or green or yellow) default is green
$hipchatNotifier->notifyInfo('YOUR MESSAGE');

//or

$hipchatNotifier->notifyInfo('YOUR MESSAGE', 'red');
```

//send exception notification to your hipchat room
```
//you can pass your color as second param (red or green or yellow) default is red
try{
    #Your code...
}catch(\Exception $ex){
    $hipchatNotifier->notifyException($ex);
}

//or

try{
    #Your code...
}catch(\Exception $ex){
    $hipchatNotifier->notifyException($ex, 'yellow');
}
```
