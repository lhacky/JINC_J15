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

class JINCModelSubscribers extends JModel {
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
        $context = 'com_jinc.subscribers';

        // Get pagination request variables
        $limit          = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart     = $mainframe->getUserStateFromRequest($context.'.limitstart', 'limitstart', 0, 'int');
        // Getting search filter from request, if found, or from session
        $filter_news_id = $mainframe->getUserStateFromRequest($context.'.filter_news_id', 'filter_news_id', '', 'int' );
        $filter_state   = $mainframe->getUserStateFromRequest($context.'.filter_state', 'filter_state', '', 'word' );
        // Getting order filter from request, if found, or from session
        $filter_order_Dir = $mainframe->getUserStateFromRequest($context.'.filter_order_Dir', 'filter_order_Dir', 'asc', 'string' );
        $filter_order     = $mainframe->getUserStateFromRequest($context.'.filter_order', 'filter_order', 'news_name', 'string' );

        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);
        $this->setState('filter_news_id', $filter_news_id);
        $this->setState('filter_order_Dir', $filter_order_Dir);
        $this->setState('filter_order', $filter_order);
    }

    function _buildQuery() {
        jincimport('utility.servicelocator');
        $servicelocator = ServiceLocator::getInstance();
        $logger = $servicelocator->getLogger();

        // $filter_order_Dir = JRequest::getString('filter_order_Dir', 'asc');
        // $filter_order     = JRequest::getString('filter_order', 'news_name');
        $filter_order_Dir = $this->getState('filter_order_Dir');
        $filter_order     = $this->getState('filter_order');
        $query = 'SELECT s.subs_id, n.news_name, s.subs_news_id, ' .
            'UNIX_TIMESTAMP(s.subs_datasub) AS subs_datasub, ' .
            'u.id, u.email, u.name, '.
            'p.pub_id, p.pub_email, p.pub_random, p.pub_news_id, ' .
            'UNIX_TIMESTAMP(p.pub_datasub) AS pub_datasub, ' .
            'CONCAT_WS(\'\',p.pub_email, u.email) as subs_email, ' .
            'CONCAT_WS(\'\',\'\', u.username) as subs_name, ' .
            'CONCAT_WS(\'\',p.pub_datasub, s.subs_datasub) as subs_data ' .
            'FROM #__jinc_newsletter n  ' .
            'LEFT JOIN #__jinc_subscriber s ON n.news_id = s.subs_news_id ' .
            'LEFT JOIN #__users u ON s.subs_user_id = u.id ' .
            'LEFT JOIN #__jinc_public_subscriber p ON n.news_id = p.pub_news_id ';
        $where = array();
        $filter_news_id = $this->getState('filter_news_id');
        if ($filter_news_id) {
            $where[] = 'n.news_id = ' . $filter_news_id;
        }
        $where = count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '';
        $query .= $where;
        $query .= ' ORDER BY '. $filter_order .' ' . $filter_order_Dir;
        $logger->debug('JINCModelSubscribers: executing query '. $query);
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

    function getFilterNewsId() {
        return $this->getState('filter_news_id');
    }

    function getFilters() {
        $filters = array();
        $filters["filter_news_id"] = $this->getState('filter_news_id');
        return $filters;
    }
}
