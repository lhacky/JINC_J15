<?php
/**
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
defined('_JEXEC') or die();
jimport('joomla.application.component.model');
jimport('joomla.html.pagination');

class NewslettersModelNewsletter extends JModel {
/**
 * Message result
 * @var string
 */
    var $_message = '';

    function __construct() {
        parent::__construct();

        global $mainframe, $option;
    }

    function getMessages() {
        jincimport('utility.servicelocator');
        $servicelocator = ServiceLocator::getInstance();
        $logger = $servicelocator->getLogger();

        $news_id = JRequest::getInt( 'news_id', 0, 'GET');
        $result = array();

        $ninstance = NewsletterFactory::getInstance();
        if ($newsletter = $ninstance->loadNewsletter($news_id, true)) {
            $max_msg = (int) $newsletter->get('front_max_msg');

            if ($max_msg > 0) {
                $query = 'SELECT msg_id, msg_subject, msg_body, msg_datasent, msg_attachment ' .
                    'FROM #__jinc_message ' .
                    'WHERE msg_news_id = ' . (int) $news_id . ' ' .
                    'AND UNIX_TIMESTAMP(msg_datasent) > 0 ' .
                    'ORDER BY msg_datasent DESC';
                $logger->debug('NewslettersModelNewsletter: Executing query: ' . $query);

                $result = $this->_getList($query, 0, $max_msg);
            }

        }

        return $result;
    }

    function getData() {
        jincimport('core.newsletterfactory');
        $news_id = JRequest::getInt( 'news_id', 0, 'GET');
        $ninstance = NewsletterFactory::getInstance();
        if (!($newsletter = $ninstance->loadNewsletter($news_id, true))) {
            $this->setError('ERROR_FER002');
            return false;
        }
        return $newsletter;
    }

    function subscribe($news_id, $subscriber_info, $attributes, $mod_jinc = 'false', $notice_resp = false) {
        jincimport('core.newsletterfactory');

        $ninstance = NewsletterFactory::getInstance();
        if (!($newsletter = $ninstance->loadNewsletter($news_id))) {
            $this->setError('ERROR_FER002');
            return false;
        }
        if (($newsletter->get('noti_id') > 0) && !$notice_resp) {
            $this->setError('ERROR_FER026');
            return false;
        }
        if ($newsletter->get('captcha') > CAPTCHA_NO) {
            include_once JPATH_COMPONENT.DS.'securimage'.DS.'securimage.php';
            
            $captcha_code =  JRequest::getString( 'captcha_code', '');
            $securimage = new Securimage();
            if ($mod_jinc == 'true')
                $securimage->setSessionPrefix('mod_jinc');
            if ($securimage->check($captcha_code) == false) {
                $this->setError('ERROR_ERR040');
                return false;
            }
        }
        if (!($newsletter->subscribe($subscriber_info, $attributes))) {
            $this->setError($newsletter->getError());
            return false;
        }
        if ($newsletter->getType() == NEWSLETTER_PUBLIC_NEWS) {
            $this->setState('message', 'INFO_FIN002');
        } else {
            $this->setState('message', 'INFO_FIN003');
        }
        return true;
    }

    function unsubscribe($news_id, $subscriber_info) {
        jincimport('core.newsletterfactory');

        $ninstance = NewsletterFactory::getInstance();
        if (!($newsletter = $ninstance->loadNewsletter($news_id))) {
            $this->setError('ERROR_FER002');
            return false;
        }
        if (!($newsletter->unsubscribe($subscriber_info))) {
            $this->setError('ERROR_FER005');
            return false;
        }
        if ($newsletter->getType() == NEWSLETTER_PUBLIC_NEWS) {
            $this->setState('message', 'INFO_FIN004');
        } else {
            $this->setState('message', 'INFO_FIN005');
        }
        return true;
    }

    function confirm($news_id, $user_mail, $pub_random) {
        jincimport('core.newsletterfactory');
        $ninstance = NewsletterFactory::getInstance();
        if (! $newsletter = $ninstance->loadNewsletter($news_id)) {
            $this->setError('ERROR_FER001');
            return false;
        }

        if ($newsletter->getType() != NEWSLETTER_PUBLIC_NEWS ) {
            $this->setError('ERROR_FER001');
            return false;
        }

        $sub_info = array();
        $sub_info['email'] = $user_mail;
        $sub_info['waiting'] = false;
        if ($newsletter->isSubscribed($sub_info)) {
             $this->setError('ERROR_ERR015');
             return false;
         }
        
        if (! $newsletter->confirmSubscription($user_mail, $pub_random)) {
            $this->setError('ERROR_FER004');
            return false;
        }
        return true;
    }

    function delconfirm($news_id, $user_mail, $pub_random) {
        jincimport('core.newsletterfactory');
        $ninstance = NewsletterFactory::getInstance();
        if (! $newsletter = $ninstance->loadNewsletter($news_id)) {
            $this->setError('ERROR_FER001');
            return false;
        }

        if ($newsletter->getType() != NEWSLETTER_PUBLIC_NEWS ) {
            $this->setError('ERROR_FER001');
            return false;
        }

        if (! $newsletter->confirmUnsubscription($user_mail, $pub_random)) {
            $this->setError('ERROR_FER004');
            return false;
        }
        return true;
    }

    function getNotice() {
        jincimport('core.newsletterfactory');
        $id = JRequest::getInt('news_id', 0, 'GET');

        $ninstance = NewsletterFactory::getInstance();
        if (!($newsletter = $ninstance->loadNewsletter($id, true))) {
            $this->setError('ERROR_FER017');
            return false;
        }

        if (!($notice = $ninstance->loadNotice($newsletter->get('noti_id')))) {
            $this->setError('ERROR_FER023');
            return false;
        }

        return $notice;
    }
}
