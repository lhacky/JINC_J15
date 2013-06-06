<?php
/**
 * @version		$Id: groupfactory.php 2010-01-19 12:01:47Z lhacky $
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
require_once 'group.php';

/**
 * GroupFactory class, building Group objects from a group
 * ID getting information from database.
 * This class implements the Factory Design Pattern.
 *
 * @package		JINC
 * @subpackage          Core
 * @since		0.6
 */

class GroupFactory {
    function GroupFactory() { }

    function &getInstance() {
        static $instance = null;
        if (null === $instance) {
            $instance = new GroupFactory();
        }
        return $instance;
    }
/**
 * The group loader. It loads a group from his identifier.
 *
 * @access	public
 * @param	integer $grp_id the group identifier.
 * @return  The Group object or -1 if group not found or false if something wrong.
 * @since	0.6
 * @see     Group
 */
    function loadGroup($grp_id) {
        jincimport('utility.servicelocator');
        $servicelocator = ServiceLocator::getInstance();
        $logger = $servicelocator->getLogger();

        $query = 'SELECT grp_name, grp_descr ' .
            'FROM #__jinc_group g ' .
            'WHERE grp_id = ' . (int) $grp_id;
        $logger->debug('GroupFactory: Executing query: ' . $query);
        $dbo =& JFactory::getDBO();
        $dbo->setQuery($query);
        // Loading group information from database
        if ($result = $dbo->loadAssocList()) {
            if (empty ($result)) {
            // Group not found in database
                $logger->finer('GroupFactory: Group not found');
                return -1;
            }
            $group = $result[0];
        } else {
            return false;
        }
        // Creating Group
        $groupObj = new Group($grp_id);
        $groupObj->set('name', $group['grp_name']);
        $groupObj->set('descr', $group['grp_descr']);
        return $groupObj;
    }
}
?>
