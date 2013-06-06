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
jincimport('utility.jinchtmlhelper');
jincimport('core.process');
JINCHTMLHelper::hint('MESSAGE_LIST', 'MESSAGE_LIST_TITLE');
$order = isset($this->filter_order) ? $this->filter_order : 'msg_subject';
$order_Dir = isset($this->filter_order_Dir) ? $this->filter_order_Dir : 'asc';
$base_jinc = JURI::base() . 'components/com_jinc/assets/images/icons/';
$options = array('height' => 16, 'width' => 16, 'title' => JText::_('ALT_MSG_ATTACHMENT'));
$attach_img = JHTML::image($base_jinc . 'attachment.png', JText::_('ALT_MSG_ATTACHMENT'), $options);
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
                    <th width="40%">
                        <?php
                        echo JHTML::_('grid.sort', 'COL_TITLE_MSG_SUBJECT', 'msg_subject', $order_Dir, $order);
                        ?>
                    </th>
                    <th width="3%">
                        <?php
                        echo JHTML::_('grid.sort', 'COL_TITLE_TYPE', 'msg_bulkmail', $order_Dir, $order);
                        ?>
                    </th>
                    <th width="3%">
                        <?php echo JText::_('COL_TITLE_PREVIEW'); ?>
                    </th>
                    <th width="3%">
                        <?php echo JText::_('COL_TITLE_SEND'); ?>
                    </th>
                    <th width="3%">
                        <?php echo JText::_('COL_TITLE_STATUS'); ?>
                    </th>
                    <th width="3%">
                        <?php echo JText::_('COL_TITLE_HISTORY'); ?>
                    </th>
                    <th width="25%">
                        <?php
                        echo JHTML::_('grid.sort', 'COL_TITLE_NEWS_NAME', 'news_name', $order_Dir, $order);
                        ?>
                    </th>
                    <th width="15%">
                        <?php
                        echo JHTML::_('grid.sort', 'COL_TITLE_MSG_LASTSENT', 'msg_datasent', $order_Dir, $order);
                        ?>
                    </th>
                    <th width="3%">
                        <?php
                        echo $attach_img;
                        ?>
                    </th>
                    <th width="3%">
                        <?php
                        echo JHTML::_('grid.sort', 'ID', 'msg_id', $order_Dir, $order);
                        ?>
                    </th>
                </tr>
            </thead>
            <?php
            $base_url = JURI::base() . 'images/';
            $options['title'] = JText::_('LEGEND_MESSAGE_BULK');
            $bulk_img = JHTML::image($base_url . 'user.png',
                JText::_('LEGEND_MESSAGE_BULK'), $options);
            $options['title'] = JText::_('LEGEND_MESSAGE_PERSONAL');
            $pers_img = JHTML::image($base_url . 'person4_f2.png',
                JText::_('LEGEND_MESSAGE_PERSONAL'), $options);
            $options['title'] = JText::_('LEGEND_MESSAGE_SEND');
            $send_img = JHTML::image($base_url . 'send_f2.png',
                JText::_('LEGEND_MESSAGE_SEND'), $options);
            $options['title'] = JText::_('LEGEND_MESSAGE_PREVIEW');
            $prev_img = JHTML::image($base_url . 'send.png',
                JText::_('LEGEND_MESSAGE_PREVIEW'), $options);

            $k = 0;
            if (isset($this->items)) {
                $options['title'] = JText::_('LEGEND_MESSAGE_HISTORY');
                $history_img = JHTML::image(JURI::base() . 'components/com_jinc/assets/images/icons/history.png',
                    JText::_('LEGEND_MESSAGE_HISTORY'), $options);

                for ($i = 0, $n = count($this->items); $i < $n; $i++) {
                    $row = & $this->items[$i];
                    $checked = JHTML::_('grid.id', $i, $row->msg_id);
                    $link = JRoute::_('index.php?option=com_jinc&controller=message&task=edit&msgid=' . $row->msg_id);
                    $sendlink = JRoute::_('index.php?option=com_jinc&view=message&layout=send&msgid=' . $row->msg_id);
                    $prevlink = JRoute::_('index.php?option=com_jinc&controller=message&task=preview&msgid=' . $row->msg_id);
                    $news_link = JRoute::_('index.php?option=com_jinc&controller=newsletter&task=edit&news_id=' . $row->news_id);
                    $history_link = JRoute::_('index.php?option=com_jinc&view=message&layout=history&msgid=' . $row->msg_id);
                    $status = isset($row->proc_status) ? $row->proc_status : 0;
                    $max_status = isset($row->proc_max_status) ? $row->proc_max_status : 0;
                    switch ($status) {
                        case PROCESS_STATUS_PAUSED:
                            $options['title'] = JText::_('LEGEND_MESSAGE_PAUSED');
                            $status_img = JHTML::image(JURI::base() . 'components/com_jinc/assets/images/icons/pause.png',
                                JText::_('LEGEND_MESSAGE_PAUSED'), $options);
                            break;
                        case PROCESS_STATUS_RUNNING:
                            $options['title'] = JText::_('LEGEND_MESSAGE_RUNNING');
                            $status_img = JHTML::image(JURI::base() . 'components/com_jinc/assets/images/icons/running.png',
                                JText::_('LEGEND_MESSAGE_RUNNING'), $options);
                            break;
                        case PROCESS_STATUS_FINISHED:
                            $options['title'] = JText::_('LEGEND_MESSAGE_FINISHED');
                            $status_img = JHTML::image(JURI::base() . 'components/com_jinc/assets/images/icons/finished.png',
                                JText::_('LEGEND_MESSAGE_FINISHED'), $options);
                            break;
                        default:
                            $options['title'] = JText::_('LEGEND_MESSAGE_STOPPED');
                            $status_img = JHTML::image(JURI::base() . 'components/com_jinc/assets/images/icons/stop.png',
                                JText::_('LEGEND_MESSAGE_STOPPED'), $options);
                            break;
                    }
                    ?>
            <tr class="<?php echo "row$k"; ?>">
                <td>
                            <?php echo $checked; ?>
                </td>
                <td>
                    <a href="<?php echo $link; ?>"><?php echo $row->msg_subject; ?></a>
                </td>
                <td align="center">
                            <?php
                            if ($row->msg_bulkmail)
                                echo $bulk_img;
                            else
                                echo $pers_img;
                            ?>
                </td>
                <td align="center">
                    <a href="<?php echo $prevlink; ?>"><?php echo $prev_img; ?></a>
                </td>
                <td align="center">
                    <a href="<?php echo $sendlink; ?>"><?php echo $send_img; ?></a>
                </td>
                <td align="center">
                            <?php echo $status_img; ?>
                </td>
                <td align="center">
                            <?php
                            if ($max_status == PROCESS_STATUS_FINISHED) {
                                ?>
                    <a href="<?php echo $history_link; ?>"><?php echo $history_img; ?></a>
                            <?php
                            }
                            ?>

                </td>
                <td>
                    <a href="<?php echo $news_link; ?>"><?php echo $row->news_name; ?></a>
                </td>
                <td>

                            <?php
                            if ($row->msg_datasent == 0) {
                                echo JText::_('LABEL_NEVER');
                            } else {
                                $date = JFactory::getDate($row->msg_datasent);
                                echo $date->toFormat(JText::_('DATE_FORMAT_LC2'));
                            }
                            ?>
                </td>
                <td>
                            <?php
                            if (strlen($row->msg_attachment) > 0) {
                                echo $attach_img;
                            }
                            ?>
                </td>
                <td>
                            <?php echo $row->msg_id; ?>
                </td>
            </tr>
                    <?php
                    $k = 1 - $k;
                }
            }
            ?>
            <tr>
                <td colspan="11">
                    <?php
                    if (isset($this->pagination)) {
                        $pagination = $this->pagination;
                        $pagination->getListFooter();
                    }
                    ?>
                </td>
            </tr>
        </table>
        <?php
        $legend_array = array();
        array_push($legend_array, array('text' => 'MESSAGE_PREVIEW',
            'icon' => 'send.png'));
        array_push($legend_array, array('text' => 'MESSAGE_SEND',
            'icon' => 'send_f2.png'));
        array_push($legend_array, array('text' => 'MESSAGE_PERSONAL',
            'icon' => 'person4_f2.png'));
        array_push($legend_array, array('text' => 'MESSAGE_BULK',
            'icon' => 'user.png'));
        JINCHTMLHelper::legend($legend_array);
        ?>
        <br>
        <?php
        $legend_array = array();
        array_push($legend_array, array('text' => 'MESSAGE_STOPPED',
            'icon' => 'stop.png'));
        array_push($legend_array, array('text' => 'MESSAGE_RUNNING',
            'icon' => 'running.png'));
        array_push($legend_array, array('text' => 'MESSAGE_PAUSED',
            'icon' => 'pause.png'));
        array_push($legend_array, array('text' => 'MESSAGE_FINISHED',
            'icon' => 'finished.png'));
        array_push($legend_array, array('text' => 'MESSAGE_HISTORY',
            'icon' => 'history.png'));
        JINCHTMLHelper::legend($legend_array, 'components/com_jinc/assets/images/icons/');
        ?>
    </div>

    <input type="hidden" name="option" value="com_jinc" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="controller" value="message" />
    <input type="hidden" name="view" value="messages" />
    <input type="hidden" name="filter_order" value="<?php echo $order; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $order_Dir; ?>" />
</form>