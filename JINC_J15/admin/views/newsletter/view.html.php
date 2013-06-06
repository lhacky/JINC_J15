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

class JINCViewNewsletter extends JView {
    function display($tpl = null) {
        JHTML::stylesheet('jinc_admin.css', 'administrator/components/com_jinc/assets/css/');
        jincimport('utility.jinchelper');
        $layout = JRequest::getString('layout', '');
        $newsletter =& $this->get('Data');
        $isNew = ($newsletter->get('id') < 1);
        if ($layout == 'uploadcsv') {
            $text = JText::_('TITLE_FORM_NEWSLETTER_IMPORT');
            JToolBarHelper::title(JText::_( 'TITLE_FORM_NEWSLETTER' ).': <small><small>[ ' . $text . ' ]</small></small>', 'jinc');
            JINCHelper::helpOnLine('45');
            if (!$isNew) {
                $csv_format = $newsletter->getSubscriptionInfo();
                $this->assignRef('csv_format', $csv_format);
                $news_id = $newsletter->get('id');
                $this->assignRef('news_id', $news_id);
                parent::display($tpl);
            } else {
                jincimport('utility.jinchtmlhelper');
                JINCHTMLHelper::showError('ERR001');
            }
        } elseif ($layout == 'import') {
            $text = JText::_('TITLE_FORM_NEWSLETTER_IMPORT');
            JToolBarHelper::title(JText::_( 'TITLE_FORM_NEWSLETTER' ).': <small><small>[ ' . $text . ' ]</small></small>', 'jinc');
            JINCHelper::helpOnLine('45');
            parent::display($tpl);
        } else {
            $text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
            JToolBarHelper::title(JText::_( 'TITLE_FORM_NEWSLETTER' ).': <small><small>[ ' . $text.' ]</small></small>', 'jinc');
            if ($layout == "addUser")
                JToolBarHelper::custom('addAndCreateUser', 'save', '', 'ADDUSER', false, false);
            else
                JToolBarHelper::save();
            if ($isNew)
                JToolBarHelper::cancel();
            else
                JToolBarHelper::cancel( 'cancel', 'Close' );

            $taglist = array();
            if (! $isNew ) {
                $taglist = $newsletter->getTagsList();
                $this->assignRef('taglist', $taglist);
            }
            JINCHelper::helpOnLine('42');

            $themes = $this->get('Themes');
            $attributes = $this->get('Attributes');

            $this->assignRef('themes', $themes);
            $this->assignRef('attributes', $attributes);

            $rolesubscriber =& $this->get('Subscribers');
            $roleproposer   =& $this->get('Authors');
            $rolesender     =& $this->get('Senders');
            $roleadmin      =& $this->get('Administrators');

            $this->assignRef('rolesubscriber', $rolesubscriber);
            $this->assignRef('roleproposer',   $roleproposer);
            $this->assignRef('rolesender',     $rolesender);
            $this->assignRef('roleadmin',      $roleadmin);

            $this->assignRef('newsletter', $newsletter);
            parent::display($tpl);
        }

    }
}
