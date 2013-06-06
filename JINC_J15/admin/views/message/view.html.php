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
jimport('joomla.application.component.view');

class JINCViewMessage extends JView {

    function display($tpl = null) {
        $layout = JRequest::getString('layout', '');
        JHTML::stylesheet('jinc_admin.css', 'administrator/components/com_jinc/assets/css/');
        jincimport('utility.jinchelper');
        if ($layout == 'form') {                        
            $message = & $this->get('Data');
            $isNew = ($message->get('id') < 1);

            $text = $isNew ? JText::_('New') : JText::_('Edit');
            JToolBarHelper::title(JText::_('TITLE_MESSAGE') . ': <small><small>[ ' . $text . ' ]</small></small>', 'jinc');
            $bar = & JToolBar::getInstance('toolbar');
            $bar->appendButton('Standard', 'send', 'Send', 'saveAndSend', false, false);

            JToolBarHelper::save();

            if ($isNew) {
                JToolBarHelper::cancel();
            } else {
                JToolBarHelper::cancel('cancel', 'Close');
            }

            $bar->prependButton('Popup', 'load-template', 'TOOLBAR_LOAD_TEMPLATE', 'index.php?option=com_jinc&controller=template&tmpl=component&task=loadTemplate&layout=select', 750, 500, 150, 150);
            JINCHelper::helpOnLine('49');

            jincimport('core.newsletterfactory');
            $news_id = $message->get('news_id');
            $taglist = array();
            $ninstance = NewsletterFactory::getInstance();
            if ($newsletter = $ninstance->loadNewsletter($news_id, false))
                $taglist = $newsletter->getTagsList();

            $this->assignRef('taglist', $taglist);
            $this->assignRef('message', $message);
        }

        if ($layout == 'send') {
            jincimport('utility.parameterprovider');
            JINCHelper::helpOnLine('50');
            JRequest::setVar('hidemainmenu', 1);
            JToolBarHelper::title(JText::_('TITLE_SENDING'), 'jinc');
            $msgid = JRequest::getInt('msgid', 0);
            $sleeptime = ParameterProvider::getMailTimeInterval() / 1000;
            $max_mails = ParameterProvider::getMaxXStep();
            $max_bulk_bcc = ParameterProvider::getMailMaxBcc();
            $ajax_log_level = ParameterProvider::getAJAXLogLevel();

            if ($process = $this->get('Process')) {
                $this->assignRef('proc_status', $process->get('status'));
                $this->assignRef('proc_sent_messages', $process->get('sent_messages'));
                $this->assignRef('proc_sent_success', $process->get('sent_success'));
                $start_time = $process->get('start_time');
                if ($start_time > 0) {
                    $this->assignRef('proc_start_time', date('r', $start_time));
                }
            }

            if ($newsletter = $this->get('Newsletter')) {
                $this->assignRef('proc_tot_recipients', $newsletter->countSubscribers());
            }

            $this->assignRef('sleeptime', $sleeptime);
            $this->assignRef('max_mails', $max_mails);
            $this->assignRef('max_bulk_bcc', $max_bulk_bcc);
            $this->assignRef('msgid', $msgid);
            $this->assignRef('ajax_log_level', $ajax_log_level);
        }

        if ($layout == 'history') {
            JINCHelper::helpOnLine('51');
            JRequest::setVar('hidemainmenu', 1);
            JToolBarHelper::title(JText::_('TITLE_HISTORY'), 'jinc');
            JToolBarHelper::back();
            $msgid = JRequest::getInt('msgid', 0);
            $history = $this->get('History');
            $this->assignRef('history', $history);
        }
        parent::display($tpl);
    }

}