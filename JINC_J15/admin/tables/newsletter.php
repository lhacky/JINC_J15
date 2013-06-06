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

class TableNewsletter extends JTable {
    var $news_id = null;
    var $news_name = null;
    var $news_type = null;
    var $news_description = null;
    var $published = null;
    var $news_created = null;
    var $news_lastsent = null;
    var $news_sendername = null;
    var $news_senderaddr = null;
    var $news_replyto_name = null;
    var $news_replyto_addr = null;
    var $news_disclaimer = null;
    var $news_welcome_subject = null;
    var $news_welcome = null;
    var $news_welcome_created = null;
    var $news_optin_subject = null;
    var $news_optin = null;
    var $news_optinremove_subject = null;
    var $news_optinremove = null;
    var $news_default_template = null;
    var $news_on_subscription = null;
    var $news_jcontact_enabled = null;
    var $news_front_theme = null;
    var $news_front_max_msg = null;
    var $news_attributes = null;
    var $news_front_type = null;
    var $news_captcha = null;
    var $news_notify = null;
    var $news_noti_id = null;
    var $news_input_style = null;

    function TableNewsletter(& $db) {
        parent::__construct('#__jinc_newsletter', 'news_id', $db);
    }
}
?>
