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
<?php isset ($this->template) or die('Template is not defined'); ?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
    <div class="col100">
        <fieldset class="adminform">
            <legend><?php echo JText::_( 'DETAILS' ); ?></legend>
            <table class="admintable">
                <tr>
                    <td width="15%" align="right" class="key">
                        <?php echo JText::_( 'INPUT_TEM_NAME' ); ?>:
                    </td>
                    <td colspan="3">
                        <input class="text_area" type="text" name="tem_name" id="tem_name" size="50" maxlength="250" value="<?php echo $this->template->tem_name;?>" />
                    </td>
                </tr>
                <tr>
                    <td width="15%" align="right" class="key">
                        <?php echo JText::_( 'INPUT_TEM_SUBJECT' ); ?>:
                    </td>
                    <td colspan="3">
                        <input class="text_area" type="text" name="tem_subject" id="tem_subject" size="50" maxlength="250" value="<?php echo $this->template->tem_subject;?>" />
                    </td>
                </tr>
                <tr>
                    <td width="10%" align="right" class="key">
                        <?php echo JText::_( 'INPUT_TEM_BODY' ); ?>:
                    </td>
                    <td width="60%">
                        <?php 
                          $editor = JFactory::getEditor();
                          echo $editor->display("tem_body", str_replace('&','&amp;',$this->template->tem_body), "90%", "", 75, 20);
                          ?>
                    </td>
                    <td width="10%" align="right" class="key">
                        <?php echo JText::_( 'SUGG_SUGGESTIONS' ); ?>:<br><br>
                    </td>
                    <td width="20%">
                        <?php echo JText::_( 'SUGG_TEM_BODY' ); ?>
                    </td>
                </tr>
            </table>
        </fieldset>
    </div>
    <div class="clr"></div>

    <input type="hidden" name="option" value="com_jinc" />
    <input type="hidden" name="tem_id" value="<?php echo $this->template->tem_id; ?>" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="controller" value="template" />
    <input type="hidden" name="view" value="templates" />
</form>
