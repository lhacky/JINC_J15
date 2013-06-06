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
?>
<script type="text/javascript">
    function checkUser(options, id) {
        for (k=0; k < options.length; k++) {
            if (options[k].value == id) return false;
        }
        return true;
    }

    function selectUsers(n, selName, fldName) {
        if (!fldName) {
            fldName = 'cb';
        }
        var f = document.adminForm;
        s = window.parent.document.getElementById(selName).options;
        for (i=0; i < n; i++) {
            cb = eval( 'f.' + fldName + '' + i );
            if (cb.checked) {
                grp_name = document.getElementById('grp_name' + i).innerHTML.trim();
                grp_id = document.getElementById('grp_id' + i).innerHTML.trim();
                if (checkUser(s, grp_id)) {
                    o = new Option(grp_name, grp_id);
                    s[s.length] = o;
                }
            }
        }
        window.parent.document.getElementById('sbox-window').close();
    }
</script>
<form action="index.php" method="post" name="adminForm">
    <div style="float: right">
        <button type="button" onclick="selectUsers(<?php echo count( $this->items ); ?>, '<?php echo $this->select; ?>');"><?php echo JText::_( 'Select' );?></button>
        <button type="button" onclick="window.parent.document.getElementById('sbox-window').close();"><?php echo JText::_( 'Cancel' );?></button>
    </div>
    <div class="configuration" >
        <?php echo JText::_( 'TITLE_GROUPS_SELECTION' );?>
    </div>

    <table class="adminlist">
        <thead>
            <tr>
                <th width="2%">
                    <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
                </th>
                <th width="30%">
                    <?php echo JText::_('COL_TITLE_GROUP_NAME'); ?>
                </th>
                <th width="65%">
                    <?php echo JText::_('COL_TITLE_GROUP_DESCRIPTION'); ?>
                </th>
                <th width="5%">
                    <?php echo JText::_('ID'); ?>
                </th>
            </tr>
        </thead>
        <?php
        $k = 0;
        for ($i=0, $n=count( $this->items ); $i < $n; $i++) {
            $row =& $this->items[$i];
            $checked   = JHTML::_('grid.id', $i, $row->grp_id);
            $link      = JRoute::_('index.php?option=com_jinc&controller=group&task=edit&cid[]='. $row->grp_id);
            ?>
        <tr class="<?php echo "row$k"; ?>">
            <td>
                    <?php echo $checked; ?>
            </td>
            <td>
                <div id="grp_name<?php echo $i; ?>"><?php echo $row->grp_name; ?></div>
            </td>
            <td>
                    <?php echo $row->grp_descr; ?>
            </td>
            <td>
                <div id="grp_id<?php echo $i; ?>"><?php echo $row->grp_id; ?></div>
            </td>
        </tr>
            <?php
            $k = 1 - $k;
        }
        ?>

        <tr>
            <td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td>
        </tr>

    </table>

    <input type="hidden" name="option" value="com_jinc" />
    <input type="hidden" name="view" value="users" />
    <input type="hidden" name="tmpl" value="component" />
</form>