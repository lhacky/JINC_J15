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
?>
<?php
jincimport('utility.jinchtmlhelper');
$version = isset($this->version) ? $this->version : '';
$copyright = isset($this->copyright) ? $this->copyright : '';
$license = isset($this->license) ? $this->license : '';
?>
<table class="adminform">
    <tr>
        <td width="55%" valign="top">

            <div id="cpanel">
                <?php
                $link = 'index.php?option=com_jinc&view=newsletters';
                echo JINCHTMLHelper::quickIconButton($link, 'icon-48-newsletter.png', JText::_('CPANEL_NEWSLETTERS'));

                $link = 'index.php?option=com_jinc&view=notices';
                echo JINCHTMLHelper::quickIconButton($link, 'icon-48-notices.png', JText::_('CPANEL_NOTICES'));

                $link = 'index.php?option=com_jinc&controller=message&view=messages';
                echo JINCHTMLHelper::quickIconButton($link, 'icon-48-message.png', JText::_('CPANEL_MESSAGES'));

                $link = 'index.php?option=com_jinc&view=templates';
                echo JINCHTMLHelper::quickIconButton($link, 'icon-48-template.png', JText::_('CPANEL_TEMPLATES'));

                $link = 'index.php?option=com_jinc&controller=subscriber&view=subscribers';
                echo JINCHTMLHelper::quickIconButton($link, 'icon-48-subscribers.png', JText::_('CPANEL_SUBSCRIBERS'));

                $link = 'index.php?option=com_jinc&view=groups';
                echo JINCHTMLHelper::quickIconButton($link, 'icon-48-groups.png', JText::_('CPANEL_GROUPS'));

                $link = 'index.php?option=com_jinc&controller=newsletter&task=stats';
                echo JINCHTMLHelper::quickIconButton($link, 'icon-48-stats.png', JText::_('CPANEL_STATS'));

                $link = 'index.php?option=com_jinc&view=tools';
                echo JINCHTMLHelper::quickIconButton($link, 'icon-48-tools.png', JText::_('CPANEL_TOOLS'));

                $link = 'index.php?option=com_config&controller=component&component=com_jinc&path=';
                echo JINCHTMLHelper::quickIconButton($link, 'icon-48-config.png', JText::_('CPANEL_PREFERENCES'), "class=\"modal\" rel=\"{handler: 'iframe', size: {x: 570, y: 350}}\"");

                $link = 'http://lhacky.altervista.org/jextensions/index.php?option=com_content&view=article&id=54';
                echo JINCHTMLHelper::quickIconButton($link, 'icon-48-info.png', JText::_('CPANEL_INFO'), "target=\"_blank\"");
                ?>

                <div style="clear:both">&nbsp;</div>
                <div style="text-align:center;padding:0;margin:0;border:0">
                    <iframe style="padding:0;margin:0;border:0" src="http://lhacky.altervista.org/jextensions/version/jinc_adv.php?version=<?php echo $version; ?>" noresize="noresize" frameborder="0" border="0" cellspacing="0" scrolling="no" width="500" marginwidth="0" marginheight="0" height="125">
                    </iframe>
                </div>
            </div>
        </td>

        <td width="45%" valign="top">
            <div style="border:1px solid #ccc;background:#fff;margin:15px;padding:15px">
                <div style="float:right;margin:10px;">
                    <img src="components/com_jinc/assets/images/icons/icon-120-jinc.png" alt="http://lhacky.altervista.org/jextensions/"  /></div>

                <h3><?php echo JText::_('CPANEL_VERSION'); ?></h3>
                <p><?php echo $version; ?></p>

                <h3><?php echo JText::_('CPANEL_LICENSE'); ?></h3>
                <p><?php echo $copyright; ?><br>
                    <a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank"><?php echo $license; ?></a>
                </p>

                <p><strong><?php echo JText::_('CPANEL_TITLE1'); ?></strong></p>
                <p><?php echo JText::_('CPANEL_TEXT1'); ?></p>

                <p><strong><?php echo JText::_('CPANEL_TITLE2'); ?></strong></p>
                <p><?php echo JText::_('CPANEL_TEXT2'); ?></p>
            </div>

            <div style="text-align:center;padding:0;margin:0;border:0">
                <iframe style="padding:0;margin:0;border:0" src="http://lhacky.altervista.org/jextensions/version/jinc.php?version=<?php echo $version; ?>" noresize="noresize" frameborder="0" border="0" cellspacing="0" scrolling="no" width="500" marginwidth="0" marginheight="0" height="100">
                </iframe>
            </div>
        </td>
    </tr>
</table>

