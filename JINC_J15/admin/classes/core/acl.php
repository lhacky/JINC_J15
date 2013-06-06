<?php
/**
 * @version		$Id: ACL.php 4-mar-2010 13.57.26 lhacky $
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
require_once 'aclentry.php';
require_once 'groupfactory.php';

define('ACL_ACCESS_NO', 0);
define('ACL_ACCESS_SUBSCRIBER', 1);
define('ACL_ACCESS_AUTHOR', 2);
define('ACL_ACCESS_SENDER', 3);
define('ACL_ACCESS_ADMINISTRATOR', 4);
define('TYPE_USER', 1);
define('TYPE_GROUP', 2);

/**
 * ACL class, defining an Access Control List. An ACL is a list on ACL Entries
 * defining the access level for users or groups.
 *
 * @package		JINC
 * @subpackage          Core
 * @since		0.6
 */

class ACL {
/**
 * Array of ACL entries
 *
 * @var		ACL entries
 * @access	protected
 * @since	0.6
 */
    var $aclentries;

    /**
     * The ACL constructor.
     *
     * @access	public
     * @return	ACL
     * @since	0.6
     * @see     ACLEntry
     */
    function ACL() {
        $this->aclentries = array();
    }

    /**
     * Adding an entry to the ACL.
     *
     * @access	public
     * @param   ACLEntry $aclentry The ACL entry to add
     * @since	0.6
     * @see     ACLEntry
     */

    function addEntry($aclentry) {
        array_push($this->aclentries, $aclentry);
    }

    /**
     * Gets array of ACL entries in a role.
     *
     * @access public
     * @param integer $role_id
     * @return array ACL entries in a role
     * @since 0.6
     */
    function getEntries($role_id) {
        $entries = array();
        if (!empty ($this->aclentries)) {
            foreach ($this->aclentries as $entry) {
                if ( $entry->get('role_id') == $role_id)
                    array_push($entries, $entry);
            }
        }
        return $entries;
    }

    /**
     * Gets array of ACL entries in a role in name format.
     *
     * @access public
     * @param integer $role_id
     * @return array ACL entries in a role
     * @since 0.6
     */
    function getEntriesName($role_id) {
        $entries = array();
        if (!empty ($this->aclentries)) {
            foreach ($this->aclentries as $aclentry) {
                if ( $aclentry->get('role_id') == $role_id) {
                    $entry_name = array('id' => $aclentry->get('obj_id'), 'name' => $aclentry->get('obj_name'));
                    array_push($entries, $entry_name);
                }
            }
        }
        return $entries;
    }

    /**
     * Gets all ACL entries.
     *
     * @access public
     * @return array ACL entries
     * @since 0.6
     */
    function getAllEntries() {
        return $this->aclentries;
    }

    /**
     * Performs effective access of a user or a group for this ACL.
     *
     * @access public
     * @param   integer obj_id the object (a user of a group) identifier.
     * @param   integer obj_type the object type. See ACL_ACCESS_ constants.
     * @return integer The maximum access level of the user of group
     * @since 0.6
     */
    function performEffectiveAccess($obj_id, $obj_type) {
        $access_level = ACL_ACCESS_NO;
        if (!empty ($this->aclentries)) {
            foreach ($this->aclentries as $entry) {
                $e_obj_type = $entry->get('obj_type');
                $e_obj_id = $entry->get('obj_id');
                $e_role_id = $entry->get('role_id');

                if ($e_obj_type == $obj_type && $e_obj_id == $obj_id) {
                    $access_level = max($access_level, $e_role_id);
                }

                if ($obj_type == TYPE_USER && $e_obj_type == TYPE_GROUP) {
                    $gfactory = GroupFactory::getInstance();
                    if ($group = $gfactory->loadGroup($e_obj_id)) {
                        if ($group->isMember($obj_id))
                            $access_level = max($access_level, $entry->get('role_id'));
                    }
                }
            }
        }
        return  $access_level;
    }

    /**
     * Gets first user of group in a role.
     *
     * @access public
     * @return group identifier or false if no groups found
     * @since 0.6
     */
    function getFirstGroupInRole($role_id) {
        $group = array();
        if (!empty ($this->aclentries)) {
            foreach ($this->aclentries as $entry) {
                if ( $entry->get('role_id') == $role_id && $entry->get('obj_type') == TYPE_GROUP)
                    return $entry->get('obj_id');
            }
        }
        return false;
    }
}

?>
