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

class JINCControllerNotice extends JINCController {
    function __construct() {
        parent::__construct();
        $this->registerTask('add','edit');
    }

    function display( $tpl = null ) {
        parent::display( $tpl );
    }

    function edit() {
        JRequest::setVar('view', 'notice');
        JRequest::setVar('layout', 'form');
        JRequest::setVar('hidemainmenu', 1);
        parent::display();
    }

    function save() {
        $model = $this->getModel('notice');
        if ($model->store($post)) {
            $msg = JText::_( 'INFO_INF001' );
        } else {
            $msg = JText::_( 'ERROR_ERR027' );
        }

        $link = 'index.php?option=com_jinc&view=notices';
        $this->setRedirect($link, $msg);
    }

    function remove() {
        $model = $this->getModel('notice');
        if($model->delete()) {
            $msg = JText::_( 'INFO_INF002' );
        } else {
            $msg = JText::_( 'ERROR_ERR028' );
        }

        $this->setRedirect( 'index.php?option=com_jinc&view=notices', $msg );
    }

    function cancel() {
        $msg = JText::_( 'WARN_CANCELLED' );
        $this->setRedirect( 'index.php?option=com_jinc&view=notices', $msg );
    }
}
?>
