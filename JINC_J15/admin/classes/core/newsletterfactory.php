<?php

/**
 * @version		$Id: newsletterfactory.php 2010-01-19 12:01:47Z lhacky $
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
require_once 'juserinforetriever.php';
require_once 'jcontactinforetriever.php';
require_once 'publicretriever.php';
require_once 'publicnewsletter.php';
require_once 'privatenewsletter.php';
require_once 'protectednewsletter.php';
require_once 'notice.php';

/**
 * NewsletterFactory class, building Newsletter objects from a newsletter
 * ID and getting information from database.
 * This class implements the Factory Design Pattern.
 *
 * @package		JINC
 * @subpackage          Core
 * @since		0.6
 */
class NewsletterFactory {

    function NewsletterFactory() {

    }

    function &getInstance() {
        static $instance = null;
        if (null === $instance) {
            $instance = new NewsletterFactory();
        }
        return $instance;
    }

    /**
     * The newsletter loader. It loads a newsletter from his identifier.
     *
     * @access	public
     * @param	integer $news_id the newsletter identifier.
     * @param       boolean $frontend if true checks access and published
     * @return  The Newsletter object or -1 if newsletter not foud or false if something wrong.
     * @since	0.6
     * @see     Newsletter
     */
    function loadNewsletter($news_id, $frontend = true) {
        jincimport('utility.servicelocator');
        $servicelocator = ServiceLocator::getInstance();
        $logger = $servicelocator->getLogger();

        $query = 'SELECT news_sendername, news_senderaddr, news_type, news_name, ' .
                'news_description, news_disclaimer, news_optin_subject, news_optin, ' .
                'news_welcome_subject, news_welcome, news_default_template, ' .
                'news_optinremove_subject, news_optinremove, news_on_subscription, ' .
                'news_jcontact_enabled, news_captcha, ' .
                'news_front_theme, news_front_max_msg, news_front_type, ' .
                'news_attributes, news_replyto_name, news_replyto_addr, ' .
                'news_notify, news_noti_id, news_input_style ' .
                'FROM #__jinc_newsletter n ' .
                'WHERE news_id = ' . (int) $news_id;
        $query .= ($frontend) ? ' AND published = 1' : '';
        $logger->debug('NewsletterFactory: Executing query: ' . $query);
        $dbo = & JFactory::getDBO();
        $dbo->setQuery($query);
        // Loading newsletter information from database
        if ($result = $dbo->loadAssocList()) {
            if (empty($result)) {
                // Newsletter not found in database
                $logger->finer('NewsletterFactory: Newsletter not found');
                return false;
            }
            $newsletter = $result[0];
        } else {
            return false;
        }
        // Creating Newsletter based on news_type value
        switch ($newsletter['news_type']) {
            case NEWSLETTER_PROTECTED_NEWS:
                $logger->finer('NewsletterFactory: Building Protected Newsletter with JUserInfoRetriever');
                if ($newsletter['news_jcontact_enabled']) {
                    $retriever = new JContactInfoRetriever($news_id);
                } else {
                    $retriever = new JUserInfoRetriever($news_id);
                }
                $newsObj = new ProtectedNewsletter($news_id, $retriever);
                break;
            case NEWSLETTER_PUBLIC_NEWS:
                $logger->finer('NewsletterFactory: Building Public Newsletter with PublicRetriever');
                $retriever = new PublicRetriever($news_id);
                $newsObj = new PublicNewsletter($news_id, $retriever);
                $newsObj->set('optin_subject', $newsletter['news_optin_subject']);
                $newsObj->set('optin', $newsletter['news_optin']);
                $newsObj->set('optinremove_subject', $newsletter['news_optinremove_subject']);
                $newsObj->set('optinremove', $newsletter['news_optinremove']);
                break;
            default:
                $logger->finer('NewsletterFactory: Building Private Newsletter with JUserInfoRetriever');
                if ($newsletter['news_jcontact_enabled']) {
                    $retriever = new JContactInfoRetriever($news_id);
                } else {
                    $retriever = new JUserInfoRetriever($news_id);
                }
                $newsObj = new PrivateNewsletter($news_id, $retriever);
                break;
        }
        // Setting newsletter properties
        $newsObj->set('welcome', $newsletter['news_welcome']);
        $newsObj->set('welcome_subject', $newsletter['news_welcome_subject']);
        $newsObj->set('description', $newsletter['news_description']);
        $newsObj->set('disclaimer', $newsletter['news_disclaimer']);
        $newsObj->set('name', $newsletter['news_name']);
        $newsObj->set('senderaddr', $newsletter['news_senderaddr']);
        $newsObj->set('sendername', $newsletter['news_sendername']);
        $newsObj->set('replyto_addr', $newsletter['news_replyto_addr']);
        $newsObj->set('notify', $newsletter['news_notify']);
        $newsObj->set('replyto_name', $newsletter['news_replyto_name']);
        $newsObj->set('default_template', $newsletter['news_default_template']);
        $newsObj->set('on_subscription', $newsletter['news_on_subscription']);
        $newsObj->set('jcontact_enabled', $newsletter['news_jcontact_enabled']);
        $front_theme = ($newsletter['news_front_theme'] == '') ? NEWSLETTER_DEFAULT_THEME : $newsletter['news_front_theme'];
        $newsObj->set('front_theme', $front_theme);
        $newsObj->set('front_max_msg', $newsletter['news_front_max_msg']);
        $newsObj->set('front_type', $newsletter['news_front_type']);
        $attributes = new JParameter('');
        $attributes->loadINI($newsletter['news_attributes']);
        $newsObj->set('attributes', $attributes);
        $newsObj->set('captcha', $newsletter['news_captcha']);
        $newsObj->set('noti_id', $newsletter['news_noti_id']);
        $newsObj->set('input_style', $newsletter['news_input_style']);


        if ($frontend) {
            $user = & JFactory::getUser();
            $user_id = $user->get('id');
            if (!($newsObj->getAccessLevel($user_id, TYPE_USER) > ACL_ACCESS_NO)) {
                $logger->finer('NewsletterFactory: Not authorized access');
                return false;
            }
        }
        return $newsObj;
    }

    /**
     * The tags list loader. It loads array of replaceable tags based on newsletter type.
     *
     * @access	public
     * @param	integer $news_type the newsletter type.
     * @return      array List of tags
     * @since	0.6
     */
    function loadTagsList($news_type, $retr_type = RETRIEVER_JUSER) {
        if ($retr_type == RETRIEVER_JCONCACT)
            $retriever = new JContactInfoRetriever(0);
        else
            $retriever = new JUserInfoRetriever(0);

        switch ($news_type) {
            case NEWSLETTER_PUBLIC_NEWS:
                $newsletter = new PublicNewsletter(0);
                break;

            case NEWSLETTER_PROTECTED_NEWS:
                $newsletter = new ProtectedNewsletter(0, $retriever);
                break;

            case NEWSLETTER_PRIVATE_NEWS:
                $newsletter = new PrivateNewsletter(0, $retriever);
                break;

            default:
                return array();
        }
        return $newsletter->getTagsList();
    }

    /**
     * The onSubscription newsletter list loader. A onSubscription newsletter is
     * a newsletter a user should be subscribed at subscription time
     *
     * @access	public
     * @return  array List onSubscription newsletter ids.
     * @since	0.6
     */
    function loadOnSubscriptionNewsletters() {
        jincimport('utility.servicelocator');
        $servicelocator = ServiceLocator::getInstance();
        $logger = $servicelocator->getLogger();

        $news_ids = array();
        $query = 'SELECT news_id FROM #__jinc_newsletter n ' .
                'WHERE news_on_subscription = 1 AND published = 1';
        $logger->debug('NewsletterFactory: Executing query: ' . $query);
        $dbo = & JFactory::getDBO();
        $dbo->setQuery($query);
        // Loading newsletter information from database
        if ($result = $dbo->loadAssocList()) {
            foreach ($result as $row) {
                array_push($news_ids, (int) $row['news_id']);
            }
        } else {
            return false;
        }
        return $news_ids;
    }

    /**
     * The themes list loader. It produces the themes list searching for CSS
     * files into the theme directory.
     *
     * @access	public
     * @return  array List of themes.
     * @since	0.7
     */
    function loadThemes() {
        jincimport('utility.servicelocator');
        $servicelocator = ServiceLocator::getInstance();
        $logger = $servicelocator->getLogger();

        $themes = array();

        $directory = JPATH_SITE . DS . 'components' . DS . 'com_jinc' . DS . 'assets' . DS . 'themes';
        $handler = opendir($directory);
        while ($file = readdir($handler)) {
            if (strtolower(substr($file, -4)) == '.css') {
                $opt = array('id' => $file, 'value' => substr($file, 0, -4));
                array_push($themes, $opt);
            }
        }
        closedir($handler);

        $logger->finer('NewsletterFactory: Found ' . count($themes) . ' themes');
        return $themes;
    }

    /**
     * The addictional attribute loader. It loads an attribute object from
     * database using the attribute name as search key
     *
     * @access	public
     * @return  Attribute the loaded attribute or false if sometring wrong.
     * @since	0.7
     */
    function loadAttribute($attr_name) {
        jincimport('utility.servicelocator');
        $servicelocator = ServiceLocator::getInstance();
        $logger = $servicelocator->getLogger();

        $query = 'SELECT attr_id, attr_description, attr_type, attr_table_name, attr_name_i18n ' .
                'FROM #__jinc_attribute ' .
                'WHERE attr_name = \'' . $attr_name . '\'';
        $logger->debug('NewsletterFactory: Executing query: ' . $query);
        $dbo = & JFactory::getDBO();
        $dbo->setQuery($query);
        // Loading newsletter information from database
        if ($result = $dbo->loadAssocList()) {
            if (empty($result)) {
                // Newsletter not found in database
                $logger->finer('NewsletterFactory: Attribute not found');
                return false;
            }
            $attr = $result[0];
            $attribute = new Attribute($attr['attr_id']);
            $attribute->set('name', $attr_name);
            $attribute->set('description', $attr['attr_description']);
            $attribute->set('type', $attr['attr_type']);
            $attribute->set('table_name', $attr['attr_table_name']);
            $attribute->set('name_i18n', $attr['attr_name_i18n']);
            return $attribute;
        }
        return false;
    }

    /**
     * The addictional attributes list loader.
     *
     * @access	public
     * @return      array List of defined attributes or false if something wrong.
     * @since	0.7
     */
    function loadAttributesList() {
        jincimport('utility.servicelocator');
        $servicelocator = ServiceLocator::getInstance();
        $logger = $servicelocator->getLogger();

        $attributes = array();
        $query = 'SELECT attr_id, attr_name, attr_description, attr_name_i18n FROM #__jinc_attribute';
        $logger->debug('NewsletterFactory: Executing query: ' . $query);
        $dbo = & JFactory::getDBO();
        $dbo->setQuery($query);
        // Loading newsletter information from database
        if ($result = $dbo->loadAssocList()) {
            foreach ($result as $row) {
                $element = array('id' => $row['attr_id'], 'name' => $row['attr_name'], 'description' => $row['attr_description'], 'name_i18n' => $row['attr_name_i18n']);
                array_push($attributes, $element);
            }
        } else {
            return false;
        }
        return $attributes;
    }

    /**
     * Load newsletter names.
     *
     * @access	public
     * @param  type 0 -> all, 1-> Private, 2-> Public
     * @return  array List of news_id/news_name pairs.
     * @since	0.7
     */
    function loadNames($type = 0) {
        jincimport('utility.servicelocator');
        $servicelocator = ServiceLocator::getInstance();
        $logger = $servicelocator->getLogger();

        $query = 'SELECT news_id, news_name FROM #__jinc_newsletter';
        if ($type == 1) {
            $query .= ' WHERE news_type >= 1';
        }
        if ($type == 2) {
            $query .= ' WHERE news_type = 0';
        }

        $logger->debug('NewsletterFactory: executing query ' . $query);
        $dbo = & JFactory::getDBO();
        $dbo->setQuery($query);

        if ($result = $dbo->loadObjectList()) {
            return $result;
        }
        return false;
    }

        /**
     * The notice loader. It loads a notice from its identifier.
     *
     * @access	public
     * @param	integer $id the notice identifier.
     * @return  The notice object or false if something wrong.
     * @since	0.9
     * @see     Newsletter
     */
    function loadNotice($id) {
        jincimport('utility.servicelocator');
        $servicelocator = ServiceLocator::getInstance();
        $logger = $servicelocator->getLogger();

        $query = 'SELECT noti_name, noti_title, noti_bdesc, noti_conditions ' .
                'FROM #__jinc_notice nt ' .
                'WHERE noti_id = ' . (int) $id;
        $logger->debug('NewsletterFactory: Executing query: ' . $query);
        $dbo = & JFactory::getDBO();
        $dbo->setQuery($query);
        // Loading notice information from database
        if ($result = $dbo->loadAssocList()) {
            if (empty($result)) {
                // Newsletter not found in database
                $logger->finer('NewsletterFactory: Notice not found');
                return false;
            }
            $notice = $result[0];
        } else {
            return false;
        }

        $ntObj = new Notice($id);
        // Setting newsletter properties
        $ntObj->set('name', $notice['noti_name']);
        $ntObj->set('title', $notice['noti_title']);
        $ntObj->set('bdesc', $notice['noti_bdesc']);
        $ntObj->set('conditions', $notice['noti_conditions']);
        return $ntObj;
    }

    /**
     * Load Notices names.
     *
     * @access	public
     * @return  array List of tem_id/tem_name pairs.
     * @since	0.7
     */
    function loadNoticeNames() {
        jincimport('utility.servicelocator');
        $servicelocator = ServiceLocator::getInstance();
        $logger = $servicelocator->getLogger();

        $query = 'SELECT noti_id, noti_name FROM #__jinc_notice';
        $logger->debug('NewsletterFactory: executing query ' . $query);
        $dbo =& JFactory::getDBO();
        $dbo->setQuery($query);

        if ($result = $dbo->loadObjectList()) {
            return $result;
        }
        return false;
    }
}

?>
