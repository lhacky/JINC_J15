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

class JINCModelTemplates extends JModel {
    var $_data;
    /**
     * Items total
     * @var integer
     */
    var $_total = null;

    /**
     * Pagination object
     * @var object
     */
    var $_pagination = null;

    function __construct() {
        parent::__construct();

        global $mainframe, $option;
        $context = 'com_jinc.templates';

        // Get pagination request variables
        $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart = $mainframe->getUserStateFromRequest($context.'.limitstart', 'limitstart', 0, 'int');
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

        // Getting order filter from request, if found, or from session
        $filter_order_Dir = $mainframe->getUserStateFromRequest($context.'.filter_order_Dir', 'filter_order_Dir', 'asc', 'string' );
        $filter_order     = $mainframe->getUserStateFromRequest($context.'.filter_order', 'filter_order', 'tem_name', 'string' );

        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);
        $this->setState('filter_order_Dir', $filter_order_Dir);
        $this->setState('filter_order', $filter_order);
    }

    function _buildQuery() {
        $filter_order_Dir = JRequest::getString('filter_order_Dir', 'asc');
        $filter_order     = JRequest::getString('filter_order', 'tem_name');
        $filter_order_Dir = $this->getState('filter_order_Dir');
        $filter_order     = $this->getState('filter_order');

        $query = 'SELECT t.tem_id, t.tem_name, t.tem_subject, t.tem_body '.
            'FROM #__jinc_template t';
        $query .= ' ORDER BY '. $filter_order .' ' . $filter_order_Dir;
        return $query;
    }

    function & getData() {
    // if data hasn't already been obtained, load it
        if (empty($this->_data)) {
            $query = $this->_buildQuery();
            $this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
        }
        return $this->_data;
    }

    function getTotal() {
    // Load the content if it doesn't already exist
        if (empty($this->_total)) {
            $query = $this->_buildQuery();
            $this->_total = $this->_getListCount($query);
        }
        return $this->_total;
    }

    function getPagination() {
    // Load the content if it doesn't already exist
        if (empty($this->_pagination)) {
            $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
        }
        return $this->_pagination;
    }
}
