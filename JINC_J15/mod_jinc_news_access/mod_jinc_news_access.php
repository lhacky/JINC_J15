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
$user =& JFactory::getUser();

$query = 'SELECT DISTINCT(n.news_id), n.news_name ' .
    'FROM #__jinc_newsletter n ' .
    'LEFT JOIN #__jinc_access     a ON n.news_id = a.acc_news_id ' .
    'LEFT JOIN #__jinc_group      g ON a.acc_grp_id = g.grp_id ' .
    'LEFT JOIN #__jinc_membership m ON g.grp_id = m.mem_grp_id ' .
    'LEFT JOIN #__users           u ON m.mem_user_id = u.id ' .
    'WHERE n.published = 1 ';

if ($user->guest) {
    $query .= 'AND (n.news_type < 1)';
} else {
    $query .= 'AND (n.news_type < 2 OR u.id = ' . (int) $user->id . ' )';
}

$dbo = JFactory::getDBO();
$dbo->setQuery( $query );
$news = $dbo->loadObjectList();
echo "<br>";

if (! empty($news)) {
    foreach ($news as $row ) {
        $link = "index.php?option=com_jinc&view=newsletter&news_id=". $row->news_id;
        echo "<a href=". $link. ">";
        echo $row->news_name;
        echo "</a><br>";
    }
}
?>
