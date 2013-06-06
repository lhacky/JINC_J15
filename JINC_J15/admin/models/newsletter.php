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

class JINCModelNewsletter extends JModel {
    function __construct() {
        parent::__construct();
    }

    function &getData() {
        jincimport('core.newsletterfactory');
        jincimport('core.newsletter');
        $news_id = JRequest::getInt('news_id', 0);
        $ninstance = NewsletterFactory::getInstance();
        if (!$newsletter = $ninstance->loadNewsletter($news_id, false)) {
            $newsletter = new Newsletter(0);
        }
        return $newsletter;
    }

    function store() {
        jincimport('core.newsletterfactory');
        $row =& $this->getTable();
        $data = JRequest::get( 'post' );
        if (!$row->bind($data)) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        $details = array();
        $details['news_optin'] = JRequest::getVar( 'news_optin', '', 'post',
            'string', JREQUEST_ALLOWRAW);
        $details['news_optinremove'] = JRequest::getVar( 'news_optinremove',
            '', 'post', 'string', JREQUEST_ALLOWRAW);
        $details['news_welcome'] = JRequest::getVar( 'news_welcome', '', 'post',
            'string', JREQUEST_ALLOWRAW);
        $details['news_disclaimer'] = JRequest::getVar( 'news_disclaimer', '',
            'post', 'string', JREQUEST_ALLOWRAW);
        $details['news_welcome_created'] = JRequest::getVar( 'news_welcome_created',
            '', 'post', 'string', JREQUEST_ALLOWRAW);
        $details['news_description'] = JRequest::getVar( 'news_description',
            '', 'post', 'string', JREQUEST_ALLOWRAW);        
        $news_on_subscription = strtolower(JRequest::getString( 'news_on_subscription'));
        $details['news_on_subscription'] = ($news_on_subscription == 'on');
        $news_jcontact_enabled = strtolower(JRequest::getString( 'news_jcontact_enabled'));
        $details['news_jcontact_enabled'] = ($news_jcontact_enabled == 'on');
        $news_notify = strtolower(JRequest::getString( 'news_notify'));
        $details['news_notify'] = ($news_notify == 'on');

        $params = JRequest::getVar( 'news_attributes', null, 'post', 'array' );
        if (is_array($params)) {
            $txt = array ();
            foreach ($params as $k => $v) {
                if ((int) $v > ATTRIBUTE_NONE)
                    $txt[] = "$k=$v";
            }
            $details['news_attributes'] = implode("\n", $txt);
        }

        $row->bind($details);

        if (!$row->check()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        $news_id = JRequest::getInt('news_id', 0);
        if ($news_id == 0) $row->news_created = date('Y-m-d h:i:s', time());

        if (!$row->store()) {
            $dbo = $row->getDBO();
            $this->setError( $dbo->getError() );
            return false;
        }

        if ($news_id == 0) {
            $dbo = $row->getDBO();
            $news_id = $dbo->insertid();
        }

        $ninstance = NewsletterFactory::getInstance();
        if (! $newsletter = $ninstance->loadNewsletter($news_id, false)) {
            $this->setError('ERROR_ERR021');
            return false;
        }

        $subscribers = JRequest::getVar( 'rolesubscriber', array(), 'post', 'array' );
        $authors = JRequest::getVar( 'roleproposer', array(), 'post', 'array' );
        $senders = JRequest::getVar( 'rolesender', array(), 'post', 'array' );
        $admins = JRequest::getVar( 'roleadmin', array(), 'post', 'array' );

        jincimport('core.acl');
        $acl = new ACL();
        foreach ($subscribers as $key => $grp_id) {
            $entry = new ACLEntry($grp_id, TYPE_GROUP, ACL_ACCESS_SUBSCRIBER);
            $acl->addEntry($entry);
        }

        foreach ($authors as $key => $grp_id) {
            $entry = new ACLEntry($grp_id, TYPE_GROUP, ACL_ACCESS_AUTHOR);
            $acl->addEntry($entry);
        }

        foreach ($senders as $key => $grp_id) {
            $entry = new ACLEntry($grp_id, TYPE_GROUP, ACL_ACCESS_SENDER);
            $acl->addEntry($entry);
        }

        foreach ($admins as $key => $grp_id) {
            $entry = new ACLEntry($grp_id, TYPE_GROUP, ACL_ACCESS_ADMINISTRATOR);
            $acl->addEntry($entry);
        }
        $newsletter->attachACL($acl);

        return true;
    }

    function delete() {
        $cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
        $row =& $this->getTable();
        if (count( $cids )) {
            foreach($cids as $cid) {
                if ( !$row->delete($cid) ) {
                    $this->setError($row->getErrorMsg());
                    return false;
                }
            }
        }
        return true;
    }

    function publish() {
        $cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
        $row =& $this->getTable();
        if (count($cids)) {
            if (!$row->publish($cids, true)) {
                $dbo = $row->_db;
                $this->setError($dbo->getErrorMsg());
                return false;
            }
        }
        return true;
    }

    function unpublish() {
        $cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
        $row =& $this->getTable();
        if ( count($cids)) {
            if (!$row->publish($cids, false)) {
                $dbo = $row->_db;
                $this->setError($dbo->getErrorMsg());
                return false;
            }
        }
        return true;
    }

    function import($news_id, $csvfile_name) {
        jincimport('core.newsletterfactory');
        jincimport('core.newsletterimporter');

        $ninstance = NewsletterFactory::getInstance();
        $newsletter = $ninstance->loadNewsletter($news_id, false);
        $importer = NewsletterImporter::getInstance();
        return $importer->ImportFromCSV($newsletter, $csvfile_name);
    }

    function getSubscribers() {
        jincimport('core.newsletterfactory');
        $news_id = JRequest::getInt('news_id', 0);

        $ninstance = NewsletterFactory::getInstance();
        if ($newsletter = $ninstance->loadNewsletter($news_id, false)) {
            if ($acl = $newsletter->loadACL()) {
                return $acl->getEntriesName(ACL_ACCESS_SUBSCRIBER);
            }
        }
        return array();
    }

    function &getThemes() {
        jincimport('core.newsletterfactory');

        $themes = array();
        $ninstance = NewsletterFactory::getInstance();
        if ($themes = $ninstance->loadThemes()) {
            return $themes;
        }

        return $themes;
    }

    function &getAttributes() {
        jincimport('core.newsletterfactory');

        $attributes = array();
        $ninstance = NewsletterFactory::getInstance();
        if ($attributes = $ninstance->loadAttributesList()) {
            return $attributes;
        }

        return $attributes;
    }

}
?>
