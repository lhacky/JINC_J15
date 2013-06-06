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
isset($this->items) or die('Items not defined');
JHtml::_('behavior.modal');
jincimport('utility.jinchtmlhelper');
JINCHTMLHelper::hint('SUBSCRIBERS_LIST', 'SUBSCRIBERS_LIST_TITLE');
$order = isset($this->filter_order) ? $this->filter_order : 'news_name';
$order_Dir = isset($this->filter_order_Dir) ? $this->filter_order_Dir : 'asc';
?>
<form action="index.php" method="post" name="adminForm">
    <div id="editcell">
        <table width="100%">
            <tr>
                <td align="left" width="100%">
                </td>
                <td nowrap="nowrap">
                    <?php
                    if (isset($this->newsletters)) {
                        $javascript = 'onchange="document.adminForm.submit();"';
                        $filter_news_id = isset($this->filter_news_id) ? $this->filter_news_id : 0;
                        echo JHTML::_('select.genericlist', $this->newsletters, 'filter_news_id', $javascript, 'news_id', 'news_name', $filter_news_id);
                    } else {
                        echo '&nbsp;';
                    }
                    ?>
                </td>
            </tr>
        </table>

        <table class="adminlist">
            <thead>
                <tr>
                    <th width="2%">
                        <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
                    </th>
                    <th width="20%">
                        <?php
                        echo JHTML::_('grid.sort', 'COL_TITLE_SUBS_EMAIL', 'subs_email', $order_Dir, $order);
                        ?>
                    </th>
                    <th width="38%">
                        <?php
                        echo JHTML::_('grid.sort', 'COL_TITLE_NEWS_NAME', 'news_name', $order_Dir, $order);
                        ?>
                    </th>
                    <th width="2%">
                        <?php echo JText::_('STATE'); ?>
                    </th>
                    <th width="20%">
                        <?php
                        echo JHTML::_('grid.sort', 'COL_TITLE_SUBS_USERNAME', 'subs_name', $order_Dir, $order);
                        ?>
                    </th>
                    <th width="16%">
                        <?php
                        echo JHTML::_('grid.sort', 'COL_TITLE_SUBS_DATA', 'subs_data', $order_Dir, $order);
                        ?>
                    </th>
                </tr>
            </thead>
            <?php
                        $k = 0;
                        if (isset($this->items)) {
                            for ($i = 0, $n = count($this->items); $i < $n; $i++) {
                                $row = & $this->items[$i];
                                $row_id = '';
                                if (isset($row->subs_id) && isset($row->subs_news_id))
                                    $row_id = $row->subs_id . '_' . $row->subs_news_id;
                                if (isset($row->pub_id) && isset($row->pub_news_id))
                                    $row_id = $row->pub_id . '_' . $row->pub_news_id;
                                $subs_id = $row->pub_id . $row->subs_id;
                                $news_id = $row->pub_news_id . $row->subs_news_id;
                                $activeimg = JHTML::image(JURI::base() . 'images/publish_g.png', JText::_('ALT_ACTIVE'), array("height" => 16, "width" => 16));
                                $waitingimg = JHTML::image(JURI::base() . 'images/publish_y.png', JText::_('ALT_WAITING'), array("height" => 16, "width" => 16));
                                if (strlen($row_id) > 0) {
                                    $checked = JHTML::_('grid.id', $i, $row_id);
            ?>
                                    <tr class="<?php echo "row$k"; ?>">
                                        <td>
                    <?php echo $checked; ?>
                                </td>
                                <td>
                                    <a class="modal" href="index.php?option=com_jinc&view=subscriber&tmpl=component&subs_id=<?php echo $subs_id; ?>&news_id=<?php echo $news_id; ?>" rel="{handler: 'iframe', size: {x: 875, y: 550}, onClose: function() {}}">
                        <?php echo $row->subs_email; ?>
                                </a>
                                </td>
                                <td>
                    <?php echo $row->news_name; ?>
                                </td>
                                <td>
                    <?php
                                    if (strlen($row->pub_random) > 0)
                                        echo $waitingimg;
                                    else
                                        echo $activeimg;
                    ?>
                                </td>
                                <td>
                    <?php echo $row->subs_name; ?>
                                </td>
                                <td>
                    <?php
                                    $timestamp = $row->subs_datasub + $row->pub_datasub;
                                    $date = JFactory::getDate($timestamp);
                                    echo $date->toFormat(JText::_('DATE_FORMAT_LC2'));
                    ?>
                                        </td>
                                    </tr>
            <?php
                                    $k = 1 - $k;
                                }
                            }
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

        <?php
                        $legend_array = array();
                        array_push($legend_array, array('text' => 'SUBS_STATE_ACTIVE',
                            'icon' => 'publish_g.png'));
                        array_push($legend_array, array('text' => 'SUBS_STATE_WAITING',
                            'icon' => 'publish_y.png'));
                        JINCHTMLHelper::legend($legend_array);
        ?>

                    </div>

                    <input type="hidden" name="option" value="com_jinc" />
                    <input type="hidden" name="task" value="" />
                    <input type="hidden" name="boxchecked" value="0" />
                    <input type="hidden" name="controller" value="subscriber" />
                    <input type="hidden" name="view" value="subscribers" />
                    <input type="hidden" name="filter_order" value="<?php echo $order; ?>" />
                    <input type="hidden" name="filter_order_Dir" value="<?php echo $order_Dir; ?>" />
</form>