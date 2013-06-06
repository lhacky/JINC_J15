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

class JINCControllerNewsletter extends JINCController {

    function __construct() {
        parent::__construct();
        $this->registerTask('add', 'edit');
    }

    function edit() {
        JRequest::setVar('view', 'newsletter');
        JRequest::setVar('layout', 'form');
        JRequest::setVar('hidemainmenu', 1);

        jincimport('core.messagefactory');
        jincimport('core.newsletterfactory');

        $minstance = MessageFactory::getInstance();
        if ($templates = & $minstance->loadTemplateNames()) {
            $empty_filter = array("tem_id" => "", "tem_name" => JText::_('SELECTION_TEMPLATE'));
            array_unshift($templates, $empty_filter);
        } else {
            $templates = array(array("tem_id" => "", "tem_name" => JText::_('SELECTION_TEMPLATE')));
        }

        $ninstance = NewsletterFactory::getInstance();
        if ($notices = & $ninstance->loadNoticeNames()) {
            $empty_filter = array("noti_id" => "", "noti_name" => JText::_('SELECTION_NOTICE'));
            array_unshift($notices, $empty_filter);
        } else {
            $notices = array(array("noti_id" => "", "noti_name" => JText::_('SELECTION_NOTICE')));
        }

        $view = & $this->getView('newsletter', 'html');
        $view->assignRef('templates', $templates);
        $view->assignRef('notices', $notices);
        parent::display();
    }

    function save() {
        $model = $this->getModel('newsletter');
        if ($model->store($post)) {
            $msg = JText::_('INFO_INF001');
        } else {
            $msg = JText::_('ERROR_ERR027');
        }

        $link = 'index.php?option=com_jinc&view=newsletters';
        $this->setRedirect($link, $msg);
    }

    function remove() {
        $model = $this->getModel('newsletter');
        if ($model->delete()) {
            $msg = JText::_('INFO_INF002');
        } else {
            $msg = JText::_('ERROR_ERR028');
        }

        $this->setRedirect('index.php?option=com_jinc&view=newsletters', $msg);
    }

    function cancel() {
        $msg = JText::_('WARN_CANCELLED');
        $this->setRedirect('index.php?option=com_jinc&view=newsletters', $msg);
    }

    function publish() {
        $model = $this->getModel('newsletter');
        if ($model->publish()) {
            $msg = JText::_('INFO_INF005');
        } else {
            $msg = JText::_('ERROR_ERR031');
        }
        $this->setRedirect('index.php?option=com_jinc&view=newsletters', $msg);
    }

    function unpublish() {
        $model = $this->getModel('newsletter');
        if ($model->unpublish()) {
            $msg = JText::_('INFO_INF006');
        } else {
            $msg = JText::_('ERROR_ERR032');
        }
        $this->setRedirect('index.php?option=com_jinc&view=newsletters', $msg);
    }

    function addRole() {
        $news_id = JRequest::getInt('news_id', 0);
        $access = JRequest::getInt('access', 0, '');
        JRequest::setVar('view', 'groups');
        JRequest::setVar('layout', 'selection');
        JRequest::setVar('hidemainmenu', 1);
        parent::display();
    }

    function statsRender() {
        jincimport('graphics.builtinrenderer');
        $session = JFactory::getSession();
        $values = $session->get('stats.values');
        $legend = $session->get('stats.legend');
        $renderer = new BuiltInRenderer($values, $legend);
        $renderer->render();
    }

    function stats() {
        jincimport('statistics.statisticfactory');
        jincimport('core.newsletterfactory');

        $session = & JFactory::getSession();
        $config = & JFactory::getConfig();
        $tzoffset = $config->getValue('config.offset');

        $news_id = JRequest::getInt('news_id', 0);
        $stat_type = JRequest::getInt('stat_type', 0);
        $time_type = JRequest::getInt('time_type', 0);
        $start_date = JRequest::getString('start_date');
        $end_date = JRequest::getString('end_date');
        $sdate = & JFactory::getDate($start_date, $tzoffset);
        $edate = & JFactory::getDate($end_date, $tzoffset);

        $stat = StatisticFactory::getStatistic($stat_type, $time_type, $sdate->toUNIX(), $edate->toUNIX(), $news_id);
        $values = $stat->getValues();
        $legend = $stat->getTimeValues();
        $session->set('stats.values', $values);
        $session->set('stats.legend', $legend);

        $timeline = $stat->getTimeLine();
        JRequest::setVar('start_date', $timeline->getStartTime());
        JRequest::setVar('end_date', $timeline->getEndTime());
        $stattypes = StatisticFactory::getTypeList();
        $timetypes = StatisticFactory::getTimeList();
        $time_format = $timeline->getJFormat();

        JRequest::setVar('view', 'stats');
        $view = & $this->getView('stats', 'html');

        $ninstance = NewsletterFactory::getInstance();
        if ($newsletters = $ninstance->loadNames()) {
            $empty_filter = array("news_id" => "0", "news_name" => JText::_('SELECTION_NEWSLETTER'));
            array_unshift($newsletters, $empty_filter);
        } else {
            $newsletters = array(array("news_id" => "0", "news_name" => JText::_('SELECTION_NEWSLETTER')));
        }

        $view->assignRef('time_format', $time_format);
        $view->assignRef('stattypes', $stattypes);
        $view->assignRef('timetypes', $timetypes);
        $view->assignRef('newsletters', $newsletters);
        parent::display();
    }

    function import() {
        JRequest::setVar('view', 'newsletter');
        $news_id = JRequest::getInt('news_id', 0);
        $csvfile = JRequest::getVar('csvfile', array(), 'FILES');
        $view = & $this->getView('newsletter', 'html');
        if (isset($csvfile['tmp_name']) && $csvfile['tmp_name'] != '' && !is_null($csvfile['tmp_name'])) {
            $mime = $csvfile['type'];
            if (true || $mime == 'application/x-csv' || $mime == 'text/csv' ||
                    $mime == 'application/csv' || $mime == 'application/excel' ||
                    $mime == 'application/vnd.ms-excel' || $mime == 'application/vnd.msexcel') {
                $model = $this->getModel('newsletter');
                if ($result = $model->import($news_id, $csvfile['tmp_name'])) {
                    JRequest::setVar('layout', 'import');
                    $view->assignRef('result', $result);
                    parent::display();
                } else {
                    $msg = JText::_('ERROR_ERR002');
                    $link = 'index.php?option=com_jinc&view=newsletter&layout=uploadcsv&news_id=' . $news_id;
                    $this->setRedirect($link, $msg);
                }
            } else {
                $msg = JText::_('ERROR_ERR014') . ' ' . $csvfile['type'];
                $link = 'index.php?option=com_jinc&view=newsletter&layout=uploadcsv&news_id=' . $news_id;
                $this->setRedirect($link, $msg);
            }
        } else {
            $msg = JText::_('ERROR_ERR002');
            $link = 'index.php?option=com_jinc&view=newsletter&layout=uploadcsv&news_id=' . $news_id;
            $this->setRedirect($link, $msg);
        }
    }

    function jsonDefaultTemplate() {
        header("Content-Type: text/plain; charset=UTF-8");
        jincimport('core.newsletterfactory');
        jincimport('utility.jsonresponse');
        jincimport('utility.jinchtmlhelper');
        $news_id = JRequest::getInt('news_id', 0);
        $tem_id = 0;
        $tag_string = '';
        $ninstance = NewsletterFactory::getInstance();
        if ($newsletter = $ninstance->loadNewsletter($news_id, false)) {
            $tem_id = $newsletter->get('default_template');
            $taglist = $newsletter->getTagsList();
            $tag_string = JINCHTMLHelper::showTags($taglist, array('CON'));
        }
        // Building JSON response
        $response = new JSONResponse();
        $response->set('tem_id', (int) $tem_id);

        $response->set('tag_string', $tag_string);
        echo $response->toString();
    }

    function listAttributes() {
        JRequest::setVar('view', 'attributes');
        JRequest::setVar('hidemainmenu', 1);
        parent::display();
    }

    function createAttribute() {
        header("Content-Type: text/plain; charset=UTF-8");
        $attr_name = strtolower(JRequest::getString('attr_name', ''));
        $attr_description = JRequest::getString('attr_description', '');
        $attr_type = JRequest::getInt('attr_type', 0);
        $attr_name_i18n = strtoupper(JRequest::getString('attr_name_i18n', ''));

        $model = $this->getModel('attributes');
        echo $model->createAttribute($attr_name, $attr_description, $attr_type, $attr_name_i18n);
    }

    function removeAttribute() {
        header("Content-Type: text/plain; charset=UTF-8");
        $attr_name = strtolower(JRequest::getString('attr_name', ''));

        $model = $this->getModel('attributes');
        echo $model->removeAttribute($attr_name);
    }

}

?>
