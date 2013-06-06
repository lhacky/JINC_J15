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
jincimport('core.newsletter');
JINCHTMLHelper::hint('NEWSLETTER_LIST', 'NEWSLETTER_LIST_TITLE');
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
                    <th width="32%">
                        <?php
                        echo JHTML::_( 'grid.sort', 'COL_TITLE_NEWS_NAME', 'news_name', $order_Dir, $order);
                        ?>
                    </th>
                    <th width="2%">
                        <?php
                        echo JHTML::_( 'grid.sort', 'COL_TITLE_STATE', 'published', $order_Dir, $order);
                        ?>
                    </th>
                    <th width="2%">
                        <?php
                        echo JHTML::_( 'grid.sort', 'COL_TITLE_TYPE', 'news_type', $order_Dir, $order);
                        ?>
                    </th>
                    <th width="3%">
                        <?php echo JText::_('COL_TITLE_IMPORT'); ?>
                    </th>
                    <th width="3%">
                        <?php echo JText::_('COL_TITLE_STATS'); ?>
                    </th>
                    <th width="17%">
                        <?php
                        echo JHTML::_( 'grid.sort', 'COL_TITLE_NEWS_CREATED', 'news_created', $order_Dir, $order);
                        ?>
                    </th>
                    <th width="17%">
                        <?php
                        echo JHTML::_( 'grid.sort', 'COL_TITLE_SENDER_ADDRESS', 'news_senderaddr', $order_Dir, $order);
                        ?>
                    </th>
                    <th width="20%">
                        <?php
                        echo JHTML::_( 'grid.sort', 'COL_TITLE_SENDER_NAME', 'news_sendername', $order_Dir, $order);
                        ?>
                    </th>
                    <th width="2%">
                        <?php
                        echo JHTML::_( 'grid.sort', 'ID', 'news_id', $order_Dir, $order);
                        ?>
                    </th>
                </tr>
            </thead>
            <?php
            $options  = array( "height" => 16, "width" => 16);
            $base_url = JURI::base() . 'images/';
            $public_img    = JHTML::image($base_url . 'send_f2.png',
                JText::_('LEGEND_NEWSLETTER_PUBLIC'), $options);
            $protected_img = JHTML::image($base_url . 'forward_mail_f2.png',
                JText::_('LEGEND_NEWSLETTER_PROTECTED'), $options);
            $private_img   = JHTML::image($base_url . 'security_f2.png',
                JText::_('LEGEND_NEWSLETTER_PRIVATE'), $options);
            $import_img    = JHTML::image($base_url . 'upload_f2.png',
                JText::_('LEGEND_IMPORT'), $options);
            $stats_img     = JHTML::image($base_url . 'cpanel.png',
                JText::_('LEGEND_STATS'), $options);
            $k = 0;
            for ($i=0, $n=count( $this->items ); $i < $n; $i++) {
                $row =& $this->items[$i];
                $checked     = JHTML::_('grid.id', $i, $row->news_id);
                $link        = JRoute::_('index.php?option=com_jinc&controller=newsletter&task=edit&news_id='. $row->news_id);
                $link_add    = JRoute::_('index.php?option=com_jinc&controller=newsletter&task=addsubscripion&news_id='. $row->news_id);
                $import_link = JRoute::_('index.php?option=com_jinc&view=newsletter&layout=uploadcsv&news_id='. $row->news_id);
                $stats_link  = JRoute::_('index.php?option=com_jinc&task=stats&controller=newsletter&news_id='. $row->news_id);
                $published   = JHTML::_('grid.published', $row, $i );
                ?>
            <tr class="<?php echo "row$k"; ?>">
                <td>
                        <?php echo $checked; ?>
                </td>
                <td>
                    <a href="<?php echo $link; ?>"><?php echo $row->news_name; ?></a>
                </td>
                <td align="center">
                        <?php echo $published; ?>
                </td>
                <td align="center">
                        <?php
                        $type = $row->news_type;
                        if ($type == NEWSLETTER_PUBLIC_NEWS) echo $public_img;
                        if ($type == NEWSLETTER_PROTECTED_NEWS) echo $protected_img;
                        if ($type == NEWSLETTER_PRIVATE_NEWS) echo $private_img;
                        ?>
                </td>
                <td align="center">
                    <a href="<?php echo $import_link; ?>"><?php echo $import_img; ?></a>
                </td>
                <td align="center">
                    <a href="<?php echo $stats_link; ?>"><?php echo $stats_img; ?></a>
                </td>
                <td>
                        <?php
                        $date = JFactory::getDate($row->news_created);
                        echo $date->toFormat(JText::_('DATE_FORMAT_LC2'));
                        ?>
                </td>
                <td>
                        <?php echo $row->news_senderaddr; ?>
                </td>
                <td>
                        <?php echo $row->news_sendername; ?>
                </td>
                <td>
                        <?php echo $row->news_id; ?>
                </td>
            </tr>
                <?php
                $k = 1 - $k;
            }
            ?>
            <tr>
                <td colspan="10">
                    <?php
                    if (isset ($this->pagination)) {
                        $pagination = $this->pagination;
                        echo $pagination->getListFooter();
                    }
                    ?>
                </td>
            </tr>

        </table>

        <?php
        $legend_array = array();
        array_push($legend_array, array('text' => 'NEWSLETTER_PUBLIC',
            'icon' => 'send_f2.png'));
        array_push($legend_array, array('text' => 'NEWSLETTER_PROTECTED',
            'icon' => 'forward_mail_f2.png'));
        array_push($legend_array, array('text' => 'NEWSLETTER_PRIVATE',
            'icon' => 'security_f2.png'));
        array_push($legend_array, array('text' => 'IMPORT',
            'icon' => 'upload_f2.png'));
        array_push($legend_array, array('text' => 'STATS',
            'icon' => 'cpanel.png'));

        JINCHTMLHelper::legend($legend_array);
        ?>
    </div>

    <input type="hidden" name="option" value="com_jinc" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="controller" value="newsletter" />
    <input type="hidden" name="view" value="newsletters" />
    <input type="hidden" name="filter_order" value="<?php echo $order; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $order_Dir; ?>" />
</form>