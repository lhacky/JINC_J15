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

class JINCViewMediaselection extends JView {

    function display($tpl = null) {
        global $mainframe;

        // Do not allow cache
        JResponse::allowCache(false);

        $style = $mainframe->getUserStateFromRequest('media.list.layout', 'layout', 'thumbs', 'word');

        JHTML::_('behavior.mootools');

        $document = &JFactory::getDocument();
        $document->addStyleSheet('components/com_media/assets/medialist-' . $style . '.css');

        $attach_id = JRequest::getInt('attach_id', 0);

        $this->assign('attach_id', $attach_id);
        
        $this->assign('baseURL', JURI::root());
        $this->assignRef('images', $this->get('images'));
        $this->assignRef('documents', $this->get('documents'));
        $this->assignRef('folders', $this->get('folders'));
        $this->assignRef('state', $this->get('state'));

        parent::display($tpl);
    }

    function setFolder($index = 0) {
        if (isset($this->folders[$index])) {
            $this->_tmp_folder = &$this->folders[$index];
        } else {
            $this->_tmp_folder = new JObject;
        }
    }

    function setImage($index = 0) {
        if (isset($this->images[$index])) {
            $this->_tmp_img = &$this->images[$index];
        } else {
            $this->_tmp_img = new JObject;
        }
    }

    function setDoc($index = 0) {
        if (isset($this->documents[$index])) {
            $this->_tmp_doc = &$this->documents[$index];
        } else {
            $this->_tmp_doc = new JObject;
        }
    }

}