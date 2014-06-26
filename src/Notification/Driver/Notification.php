<?php

namespace Notification\Driver;

use \Norm\Norm;

class Notification {

    public function notify($data, $options) {
        $app = \Bono\App::getInstance();

        $render = $app->theme->partial($data['body'][$data['type']]['template'], array(
            'data' => $data['body'][$data['type']]
        ));
        $data['body'][$data['type']]['content'] = $render;

        $model = Norm::factory('Contentnotification')->newInstance();
        $model->set($data['body'][$data['type']]);
        $model->save();
        $contentId = $model->getId();

        foreach ($data['recipients'] as $key => $value) {
            $value['content_id'] = $contentId;
            $value['type'] = $data['type'];

            switch ($data['action']) {
                case 1:
                    $sendResult = $this->sendMail($value, $data['body'][$data['type']], $options);
                    $value['status_notif'] = 1;
                    break;
                case 2:
                    $value['status_notif'] = 2;
                    break;
            }

            $model = Norm::factory('Notification')->newInstance();
            $model->set($value);
            $model->save();
        }
    }
}

























