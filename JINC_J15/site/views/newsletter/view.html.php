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
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

class NewslettersViewNewsletter extends JView {

    function display($tpl = null) {
        $layout = $this->getLayout();
        if ($layout == 'subscription') {
            $newsletters = array();
            if (isset($this->msg)) {
                jincimport('utility.servicelocator');
                $servicelocator = ServiceLocator::getInstance();
                $logger = $servicelocator->getLogger();

                jincimport('core.newsletterfactory');
                $ninstance = NewsletterFactory::getInstance();

                foreach ($this->msg as $news_id => $text) {
                    if ($newsletter = $ninstance->loadNewsletter($news_id, true)) {
                        $newsletters[$news_id] = $newsletter;
                    }
                }
                $this->assignRef('newsletters', $newsletters);                
            }
            parent::display($tpl);
        } else {
            if ($newsletter = $this->get('Data')) {
                $this->assignRef('newsletter', $newsletter);
                if ($messages = $this->get('Messages')) {
                    $this->assignRef('messages', $messages);
                }

                if ($newsletter->get('noti_id') > 0) {
                    if ($notice = $this->get('Notice')) {
                        $this->assignRef('notice', $notice);
                    }
                }

                parent::display($tpl);
            } else {
                echo JHTML::stylesheet('ice.css', 'components/com_jinc/assets/css/');
                echo "<br><div class=\"jinc_error\">" . JText::_('ERROR_FER002') . "</div>";
            }
        }
    }

}

?>