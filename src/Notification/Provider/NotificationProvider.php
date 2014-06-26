<?php

namespace Notification\Provider;

use \Bono\Provider\Provider;
use \Notification\Driver\EmailNotification as Email;
use \Notification\Driver\SystemNotification as System;

class NotificationProvider extends Provider {

    public function __construct($options = null){
        parent::__construct($options);
    }

    public function initialize(){
        $this->email = New Email;
        $this->system = New System;

        $this->drivers = array(
            1 => 'system',
            2 => 'email'
        );

        $that = $this;
        $this->app->hook(
            'bono.notification',
            function($notificationData) use ($that){
                $that->notify($notificationData);
            }
        );
    }

    public function notify($notificationData){
        foreach ($this->drivers as $key => $driver) {
            if(in_array($key,array_keys($notificationData['body']))){
                $notificationData['type'] = $key;
                $this->$driver->notify($notificationData, $this->options);
            }
        }
    }
}
