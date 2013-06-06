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
defined('_JEXEC') or die();
jimport('joomla.application.component.model');

class JINCModelTools extends JModel {

    function __construct() {
        parent::__construct();
    }

    function _newsletterQuery() {

        $query = "INSERT IGNORE INTO `#__jinc_newsletter` " .
                "(`news_id`, `news_type`, `news_name`, `news_description`, `published`, `news_created`, " .
                "`news_lastsent`, `news_sendername`, `news_senderaddr`, " .
                "`news_welcome_subject`, `news_welcome`, `news_welcome_created`, " .
                "`news_optin_subject`, `news_optin`, `news_optinremove_subject`, `news_optinremove`, " .
                "`news_disclaimer`, `news_default_template`) VALUES " .
                "(1000, 1, 'News for every Joomla! users', 'Stay tuned with the newsletter dedicated to every Joomla! users of this site.', 1, now(), " .
                "'0000-00-00 00:00:00', 'Sample sender', 'sample@sample.org', " .
                "'Your subscription to our newletter', " .
                "'<p>Dear [USERNAME],</p><p>here is your access credential:</p>" .
                "<p>User name: [USERID]</p>Bye, Lhacky.', '<p>Dear [USERNAME],</p>" .
                "<p>we just added you to our newsletter. </p>" .
                "<p>Here is your access credential:</p><p>User name: [USERID]</p>" .
                "<p>Password: [USERPASSWORD] </p>Bye, Lhacky.', '', '', '', '', '', 1000), ";

        $query .= "(1001, 2, 'Private news for elected people', 'Private news for selected Joomla! users.', 1, now(), " .
                "'0000-00-00 00:00:00', 'Sample sender', 'sample@sample.org', " .
                "'Your subscription to our newletter', " .
                "'<p>Dear [USERNAME],</p><p>here is your access credential:</p>" .
                "<p>User name: [USERID]</p>Bye, Lhacky.', '<p>Dear [USERNAME],</p>" .
                "<p>we just added you to our newsletter. </p>" .
                "<p>Here is your access credential:</p><p>User name: [USERID]</p>" .
                "<p>Password: [USERPASSWORD] </p>Bye, Lhacky.', '', '', '', '', '', 1000), ";

        $query .= "(1002, 0, 'News for everyone !!', 'Public newsletter accessible by everyone ... also simple guests.', 1, now(), " .
                "'0000-00-00 00:00:00', 'Sample sender', 'sample@sample.org', " .
                "'Your subscription to our newletter', " .
                "'<p>Dear [USERNAME],</p><p>here is your access credential:</p>" .
                "<p>User name: [USERID]</p>Bye, Lhacky.', '<p>Dear [USERNAME],</p>" .
                "<p>we just added you to our newsletter. </p>" .
                "<p>Here is your access credential:</p><p>User name: [USERID]</p>" .
                "<p>Password: [USERPASSWORD] </p>Bye, Lhacky.', " .
                "'" . JText::_('DEF_NEWS_OPTIN_SUBJECT') . "', " .
                "'" . JText::_('DEF_NEWS_OPTIN') . "', " .
                "'" . JText::_('DEF_NEWS_OPTINREMOVE_SUBJECT') . "', " .
                "'" . JText::_('DEF_NEWS_OPTINREMOVE') . "', " .
                "'', 1001)";
        return $query;
    }

    function _messageQuery() {
        $query = "INSERT IGNORE INTO `#__jinc_message` " .
                "(`msg_id`, `msg_news_id`, `msg_subject`, `msg_body`, " .
                "`msg_attachment`, `msg_plaintext`, `msg_bulkmail`, `msg_datasent`) " .
                "VALUES (1001, 1001, 'Secret message', " .
                "'<table style=\"height: 356px;\" align=\"center\" border=\"1\" cellpadding=\"0\" cellspacing=\"0\" width=\"530\"><tbody style=\"text-align: left;\"><tr style=\"text-align: left;\"><td style=\"text-align: left; background-color: #b09fcf; width: 644px;\"><p> </p><p style=\"text-align: center;\"><strong><em>[NEWSLETTER]</em></strong></p><p> </p></td></tr><tr style=\"text-align: left;\"><td style=\"text-align: left; background-color: #eee7f7; width: 644px;\" valign=\"top\"><p><img alt=\"articles\" src=\"images/stories/articles.jpg\" height=\"64\" width=\"526\" /></p><p> </p><p>Dear [USERNAME],</p><p> </p><p>Here is our little secret ... </p><p> </p><p> </p><p>Kind Regards,</p><p> </p><p>The staff.</p><p> </p><p> </p><p> </p></td></tr></tbody></table>', " .
                "'', 0, 0, '0000-00-00 00:00:00'), " .
                "(1002, 1002, 'Hello everybody !!!', " .
                "'<div align=\"center\" style=\"font-family: Arial,Helvetica,sans-serif; font-size: 18px; background-color: #ffff33\"> </div><div align=\"center\" style=\"font-family: Arial,Helvetica,sans-serif; font-size: 18px; text-decoration: underline; background-color: #ffff33\">New Message from [NEWSLETTER]</div><div align=\"center\" style=\"font-family: Arial,Helvetica,sans-serif; font-size: 18px; background-color: #ffff33\"> </div><div align=\"center\" style=\"font-family: Arial,Helvetica,sans-serif; font-size: 14px; background-color: #ffffcc\"> </div><div style=\"font-family: Arial,Helvetica,sans-serif; font-size: 14px; background-color: #ffffcc\"> </div><div style=\"font-family: Arial,Helvetica,sans-serif; font-size: 14px; background-color: #ffffcc\">Put your message here. <br /></div><div style=\"font-family: Arial,Helvetica,sans-serif; font-size: 14px; background-color: #ffffcc\"> </div><div style=\"font-family: Arial,Helvetica,sans-serif; font-size: 14px; background-color: #ffffcc\"> </div><div align=\"center\" style=\"font-size: 9px; background-color: #ffff66\">Message sent using JINC. <br /></div><div style=\"background-color: #ffffff\"> </div>', " .
                "'', 0, 1, '0000-00-00 00:00:00')";
        return $query;
    }

    function _groupQuery() {
        $query = "INSERT IGNORE INTO `#__jinc_group` " .
                "(`grp_id`, `grp_name`, `grp_descr`) " .
                "VALUES (1000, 'Test group', 'Test group loaded as sample data')";
        return $query;
    }

    function _accessQuery() {
        $query .= "INSERT IGNORE INTO `#__jinc_access` " .
                "(`acc_news_id`, `acc_grp_id`, `acc_role`) " .
                "VALUES (1001, 1000, 1) ";
        return $query;
    }

    function _templateQuery() {
        $query = "INSERT IGNORE INTO `#__jinc_template` " .
                "(`tem_id`, `tem_name`, `tem_subject`, `tem_body`) " .
                "VALUES (1000, 'Test Template', 'Test Template Subject', " .
                "'<table style=\"height: 356px;\" align=\"center\" border=\"1\" cellpadding=\"0\" cellspacing=\"0\" width=\"530\"><tbody style=\"text-align: left;\"><tr style=\"text-align: left;\"><td style=\"text-align: left; background-color: #b09fcf; width: 644px;\"><p> </p><p style=\"text-align: center;\"><strong><em>[NEWSLETTER]</em></strong></p><p> </p></td></tr><tr style=\"text-align: left;\"><td style=\"text-align: left; background-color: #eee7f7; width: 644px;\" valign=\"top\"><p><img alt=\"articles\" src=\"images/stories/articles.jpg\" height=\"64\" width=\"526\" /></p><p> </p><p>Dear [USERNAME],</p><p> </p><p>Message ...</p><p> </p><p> </p><p>Kind Regards,</p><p> </p><p>The staff.</p><p> </p><p> </p><p> </p></td></tr></tbody></table>')";
        //"'<div align=\"center\" style=\"font-family: Arial,Helvetica,sans-serif; font-size: 18px; background-color: #ffff33\"> </div><div align=\"center\" style=\"font-family: Arial,Helvetica,sans-serif; font-size: 18px; text-decoration: underline; background-color: #ffff33\">New Message from [NEWSLETTER]</div><div align=\"center\" style=\"font-family: Arial,Helvetica,sans-serif; font-size: 18px; background-color: #ffff33\"> </div><div align=\"center\" style=\"font-family: Arial,Helvetica,sans-serif; font-size: 14px; background-color: #ffffcc\"> </div><div style=\"font-family: Arial,Helvetica,sans-serif; font-size: 14px; background-color: #ffffcc\"> </div><div style=\"font-family: Arial,Helvetica,sans-serif; font-size: 14px; background-color: #ffffcc\">Put your message here. <br /></div><div style=\"font-family: Arial,Helvetica,sans-serif; font-size: 14px; background-color: #ffffcc\"> </div><div style=\"font-family: Arial,Helvetica,sans-serif; font-size: 14px; background-color: #ffffcc\"> </div><div align=\"center\" style=\"font-size: 9px; background-color: #ffff66\">Message sent using JINC. <br /></div><div style=\"background-color: #ffffff\"> </div>')";
        return $query;
    }

    function loadSampleData() {
        $dbo = & $this->getDBO();
        // Loading newsletters
        $query = $this->_newsletterQuery();
        $dbo->setQuery($query);
        if (!$dbo->query()) {
            $this->setError($dbo->getErrorMsg());
            return false;
        }
        // Loading messages
        $query = $this->_messageQuery();
        $dbo->setQuery($query);
        if (!$dbo->query()) {
            $this->setError($dbo->getErrorMsg());
            return false;
        }
        // Loading groups
        $query = $this->_groupQuery();
        $dbo->setQuery($query);
        if (!$dbo->query()) {
            $this->setError($dbo->getErrorMsg());
            return false;
        }
        // Loading groups
        $query = $this->_accessQuery();
        $dbo->setQuery($query);
        if (!$dbo->query()) {
            $this->setError($dbo->getErrorMsg());
            return false;
        }
        // Loading templates
        $query = $this->_templateQuery();
        $dbo->setQuery($query);
        if (!$dbo->query()) {
            $this->setError($dbo->getErrorMsg());
            return false;
        }
        return true;
    }

    function loadSampleStatisticalData() {
        $this->loadSampleData();
        $dbo = & $this->getDBO();
        $now = JFactory::getDate();
        for ($i = 0; $i < 60; $i++) {
            $num = (int) rand(15, 50);
            for ($j = 0; $j < $num; $j++) {
                $d = new JDate($now->toUNIX() - $i * 24 * 60 * 60 + rand(0, 24 * 60) * 60);
                $query = "INSERT INTO #__jinc_stats_event (stat_type, stat_date, stat_news_id) " .
                        "VALUES (0, '" . $d->toMySQL() . "', 1000)";
                $dbo->setQuery($query);
                if (!$dbo->query()) {
                    $this->setError($dbo->getErrorMsg() . ': ' . $query);
                    return false;
                }
            }
            $num = (int) rand(5, 15);
            for ($j = 0; $j < $num; $j++) {
                $d = new JDate($now->toUNIX() - $i * 24 * 60 * 60 + rand(0, 24 * 60) * 60);
                $query = "INSERT INTO #__jinc_stats_event (stat_type, stat_date, stat_news_id) " .
                        "VALUES (1, '" . $d->toMySQL() . "', 1000)";
                $dbo->setQuery($query);
                if (!$dbo->query()) {
                    $this->setError($dbo->getErrorMsg() . ': ' . $query);
                    return false;
                }
            }

            $num = (int) rand(15, 50);
            for ($j = 0; $j < $num; $j++) {
                $d = new JDate($now->toUNIX() - $i * 24 * 60 * 60 + rand(0, 24 * 60) * 60);
                $query = "INSERT INTO #__jinc_stats_event (stat_type, stat_date, stat_news_id) " .
                        "VALUES (0, '" . $d->toMySQL() . "', 1001)";
                $dbo->setQuery($query);
                if (!$dbo->query()) {
                    $this->setError($dbo->getErrorMsg() . ': ' . $query);
                    return false;
                }
            }
            $num = (int) rand(5, 15);
            for ($j = 0; $j < $num; $j++) {
                $d = new JDate($now->toUNIX() - $i * 24 * 60 * 60 + rand(0, 24 * 60) * 60);
                $query = "INSERT INTO #__jinc_stats_event (stat_type, stat_date, stat_news_id) " .
                        "VALUES (1, '" . $d->toMySQL() . "', 1001)";
                $dbo->setQuery($query);
                if (!$dbo->query()) {
                    $this->setError($dbo->getErrorMsg() . ': ' . $query);
                    return false;
                }
            }

            $num = (int) rand(15, 50);
            for ($j = 0; $j < $num; $j++) {
                $d = new JDate($now->toUNIX() - $i * 24 * 60 * 60 + rand(0, 24 * 60) * 60);
                $query = "INSERT INTO #__jinc_stats_event (stat_type, stat_date, stat_news_id) " .
                        "VALUES (0, '" . $d->toMySQL() . "', 1002)";
                $dbo->setQuery($query);
                if (!$dbo->query()) {
                    $this->setError($dbo->getErrorMsg() . ': ' . $query);
                    return false;
                }
            }
            $num = (int) rand(5, 15);
            for ($j = 0; $j < $num; $j++) {
                $d = new JDate($now->toUNIX() - $i * 24 * 60 * 60 + rand(0, 24 * 60) * 60);
                $query = "INSERT INTO #__jinc_stats_event (stat_type, stat_date, stat_news_id) " .
                        "VALUES (1, '" . $d->toMySQL() . "', 1002)";
                $dbo->setQuery($query);
                if (!$dbo->query()) {
                    $this->setError($dbo->getErrorMsg() . ': ' . $query);
                    return false;
                }
            }
        }
        
        return true;
    }

    function cleanRemovedData() {
        
    }

}

?>
