<?php
/**
 * @version		$Id: protectednewsletter.php 20-gen-2010 17.06.09 lhacky $
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
include_once 'newsletter.php';
include_once 'groupfactory.php';

/**
 * ProtectedNewsletter class, defining a protected newsletter.
 *
 * @package		JINC
 * @subpackage          Core
 * @since		0.6
 */

class ProtectedNewsletter extends Newsletter {
/**
 * The newsletter welcome message
 *
 * @var		The newsletter welcome message
 * @access	protected
 * @since	0.6
 */
    var $welcome = '';

    /**
     * The newsletter welcome message subject
     *
     * @var		The newsletter welcome message subject
     * @access	protected
     * @since	0.6
     */
    var $welcome_subject = '';

    function ProtectedNewsletter($news_id, $subs_retriever = null) {
        if (is_null($subs_retriever))
            $subs_retriever = new JUserInfoRetriever($news_id);
        parent::Newsletter($news_id, $subs_retriever);
    }

    /**
     * Method to know if a user is already subscribed to the newsletter.
     *
     * @access	public
     * @param   array $subscriber_info Subscriber info based on newsletter type.*
     * @return  true if can subscribe. false if not. -1 if something wrong.
     * @since	0.6
     */

    function isSubscribed($subscriber_info) {
        jincimport('utility.servicelocator');
        $servicelocator = ServiceLocator::getInstance();
        $logger = $servicelocator->getLogger();

        if (!isset ($subscriber_info['user_id'])) return -1;

        $user_id = $subscriber_info['user_id'];
        $news_id = $this->get('id');
        $query = 'SELECT s.subs_user_id ' .
            'FROM #__jinc_newsletter n ' .
            'LEFT JOIN #__jinc_subscriber s ON n.news_id = s.subs_news_id ' .
            'WHERE n.news_id = ' . (int) $news_id . ' AND s.subs_user_id = ' . (int) $user_id;
        $logger->debug('ProtectedNewsletter: Executing query: ' . $query);
        $dbo =& JFactory::getDBO();
        $dbo->setQuery($query);
        if ( $user_info = $dbo->loadObjectList() ) {
            return !empty($user_info);
        }
        return false;
    }

    /**
     * Method to subscribe a user to the newsletter. Implements the parent
     * abstract method.
     *
     * Hint: $subscriber_info array description:
     *
     * $subscriber_info[user_id] - User id of the user to subscribe.
     * $subscriber_info[username] - Username of the user to subscribe.
     * $subscriber_info[name] - Name of the user to subscribe.
     * $subscriber_info[email] - Email of the user to subscribe.
     *
     * username, name, email are not mandatory. If they are set the user will
     * be created (if does not already exist) and will be subscribed.
     *
     * @access	public
     * @param   array $subscriber_info Subscriber info based on newsletter type.
     * @param   array $attributes Array of addictional attributes for subscription
     * @return  true if successfully subscribed. false if something wrong.
     * @since	0.6
     */

    function subscribe($subscriber_info, $attributes = null) {
        if (is_null($attributes) || ! is_array($attributes))
            $attributes = array();

        jincimport('utility.servicelocator');
        jincimport('utility.jincjoomlahelper');
        $servicelocator = ServiceLocator::getInstance();
        $logger = $servicelocator->getLogger();

        if (! isset ($subscriber_info['user_id'])) {
            $this->setError('ERR008');
            return false;
        }

        $user_id = $subscriber_info['user_id'];
        $user_info = JINCJoomlaHelper::getUserInfo($user_id);
        if (empty ($user_info)) {
            $this->setError('ERR008');
            return false;
        }

        if (! $this->checkMandatoryAttributes($attributes)) {
            $this->setError('ERR048');
            return false;
        }

        if ($this->isSubscribed($subscriber_info)) {
            $this->setError('ERR015');
            return false;
        }

        $news_id = $this->get('id');
        $query = 'INSERT IGNORE INTO #__jinc_subscriber ' .
            '(subs_news_id , subs_user_id, subs_datasub) '.
            'VALUES (' . (int) $news_id . ', ' . (int) $user_id . ', now())';
        $logger->debug('ProtectedNewsletter: Executing query: ' . $query);
        $dbo =& JFactory::getDBO();
        $dbo->setQuery($query);
        if (! $dbo->query() ) {
            $this->setError('ERR009');
            return false;
        }

        $sub_id = $dbo->insertid();
        $this->insertAttributeOnSubscription($sub_id, $attributes);

        $this->sendWelcome($user_info);
        
        $dispatcher = &JDispatcher::getInstance();
        $params = array('news_id' => $this->get('id'),
            'news_name' => $this->get('name'),
            'subs_name' => $user_info['name'],
            'news_notify' => $this->get('notify'));
        $result = $dispatcher->trigger('jinc_subscribe', $params);
        return true;
    }

    /**
     * Newsletter type getter.
     *
     * @access	public
     * @return  Newsletter type identifier
     * @since	0.6
     */

    function getType() {
        return NEWSLETTER_PROTECTED_NEWS;
    }

    /**
     * Returns information needed to subscribe a user to this newsletter.
     *
     * @access	public
     * @return  array() of fields necessary to subscribe a user to the newsletter
     * @since	0.6
     */

    function getSubscriptionInfo() {
        $info = array();
        array_push($info, 'username');
        array_push($info, 'name');
        array_push($info, 'email');
        $attributes = $this->attributes;
        foreach ($attributes->toArray() as $attr_name => $attr_value) {
            array_push($info, 'attr_' . $attr_name);
        }
        return $info;
    }

    /**
     * Method to unsubscribe a newsletter user.
     *
     * @access	public
     * @param   array $subscriber_info Subscriber info based on newsletter type.
     * @return  true if successfully unsubscribed. false if something wrong.
     * @since	0.6
     * @abstract
     */

    function unsubscribe($subscriber_info) {
        jincimport('utility.servicelocator');
        $servicelocator = ServiceLocator::getInstance();
        $logger = $servicelocator->getLogger();
        $dbo =& JFactory::getDBO();

        $query = '';
        if (isset ($subscriber_info['subs_id'])) {
            $id = (int) $subscriber_info['subs_id'];
            $query = 'DELETE FROM #__jinc_subscriber ' .
                'WHERE subs_id = ' . (int) $id . ' ' .
                'AND subs_news_id = ' . (int) $this->get('id');
        }

        if (isset ($subscriber_info['user_id'])) {
            $id = (int) $subscriber_info['user_id'];
            $query = 'DELETE FROM #__jinc_subscriber ' .
                'WHERE subs_user_id = ' . (int) $id . ' ' .
                'AND subs_news_id = ' . (int) $this->get('id');
        }

        if (strlen($query) > 0) {
            $logger->debug('PrivateNewsletter: Executing query: ' . $query);
            $dbo =& JFactory::getDBO();
            $dbo->setQuery($query);
            if (! $dbo->query() ) {
                $this->setError('ERR025');
                return false;
            }
            if (isset ($subscriber_info['subs_id']))
                $this->removeAttributeOnUnsubscription($id);
        }

        // Triggering unsubscription event
        $dispatcher = &JDispatcher::getInstance();
        $params = array('news_id' => $this->get('id'));
        $result = $dispatcher->trigger('jinc_unsubscribe', $params);

        return true;
    }

    /**
     * Gets TAGS to substitute in subscriber info for this newsletter.
     *
     * @access	public
     * @param boolean only_newsletter return only newsletter related tags
     * @param boolean only_retriever  return only retriever related tags
     * @return	array Array of tags.
     * @since	0.6
     */
    function getTagsList($only_newsletter = false, $only_retriever = false) {
        $news_tags = array('NEWSLETTER', 'SENDER', 'SENDERMAIL', 'UNSUBSCRIPTIONURL');
        if ($only_newsletter)
            return $news_tags;

        $attributes = $this->attributes;
        $retriever = $this->_subs_retriever;
        $ret_tags = $retriever->getTagsList($attributes->toArray());
        
        if ($only_retriever)
            return $ret_tags;
        return array_merge($news_tags, $ret_tags);
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
     * @since	0.6
     */
    function getAccessLevel($obj_id, $obj_type, $reload = false) {
        if ($acl = $this->loadACL($reload)) {
            $min_level = ACL_ACCESS_SUBSCRIBER;
            $access = $acl->performEffectiveAccess($obj_id, $obj_type);
            // If user or group exists they can access as subscribers
            if ($obj_type == TYPE_USER) {
                jincimport('utility.jincjoomlahelper');
                $user_info = JINCJoomlaHelper::getUserInfo($obj_id);
                if (empty ($user_info)) {
                    $min_level = ACL_ACCESS_NO;
                }
            }
            if ($obj_type == TYPE_GROUP) {
                $gfactory = GroupFactory::getInstance();
                if ($group = $gfactory->loadGroup($obj_id)) {
                    $min_level = ACL_ACCESS_SUBSCRIBER;
                }
            }
            return max($min_level, $access);
        } else
            return ACL_ACCESS_NO;
    }
}
?>
