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
jimport('joomla.application.component.view');

class JINCViewNotices extends JView {

    function display($tpl = null) {
        JHTML::stylesheet('jinc_admin.css', 'administrator/components/com_jinc/assets/css/');
        JToolBarHelper::title(JText::_('TITLE_NOTICES'), 'jinc');
        JToolBarHelper::deleteList();
        JToolBarHelper::editListX();
        JToolBarHelper::addNewX();
        jincimport('utility.jinchelper');
        JINCHelper::helpOnLine('73');

        $filter_order_Dir = JRequest::getString('filter_order_Dir', 'asc');
        $filter_order = JRequest::getString('filter_order', 'noti_name');
        $this->assignRef('filter_order_Dir', $filter_order_Dir);
        $this->assignRef('filter_order', $filter_order);

        // Get data from the model
        $items = & $this->get('Data');
        $pagination = & $this->get('Pagination');

        $this->assignRef('items', $items);
        $this->assignRef('pagination', $pagination);

        parent::display($tpl);
    }

}