<?php
/**
 * @version		$Id: group.php 2010-01-19 12:01:47Z lhacky $
 * @package		JINC
 * @subpackage          Core
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

/**
 * Requiring PHP libraries and defining constants
 */
require_once 'jincobject.php';

/**
 * Group class, defining a user group in order to define ACL for newsletter
 * access.
 *
 * @package		JINC
 * @subpackage          Core
 * @since		0.6
 */

class Group extends JINCObject {
/**
 * The group identifier
 *
 * @var		The group identifier
 * @access	protected
 * @since	0.6
 */
    var $id;
    /**
     * The group name
     *
     * @var		The group name
     * @access	protected
     * @since	0.6
     */
    var $name = '';
    /**
     * The group description
     *
     * @var		The group description
     * @access	protected
     * @since	0.6
     */
    var $descr = '';

    /**
     * The Group constructor. A group can be constructed directly using the
     * constructor or using the GroupFactory class.
     *
     * @access	public
     * @param   integer grp_id The group identifier.
     * @return	Group
     * @since	0.6
     * @see     GroupFactory
     */
    function Group($grp_id) {
        parent::JObject();
        $this->set('id', $grp_id);
    }

    /**
     * Adds a user to a group.
     *
     * @access	public
     * @param   integer user_id The user identifier.
     * @return	true or false if something wrong
     * @since	0.6
     */
    function addUser($user_id) {
        jincimport('utility.servicelocator');
        $servicelocator = ServiceLocator::getInstance();
        $logger = $servicelocator->getLogger();

        $query = 'INSERT IGNORE INTO #__jinc_membership ' .
            '(mem_grp_id, mem_user_id) '.
            'VALUES (' . (int) $this->get('id') . ', ' . (int) $user_id . ')';
        $logger->debug('Group: Executing query: ' . $query);
        $dbo =& JFactory::getDBO();
        $dbo->setQuery($query);
        if (! $dbo->query() ) {
            $this->setError('ERR017');
            return false;
        }
        return true;
    }

    /**
     * Removes a user from a group.
     *
     * @access	public
     * @param   integer user_id The user identifier.
     * @return	true or false if something wrong
     * @since	0.6
     */
    function removeUser($user_id) {
        jincimport('utility.servicelocator');
        $servicelocator = ServiceLocator::getInstance();
        $logger = $servicelocator->getLogger();

        $query = 'DELETE FROM #__jinc_membership ' .
            'WHERE mem_user_id = ' . (int) $user_id . ' AND ' .
            'mem_grp_id = ' . (int) $this->get('id');
        $logger->debug('Group: Executing query: ' . $query);
        $dbo =& JFactory::getDBO();
        $dbo->setQuery($query);
        if (! $dbo->query() ) {
            $this->setError('ERR027');
            return false;
        }
        return true;
    }

    /**
     * Verify if a user is a member of the group
     *
     * @access	public
     * @param   integer user_id The user identifier.
     * @return	true if user belongs to the group
     * @since	0.6
     */
    function isMember($user_id) {
        jincimport('utility.servicelocator');
        $servicelocator = ServiceLocator::getInstance();
        $logger = $servicelocator->getLogger();

        $query = 'SELECT mem_id FROM #__jinc_membership ' .
            'WHERE mem_grp_id = ' . (int) $this->get('id') . ' ' .
            'AND mem_user_id = ' . (int) $user_id;
        $logger->debug('Group: Executing query: ' . $query);
        $dbo =& JFactory::getDBO();
        $dbo->setQuery($query);
        if ($result = $dbo->loadAssocList()) {
            if (! empty ($result)) {
                return true;
            }
        }
        return false;
    }
}
?>
