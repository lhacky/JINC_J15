<?php
/**
 * @version		$Id: JINCSubscription.php 1-mar-2010 13.23.11 lhacky $
 * @package		plgJINCNewsSubscription
 * @subpackage
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
 * plgUserJINCSubscription class subscribing users to a newsletter at
 * registration time and unsubscribing users at unregistration time.
 *
 * @package		plgUserJINCSubscription
 * @subpackage
 * @since		0.6
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');

// Preload the JINCFactory
jimport('joomla.filesystem.file');
require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_jinc'.DS.'classes'.DS.'factory.php';

class plgUserJINCSubscription extends JPlugin {

    /**
     * Constructor
     *
     * For php4 compatability we must not use the __constructor as a constructor for plugins
     * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
     * This causes problems with cross-referencing necessary for the observer design pattern.
     *
     * @param       object $subject The object to observe
     * @param       array  $config  An array that holds the plugin configuration
     * @since 1.5
     */
    function plgUserJINCSubscription(& $subject, $config) {
        parent::__construct($subject, $config);
    }

    /**
     * It subscribes just created user to the newsletter with on_subscription flag.
     *
     * @param array   $user   Array of user info
     * @param boolean $isnew  true if user is just created
     * @param boolean $succes true if user is created successfully
     * @param string  $msg    User creation message
     */
    function onAfterStoreUser($user, $isnew, $succes, $msg) {
        global $mainframe;
        if (! $succes) return ;
        if (! $isnew) return ;

        $user_id    = $user['id'];
        $user_name  = $user['name'];
        $user_email = $user['email'];

        jincimport('core.newsletterfactory');
        $ninstance = NewsletterFactory::getInstance();
        if ($newslist = $ninstance->loadOnSubscriptionNewsletters()) {
            foreach ($newslist as $news_id) {
                if ( $newsletter = $ninstance->loadNewsletter($news_id, false) ) {
                    if ($newsletter->getType() == NEWSLETTER_PRIVATE_NEWS) {
                        if ($acl = $newsletter->loadACL()) {
                            if ($grp_id = $acl->getFirstGroupInRole(ACL_ACCESS_SUBSCRIBER)) {
                                $gfactory = GroupFactory::getInstance();
                                if ($group = $gfactory->loadGroup($grp_id))
                                    $group->addUser($user_id);
                            }
                        }
                    }
                    $subscriber_info = array('user_id' => $user_id,
                        'email' => $user_email, 'name' => $user_name,
                        'noptin' => true);
                    $newsletter->subscribe($subscriber_info);
                }
            }
        }
    }

    /**
     * It ussubscribes just deleted user from every newsletters.
     *
     * @param array   $user   Array of user info
     * @param boolean $succes true if user is created successfully
     * @param string  $msg    User deletion message
     */

    function onAfterDeleteUser($user, $succes, $msg) {
        global $mainframe;
        if (! $succes) return ;

        $user_id    = $user['id'];
        $user_email = $user['email'];

        jincimport('core.newsletterfactory');
        jincimport('utility.servicelocator');
        $servicelocator = ServiceLocator::getInstance();
        $logger = $servicelocator->getLogger();

        $protected = NEWSLETTER_PROTECTED_NEWS;
        $private   = NEWSLETTER_PRIVATE_NEWS;
        $query = 'SELECT news_id FROM #__jinc_newsletter n ' .
                'WHERE news_type = ' . $private . ' OR news_type = ' . $protected;

        $logger->debug('plgUserJINCSubscription: Executing query: ' . $query);
        $dbo =& JFactory::getDBO();
        $dbo->setQuery($query);
        // Unsubscribing user from every JINC newsletter
        if ($result = $dbo->loadAssocList()) {
            foreach ($result as $row) {
                $news_id = (int) $row['news_id'];
                $ninstance = NewsletterFactory::getInstance();
                if ( $newsletter = $ninstance->loadNewsletter($news_id, false) ) {
                    $subscriber_info = array('user_id' => $user_id);
                    $newsletter->unsubscribe($subscriber_info);
                }
            }
        }

        // Deleting user from every JINC groups
        $query = 'DELETE FROM #__jinc_membership ' .
                'WHERE `mem_user_id` = ' . (int) $user_id ;
        $logger->debug('plgUserJINCSubscription: Executing query: ' . $query);
        $dbo->setQuery($query);
        $dbo->query();
    }
}
?>
