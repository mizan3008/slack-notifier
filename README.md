Hipchat Notifier
=============

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

Examples
--------

First we need setup some configuration variable in .env
```
HIPCHAT_DOMAIN=https://example.hipchat.com
HIPCHAT_TOKEN=your-room-token
HIPCHAT_ROOM_ID=your-room-id
HIPCHAT_BOT_NAME=your-bot-name (this one is optional, default bot name is HIP-BOT)
```

use this on top of your class:

```
use HipchatNotifier\HipchatNotifier;
```

// initialize hipchat notifier
```
$hipchatNotifier = new HipchatNotifier();
```

//send error notification to your hipchat room
```
$hipchatNotifier->notifyError('YOUR ERROR MESSAGE');
```

//send success or info notification to your hipchat room
```
$hipchatNotifier->notifyInfo('YOUR ERROR MESSAGE');
```

//send exception notification to your hipchat room
```
try{
    #Your code...
}catch(\Exception $ex){
    $hipchatNotifier->notifyException($ex);
}
```
