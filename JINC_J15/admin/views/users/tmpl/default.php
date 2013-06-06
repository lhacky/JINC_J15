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
JHTML::_('behavior.tooltip');
isset($this->items) or die ('Items not defined');
jincimport('utility.jinchtmlhelper');
jincimport('core.newsletter');
JINCHTMLHelper::hint('USER_LIST', 'USER_LIST_TITLE');
$order     = isset($this->filter_order)?$this->filter_order:'username';
$order_Dir = isset ($this->filter_order_Dir)?$this->filter_order_Dir:'asc';
$grp_id = isset ($this->grp_id)? $this->grp_id:'';
?>

<form action="index.php" method="post" name="adminForm">
    <div style="float: right">
        <button type="submit"><?php echo JText::_( 'ADD' );?></button>
        <button type="button" onclick="window.parent.window.location.reload(); window.parent.document.getElementById('sbox-window').close();"><?php echo JText::_( 'Close' );?></button>
    </div>
    <div class="configuration" >
        <?php echo JText::_( 'TITLE_USERS_SELECTION' );?>
    </div>

    <table class="adminlist" width="40%">
        <thead>
            <tr>
                <th width="2%">
                    <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( isset($this->items)?$this->items:array() ); ?>);" />
                </th>
                <th width="10%">
                    <?php
                    echo JHTML::_( 'grid.sort', 'COL_TITLE_USERNAME', 'username', $order_Dir, $order);
                    ?>
                </th>
                <th width="15%">
                    <?php
                    echo JHTML::_( 'grid.sort', 'COL_TITLE_NAME', 'name', $order_Dir, $order);
                    ?>
                </th>
                <th width="10%">
                    <?php
                    echo JHTML::_( 'grid.sort', 'COL_TITLE_EMAIL', 'email', $order_Dir, $order);
                    ?>
                </th>
                <th width="2%">
                    <?php
                    echo JHTML::_( 'grid.sort', 'ID', 'id', $order_Dir, $order);
                    ?>
                </th>
            </tr>
        </thead>
        <?php

        $k = 0;
        for ($i=0, $n=count( $this->items ); $i < $n; $i++) {
            $row     =& $this->items[$i];
            $checked = JHTML::_('grid.id', $i, $row->id);
            ?>
        <tr>
            <td>
                    <?php echo $checked; ?>
            </td>
            <td>
                <?php echo $row->username; ?>
            </td>
            <td>
                <?php echo $row->name; ?>
            </td>
            <td>
                    <?php echo $row->email; ?>
            </td>
            <td align="center">
                <?php echo $row->id; ?>
            </td>
        </tr>
            <?php
            $k = 1 - $k;
        }
        ?>
        <tr>
            <td colspan="5">
                <?php
                if (isset ($this->pagination)) {
                    $pagination = $this->pagination;
                    echo $pagination->getListFooter();
                }
                ?>
            </td>
        </tr>
    </table>
    <input type="hidden" name="option" value="com_jinc" />
    <input type="hidden" name="grp_id" value="<?php echo $grp_id; ?>" />
    <input type="hidden" name="task" value="addmember" />
    <input type="hidden" name="tmpl" value="component" />
    <input type="hidden" name="controller" value="group" />
    <input type="hidden" name="filter_order" value="<?php echo $order; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $order_Dir; ?>" />
</form>