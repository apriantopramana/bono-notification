<?php

namespace Notification\Driver;

use \Monolog\Logger;
use \Monolog\Handler\StreamHandler;

class EmailNotification extends Notification{

    public function sendMail($recipients, $contentNotification, $emailConfig){
        $app = \Bono\App::getInstance();
        $log = new Logger('EMAIL');
        $fileLog = dirname(dirname(dirname(__DIR__))).'/email.log';

        $transport = \Swift_SmtpTransport::newInstance($emailConfig['notification.mail']['mail.transport.host'], $emailConfig['notification.mail']['mail.transport.port'], $emailConfig['notification.mail']['mail.transport.ssl'])
            ->setUsername($emailConfig['notification.mail']['mail.transport.username'])
            ->setPassword($emailConfig['notification.mail']['mail.transport.password']);
        $mailer = \Swift_Mailer::newInstance($transport);

        try {
            $message = \Swift_Message::newInstance($contentNotification['title'])
              ->setFrom($emailConfig['notification.mail']['mail.message.from']) // This email is not working because Gmail disallows overriding the FROM name except from verfied email addresses that you prove to gmail your own
              ->setTo($recipients['email'])
              ->setBody($contentNotification['content'])
              ->setContentType("text/html");

              if ($numSent = $mailer->send($message)){
                    $msg = "Success sent email to ".$recipients['email']." | Title: ".$contentNotification['title']. " |";
                    $log->pushHandler(new StreamHandler($fileLog, Logger::INFO));
                    $log->addInfo($msg, $recipients, $contentNotification);
                } else {
                    $msg = "Failed sent email to ".$recipients['email']." | Title: ".$contentNotification['title']. " |";
                    $log->pushHandler(new StreamHandler($fileLog, Logger::WARNING));
                    $log->addWarning($msg, $recipients, $contentNotification);
                }
        } catch (\Exception $e) {
            $log->pushHandler(new StreamHandler($fileLog, Logger::ERROR));
            $log->addError('ERROR', $e);
        }
    }

}
