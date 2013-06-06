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
isset($this->items) or die ('Items not defined');
jincimport('utility.jinchtmlhelper');
JINCHTMLHelper::hint('GROUP_LIST', 'GROUP_LIST_TITLE');
$order     = isset($this->filter_order)?$this->filter_order:'news_name';
$order_Dir = isset ($this->filter_order_Dir)?$this->filter_order_Dir:'asc';
?>
<form action="index.php" method="post" name="adminForm">
    <div id="editcell">
        <table class="adminlist">
            <thead>
                <tr>
                    <th width="2%">
                        <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
                    </th>
                    <th width="30%">
                        <?php
                        echo JHTML::_( 'grid.sort', 'COL_TITLE_GROUP_NAME', 'grp_name', $order_Dir, $order);
                        ?>
                    </th>
                    <th width="60%">
                        <?php echo JText::_('COL_TITLE_GROUP_DESCRIPTION'); ?>
                    </th>
                    <th width="5%">
                        <?php echo JText::_('COL_TITLE_GROUP_MEM_NUMBER'); ?>
                    </th>
                    <th width="5%">
                        <?php
                        echo JHTML::_( 'grid.sort', 'ID', 'grp_id', $order_Dir, $order);
                        ?>
                    </th>
                </tr>
            </thead>
            <?php
            $k = 0;
            for ($i=0, $n=count( $this->items ); $i < $n; $i++) {
                $row       =& $this->items[$i];
                $checked   = JHTML::_('grid.id', $i, $row->grp_id);
                $link      = JRoute::_('index.php?option=com_jinc&controller=group&task=edit&cid[]='. $row->grp_id);
                ?>
            <tr class="<?php echo "row$k"; ?>">
                <td>
                        <?php echo $checked; ?>
                </td>
                <td>
                    <a href="<?php echo $link; ?>"><?php echo $row->grp_name; ?></a>
                </td>
                <td>
                        <?php echo $row->grp_descr; ?>
                </td>
                <td>
                        <?php echo $row->mem_number; ?>
                </td>
                <td>
                        <?php echo $row->grp_id; ?>
                </td>
            </tr>
                <?php
                $k = 1 - $k;
            }
            ?>

            <tr>
                <td colspan="9">
                    <?php
                    if (isset($this->pagination)) {
                        $pagination = $this->pagination;
                        echo $pagination->getListFooter();
                    }
                    ?>
                </td>
            </tr>
        </table>
    </div>

    <input type="hidden" name="option" value="com_jinc" />
    <input type="hidden" name="controller" value="group">
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="view" value="groups">
    <input type="hidden" name="filter_order" value="<?php echo $order; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $order_Dir; ?>" />
</form>