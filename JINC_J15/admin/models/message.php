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
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class JINCModelMessage extends JModel {

    function __construct() {
        parent::__construct();
        $array = JRequest::getVar('cid', 0, '', 'array');
        $this->setId((int) $array[0]);
    }

    function setId($id) {
        $this->_id = $id;
        $this->_data = null;
    }

    function getId() {
        return $this->_id;
    }

    function &getData() {
        jincimport('core.messagefactory');
        jincimport('core.message');
        $msg_id = JRequest::getInt('msgid', 0);
        $minstance = MessageFactory::getInstance();
        if (!$message = $minstance->loadMessage($msg_id)) {
            $message = new PersonalMessage(0);
        }
        return $message;
    }

    function getProcess() {
        jincimport('core.messagefactory');
        jincimport('utility.servicelocator');
        $servicelocator = ServiceLocator::getInstance();
        $logger = $servicelocator->getLogger();

        $msg_id = JRequest::getInt('msgid', 0);
        $minstance = MessageFactory::getInstance();

        return $minstance->loadProcess($msg_id, true);
    }

    function getHistory() {
        jincimport('core.messagefactory');
        jincimport('utility.servicelocator');
        $servicelocator = ServiceLocator::getInstance();
        $logger = $servicelocator->getLogger();

        $msg_id = JRequest::getInt('msgid', 0);
        $minstance = MessageFactory::getInstance();

        if (!($history = $minstance->loadHistory($msg_id))) {
            $history = array();
        }
        return $history;
    }

    function getNewsletter() {
        jincimport('core.newsletterfactory');
        jincimport('core.messagefactory');
        jincimport('utility.servicelocator');
        $servicelocator = ServiceLocator::getInstance();
        $logger = $servicelocator->getLogger();

        $msg_id = JRequest::getInt('msgid', 0);
        $minstance = MessageFactory::getInstance();
        $ninstance = NewsletterFactory::getInstance();

        if ($message = $minstance->loadMessage($msg_id)) {
            $news_id = $message->get('news_id');
            if ($newsletter = $ninstance->loadNewsletter($news_id))
                return $newsletter;
        }
        return false;
    }

    function store() {
        $row = & $this->getTable();
        $data = JRequest::get('post');
        if (!$row->bind($data)) {
            $dbo = $row->getDBO();
            $this->setError($dbo->getErrorMsg());
            return false;
        }
        $details = array();
        $details['msg_body'] = JRequest::getVar('msg_body', '', 'post', 'string', JREQUEST_ALLOWRAW);

        $params = JRequest::getVar( 'msg_attachment', null, 'post', 'array' );
        if (is_array($params)) {
            $txt = array ();
            $i = 0;
            foreach ($params as $k => $v) {
                if (strlen($v)) {
                    $txt[] = "$i=$v";
                    $i++;
                }
            }
            $details['msg_attachment'] = implode("\n", $txt);
        }

        $row->bind($details);

        if (!$row->check()) {
            $dbo = $row->getDBO();
            $this->setError($dbo->getErrorMsg());
            return false;
        }
        if (!$row->store()) {
            $dbo = $row->getDBO();
            $this->setError($dbo->getErrorMsg());
            return false;
        }
        $this->setId($row->get('msg_id'));
        return true;
    }

    function delete() {
        $cids = JRequest::getVar('cid', array(0), 'post', 'array');
        $row = & $this->getTable();
        if (count($cids)) {
            foreach ($cids as $cid) {
                if (!$row->delete($cid)) {
                    $this->setError($row->getErrorMsg());
                    return false;
                }
            }
        }
        return true;
    }

    function send($msg_id, $client_id, $restart = 0) {
        jincimport('core.messagefactory');
        jincimport('utility.jsonresponse');
        jincimport('utility.servicelocator');
        $servicelocator = ServiceLocator::getInstance();
        $logger = $servicelocator->getLogger();

        $success = true;

        $minstance = MessageFactory::getInstance();
        if ($process = $minstance->loadProcess($msg_id, true)) {
            if (!$process->play($client_id)) {
                $this->setError($process->getError());
                $success = false;
            }
        } else {
            $this->setError('ERROR_ERR041');
            $success = false;
        }

        $response = new JSONResponse();
        if ($success) {
            $response->set('status', $process->get('status'));
            $response->set('tot_recipients', $process->get('tot_recipients'));
            $response->set('sent_messages', $process->get('sent_messages'));
            $response->set('sent_success', $process->get('sent_success'));
            $response->set('last_subscriber_time', $process->get('last_subscriber_time'));
            $response->set('server_time', date('r', time()));
            $response->set('start_time', date('r', $process->get('start_time')));
        } else {
            $response->set('status', -1);
            $response->set('errcode', $this->getError());
            $response->set('errmsg', JText::_($this->getError()));
        }
        $logger->debug('JSON: ' . $response->toString());
        return $response->toString();
    }

    function pause($msg_id) {
        jincimport('core.messagefactory');
        jincimport('utility.jsonresponse');
        jincimport('utility.servicelocator');
        $servicelocator = ServiceLocator::getInstance();
        $logger = $servicelocator->getLogger();

        $success = true;

        $minstance = MessageFactory::getInstance();
        if ($process = $minstance->loadProcess($msg_id, true)) {
            if (!$process->pause()) {
                $this->setError($process->getError());
                $success = false;
            }
        } else {
            $this->setError('ERROR_ERR041');
            $success = false;
        }

        $response = new JSONResponse();
        if ($success) {
            $response->set('status', $process->get('status'));
        } else {
            $response->set('status', -1);
            $response->set('errcode', $this->getError());
            $response->set('errmsg', JText::_($this->getError()));
        }
        $logger->debug('JSON: ' . $response->toString());
        return $response->toString();
    }

    function stop($msg_id) {
        jincimport('core.messagefactory');
        jincimport('utility.jsonresponse');
        jincimport('utility.servicelocator');
        $servicelocator = ServiceLocator::getInstance();
        $logger = $servicelocator->getLogger();

        $success = true;

        $minstance = MessageFactory::getInstance();
        if ($process = $minstance->loadProcess($msg_id, true)) {
            if (!$process->stop()) {
                $this->setError($process->getError());
                $success = false;
            }
        } else {
            $this->setError('ERROR_ERR041');
            $success = false;
        }

        $response = new JSONResponse();
        if ($success) {
            $response->set('status', $process->get('status'));
        } else {
            $response->set('status', -1);
            $response->set('errcode', $this->getError());
            $response->set('errmsg', JText::_($this->getError()));
        }
        $logger->debug('JSON: ' . $response->toString());
        return $response->toString();
    }

    function preview($msg_id) {
        jincimport('core.messagefactory');
        jincimport('utility.jsonresponse');
        jincimport('utility.servicelocator');
        $servicelocator = ServiceLocator::getInstance();
        $logger = $servicelocator->getLogger();

        $minstance = MessageFactory::getInstance();
        if (!$message = $minstance->loadMessage($msg_id)) {
            $this->setError('ERROR_ERR037');
            return false;
        }

        if (!( $result = $message->preview())) {
            $this->setError($message->getError());
            return false;
        }
        return $result;
    }

}
?>
