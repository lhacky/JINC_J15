<?php
/**
 * @version		$Id: ACLEntry.php 4-mar-2010 10.17.56 lhacky $
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
 * ACLEntry class, defining an ACL entry.
 *
 * @package		JINC
 * @subpackage          Core
 * @since		0.6
 */

class ACLEntry extends JINCObject {
/**
 * The object identifier
 *
 * @var		The object identifier
 * @access	protected
 * @since	0.6
 */
    var $obj_id = 0;

    /**
     * The object name
     *
     * @var		The object name
     * @access	protected
     * @since	0.6
     */

    var $obj_name = '';

    /**
     * The object type identifier
     *
     * @var		The object type identifier
     * @access	protected
     * @since	0.6
     */
    var $obj_type = 0;

    /**
     * The role identificator
     *
     * @var		The role identifier
     * @access	protected
     * @since	0.6
     */
    var $role_id = 0;

    /**
     * The ACLEntry constructor.
     *
     * @access	public
     * @param   integer obj_id the object (a user of a group) identifier.
     * @param   integer obj_type the object type. See ACCESS_ variables.
     * @param   integer role_id the role identifier. See TYPE_ variables.
     * @return	ACLEntry
     * @since	0.6
     */
    function ACLEntry($obj_id, $obj_type, $role_id, $obj_name = '') {
        parent::JObject();
        $this->set('obj_id', $obj_id);
        $this->set('obj_type', $obj_type);
        $this->set('role_id', $role_id);
        $this->set('obj_name', $obj_name);
    }
}
?>