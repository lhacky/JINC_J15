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

class JINCModelGroup extends JModel {
    function __construct() {
        parent::__construct();
        $array = JRequest::getVar('cid', 0, '', 'array');
        $this->setId((int)$array[0]);
    }

    function setId($id) {
        $this->_id = $id;
        $this->_data = null;
    }

    function &getData() {
        if (empty( $this->_data )) {
            $query = 'SELECT grp_id, grp_name, grp_descr FROM #__jinc_group '.
                '  WHERE grp_id = ' . (int) $this->_id;
            $this->_db->setQuery($query);
            $this->_data = $this->_db->loadObject();
        }
        if (!$this->_data) {
            $this->_data = new stdClass();
            $this->_data->rea_id = 0;
            $this->_data->rea_name = "";
        }
        return $this->_data;
    }

    function store() {
        $row =& $this->getTable();
        $data = JRequest::get( 'post' );
        if (!$row->bind($data)) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        if (!$row->check()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        if (!$row->store()) {
            $dbo = $row->getDBO();
            $this->setError( $dbo->getErrorMsg() );
            return false;
        }
        return true;
    }

    function delete() {
        $cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
        $row =& $this->getTable();
        if (count( $cids )) {
            foreach($cids as $cid) {
                if (!$row->delete( $cid )) {
                    $this->setError($row->getErrorMsg());
                    return false;
                }
            }
        }
        return true;
    }

    function getMembers() {
        $query = "SELECT g.grp_name, u.name, u.username, u.email, u.id " .
            "FROM #__jinc_group AS g " .
            "INNER JOIN #__jinc_membership AS m ON g.grp_id = m.mem_grp_id " .
            "INNER JOIN #__users AS u on m.mem_user_id = u.id " .
            "WHERE g.grp_id = " . (int) $this->_id;

        $members = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
        return $members;
    }

    function removeMember($grp_id, $user_id) {
        jincimport('core.groupfactory');
        $gfactory = GroupFactory::getInstance();
        if (! $group = $gfactory->loadGroup($grp_id) ) {
            $this->setError('');
            return false;
        }

        if ( ! $group->removeUser((int) $user_id )) {
            $this->setError($group->getError());
            return false;
        }
        return true;
    }



    function addMember($grp_id, $ids) {
        jincimport('core.groupfactory');

        $gfactory = GroupFactory::getInstance();
        if (! $group = $gfactory->loadGroup($grp_id) ) {
            $this->setError('');
            return false;
        }
        
        if (count($ids)) {
            foreach ($ids as $user_id) {
                if ( ! $group->addUser((int) $user_id )) {
                    $this->setError($group->getError());
                    return false;
                }
            }
        }
        return true;
    }
}
?>
