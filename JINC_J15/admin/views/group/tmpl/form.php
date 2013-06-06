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
<?php isset($this->group) or die('Group not defined'); ?>
<?php
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
JHTML::_('behavior.switcher');
JHTML::stylesheet('jinc_admin.css', 'administrator/components/com_jinc/assets/css/');
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
    <div class="col100">
        <fieldset class="adminform">
            <legend><?php echo JText::_( 'Details' ); ?></legend>
            <table width="100%">
                <tr>
                    <td width="100" align="right" class="key">
                        <?php echo JText::_( 'INPUT_GRP_NAME' ); ?>:
                    </td>
                    <td>
                        <input class="text_area" type="text" name="grp_name" id="grp_name" size="50" maxlength="250" value="<?php if (isset( $this->group->grp_name )) { echo $this->group->grp_name; } ?>" />
                    </td>
                </tr>
                <tr>
                    <td width="100" align="right" class="key">
                        <?php echo JText::_( 'INPUT_GRP_DESCRIPTION' ); ?>:
                    </td>
                    <td>
                        <textarea class="text_area" name="grp_descr" id="grp_descr" cols="50" rows="10" style="width:90%"><?php if (isset( $this->group->grp_descr )) { echo str_replace('&','&amp;',$this->group->grp_descr); } ?></textarea>
                    </td>
                </tr>
            </table>
        </fieldset>
    </div>
    <?php
    $group = $this->group;
    $grp_id = isset( $group->grp_id ) ? $group->grp_id : null;
    if ($grp_id > 0) {
        ?>
    <div class="col100">
        <fieldset class="adminform">
            <legend><?php echo JText::_( 'INPUT_GRP_MEMBERS' ); ?></legend>
            <table width="90%" align="center">
                <thead>
                    <tr>
                        <th width="10%" align="left">

                        </th>
                        <th width="25%" align="left">
                                <?php echo JText::_('COL_TITLE_USERNAME'); ?>
                        </th>
                        <th width="30%" align="left">
                                <?php echo JText::_('COL_TITLE_NAME'); ?>
                        </th>
                        <th width="30%" align="left">
                                <?php echo JText::_('COL_TITLE_EMAIL'); ?>
                        </th>
                        <th width="5%" align="left">
                                <?php echo JText::_('ID'); ?>
                        </th>
                    </tr>
                </thead>
                    <?php
                    $k = 0;
                    if (isset ($this->members)) {
                        for ($i=0, $n=count( $this->members ); $i < $n; $i++) {
                            $row        = $this->members[$i];
                            $user_id    = $row->id;
                            $delete_lnk = "index.php?option=com_jinc&controller=group&task=removeMember&grp_id=$grp_id&user_id=$user_id";
                            ?>
                <tr class="<?php echo "row$k"; ?>">
                    <td>
                        <a href="<?php echo $delete_lnk; ?>"><span class="icon-remove users"><span class="icon-text"><?php echo JText::_( 'BTN_REMOVEUSER' ); ?></span></span></a>
                    </td>
                    <td>
                        <a href="<?php echo $link; ?>"><?php echo $row->username; ?></a>
                    </td>
                    <td>
                                    <?php echo $row->name; ?>
                    </td>
                    <td>
                                    <?php echo $row->email; ?>
                    </td>
                    <td>
                                    <?php echo $user_id; ?>
                    </td>
                </tr>
                            <?php
                            $k = 1 - $k;
                        }
                    }
                    ?>
                <tr>
                    <td align="center" colspan="5">
                        <a class="modal" rel="{handler: 'iframe', size: {x: 750, y: 600}}" href="index.php?option=com_jinc&controller=group&tmpl=component&task=addmember&grp_id=<?php echo $grp_id; ?>">
                            <span class="icon-add users">
                                <span class="icon-text">
                                        <?php echo JText::_('BTN_ADDUSER');?>
                                </span>
                            </span>
                        </a>
                    </td>
                </tr>
            </table>
        </fieldset>
    </div>
    <?php
    }
    ?>

    <input type="hidden" name="option" value="com_jinc" />
    <input type="hidden" name="grp_id" value="<?php echo $grp_id; ?>" />
    <input type="hidden" name="task" value="save" />
    <input type="hidden" name="controller" value="group" />
</form>
