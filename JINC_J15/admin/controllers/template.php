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

class JINCControllerTemplate extends JINCController {
    function __construct() {
        parent::__construct();
        $this->registerTask('add','edit');
    }

    function display( $tpl = null ) {
        parent::display( $tpl );
    }

    function edit() {
        JRequest::setVar('view', 'template');
        JRequest::setVar('layout', 'form');
        JRequest::setVar('hidemainmenu', 1);
        parent::display();
    }

    function save() {
        $model = $this->getModel('template');
        if ($model->store($post)) {
            $msg = JText::_( 'INFO_INF001' );
        } else {
            $msg = JText::_( 'ERROR_ERR027' );
        }

        $link = 'index.php?option=com_jinc&view=templates';
        $this->setRedirect($link, $msg);
    }

    function remove() {
        $model = $this->getModel('template');
        if($model->delete()) {
            $msg = JText::_( 'INFO_INF002' );
        } else {
            $msg = JText::_( 'ERROR_ERR028' );
        }

        $this->setRedirect( 'index.php?option=com_jinc&view=templates', $msg );
    }

    function cancel() {
        $msg = JText::_( 'WARN_CANCELLED' );
        $this->setRedirect( 'index.php?option=com_jinc&view=templates', $msg );
    }

    function jsonTemplateInfo() {
        header("Content-Type: text/plain; charset=UTF-8");
        jincimport('core.messagefactory');
        jincimport('utility.jsonresponse');
        $tem_id = JRequest::getInt('tem_id', 0);
        $minstance = MessageFactory::getInstance();
        if (! $template = $minstance->loadTemplate($tem_id))
            $template = new MessageTemplate(0);
        // Building JSON response
        $response = new JSONResponse();
        $response->set('subject', $template->get('subject'));
        $response->set('body', $template->get('body'));
        echo $response->toString();
    }

    function loadTemplate() {
        JRequest::setVar('view', 'templates');
        JRequest::setVar('hidemainmenu', 1);
        parent::display();
    }
}
?>
