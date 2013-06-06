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

class JINCControllerMessage extends JINCController {
    function __construct() {
        parent::__construct();
        $this->registerTask('add','edit');
    }

    function display( $tpl = null ) {
        jincimport('core.newsletterfactory');

        $ninstance = NewsletterFactory::getInstance();
        if($newsletters = $ninstance->loadNames()) {
            $empty_filter = array("news_id" => "0", "news_name" => JText::_( 'SELECTION_NEWSLETTER' ));
            array_unshift($newsletters, $empty_filter);
        } else {
            $newsletters = array(array("news_id" => "0", "news_name" => JText::_( 'SELECTION_NEWSLETTER' )));
        }

        $view =& $this->getView('messages', 'html');
        $view->assignRef('newsletters', $newsletters);
        parent::display( $tpl );
    }

    function edit() {
        JRequest::setVar('view', 'message');
        JRequest::setVar('layout', 'form');
        JRequest::setVar('hidemainmenu', 1);

        jincimport('core.newsletterfactory');
        jincimport('core.messagefactory');

        $ninstance = NewsletterFactory::getInstance();

        if($newsletters = $ninstance->loadNames()) {
            $empty_filter = array("news_id" => "0", "news_name" => JText::_( 'SELECTION_NEWSLETTER' ));
            array_unshift($newsletters, $empty_filter);
        } else {
            $newsletters = array(array("news_id" => "0", "news_name" => JText::_( 'SELECTION_NEWSLETTER' )));
        }
        $view =& $this->getView('message', 'html');
        $view->assignRef('newsletters', $newsletters);

        $minstance = MessageFactory::getInstance();
        if ($templates =& $minstance->loadTemplateNames()) {
            $empty_filter = array("tem_id" => "0", "tem_name" => JText::_( 'TEMPLATE_SELECT' ));
            array_unshift($templates, $empty_filter);
        } else {
            $templates = array(array("tem_id" => "0", "tem_name" => JText::_( 'TEMPLATE_SELECT' )));
        }
        $view =& $this->getView('message', 'html');
        $view->assignRef('templates', $templates);

        parent::display();
    }

    function save() {
        $model = $this->getModel('message');
        if ($model->store($post)) {
            $msg = JText::_( 'INFO_INF001' );
        } else {
            $msg = JText::_( 'ERROR_ERR027' );
        }

        $link = 'index.php?option=com_jinc&controller=message&view=messages';
        $this->setRedirect($link, $msg);
    }

    function saveAndSend() {
        $model = $this->getModel('message');
        if ($model->store($post)) {
            $msg = JText::_( 'INFO_INF001' );
            $link = 'index.php?option=com_jinc&view=message&layout=send&msgid=' . $model->getId();
            $this->setRedirect($link, $msg);
        } else {
            $link = 'index.php?option=com_jinc&controller=message&view=messages';
            $this->setRedirect($link, $msg);
            $msg = JText::_( 'ERROR_ERR027' );
        }

    }

    function remove() {
        $model = $this->getModel('message');
        if($model->delete()) {
            $msg = JText::_( 'INFO_INF002' );
        } else {
            $msg = JText::_( 'ERROR_ERR028' );
        }

        $this->setRedirect( 'index.php?option=com_jinc&controller=message&view=messages', $msg );
    }

    function cancel() {
        $msg = JText::_( 'WARN_CANCELLED' );
        $this->setRedirect( 'index.php?option=com_jinc&controller=message&view=messages', $msg );
    }

    function send() {
        header("Content-Type: text/plain; charset=UTF-8");
        $msgid = JRequest::getInt( 'msgid', 0);
        $client_id = JRequest::getString( 'client_id', '');
        $start = JRequest::getInt( 'start', 0);
        $model = $this->getModel('message');

        echo $model->send($msgid, $client_id);
    }

    function pause() {
        header("Content-Type: text/plain; charset=UTF-8");
        $msgid = JRequest::getInt( 'msgid', 0);
        $model = $this->getModel('message');

        echo $model->pause($msgid);
    }

    function stop() {
        header("Content-Type: text/plain; charset=UTF-8");
        $msgid = JRequest::getInt( 'msgid', 0);
        $model = $this->getModel('message');

        echo $model->stop($msgid);
    }

    function preview() {
        $msgid = JRequest::getInt( 'msgid', 0, 'GET');
        $model = $this->getModel('message');
        $link = 'index.php?option=com_jinc&controller=message&view=messages';
        if (! ($result = $model->preview($msgid))) {
            $msg =  JText::_($model->getError());
            $this->setRedirect( $link, $msg );
        } else {
            $msg =  JText::_('SUCC_SEND_PREVIEW') . ' ' . implode(', ', $result);
        }

        $this->setRedirect( $link, $msg );
    }
}
?>
