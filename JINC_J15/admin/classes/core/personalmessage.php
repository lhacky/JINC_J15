<?php

/**
 * @version		$Id: personalmessage.php 2010-01-19 12:01:47Z lhacky $
 * @package		JINC
 * @subpackage          Core
 * @copyright           Copyright (C) 2010 - Lhacky
 * @license		GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html
 *   This file is part of JINC.
 *
 *   JINC is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   JINC is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with JINC.  If not, see <http://www.gnu.org/licenses/>.
 */
/**
 * Requiring PHP libraries and defining constants
 */
include_once 'message.php';
require_once 'newsletterfactory.php';

/**
 * PersonalMessage class, defining personal message implementation.
 * It will send a single message to every subscribers.
 *
 * @package		JINC
 * @subpackage          Core
 * @since		0.6
 */
class PersonalMessage extends Message {

    /**
     * The PersonalMessage constructor
     *
     * @access	public
     * @param       integer msg_id The message identifier.
     * @return	PersonalMessage
     * @see         Message, MessageFactory
     * @since	0.6
     */
    function PersonalMessage($msg_id = 0) {
        parent::Message($msg_id);
    }

    /**
     * Send a personal message to every subscribers. It overrides the abstract
     * method defined in Message class.
     *
     * Hint: it starts to send messages from the the $start-th subscribers of
     * the message
     *
     * @access	public
     * @param	int $start_time subscription time to begin to
     * @param	int $start_id subscriber identifier to begin to
     * @return  array containing next suscription time, subscriber id and number of sent messages
     * @since	0.6
     */
    function send($start_time = 0, $start_id = 0) {
        jincimport('utility.servicelocator');
        $servicelocator = ServiceLocator::getInstance();
        $logger = $servicelocator->getLogger();

        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');
        jincimport('utility.parameterprovider');

        $mailmsg = & JFactory::getMailer();
        $root_uri = JURI::root();

        $sleeptime = ParameterProvider::getMailTimeInterval();
        $max_mails = ParameterProvider::getMaxXStep();
        $send_mail = ParameterProvider::getSendMail();
        $logger->finer('PersonalMessage: Sleep time ' . $sleeptime . ' - Max mails ' . $max_mails . ' Send mail ' . $send_mail);

        $last_time = $start_time;
        $last_id = $start_id;
        $nmessages = 0;
        $nsuccess = 0;

        if ($newsletter = $this->loadNewsletter()) {
            $logger->finer('PersonalMessage: Newsletter loaded');
            $msg = $this->get('body') . $newsletter->get('disclaimer');
            // Newsletter info substitution in message body
            $msg = preg_replace('/\[SENDER\]/s', $newsletter->get('sendername'), $msg);
            $msg = preg_replace('/\[SENDERMAIL\]/s', $newsletter->get('senderaddr'), $msg);
            $msg = preg_replace('/\[NEWSLETTER\]/s', $newsletter->get('name'), $msg);
            $news_id = $newsletter->get('id');
            $unsub_link = JURI::root() . 'index.php?option=com_jinc&view=newsletter&layout=unsubscription&news_id=' . $news_id;
            $msg = preg_replace('/\[UNSUBSCRIPTIONURL\]/s', $unsub_link, $msg);

            $msg = preg_replace('#src[ ]*=[ ]*\"(?!https?://)(?:\.\./|\./|/)?#', 'src="' . $root_uri, $msg);
            $msg = preg_replace('#href[ ]*=[ ]*\"(?!https?://)(?!mailto?:)(?:\.\./|\./|/)?#', 'href="' . $root_uri, $msg);
            // Setting message general properties
            $mailmsg->ContentType = ($this->get('plaintext')) ? "text/plain" : "text/html";
            $subject = $this->get('subject');
            $subject = preg_replace('/\[SENDER\]/s', $newsletter->get('sendername'), $subject);
            $subject = preg_replace('/\[SENDERMAIL\]/s', $newsletter->get('senderaddr'), $subject);
            $subject = preg_replace('/\[NEWSLETTER\]/s', $newsletter->get('name'), $subject);

            if (strlen($newsletter->get('senderaddr')) > 0)
                $mailmsg->setSender(array($newsletter->get('senderaddr'), $newsletter->get('sendername')));
            if (strlen($newsletter->get('replyto_addr')) > 0)
                $mailmsg->addReplyTo(array($newsletter->get('replyto_addr'), $newsletter->get('replyto_name')));

            $msg_attachment = $this->get('attachment');
            $path_abs_root = JPATH_ROOT . DS . 'images';
            for ($i = 0; $i < count($msg_attachment->toArray()); $i++) {
                $attachment = str_replace('/', DS, $msg_attachment->get($i));
                if (strlen($attachment))
                    $mailmsg->addAttachment($path_abs_root . DS . $attachment);
            }

            $logger->finer('PersonalMessage: going to add recipients');
            $recipients = $newsletter->getSubscribersList($start_time, $start_id);
            $nrecips = count($recipients);
            $logger->finer('PersonalMessage: found ' . $nrecips . ' subscribers');
            for ($i = 0; $i < $nrecips; $i++) {
                $current = $recipients[$i];
                $subj = $subject;
                $body = $msg;
                foreach ($current as $key => $value) {
                    $subj = preg_replace('/\[' . strtoupper($key) . '\]/s', $value, $subj);
                    $body = preg_replace('/\[' . strtoupper($key) . '\]/s', $value, $body);
                }
                $last_time = $current['last_time'];
                $last_id = $current['last_id'];

                $mailmsg->addRecipient($current['usermail']);

                $mailmsg->setSubject($subj);
                $mailmsg->setBody($body);
                $nmessages = $nmessages + 1;
                if ($send_mail) {
                    $nsuccess = $nsuccess + $mailmsg->send();
                } else {
                    $logger->info('PersonalMessage: simulate sending mail. BCC = ' . $current['usermail']);
                    $logger->info('PersonalMessage: simulate sending mail. Body = ' . $body);
                    $nsuccess = $nsuccess + 1;
                }
                // socket_select($read = NULL, $write = NULL, $sock = array(socket_create (AF_INET, SOCK_RAW, 0)), 0, $sleeptime);
                usleep($sleeptime);
                $mailmsg->ClearAllRecipients();
            }
        } else {
            $this->setError('ERR001');
            return false;
        }

        /*
          if ($nrecips == 0) {
          $this->setError('ERR003');
          return false;
          }

          if ($start == 0) {
          // Triggering events after message sending
          $logger->finer('PersonalMessage: triggering sent event');
          $dispatcher = &JDispatcher::getInstance();
          $params = array('news_id' => $this->get('news_id'),
          'msg_id' => $this->get('id'));
          $result = $dispatcher->trigger('jinc_sent', $params);
          }
         */
        return array('last_time' => $last_time, 'last_id' => $last_id, 'nmessages' => $nmessages, 'nsuccess' => $nsuccess);
    }

    /**
     * Newsletter type getter.
     *
     * @access	public
     * @return  Newsletter type identifier
     * @since	0.8
     * @abstract
     */
    function getType() {
        return MESSAGE_PERSONAL_MSG;
    }

}

?>