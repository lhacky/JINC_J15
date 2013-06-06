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

class TableMessage extends JTable {
    var $msg_id = null;
    var $msg_news_id = null;
    var $msg_subject = null;
    var $msg_body = null;
    var $msg_attachment = null;
    var $msg_plaintext = false;
    var $news_name = null;
    var $msg_datasent = null;
    var $msg_bulkmail = false;

    function TableMessage(& $db) {
        parent::__construct('#__jinc_message', 'msg_id', $db);
    }
}
?>
