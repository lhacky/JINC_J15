<?php

/**
 * @version		$Id: newsletter.php 2010-01-19 12:01:47Z lhacky $
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
require_once 'jincobject.php';
require_once 'acl.php';
require_once 'attribute.php';
require_once 'subscriptionnotifyevent.php';
//require_once 'newsletterfactory.php';
// Registering subscription and unsubscription event.
jincimport('statistics.subscriptionevent');
jincimport('statistics.unsubscriptionevent');
$dispatcher = &JDispatcher::getInstance();
$dispatcher->register('jinc_subscribe', 'SubscriptionEvent');
$dispatcher->register('jinc_unsubscribe', 'UnsubscriptionEvent');
$dispatcher->register('jinc_unsubscribe', 'SubscriptionNotifyEvent');

define('NEWSLETTER_PRIVATE_NEWS', 2);
define('NEWSLETTER_PROTECTED_NEWS', 1);
define('NEWSLETTER_PUBLIC_NEWS', 0);

define('NEWSLETTER_DEFAULT_THEME', 'blue.css');

define('ATTRIBUTE_NONE', 0);
define('ATTRIBUTE_MANDATORY', 1);
define('ATTRIBUTE_OPTIONAL', 2);

define('NEWSLETTER_FRONT_TYPE_ONLY_TITLE', 0);
define('NEWSLETTER_FRONT_TYPE_CLICKABLE_TITLE', 1);
define('NEWSLETTER_FRONT_TYPE_ENTIRE_MESSAGE', 2);

define('NEWSLETTER_INPUT_STYLE_INHERITED', 0);
define('NEWSLETTER_INPUT_STYLE_STANDARD', 1);
define('NEWSLETTER_INPUT_STYLE_MINIMAL', 2);

define('CAPTCHA_NO', 0);
define('CAPTCHA_REQUIRED', 1);
define('CAPTCHA_SOUND', 2);

/**
 * Newsletter class, defining newsletter properties and methods. This class
 * define methods getSubscribersList() and countSubscribers() implemented as
 * a strategy design pattern.
 *
 * Hint: this class inherits from JObject in order to avoid getter and setter
 * redefinition and to use getError() and setError() methods.
 *
 * @package		JINC
 * @subpackage          Core
 * @since		0.6
 */
class Newsletter extends JINCObject {

    /**
     * The newsletter identifier
     *
     * @var		The newsletter identifier
     * @access	protected
     * @since	0.6
     */
    var $id;
    /**
     * The newsletter name
     *
     * @var		The newsletter name
     * @access	protected
     * @since	0.6
     */
    var $name = '';
    /**
     * The newsletter description
     *
     * @var		The newsletter description
     * @access	protected
     * @since	0.6
     */
    var $description = '';
    /**
     * The newsletter sender name
     *
     * @var		The newsletter sender name
     * @access	protected
     * @since	0.6
     */
    var $sendername = '';
    /**
     * The newsletter sender address
     *
     * @var		The newsletter sender address
     * @access	protected
     * @since	0.6
     */
    var $senderaddr = '';
    /**
     * The newsletter reply-to name
     *
     * @var		The newsletter reply-to name
     * @access	protected
     * @since	0.8
     */
    var $replyto_name = '';
    /**
     * The newsletter reply-to address
     *
     * @var		The newsletter reply-to address
     * @access	protected
     * @since	0.8
     */
    var $replyto_addr = '';
    /**
     * The newsletter notify flag. If set to true a notity mail will be sent
     * to Joomla! administrator every subscription
     *
     * @var		The newsletter notify flag
     * @access	protected
     * @since	0.8
     */
    var $notify = false;
    /**
     *
     * The newsletter disclaimer
     *
     * @var		The newsletter disclaimer
     * @access	protected
     * @since	0.6
     */
    var $disclaimer = '';
    /**
     * The newsletter default template identifier
     *
     * @var	The newsletter default template identifier
     * @access	protected
     * @since	0.6
     */
    var $default_template = 0;
    /**
     * True if user must be subscribed at registration time.
     *
     * @var	On subscription registration
     * @access	protected
     * @see     NewsletterFactory
     * @since	0.6
     */
    var $on_subscription = 0;
    /**
     * True if Joomla! Contats Integration is enabled.
     *
     * @var	JContant Integration Enabled
     * @access	protected
     * @see     NewsletterFactory
     * @since	0.7
     */
    var $jcontact_enabled = 0;
    /**
     * Captcha requirment to subscribe the newsletter
     * 0 -> No captcha required
     * 1 -> Captcha required
     * 2 -> Captcha with reading sound (only english) required
     *
     * @var	Captcha requirement
     * @access	protected
     * @see     NewsletterFactory
     * @since	0.8
     */
    var $captcha = 0;
    /**
     * Newsletter frontend theme.
     *
     * @var	String Frontend theme
     * @access	protected
     * @since	0.7
     */
    var $front_theme = NEWSLETTER_DEFAULT_THEME;
    /**
     * Max number of sent messages to show in frontend.
     *
     * @var	integer
     * @access	protected
     * @see     NewsletterFactory
     * @since	0.7
     */
    var $front_max_msg = 0;
    /**
     * Frontend presentation type for the messages list for the newsletter.
     *
     * @var	integer
     * @access	protected
     * @see     NewsletterFactory
     * @since	0.7
     */
    var $front_type = NEWSLETTER_FRONT_TYPE_ONLY_TITLE;
    /**
     * The newsletter retriever used to load information about subscribers and
     * built based on the newsletter type by NewsletterFactory
     *
     * @var		The newsletter retriever
     * @access	protected
     * @see     NewsletterFactory
     * @since	0.6
     */
    var $subs_retriever;
    /**
     * The newsletter Access Controlo List
     *
     * @var		The ACL
     * @access	protected
     * @see     ACL
     * @since	0.6
     */
    var $acl = null;
    /**
     * Array of addictional attributes for the newsletter.
     * An addictional parameter can be defined by an administrator and can be
     * optional or mandatory
     *
     * @var	The array of addictiona paramters
     * @access	protected
     * @since	0.7
     */
    var $attributes;
    /**
     * The newsletter notice identifier. The ID related to the notice a
     * user must accept to subscribe the newsletter
     *
     * @var	The newsletter notice identifier
     * @access	protected
     * @since	0.9
     */
    var $noti_id = 0;
    /**
     * Input style related to the newsletter.
     *
     * @var	The notice ID
     * @access	protected
     * @since	0.9
     */
    var $input_style;

    /**
     * The Newsletter constructor. A newsletter can be constructed using the
     * constructor or using the NewsletterFactory class.
     *
     * @access	public
     * @param   integer news_id The newsletter identifier.
     * @param   SubsRetriever $subs_retriever The subscribers info retriever
     * @return	Newsletter
     * @since	0.6
     * @see     NewsletterFactory
     */
    function Newsletter($news_id, $subs_retriever = null) {
        parent::JObject();
        $this->set('id', $news_id);
        $this->_subs_retriever = $subs_retriever;
        $this->attributes = new JParameter('');
    }

    /**
     * Method to send welcome message to the subscriber.
     *
     * @access	public
     * @param	boolean $user_info array of user info for tag substitution
     * @return  The number of the next subscriber to send message
     * @since	0.7
     */
    function sendWelcome($user_info) {
        jincimport('utility.servicelocator');
        $servicelocator = ServiceLocator::getInstance();
        $logger = $servicelocator->getLogger();

        $msg = $this->get('welcome');
        $usermail = '';

        $subject = $this->get('welcome_subject');
        $subject = preg_replace('/\[SENDER\]/s', $this->get('sendername'), $subject);
        $subject = preg_replace('/\[SENDERMAIL\]/s', $this->get('senderaddr'), $subject);
        $subject = preg_replace('/\[NEWSLETTER\]/s', $this->get('name'), $subject);

        if (array_key_exists('email', $user_info)) {
            $usermail = $user_info['email'];
            $subject = preg_replace('/\[USERMAIL\]/s', $usermail, $subject);
            $msg = preg_replace('/\[USERMAIL\]/s', $usermail, $msg);
        }

        if (array_key_exists('username', $user_info)) {
            $subject = preg_replace('/\[USERID\]/s', $user_info['username'], $subject);
            $msg = preg_replace('/\[USERID\]/s', $user_info['username'], $msg);
        }
        if (array_key_exists('name', $user_info)) {
            $subject = preg_replace('/\[USERNAME\]/s', $user_info['name'], $subject);
            $msg = preg_replace('/\[USERNAME\]/s', $user_info['name'], $msg);
        }
        foreach ($user_info as $key => $value) {
            $subject = preg_replace('/\[' . strtoupper($key) . '\]/s', $value, $subject);
            $msg = preg_replace('/\[' . strtoupper($key) . '\]/s', $value, $msg);
        }

        $msg = preg_replace('/\[USERMAIL\]/s', $usermail, $msg);
        $msg = preg_replace('/\[SENDER\]/s', $this->get('sendername'), $msg);
        $msg = preg_replace('/\[SENDERMAIL\]/s', $this->get('senderaddr'), $msg);
        $msg = preg_replace('/\[NEWSLETTER\]/s', $this->get('name'), $msg);
        $news_id = $this->get('id');
        $unsub_link = JURI::root() . 'index.php?option=com_jinc&view=newsletter&layout=unsubscription&news_id=' . $news_id;
        $msg = preg_replace('/\[UNSUBSCRIPTIONURL\]/s', $unsub_link, $msg);

        $message = & JFactory::getMailer();
        $message->ContentType = "text/html";
        $message->setSubject($subject);

        $message->setBody($msg);
        if (strlen($this->get('senderaddr')) > 0)
            $message->setSender(array($this->get('senderaddr'), $this->get('sendername')));
        if (strlen($this->get('replyto_addr')) > 0)
            $message->addReplyTo(array($this->get('replyto_addr'), $this->get('replyto_name')));

        $message->addRecipient($usermail);
        $logger->finer('Newsletter: Sending mail to ' . $usermail . ' with body ' . $msg);

        jincimport('utility.parameterprovider');
        $send_mail = ParameterProvider::getSendMail();
        if ($send_mail) {
            if (!$message->send()) {
                $this->setError('ERR010');
                return false;
            }
        } else {
            $logger->info('Newsletter: simulate sending mail. Body = ' . $msg);
        }
    }

    /**
     * Method to obtain subscribes list implemented as strategy design pattern.
     *
     * Hint: it starts to retrieve from the $start-th subscribers and returns
     * $multiplexer * $max_mails results, where $max_mails is the max number
     * of mails to send every step of a sending process
     *
     * @access	public
     * @param	boolean $start the number of subscriber to start to send message
     * @return  The number of the next subscriber to send message
     * @since	0.6
     * @see     SubsRetriever
     */
    function getSubscribersList($start_time = 0, $start_id = 0, $multiplexer = 1) {
        $retriever = $this->_subs_retriever;
        $attributes = $this->attributes;
        return $retriever->getSubscribersList($start_time, $start_id, $multiplexer, $attributes->toArray());
    }

    /**
     * Method to obtain single subscriber information.
     *
     * @access	public
     * @param	integer $id Subscriber identifier
     * @return  false if something wrong or subscriber not found
     * @since	0.9
     * @see     SubsRetriever
     */
    function getSubscriber($id = 0) {
        $retriever = $this->_subs_retriever;
        $attributes = $this->attributes;
        return $retriever->getSubscriber($id, $attributes->toArray());
    }

    /**
     * Method to obtain total number of subscribers implemented as strategy
     * design pattern.
     *
     * @access	public
     * @return  The total number of newsletters subscribers.
     * @since	0.6
     * @see     SubsRetriever
     */
    function countSubscribers() {
        $retriever = $this->_subs_retriever;
        return $retriever->countSubscribers();
    }

    /**
     * Newsletter type getter. This method is abstract.
     *
     * @access	public
     * @return  Newsletter type.
     * @since	0.6
     * @abstract
     */
    function getType() {
        die('Newsletter class: getType() is an abstract method');
    }

    /**
     * Method to know if a user is already subscribed to the newsletter.
     * This method is abstract.
     *
     * @access	public
     * @param   array $subscriber_info Subscriber info based on newsletter type.*
     * @return  true if can subscribe. false if not. -1 if something wrong.
     * @since	0.6
     * @abstract
     */
    function isSubscribed($subscriber_info) {
        die('Newsletter class: isSubscribed() is an abstract method');
    }

    /**
     * Method to insert attributes during subscription.
     *
     * @access	private
     * @param   integer $sub_id subscription identifier
     * @param   array $attributes list of name/value pair for attributes values
     * @since	0.7
     * @abstract
     */
    function insertAttributeOnSubscription($sub_id, $attributes) {
        jincimport('utility.servicelocator');
        $servicelocator = ServiceLocator::getInstance();
        $logger = $servicelocator->getLogger();
        $dbo = & JFactory::getDBO();
        $news_id = $this->get('id');
        foreach ($attributes as $attr_name => $attr_value) {
            $attr_name_db = $dbo->getEscaped($attr_name);
            $attr_value_db = $dbo->quote($dbo->getEscaped($attr_value), false);
            $query = 'INSERT IGNORE INTO `#__jinc_attribute_' . $attr_name_db . '` ' .
                    '(news_id, id, value) ' .
                    'VALUES (' . (int) $news_id . ', ' . (int) $sub_id . ', ' . $attr_value_db . ')';
            $logger->debug('PublicNewsletter: Executing query: ' . $query);
            $dbo->setQuery($query);
            if (!$dbo->query()) {
                $this->setError('ERR047');
                $logger->warning($this->getError());
            }
        }
    }

    /**
     * Method to subscribe a user to the newsletter. This method is abstract.
     *
     * @access	public
     * @param   array $subscriber_info Subscriber info based on newsletter type.
     * @param   array $attributes Array of addictional attributes for subscription
     * @return  true if successfully subscribed. false if something wrong.
     * @since	0.6
     * @abstract
     */
    function subscribe($subscriber_info, $attributes) {
        die('Newsletter class: subscribe() is an abstract method');
    }

    /**
     * Method to remove attributes after unsubscription.
     *
     * @access	private
     * @param   integer $sub_id subscription identifier
     * @since	0.7
     * @abstract
     */
    function removeAttributeOnUnsubscription($sub_id) {
        jincimport('utility.servicelocator');
        $servicelocator = ServiceLocator::getInstance();
        $logger = $servicelocator->getLogger();
        $dbo = & JFactory::getDBO();
        $news_id = $this->get('id');

        $ninstance = NewsletterFactory::getInstance();
        $attributes = $ninstance->loadAttributesList();
        foreach ($attributes as $attribute) {
            $attr_name_db = $dbo->getEscaped($attribute['name']);
            $query = 'DELETE FROM `#__jinc_attribute_' . $attr_name_db . '` ' .
                    'WHERE news_id = ' . (int) $news_id . ' AND id = ' . (int) $sub_id;
            $logger->debug('PublicNewsletter: Executing query: ' . $query);
            $dbo->setQuery($query);
            if (!$dbo->query()) {
                $this->setError('ERR048');
                $logger->warning($this->getError());
            }
        }
    }

    /**
     * Check if every mandatory attribute are avaivalable for subscription purpose.
     *
     * @access	protected
     * @param   integer $attributes list of attribute values
     * @since	0.7
     * @abstract
     */
    function checkMandatoryAttributes($attributes) {
        jincimport('utility.servicelocator');
        $servicelocator = ServiceLocator::getInstance();
        $logger = $servicelocator->getLogger();

        $news_attributes = $this->attributes;
        foreach ($news_attributes->toArray() as $attr_name => $attr_cardinality) {
            if ($attr_cardinality == ATTRIBUTE_MANDATORY) {
                if ((!in_array($attr_name, array_keys($attributes))) || (strlen($attributes[$attr_name]) == 0)) {
                    $logger->finer("Newsletter. Mandatory attribute not defined: " . $attr_name);
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Method to unsubscribe a user to the newsletter. This method is abstract.
     *
     * @access	public
     * @param   array $subscriber_info Subscriber info based on newsletter type.
     * @return  true if successfully unsubscribed. false if something wrong.
     * @since	0.6
     * @abstract
     */
    function unsubscribe($subscriber_info) {
        die('Newsletter class: unsubscribe() is an abstract method');
    }

    /**
     * Returns information needed to subscribe a user to this newsletter.
     *
     * @access	public
     * @return  array() of fields necessary to subscribe a user to the newsletter
     * @since	0.6
     * @abstract
     */
    function getSubscriptionInfo() {
        die('Newsletter class: getSubscriptionInfo() is an abstract method');
    }

    /**
     * Gets TAGS to substitute in subscriber info for this newsletter. Abstract.
     *
     * @access	public
     * @param boolean only_newsletter return only newsletter related tags
     * @param boolean only_retriever  return only retriever related tags
     * @return	array Array of tags.
     * @since	0.6
     * @abstract
     */
    function getTagsList($only_newsletter = false, $only_retriever = false) {
        die('Newsletter class: getTagsList() is an abstract method');
    }

    /**
     * Method to know the access level of a user or group to the newsletter.
     * Use TYPE_GROUP for groups and TYPE_USER for users.
     * The possible access level are:
     *
     * ACL_ACCESS_NO
     * ACL_ACCESS_SUBSCRIBER
     * ACL_ACCESS_AUTHOR
     * ACL_ACCESS_SENDER
     * ACL_ACCESS_ADMINISTRATOR
     *
     * @access	public
     * @param   integer $obj_id User or group identifier
     * @param   integer $obj_type TYPE_GROUP for group TYPE_USER for user
     * @param bool $reload force acl reloading
     * @return  access level.
     * @abstract
     * @since	0.6
     */
    function getAccessLevel($obj_id, $obj_type, $reload = false) {
        die('Newsletter class: getAccessLevel() is an abstract method');
    }

    /**
     * Attach an ACL to the newsletter
     *
     * @param ACL $acl ACL to attach to the newsletter
     * @return true or false if something wrong
     * @since 0.6
     */
    function attachACL($acl) {
        jincimport('utility.servicelocator');
        $servicelocator = ServiceLocator::getInstance();
        $logger = $servicelocator->getLogger();

        $news_id = $this->get('id');
        $query = "DELETE FROM #__jinc_access " .
                "WHERE acc_news_id = " . (int) $news_id;
        $logger->debug('Newsletter: Executing query: ' . $query);
        $dbo = & JFactory::getDBO();
        $dbo->setQuery($query);
        if (!$dbo->query()) {
            $this->setError('ERR018');
            return false;
        }

        $aclentries = $acl->getAllEntries();
        if (!empty($aclentries)) {
            foreach ($aclentries as $aclentry) {
                $obj_id = $aclentry->get('obj_id');
                $role_id = $aclentry->get('role_id');
                $query = 'INSERT IGNORE INTO #__jinc_access ' .
                        '(`acc_news_id`, `acc_grp_id`, `acc_role`) VALUES ' .
                        '(' . (int) $news_id . ', ' . (int) $obj_id . ', ' . (int) $role_id . ')';
                $logger->debug('Newsletter: Executing query: ' . $query);
                $dbo = & JFactory::getDBO();
                $dbo->setQuery($query);
                if (!$dbo->query()) {
                    $this->setError('ERR020');
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Loads newsletter Acess Control List
     *
     * @param bool $reload force acl reloading
     * @return acl or false if something wrong
     * @since 0.6
     */
    function loadACL($reload = false) {
        if (is_null($this->acl) || $reload) {
            jincimport('utility.servicelocator');
            $servicelocator = ServiceLocator::getInstance();
            $logger = $servicelocator->getLogger();

            $loaded_acl = new ACL();

            $query = 'SELECT a.acc_grp_id, a.acc_role, g.grp_name  ' .
                    'FROM #__jinc_access a ' .
                    'LEFT JOIN #__jinc_group g ON a.acc_grp_id = g.grp_id ' .
                    'WHERE acc_news_id = ' . (int) $this->get('id');
            $logger->debug('Newsletter: Executing query: ' . $query);
            $dbo = & JFactory::getDBO();
            $dbo->setQuery($query);
            if ($entries = $dbo->loadAssocList()) {
                if (!empty($entries)) {
                    foreach ($entries as $entry) {
                        $aclentry = new ACLEntry($entry['acc_grp_id'], TYPE_GROUP, $entry['acc_role'], $entry['grp_name']);
                        $loaded_acl->addEntry($aclentry);
                    }
                }
            }
            $this->acl = $loaded_acl;
        }
        return $this->acl;
    }

}

?>
