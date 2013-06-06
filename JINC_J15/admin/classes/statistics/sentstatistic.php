<?php
/**
 * @package		JINC
 * @subpackage          Statistics
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
require_once 'newsstatistic.php';

/**
 * SentStatistic class, defining a statistic on number of sent message of a
 * newsletter.
 *
 * @package		JINC
 * @subpackage          Graphics
 * @since		0.6
 */
class SentStatistic extends NewsletterStatistic {
    /**
     * SentStatistic constructor
     *
     * @param TimeLine $timeline Statistic time line
     */
    function SentStatistic($timeline) {
        parent::NewsletterStatistic($timeline);
    }

    /**
     * It performs the Statistic about sent messages of a newsletter.
     */
    function perform() {
        $this->clean();
        $timeline = $this->getTimeLine();
        $myformat = $timeline->getMySQLFormat();
        $phpformat = $timeline->getPHPFormat();
        $phplformat = $timeline->getPHPLeftLimitFormat();
        $phprformat = $timeline->getPHPRightLimitFormat();
        
        $query = "SELECT date_format(`stat_date`, '" . $myformat . "') as statdate, count(1) as nsent " .
             "FROM `#__jinc_stats_event` " .
             "WHERE `stat_date` >= '" . date($phplformat, $timeline->getStartTime()) . "' AND " .
             "`stat_date` <= '" . date($phprformat, $timeline->getEndTime()) . "' AND " .
             "`stat_type` = 2 AND " .
             "`stat_news_id` = " . (int) $this->getNewsId() . " GROUP BY 1";

        $dbo =& JFactory::getDBO();
        $dbo->setQuery($query);
        if ($result = $dbo->loadAssocList()) {
            for ($i = 0 ; $i < count($result) ; $i++) {
                $row = $result[$i];
                $date = $row['statdate'];
                $this->_values[$date] = $row['nsent'];
            }
        } else {
            return false;
        }
        return true;
    }
}
?>