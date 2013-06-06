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

class JINCModelGroups extends JModel {
    var $_names;

    function __construct() {
        parent::__construct();
        global $mainframe, $option;
        $context = 'com_jinc.groups';

        $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart = $mainframe->getUserStateFromRequest($context.'.limitstart', 'limitstart', 0, 'int');
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
        // Getting order filter from request, if found, or from session
        $filter_order_Dir = $mainframe->getUserStateFromRequest($context . '.filter_order_Dir', 'filter_order_Dir', 'asc', 'string' );
        $filter_order     = $mainframe->getUserStateFromRequest($context . '.filter_order', 'filter_order', 'grp_name', 'string' );

        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);
        $this->setState('filter_order_Dir', $filter_order_Dir);
        $this->setState('filter_order', $filter_order);
    }

    function _buildQuery() {
        // $filter_order_Dir = JRequest::getString('filter_order_Dir', 'asc');
        // $filter_order     = JRequest::getString('filter_order', 'grp_name');
        $filter_order_Dir = $this->getState('filter_order_Dir');
        $filter_order     = $this->getState('filter_order');
        
        $query = 'SELECT grp_id, grp_name, grp_descr, count(m.mem_user_id) as mem_number ' .
            'FROM #__jinc_group g ' .
            'LEFT JOIN #__jinc_membership m ON g.grp_id = m.mem_grp_id ' .
            'GROUP BY g.grp_id';
        $query .= ' ORDER BY '. $filter_order .' ' . $filter_order_Dir;
        return $query;
    }

    function & getData() {
        $query = $this->_buildQuery();
        $data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
        return $data;
    }

    function getTotal() {
        $query = $this->_buildQuery();
        $total = $this->_getListCount($query);
        return $total;
    }

    function getPagination() {
        jimport('joomla.html.pagination');
        $pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
        return $pagination;
    }
}
