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

class NewslettersControllerNewsletter extends NewslettersController {

    function __construct() {
        parent::__construct();
    }

    function subscribe() {
        JRequest::checkToken() or die('Invalid Token');
        $news_id = JRequest::getInt('news_id', 0, 'POST');
        $mod_jinc = JRequest::getString('mod_jinc', 'false', 'POST');
        $attributes = JRequest::getVar('attrs', null, 'post', 'array');
        $user_mail = isset ($attributes['user_mail'])?$attributes['user_mail']:'';
        $notice_accept = JRequest::getString('notice_accept', '', 'POST');
        $accepted = ($notice_accept == 'on');

        $info = array();
        $user = JFactory::getUser();
        if (!$user->guest)
            $info['user_id'] = $user->get('id');
        if (strlen($user_mail) > 0)
            $info['email'] = $user_mail;

        $model = $this->getModel('newsletter');
        $gateway = & $this->getView('gateway', 'html');
        if ($model->subscribe($news_id, $info, $attributes, $mod_jinc, $accepted)) {
            $gateway->assignRef('msg', $model->getState('message'));
        } else {
            $gateway->assignRef('msg', $model->getError());
            $gateway->setLayout('error');
        }
        $gateway->display();
    }

    function unsubscribe() {
        JRequest::checkToken() or die('Invalid Token');
        $news_id = JRequest::getInt('news_id', 0, 'POST');
        $user_mail = JRequest::getString('user_mail', '', 'POST');
        $info = array();
        $user = JFactory::getUser();
        if (!$user->guest)
            $info['user_id'] = $user->get('id');
        if (strlen($user_mail) > 0)
            $info['email'] = $user_mail;

        $model = $this->getModel('newsletter');
        $gateway = & $this->getView('gateway', 'html');
        if ($model->unsubscribe($news_id, $info)) {
            $gateway->assignRef('msg', $model->getState('message'));
        } else {
            $gateway->assignRef('msg', $model->getError());
            $gateway->setLayout('error');
        }
        $gateway->display();
    }

    function confirm() {
        $news_id = JRequest::getInt('news_id', 0);
        $user_mail = JRequest::getString('user_mail', '');
        $pub_random = JRequest::getString('pub_random', '');

        $model = $this->getModel('newsletter');

        $model = $this->getModel('newsletter');
        $gateway = & $this->getView('gateway', 'html');
        if ($model->confirm($news_id, $user_mail, $pub_random)) {
            $gateway->assignRef('msg', JText::_('INFO_FIN001'));
        } else {
            $gateway->assignRef('msg', $model->getError());
            $gateway->setLayout('error');
        }
        $gateway->display();
    }

    function delconfirm() {
        $news_id = JRequest::getInt('news_id', 0);
        $user_mail = JRequest::getString('user_mail', '');
        $pub_random = JRequest::getString('pub_random', '');

        $model = $this->getModel('newsletter');

        $model = $this->getModel('newsletter');
        $gateway = & $this->getView('gateway', 'html');
        if ($model->delconfirm($news_id, $user_mail, $pub_random)) {
            $gateway->assignRef('msg', JText::_('INFO_FIN006'));
        } else {
            $gateway->assignRef('msg', $model->getError());
            $gateway->setLayout('error');
        }
        $gateway->display();
    }

    function showCaptcha() {
        include JPATH_COMPONENT . DS . 'securimage' . DS . 'securimage.php';

        $img = new securimage();

        $mod_jinc = JRequest::getString('mod_jinc', 'false');
        $mod_jinc = trim($mod_jinc);

        if ($mod_jinc == 'true') {
            $img->image_width = 125;
            $img->image_height = 30;
            $img->code_length = rand(4, 4);
            $img->setSessionPrefix('mod_jinc');
        } else {
            $img->image_width = 250;
            $img->image_height = 40;
            $img->code_length = rand(5, 6);
        }

        $img->perturbation = 0.7;
        $img->image_bg_color = new Securimage_Color("#ffffff");
        $img->use_transparent_text = true;
        $img->text_transparency_percentage = 45; // 100 = completely transparent
        $img->num_lines = 2;
        $img->image_signature = '';
        $img->text_color = new Securimage_Color("#333366");
        $img->line_color = new Securimage_Color("#FFFFCC");

        $img->show(''); // alternate use:  $img->show('/path/to/background_image.jpg');
    }

    function multisubscribe() {
        JRequest::checkToken() or die('Invalid Token');
        $cid = JRequest::getVar('cid', array(), 'POST', 'array');        
        $mod_jinc = JRequest::getString('mod_jinc', 'false', 'POST');
        $attributes = JRequest::getVar('attrs', null, 'post', 'array');
        $user_mail = isset ($attributes['user_mail'])?$attributes['user_mail']:'';
        $notices = JRequest::getVar('notice', array(), 'POST', 'array');

        $info = array();
        $user = JFactory::getUser();
        if (!$user->guest)
            $info['user_id'] = $user->get('id');
        if (strlen($user_mail) > 0)
            $info['email'] = $user_mail;

        $model = $this->getModel('newsletter');
        $mmsg = array();
        foreach ($cid as $id) {
            if ($model->subscribe($id, $info, $attributes, $mod_jinc, $notices)) {
                $mmsg[$id] = JText::_($model->getState('message'));
            } else {
                $mmsg[$id] = JText::_($model->getError());
            }
        }
        $view = & $this->getView('gateway', 'html');
        $view->setLayout('multisubscription');
        $view->assignRef('mmsg', $mmsg);
        $view->display();
    }
}
?>