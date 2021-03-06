<?php

class onMessagesQueueSendEmail extends cmsAction {

	public function run($attempt, $to, $letter, $is_nl2br_text = null){

        $mailer = new cmsMailer();

        $mailer->addTo($to['email'], $to['name']);

        if (!empty($to['email_reply_to'])){
            $mailer->setReplyTo($to['email_reply_to'], $to['name_reply_to']);
        }

        if (!empty($to['custom_headers'])){
            foreach ($to['custom_headers'] as $name => $value) {
                $mailer->addCustomHeader($name, $value);
            }
        }

        $letter['text'] = $mailer->parseSubject($letter['text']);
        $letter['text'] = $mailer->parseAttachments($letter['text']);

        $mailer->setBodyHTML( (!empty($is_nl2br_text) ? nl2br($letter['text']) : $letter['text']) );

        $result = $mailer->send();

        $mailer->clearTo()->clearAttachments();

        if(!$result && $attempt < cmsQueue::getMaxAttempts()){
            return false;
        }

        return $result ? true : $mailer->getErrorInfo();

    }

}
