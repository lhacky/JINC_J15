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
JHTML::_('behavior.tooltip');
jimport('joomla.html.pane');

jincimport('core.newsletterfactory');
jincimport('core.newsletter');
jincimport('utility.jinchtmlhelper');
jincimport('utility.jinchtmlhelper');

JHTML::stylesheet('jinc_admin.css', 'administrator/components/com_jinc/assets/css/');
$newsletter = isset($this->newsletter) ? $this->newsletter : new Newsletter(0);
$isNew = ($newsletter->get('id') < 1);

$ninstance = NewsletterFactory::getInstance();
$publicTags = $ninstance->loadTagsList(NEWSLETTER_PUBLIC_NEWS);
$privateTags = $ninstance->loadTagsList(NEWSLETTER_PRIVATE_NEWS);
$privateTags_jcontact = $ninstance->loadTagsList(NEWSLETTER_PRIVATE_NEWS, RETRIEVER_JCONCACT);

$tags = $isNew ? array() : $newsletter->getTagsList();
?>

<script language="Javascript" type="text/javascript">
    function changeNewsType() {
        var publicMsg = '<?php echo JINCHTMLHelper::showTags($publicTags); ?>';
        var privateMsg = '<?php echo JINCHTMLHelper::showTags($privateTags); ?>';
        var privateMsg_jcontact = '<?php echo JINCHTMLHelper::showTags($privateTags_jcontact, array('CON')); ?>';

        var news_jcontact_enabled = document.getElementById('news_jcontact_enabled');
        var news_welcome_tags = document.getElementById('news_welcome_tags');
        var news_disclaimer_tags = document.getElementById('news_disclaimer_tags');

        var news_type = document.getElementById("news_type");
        var ntype = news_type.options[news_type.selectedIndex].value;
        var panel3 = document.getElementById("panel3");
        var panel4 = document.getElementById("panel4");
        panel3.style.visibility = 'visible';
        panel4.style.visibility = 'visible';
        if (ntype == 0) {
            news_welcome_tags.innerHTML = publicMsg;
            news_disclaimer_tags.innerHTML = publicMsg;
            panel3.style.visibility = 'hidden';
            news_jcontact_enabled.checked = false;
            news_jcontact_enabled.disabled = true;
        } else if (ntype == 1) {
            if (news_jcontact_enabled.checked) {
                news_welcome_tags.innerHTML = privateMsg_jcontact;
                news_disclaimer_tags.innerHTML = privateMsg_jcontact;
            } else {
                news_welcome_tags.innerHTML = privateMsg;
                news_disclaimer_tags.innerHTML = privateMsg;
            }
            panel3.style.visibility = 'hidden';
            panel4.style.visibility = 'hidden';
            news_jcontact_enabled.disabled = false;
        } else if (ntype == 2) {
            if (news_jcontact_enabled.checked == true) {
                news_welcome_tags.innerHTML = privateMsg_jcontact;
                news_disclaimer_tags.innerHTML = privateMsg_jcontact;
            } else {
                news_welcome_tags.innerHTML = privateMsg;
                news_disclaimer_tags.innerHTML = privateMsg;
            }
            panel4.style.visibility = 'hidden';
            news_jcontact_enabled.disabled = false;
        }
    }
</script>

<script language="javascript" type="text/javascript">
    function checkRole(selectBox) {
        selectBox = document.getElementById(selectBox);
        if (null != selectBox) {
            if (selectBox.type == "select-multiple") {
                for (var i = 0; i < selectBox.options.length; i++) {
                    selectBox.options[i].selected = true;
                }
            }
        }
    }

    function removeRole(selectBox) {
        selectBox = document.getElementById(selectBox);
        for (i = selectBox.length - 1; i>=0; i--) {
            if (selectBox.options[i].selected) {
                selectBox.remove(i);
            }
        }
    }

    function checkRoles() {
        checkRole("rolesubscriber");
    }
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm" onsubmit="checkRoles();">
    <div class="col100">
        <?php
        $pane = & JPane::getInstance('tabs', array('startOffset' => 0));
        echo $pane->startPane('pane');
        ?>

        <?php
        echo $pane->startPanel(JText::_('TAB_TITLE_COMMONS_DATA'), 'panel1');
        ?>
        <table class="admintable" width="97%">
            <tr>
                <td width="12%"></td>
                <td width="48%"></td>
                <td width="40%"></td>
            </tr>

            <tr>
                <td align="right" class="key">
                    <?php
                    echo JHTML::tooltip(JText::_('TT_NEWS_NAME'), JText::_('TT_TITLE_NEWS_NAME'),
                            '', JText::_('INPUT_NEWS_NAME'));
                    ?>
                </td>
                <td >
                    <input class="text_area" type="text" name="news_name" id="news_name" size="50" maxlength="250" value="<?php echo $newsletter->get('name'); ?>" />
                </td>
                <td rowspan="5" valign="top" width="40%">
                    <?php
                    $box = &JPane::getInstance('sliders', array('allowAllClose' => true));
                    echo $box->startPane("content-pane");

                    echo $box->startPanel(JText::_('PANEL_ADDRESSES'), "params-page");
                    ?>
                    <table cellpadding="5px" width="100%">
                        <tr>
                            <td width="50%" align="right">
                                <strong>
                                    <?php echo JHTML::tooltip(JText::_('TT_NEWS_SENDER'), JText::_('TT_TITLE_NEWS_SENDER'),
                                            '', JText::_('INPUT_NEWS_SENDER')); ?>
                                </strong>
                            </td>
                            <td width="50%">
                                <input class="text_area" type="text" name="news_sendername" id="news_sendername" size="40" maxlength="250" value="<?php echo $newsletter->get('sendername'); ?>" />
                            </td>
                        </tr>
                        <tr>
                            <td width="50%" align="right">
                                <strong>
                                    <?php echo JHTML::tooltip(JText::_('TT_NEWS_SENDERMAIL'), JText::_('TT_TITLE_NEWS_SENDERMAIL'),
                                            '', JText::_('INPUT_NEWS_SENDERMAIL')); ?>
                                </strong>
                            </td>
                            <td width="50%">
                                <input class="text_area" type="text" name="news_senderaddr" id="news_senderaddr" size="40" maxlength="250" value="<?php echo $newsletter->get('senderaddr'); ?>" />
                            </td>
                        </tr>

                        <tr>
                            <td width="50%" align="right">
                                <strong>
                                    <?php echo JHTML::tooltip(JText::_('TT_NEWS_REPLYTO'), JText::_('TT_TITLE_NEWS_REPLYTO'),
                                            '', JText::_('INPUT_NEWS_REPLYTO')); ?>
                                </strong>
                            </td>
                            <td width="50%">
                                <input class="text_area" type="text" name="news_replyto_name" id="news_replyto_name" size="40" maxlength="250" value="<?php echo $newsletter->get('replyto_name'); ?>" />
                            </td>
                        </tr>
                        <tr>
                            <td width="50%" align="right">
                                <strong>
                                    <?php echo JHTML::tooltip(JText::_('TT_NEWS_REPLYTOMAIL'), JText::_('TT_TITLE_NEWS_REPLYTOMAIL'),
                                            '', JText::_('INPUT_NEWS_REPLYTOMAIL')); ?>
                                </strong>
                            </td>
                            <td width="50%">
                                <input class="text_area" type="text" name="news_replyto_addr" id="news_replyto_addr" size="40" maxlength="250" value="<?php echo $newsletter->get('replyto_addr'); ?>" />
                            </td>
                        </tr>
                    </table>
                    <?php
                                    echo $box->endPanel();

                                    echo $box->startPanel(JText::_('PANEL_FRONTEND'), "attrs-page");
                    ?>
                                    <table cellpadding="5px" width="100%">
                                        <tr>
                                            <td width="50%" align="right" class="key">
                                                <strong>
                                    <?php
                                    echo JHTML::tooltip(JText::_('TT_NEWS_THEME'), JText::_('TT_TITLE_NEWS_THEME'),
                                            '', JText::_('INPUT_NEWS_THEME'));
                                    ?>
                                </strong>
                            </td>
                            <td width="50%">
                                <?php
                                    $themes = isset($this->themes) ? $this->themes : array();
                                    echo JHTML::_('select.genericlist', $themes, 'news_front_theme', '', 'id', 'value', $newsletter->get('front_theme'));
                                ?>
                                </td>
                            </tr>
                            <tr>
                                <td width="50%" align="right" class="key">
                                <?php
                                    echo JHTML::tooltip(JText::_('TT_NEWS_MAX_MSG'), JText::_('TT_TITLE_NEWS_MAX_MSG'),
                                            '', JText::_('INPUT_NEWS_MAX_MSG'));
                                ?>
                                </td>
                                <td width="50%">
                                <?php
                                    $def_value = $newsletter->get('front_max_msg');
                                    if ($isNew)
                                        $def_value = 0;
                                ?>
                                    <input class="text_area" type="text" name="news_front_max_msg" id="news_front_max_msg" size="2" maxlength="3" value="<?php echo $newsletter->get('front_max_msg'); ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td width="50%" align="right" class="key">
                                <?php
                                    echo JHTML::tooltip(JText::_('TT_NEWS_FRONT_TYPE'), JText::_('TT_TITLE_NEWS_FRONT_TYPE'),
                                            '', JText::_('INPUT_NEWS_FRONT_TYPE'));
                                ?>
                                </td>
                                <td width="50%">
                                <?php
                                    $def_value = $newsletter->get('front_type');
                                    if ($isNew)
                                        $def_value = 0;
                                ?>
                                    <select name="news_front_type" id="news_front_type">
                                    <?php $selected = ($newsletter->get('front_type') == NEWSLETTER_FRONT_TYPE_ONLY_TITLE) ? 'selected' : ''; ?>
                                    <option value=<?php echo '"' . NEWSLETTER_FRONT_TYPE_ONLY_TITLE . '" ' . $selected; ?>><?php echo JText::_('LABEL_FRONT_TYPE_ONLY_TITLE'); ?></option>
                                    <?php $selected = ($newsletter->get('front_type') == NEWSLETTER_FRONT_TYPE_CLICKABLE_TITLE) ? 'selected' : ''; ?>
                                    <option value=<?php echo '"' . NEWSLETTER_FRONT_TYPE_CLICKABLE_TITLE . '" ' . $selected; ?>><?php echo JText::_('LABEL_FRONT_TYPE_CLICKABLE_TITLE'); ?></option>
                                    <?php $selected = ($newsletter->get('front_type') == NEWSLETTER_FRONT_TYPE_ENTIRE_MESSAGE) ? 'selected' : ''; ?>
                                    <option value=<?php echo '"' . NEWSLETTER_FRONT_TYPE_ENTIRE_MESSAGE . '" ' . $selected; ?>><?php echo JText::_('LABEL_FRONT_TYPE_ENTIRE_MESSAGE'); ?></option>
                                </select>
                            </td>
                        </tr>

                            <tr>
                                <td width="50%" align="right" class="key">
                                <?php
                                    echo JHTML::tooltip(JText::_('TT_NEWS_INPUT_STYLE'), JText::_('TT_TITLE_NEWS_INPUT_STYLE'),
                                            '', JText::_('INPUT_NEWS_INPUT_STYLE'));
                                ?>
                                </td>
                                <td width="50%">
                                <?php
                                    $def_value = $newsletter->get('input_style');
                                    if ($isNew)
                                        $def_value = 0;
                                ?>
                                    <select name="news_input_style" id="news_input_style">
                                    <?php $selected = ($newsletter->get('input_style') == NEWSLETTER_INPUT_STYLE_STANDARD) ? 'selected' : ''; ?>
                                    <option value=<?php echo '"' . NEWSLETTER_INPUT_STYLE_STANDARD . '" ' . $selected; ?>><?php echo JText::_('LABEL_INPUT_STYLE_STANDARD'); ?></option>
                                    <?php $selected = ($newsletter->get('input_style') == NEWSLETTER_INPUT_STYLE_MINIMAL) ? 'selected' : ''; ?>
                                    <option value=<?php echo '"' . NEWSLETTER_INPUT_STYLE_MINIMAL . '" ' . $selected; ?>><?php echo JText::_('LABEL_INPUT_STYLE_MINIMAL'); ?></option>
                                </select>
                            </td>
                        </tr>
                        
                        </table>
                    <?php
                                    echo $box->endPanel();

                                    echo $box->startPanel(JText::_('PANEL_SECURITY'), "attrs-page");
                    ?>
                                    <table cellpadding="5px" width="100%">
                            <tr>
                            <td width="50%" align="right" class="key">
                                <?php
                                    echo JHTML::tooltip(JText::_('TT_NEWS_CAPTCHA'), JText::_('TT_TITLE_NEWS_CAPTCHA'),
                                            '', JText::_('INPUT_NEWS_CAPTCHA'));
                                ?>
                                </td>
                                <td width="50%">
                                <?php
                                    $captchas = array();
                                    array_push($captchas, array('captcha_id' => CAPTCHA_NO, 'captcha_value' => JText::_('LABEL_CAPTCHA_NO')));
                                    array_push($captchas, array('captcha_id' => CAPTCHA_REQUIRED, 'captcha_value' => JText::_('LABEL_CAPTCHA_REQUIRED')));
                                    // array_push($captchas, array('captcha_id' => CAPTCHA_SOUND, 'captcha_value' => JText::_('LABEL_CAPTCHA_SOUND')));
                                    echo JHTML::_('select.genericlist', $captchas, 'news_captcha', null, 'captcha_id', 'captcha_value', $newsletter->get('captcha'));
                                ?>
                                </td>
                            </tr>
                            <tr>
                                <td align="right" class="key">
                                <?php
                                    echo JHTML::tooltip(JText::_('TT_NEWS_NOTICE'), JText::_('TT_TITLE_NEWS_NOTICE'),
                                            '', JText::_('INPUT_NEWS_NOTICE'));
                                ?>
                                </td>
                                <td>
                                <?php
                                    if (isset($this->notices) && !(empty($this->notices)))
                                        echo JHTML::_('select.genericlist', $this->notices, 'news_noti_id', null, 'noti_id', 'noti_name', $newsletter->get('noti_id'));
                                    else
                                        echo JText::_('INFO_INF007');
                                ?>
                                </td>
                            </tr>

                        </table>
                    <?php
                                    echo $box->endPanel();                                    
                                    
                                    echo $box->startPanel(JText::_('PANEL_ATTRIBUTES'), "params-page");
                    ?>
                                    <table cellpadding="5px" width="100%">
                        <?php
                                    $news_attributes = $newsletter->get('attributes');
                                    $attributes = (isset($this->attributes) ? $this->attributes : array());
                                    if (empty($attributes)) {
                                        echo "No Addictional Attributes defined";
                                    } else {
                                        $opt0 = array('id' => ATTRIBUTE_NONE, 'value' => JText::_('LABEL_ATTRIBUTE_NONE'));
                                        $opt1 = array('id' => ATTRIBUTE_MANDATORY, 'value' => JText::_('LABEL_ATTRIBUTE_MANDATORY'));
                                        $opt2 = array('id' => ATTRIBUTE_OPTIONAL, 'value' => JText::_('LABEL_ATTRIBUTE_OPTIONAL'));
                                        $attr_options = array();
                                        array_push($attr_options, $opt0);
                                        array_push($attr_options, $opt1);
                                        array_push($attr_options, $opt2);
                                        foreach ($attributes as $attribute) {
                                            echo '<tr><td><strong>';
                                            echo JHTML::tooltip(JText::_($attribute['description']), JText::_($attribute['name']), '', JText::_($attribute['name']));
                                            echo '</strong></td>';
                                            echo '<td>';
                                            echo JHTML::_('select.genericlist', $attr_options, 'news_attributes[' . $attribute['name'] . ']', '', 'id', 'value', $news_attributes->get($attribute['name']));
                                            echo '</td></tr>';
                                        }
                                    }
                        ?>
                                </table>
                    <?php
                                    echo $box->endPanel();

                                    echo $box->endPane();
                    ?>
                                </td>

                                </td>
                            </tr>
                            <tr>
                                <td align="right" class="key">
                    <?php
                                    echo JHTML::tooltip(JText::_('TT_NEWS_TYPE'), JText::_('TT_TITLE_NEWS_TYPE'),
                                            '', JText::_('INPUT_NEWS_TYPE'));
                    ?>
                                </td>
                                <td>
                    <?php
                                    $attrs = "onchange='changeNewsType()'";
                                    if (!$isNew)
                                        $attrs .= ' disabled';
                                    $opts = array();
                                    array_push($opts, array('id' => 2, 'value' => 'private'));
                                    array_push($opts, array('id' => 0, 'value' => 'public'));
                                    array_push($opts, array('id' => 1, 'value' => 'protected'));
                                    $news_type = ($isNew) ? 0 : $newsletter->getType();
                                    echo JHTML::_('select.genericlist', $opts, 'news_type', $attrs, 'id', 'value', $news_type);
                    ?>
                                </td>
                            </tr>

                            <tr>
                                <td align="right" class="key">
                    <?php
                                    echo JHTML::tooltip(JText::_('TT_NEWS_ON_SUBSCRIPTION'), JText::_('TT_TITLE_NEWS_ON_SUBSCRIPTION'),
                                            '', JText::_('INPUT_NEWS_ON_SUBSCRIPTION'));
                    ?>
                                </td>
                                <td>
                                    <input type="checkbox" id="news_on_subscription" name="news_on_subscription" <?php echo $newsletter->get('on_subscription') ? 'checked' : '' ?> >
                                </td>
                            </tr>

                            <tr>
                                <td align="right" class="key">
                    <?php
                                    echo JHTML::tooltip(JText::_('TT_NEWS_JCONTACT_ENABLED'), JText::_('TT_TITLE_NEWS_JCONTACT_ENABLED'),
                                            '', JText::_('INPUT_NEWS_JCONTACT_ENABLED'));
                    ?>
                                </td>
                                <td>
                                    <input type="checkbox" id="news_jcontact_enabled" name="news_jcontact_enabled" <?php echo $newsletter->get('jcontact_enabled') ? 'checked' : '' ?> onchange='changeNewsType();'>
                                </td>
                            </tr>
                            <tr>
                                <td align="right" class="key">
                                    <strong>
                        <?php echo JHTML::tooltip(JText::_('TT_NEWS_NOTIFY'), JText::_('TT_TITLE_NEWS_NOTIFY'),
                                            '', JText::_('INPUT_NEWS_NOTIFY')); ?>
                                </strong>
                            </td>
                            <td>
                                <input type="checkbox" id="news_notify" name="news_notify" <?php echo $newsletter->get('notify') ? 'checked' : '' ?> onchange='changeNewsType();'>
                            </td>
                        </tr>
                        <tr>
                            <td align="right" class="key">
                    <?php
                                    echo JHTML::tooltip(JText::_('TT_NEWS_TEMPLATE'), JText::_('TT_TITLE_NEWS_TEMPLATE'),
                                            '', JText::_('INPUT_NEWS_TEMPLATE'));
                    ?>
                                </td>
                                <td>
                    <?php
                                    if (isset($this->templates) && !(empty($this->templates)))
                                        echo JHTML::_('select.genericlist', $this->templates, 'news_default_template', null, 'tem_id', 'tem_name', $newsletter->get('default_template'));
                                    else
                                        echo JText::_('INFO_INF007');
                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td align="right" class="key">
                    <?php echo JText::_('INPUT_NEWS_DESCR'); ?>
                                </td>
                                <td colspan="2">
                                    <?php
                                    $editor = JFactory::getEditor();
                                    $description = $newsletter->get('description');
                                    echo $editor->display("news_description", str_replace('&', '&amp;', $description), "90%", "", 75, 20);                                    
                                    ?>                                    
                                </td>
                            </tr>
                        </table>
        <?php
                                    echo $pane->endPanel();
                                    echo $pane->startPanel(JText::_('TAB_TITLE_MESSAGES'), 'panel2');
        ?>
                                    <table class="admintable">
                                        <tr>
                                            <td width="100" align="right" class="key">
                    <?php echo JText::_('INPUT_NEWS_WELCOME_SUBJECT'); ?>:
                                </td>
                                <td>
                    <?php
                                    $def_value = $newsletter->get('welcome_subject');
                                    if ($isNew)
                                        $def_value = JText::_('DEF_NEWS_WELCOME_SUBJECT');
                    ?>
                                    <input class="text_area" type="text" name="news_welcome_subject" id="news_welcome_subject" size="50" maxlength="250" value="<?php echo $def_value; ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td width="10%" align="right" class="key">
                    <?php echo JText::_('INPUT_NEWS_WELCOME'); ?>
                                </td>
                                <td width="60%">
                    <?php
                                    $editor = JFactory::getEditor();
                                    $def_value = $newsletter->get('welcome');
                                    if ($isNew)
                                        $def_value = JText::_('DEF_NEWS_WELCOME');
                                    echo $editor->display("news_welcome", str_replace('&', '&amp;', $def_value), "90%", "", 75, 20);
                    ?>
                                </td>
                                <td width="10%" align="right" class="key">
                    <?php echo JText::_('SUGG_SUGGESTIONS'); ?>:<br><br>
                                </td>
                                <td width="20%">
                    <?php echo JText::_('SUGG_NEWS_WELCOME'); ?><br><br>
                                    <div id="news_welcome_tags">
                        <?php
                                    if (isset($this->taglist))
                                        echo JINCHTMLHelper::showTags($this->taglist, array('CON', 'ATTR'));
                        ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td width="10%" align="right" class="key">
                    <?php echo JText::_('INPUT_NEWS_DISCLAIMER'); ?>
                                </td>
                                <td width="60%">
                    <?php
                                    $editor = JFactory::getEditor();
                                    $news_disclaimer = str_replace('&', '&amp;', $newsletter->get('disclaimer'));
                                    echo $editor->display("news_disclaimer", $news_disclaimer, "90%", "", 75, 20);
                    ?>

                                </td>
                                <td width="10%" align="right" class="key">
                    <?php echo JText::_('SUGG_SUGGESTIONS'); ?>:<br><br>
                                </td>
                                <td width="20%">
                    <?php echo JText::_('SUGG_NEWS_DISCLAIMER'); ?><br><br>
                                    <div id="news_disclaimer_tags">
                        <?php
                                    if (isset($this->taglist))
                                        echo JINCHTMLHelper::showTags($this->taglist, array('CON', 'ATTR'));
                        ?>
                                </div>
                            </td>
                        </tr>
                    </table>
        <?php
                                    echo $pane->endPanel();
                                    echo $pane->startPanel(JText::_('TAB_TITLE_ACCESS'), 'panel3');
        ?>
                                    <table class="admintable">
                                        <tr>
                                            <td width="100" align="right" class="key">
                    <?php echo JText::_('INPUT_NEWS_ROLE_SUBSCRIBERS'); ?>:
                                </td>
                                <td>
                    <?php
                                    $rolesubscriber = isset($this->rolesubscriber) ? $this->rolesubscriber : array();
                                    $role_options = 'multiple="multiple" size="6" style="width:150px"';
                                    if (empty($rolesubscriber)) {
                                        echo '<select name="rolesubscriber[]" id="rolesubscriber" ' . $role_options . '></select>';
                                    } else {
                                        echo JHTML::_('select.genericlist', $rolesubscriber, 'rolesubscriber[]', $role_options, 'id', 'name');
                                    }
                    ?>
                                </td>
                                <td valign="middle" align="left">
                                    <a class="modal" rel="{handler: 'iframe', size: {x: 750, y: 600}}" href="index.php?option=com_jinc&controller=newsletter&tmpl=component&task=addrole&select=rolesubscriber&news_id=<?php echo $news_id; ?>"><span class="icon-add users"><span class="icon-text"><?php echo JText::_('BTN_ADDGROUP'); ?></span></span></a>
                                    <br><a href="#" onclick="removeRole('rolesubscriber');"><span class="icon-remove users"><span class="icon-text"><?php echo JText::_('BTN_REMOVEGROUP'); ?></span></span></a>
                                </td>
                                <td width="10%" align="right" class="key">
                    <?php echo JText::_('SUGG_SUGGESTIONS'); ?>:<br><br>
                                </td>
                                <td width="20%">
                    <?php echo JText::_('SUGG_NEWS_ROLE_SUBSCRIBERS'); ?><br>
                                </td>
                            </tr>
                        </table>
        <?php
                                    echo $pane->endPanel();
                                    echo $pane->startPanel(JText::_('TAB_TITLE_OPTIN'), 'panel4');
        ?>
                                    <table class="admintable">
                                        <tr>
                                            <td width="100" align="right" class="key">
                    <?php echo JText::_('INPUT_NEWS_OPTIN_SUBJECT'); ?>:
                                </td>
                                <td>
                    <?php
                                    $opt_in_sub = $newsletter->get('optin_subject');
                                    if ($isNew)
                                        $opt_in_sub = JText::_('DEF_NEWS_OPTIN_SUBJECT');
                    ?>
                                    <input class="text_area" type="text" name="news_optin_subject" id="news_optin_subject" size="50" maxlength="250" value="<?php echo $opt_in_sub; ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td width="10%" align="right" class="key">
                    <?php echo JText::_('INPUT_NEWS_OPTIN'); ?>
                                </td>
                                <td width="60%">
                    <?php
                                    $editor = JFactory::getEditor();
                                    $opt_in_msg = $newsletter->get('optin');
                                    if ($isNew)
                                        $opt_in_msg = JText::_('DEF_NEWS_OPTIN');
                                    echo $editor->display("news_optin", str_replace('&', '&amp;', $opt_in_msg), "90%", "", 75, 20);
                    ?>
                                </td>
                                <td width="10%" align="right" class="key">
                    <?php echo JText::_('SUGG_SUGGESTIONS'); ?>:<br><br>
                                </td>
                                <td width="20%">
                    <?php echo JText::_('SUGG_NEWS_OPTIN'); ?><br><br>
                    <?php echo JINCHTMLHelper::showTags($tags, array('OPTIN')); ?>
                                </td>
                            </tr>

                            <tr>
                                <td width="100" align="right" class="key">
                    <?php echo JText::_('INPUT_NEWS_OPTINREMOVE_SUBJECT'); ?>:
                                </td>
                                <td>
                    <?php
                                    $opt_in_unsub = $newsletter->get('optinremove_subject');
                                    if ($isNew)
                                        $opt_in_unsub = JText::_('DEF_NEWS_OPTINREMOVE_SUBJECT');
                    ?>
                                    <input class="text_area" type="text" name="news_optinremove_subject" id="news_optinremove_subject" size="50" maxlength="250" value="<?php echo $opt_in_unsub; ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td width="10%" align="right" class="key">
                    <?php echo JText::_('INPUT_NEWS_OPTINREMOVE'); ?>
                                </td>
                                <td width="60%">
                    <?php
                                    $editor = JFactory::getEditor();
                                    $opt_in_unmsg = $newsletter->get('optinremove');
                                    if ($isNew)
                                        $opt_in_unmsg = JText::_('DEF_NEWS_OPTINREMOVE');
                                    echo $editor->display("news_optinremove", str_replace('&', '&amp;', $opt_in_unmsg), "90%", "", 75, 20); ?>
                                </td>
                                <td width="10%" align="right" class="key">
                    <?php echo JText::_('SUGG_SUGGESTIONS'); ?>:<br><br>
                                </td>
                                <td width="20%">
                    <?php echo JText::_('SUGG_NEWS_OPTINREMOVE'); ?><br><br>
                    <?php echo JINCHTMLHelper::showTags($tags, array('OPTINREMOVE')); ?>
                                </td>
                            </tr>
                        </table>
        <?php
                                    echo $pane->endPanel();
                                    echo $pane->endPane();
        ?>
                                </div>
                                <div class="clr"></div>

                                <input type="hidden" name="option" value="com_jinc" />
                                <input type="hidden" name="news_id" value="<?php echo $newsletter->get('id'); ?>" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="controller" value="newsletter" />
</form>

<script language="Javascript" type="text/javascript">
    changeNewsType();
</script>