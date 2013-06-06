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

jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');

// Preload the JINCFactory
jimport('joomla.filesystem.file');
require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_jinc'.DS.'classes'.DS.'factory.php';

$lang = JFactory::getLanguage();
$lang->load('com_jinc');

$news_id = $params->get( 'news_id', 0 );

jincimport('core.newsletterfactory');
$ninstance = NewsletterFactory::getInstance();
if ( $newsletter = $ninstance->loadNewsletter($news_id, true) ) {
       $layout = JModuleHelper::getLayoutPath('mod_jinc_unsubscription');
       require($layout);
} else {
    echo JText::_('ERROR_FER002');
}
?>
