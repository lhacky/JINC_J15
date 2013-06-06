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
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class JINCModelTemplate extends JModel {
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
            $query = 'SELECT tem_id, tem_name, tem_subject, tem_body '.
                'FROM #__jinc_template WHERE tem_id = ' . (int) $this->_id;
            $this->_db->setQuery($query);
            $this->_data = $this->_db->loadObject();
        }
        if (!$this->_data) {
            $this->_data = new stdClass();
            $this->_data->tem_id = 0;
            $this->_data->tem_name = "";
            $this->_data->tem_subject = "";
            $this->_data->tem_body = null;
        }
        return $this->_data;
    }

    function store() {
        $row =& $this->getTable();
        $data = JRequest::get( 'post' );
        if (!$row->bind($data)) {
            $dbo = $row->getDBO();
            $this->setError($dbo->getErrorMsg());
            return false;
        }
        $details = array();
        $details['tem_body'] = JRequest::getVar( 'tem_body', '', 'post', 'string', JREQUEST_ALLOWRAW);
        $row->bind($details);
        
        if (!$row->check()) {
            $dbo = $row->getDBO();
            $this->setError($dbo->getErrorMsg());
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
}
?>
