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
jimport( 'joomla.application.component.view' );

class JINCViewStats extends JView {
    function display($tpl = null) {
        JHTML::stylesheet('jinc_admin.css', 'administrator/components/com_jinc/assets/css/');
        JToolBarHelper::title( JText::_('TITLE_STATISTICS'), 'jinc' );
        jincimport('utility.jinchelper');
        JINCHelper::helpOnLine('44');

        $session =& JFactory::getSession();
        $config =& JFactory::getConfig();
        $tzoffset = $config->getValue('config.offset');

        $news_id = JRequest::getInt('news_id', 0);
        $stat_type = JRequest::getInt('stat_type', 0);
        $time_type = JRequest::getInt('time_type', 0);

        $start_date = JRequest::getFloat('start_date', time());
        $end_date = JRequest::getFloat('end_date', time());
        $sdate =& JFactory::getDate($start_date, $tzoffset);
        $edate =& JFactory::getDate($end_date, $tzoffset);

        $values = $session->get('stats.values');
        $legend = $session->get('stats.legend');

        $this->assignRef('stat_type', $stat_type);
        $this->assignRef('time_type', $time_type);
        $this->assignRef('news_id', $news_id);
        $this->assignRef('start_time', $sdate);
        $this->assignRef('end_time', $edate);
        $this->assignRef('stats_values', $values);
        $this->assignRef('stats_legend', $legend);
        parent::display($tpl);
    }
}