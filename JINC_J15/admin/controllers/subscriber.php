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

class JINCControllerSubscriber extends JINCController {
    function __construct() {
        parent::__construct();            
    }

    function display( $tpl = null ) {
        jincimport('core.newsletterfactory');

        $ninstance = NewsletterFactory::getInstance();
        if($newsletters = $ninstance->loadNames()) {
            $empty_filter = array("news_id" => "", "news_name" => JText::_( 'SELECTION_NEWSLETTER' ));
            array_unshift($newsletters, $empty_filter);
        } else {
            $newsletters = array(array("news_id" => "0", "news_name" => JText::_( 'SELECTION_NEWSLETTER' )));
        }
        $view =& $this->getView('subscribers', 'html');
        $view->assignRef('newsletters', $newsletters);
        parent::display( $tpl );
    }
    
    function remove() {
        $model = $this->getModel('subscriber');
        if($model->delete()) {
            $msg = JText::_( 'INFO_INF002' );
        } else {
            $msg = JText::_( 'ERROR_ERR028' );
        }

        $this->setRedirect( 'index.php?option=com_jinc&controller=subscriber&view=subscribers', $msg );
    }

    function cancel() {
        $msg = JText::_( 'WARN_CANCELLED' );
        $this->setRedirect( 'index.php?option=com_jinc&controller=subscriber&view=subscribers', $msg );
    }

    function approve() {
        $model = $this->getModel('subscriber');
        if($model->approve()) {
            $msg = JText::_( 'INFO_INF009' );
        } else {
            $msg = JText::_( 'ERROR_ERR028' );
        }

        $this->setRedirect( 'index.php?option=com_jinc&controller=subscriber&view=subscribers', $msg );
    }
}
?>
