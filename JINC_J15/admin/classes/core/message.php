<?php

/**
 * @version		$Id: message.php 2010-01-19 12:01:47Z lhacky $
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
define('MESSAGE_BULK_MSG', 1);
define('MESSAGE_PERSONAL_MSG', 0);

require_once 'newsletterfactory.php';
require_once 'jincobject.php';
require_once 'sentmsgevent.php';

// Registering sending message event.
jincimport('statistics.sentevent');
$dispatcher = &JDispatcher::getInstance();
$dispatcher->register('jinc_sent', 'SentEvent');
$dispatcher->register('jinc_sent', 'SentMsgEvent');

/**
 * Message class, defining message properties and methods. This class define an
 * abstract method send() to implements to send a message.
 *
 * Hint: this class inherits from JObject in order to avoid getter and setter
 * redefinition and to use getError() and setError() methods.
 *
 * @package		JINC
 * @subpackage          Core
 * @since		0.6
 */
class Message extends JINCObject {

    /**
     * The message identifier
     *
     * @var		The message identifier
     * @access	protected
     * @since	0.6
     */
    var $id;
    /**
     * The message body
     *
     * @var	The message body
     * @access	protected
     * @since	0.6
     */
    var $body = '';
    /**
     * The message subject
     *
     * @var		the message subject
     * @access	protected
     * @since	0.6
     */
    var $subject = '';
    /**
     * Message mime type
     *
     * @var	true for text/plain or false for text/html
     * @access	protected
     * @since	0.6
     */
    var $plaintext = false;
    /**
     * The message attachment
     *
     * @var	The message attachment string
     * @access	protected
     * @since	0.6
     */
    var $attachment = '';
    /**
     * The newsletter identifier
     *
     * @var	The newsletter identifier the message belongs to
     * @access	protected
     * @since	0.6
     */
    var $news_id = 0;
    /**
     * The newsletter the message belongs to
     *
     * @var	The newsletter the message belongs to
     * @access	protected
     * @since	0.6
     */
    var $_newsletter = null;

    /**
     * The Message constructor. A message can be constructed directly using the
     * constructor or using the MessageFactory class.
     *
     * @access	public
     * @param   integer msg_id The message identifier.
     * @return	Message
     * @since	0.6
     * @see     MessageFactory
     */
    function Message($msg_id = 0) {
        parent::JObject();
        $this->attachment = new JParameter('');
        $this->set('id', $msg_id);
    }

    /**
     * The newsletter loader. It load newsletter the message belongs to from his
     * newsletter identifier.
     *
     * @access	public
     * @param	boolean $reload true to force newsletter info reloading
     * @return  The loaded newsletter
     * @since	0.6
     * @see     NewsletterFactory
     */
    function loadNewsletter($reload = false, $frontend = false) {
        if (is_null($this->_newsletter) || $reload) {
            $ninstance = NewsletterFactory::getInstance();
            $this->_newsletter = $ninstance->loadNewsletter($this->get('news_id'), $frontend);
        }
        return $this->_newsletter;
    }

    /**
     * Method to send the message to every subscribers. It is an abstract method.
     *
     * @access	public
     * @param	int $start_time subscription time to begin to
     * @param	int $start_id subscriber identifier to begin to
     * @return  array containing next suscription time, subscriber id and number of sent messages
     * @since	0.6
     * @abstract
     */
    function send($start_time = 0, $start_id = 0) {
        die('Message class: send() is an abstract method');
    }

    /**
     * Method to send the message preview.
     *
     * @access	public
     * @return  false if sometring wrong
     * @since	0.6
     * @abstract
     */
    function preview() {
        jincimport('utility.servicelocator');
        $servicelocator = ServiceLocator::getInstance();
        $logger = $servicelocator->getLogger();

        $user = & JFactory::getUser();
        if ($user->guest) {
            $this->setError('ERR024');
            return false;
        }

        $mailmsg = & JFactory::getMailer();

        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');
        $root_uri = JURI::root();

        jincimport('utility.parameterprovider');
        $send_mail = ParameterProvider::getSendMail();

        $result = array();
        if ($newsletter = $this->loadNewsletter()) {
            $logger->finer('Message: Newsletter loaded');
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
            $mailmsg->setSubject($this->get('subject'));
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
            $user_mail = $user->get('email');
            $userid = $user->get('username');
            $username = $user->get('name');
            $logger->finer('Message: Sending preview to ' . $user_mail);
            $mailmsg->addRecipient($user_mail);
            $current = array('usermail' => $user_mail, 'userid' => $userid, 'username' => $username);
            foreach ($current as $key => $value) {
                $msg = preg_replace('/\[' . strtoupper($key) . '\]/s', $value, $msg);
            }
            $mailmsg->setBody($msg);
            if ($send_mail) {
                if ($mailmsg->send())
                    array_push($result, $user_mail);
            } else {
                $logger->info('Message: simulate sending preview. Body = ' . $msg);
                array_push($result, $user_mail);
            }
        } else {
            $this->setError('ERR001');
            return false;
        }
        return $result;
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
        die('Message class: getType() is an abstract method');
    }
}

?>
