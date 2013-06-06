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
jimport('joomla.user.user');

class JINCModelSubscriber extends JModel {

    var $_password = null;

    function __construct() {
        parent::__construct();
        $array = JRequest::getVar('cid', 0, '', 'array');
        $this->setId((int) $array[0]);
    }

    function setId($id) {
        $this->_id = $id;
        $this->_data = null;
    }

    function &getData() {
        if (empty($this->_data)) {
            $query = 'SELECT subs_id, subs_news_id, subs_user_id ' .
                    'FROM #__jinc_subscriber ' .
                    'WHERE subs_id = ' . (int) $this->_id;
            $this->_db->setQuery($query);
            $this->_data = $this->_db->loadObject();
        }
        if (!$this->_data) {
            $this->_data = new stdClass();
            $this->_data->subs_id = 0;
        }
        return $this->_data;
    }

    function delete() {
        jincimport('core.newsletterfactory');
        $cids = JRequest::getVar('cid', array(0), 'post', 'array');
        if (count($cids)) {
            $ninstance = NewsletterFactory::getInstance();
            foreach ($cids as $cid) {
                $cid_split = explode('_', $cid, 2);
                $subs_id = (int) $cid_split[0];
                $news_id = (int) $cid_split[1];
                if (!$newsletter = $ninstance->loadNewsletter($news_id, false)) {
                    $this->setError('ERROR_ERR023');
                    return false;
                }
                $subscriber_info = array('subs_id' => $subs_id);
                if (!$newsletter->unsubscribe($subscriber_info)) {
                    $this->setError($newsletter->getError());
                    return false;
                }
            }
        }
        return true;
    }

    function getInfo() {
        $subs_id = JRequest::getVar('subs_id', 0, 'GET', 'int');
        $news_id = JRequest::getVar('news_id', 0, 'GET', 'int');

        jincimport('core.newsletterfactory');
        $ninstance = NewsletterFactory::getInstance();
        if ($newsletter = $ninstance->loadNewsletter($news_id)) {
            return $newsletter->getSubscriber($subs_id);
        }

        return false;
    }

    function approve() {
        $cids = JRequest::getVar('cid', array(0), 'post', 'array');
        if (count($cids)) {
            foreach ($cids as $cid) {
                $cid_split = explode('_', $cid, 2);
                $subs_id = (int) $cid_split[0];
                $news_id = (int) $cid_split[1];
                $query = 'UPDATE #__jinc_public_subscriber SET pub_datasub = now(), pub_random = \'\' '.
                    'WHERE ISNULL(pub_datasub) AND pub_id = ' . $subs_id . ' AND pub_news_id = ' . $news_id;                
                $this->_db->setQuery($query);
                if (!$this->_db->query()) {
                    $this->setError($this->_db->getError());
                }
            }
        }
        return true;
    }

}

?>
