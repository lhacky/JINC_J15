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

isset ($this->message) or die('Message not defined');
$message = $this->message;
$msg_editor = JFactory::getEditor();

JHTML::script('phplivex.js', 'administrator/components/com_jinc/assets/js/');
JHTML::script('commons.js', 'administrator/components/com_jinc/assets/js/');
$ajax_loader = JHTML::image(JURI::base() . 'components/com_jinc/assets/images/icons/simple-loader.gif', JText::_('ALT_LOADING'), array( "height" => 16, "width" => 16));
jincimport('utility.PHPLiveX');
jincimport('utility.jinchtmlhelper');

$msg_attachment = $message->get('attachment');
$count_attachment = count($msg_attachment->toArray());
?>

<script type="text/javascript">
    var nAttachment = <?php echo max(1, $count_attachment); ?>;

    function chooseFile(id) {
        var attButton = document.getElementById("attButton");
        var anchorAtt = document.getElementById("anchorAtt");
        anchorAtt.setAttribute('href', 'index.php?option=com_jinc&view=mediaselection&tmpl=component&attach_id=' + id + '&folder=');
        attButton.click();
    }

    function addAttachment() {
        for (i = 0; i < nAttachment; i++) {
            var myTextField = document.getElementById('msg_attachment_' + i);
            if(myTextField.value == "") {
                myTextField.focus();
                return;
            }
        }
        var tbl = document.getElementById('attTable');
        var lastRow = tbl.rows.length;
        // if there's no header row in the table, then iteration = lastRow + 1
        var iteration = lastRow;
        var row = tbl.insertRow(lastRow);

        var cellRight = row.insertCell(0);

        var el = document.createElement('input');
        el.setAttribute('class', 'text_area');
        el.setAttribute('type', 'text');
        el.setAttribute('name', 'msg_attachment[' + nAttachment+ ']');
        el.setAttribute('id', 'msg_attachment_' + nAttachment);
        el.setAttribute('size', '50');
        el.setAttribute('maxlength', '250');
        el.setAttribute('value', '');

        var anc = document.createElement('a');
        anc.setAttribute('class', 'modal-button');
        anc.setAttribute('style', 'text-decoration: underline;');
        anc.setAttribute('title', 'File');
        anc.setAttribute('href', '#');
        var call = 'chooseFile(' + nAttachment + ')';
        anc.onclick=new Function(call);

        var inp = document.createElement('input');
        inp.setAttribute('type', 'button');
        inp.setAttribute('value', '<?php echo JText::_('LABEL_ATTACHMENT_CHOOSE'); ?>');
        anc.appendChild(inp);

        cellRight.appendChild(el);
        cellRight.appendChild(anc);
        nAttachment++;
    }

    function getTemplate(tem_id) {
        var plx = new PHPLiveX();
        return plx.ExternalRequest({
            'content_type': 'json',
            'url': 'index.php?option=com_jinc&controller=template&task=jsonTemplateInfo&format=raw',
            'onFinish': function(response){
                var content = <?php echo $msg_editor->getContent('msg_body'); ?>
                var answer = true;
                if ((content != undefined) && (content.length > 0)) answer = confirm ('<?php echo addslashes(JText::_( 'JS_WARNING_MSG_OVERWRITE' )); ?>');
                if (answer) {                    
                    document.getElementById("msg_subject").value = response.subject;
                    var editor_name = '<?php echo $msg_editor->_name; ?>';
                    if (editor_name.match("^jce")=="jce") {                        
                        if (WFEditor != undefined) 
                          WFEditor.setContent('msg_body',response.body);
                        else
                          JContentEditor.setContent('msg_body',response.body);
                    } else {
                        <?php echo $msg_editor->setContent('msg_body', 'response.body'); ?>
                    }                    
                }
            },
            'params':{'tem_id': tem_id}
        });
    }

    function getDefaultTemplate() {
        news_id = document.getElementById("msg_news_id").value;
        var plx = new PHPLiveX();
        return plx.ExternalRequest({
            'content_type': 'json',
            'preloader':'pr_news',
            'url': 'index.php?option=com_jinc&controller=newsletter&task=jsonDefaultTemplate&format=raw',
            'onFinish': function(response){
                var tem_id = response.tem_id;
                if (tem_id != 0) {
                    getTemplate(tem_id);
                }
            },
            'params':{'news_id': news_id}
        });
    }
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
    <div class="col100">
        <fieldset class="adminform">
            <legend><?php echo JText::_( 'DETAILS' ); ?></legend>

            <table class="admintable" width="100%">
                <tr>
                    <td width="12%"></td>
                    <td width="48%"></td>
                    <td width="12%"></td>
                    <td width="28%"></td>
                </tr>
                <tr>
                    <td align="right" class="key">
                        <?php echo JText::_( 'INPUT_MSG_SUBJECT' ); ?>:
                    </td>
                    <td >
                        <input class="text_area" type="text" name="msg_subject" id="msg_subject" size="50" maxlength="250" value="<?php echo $message->get('subject');?>" />
                    </td>
                    <td align="right" class="key">
                        <?php
                        echo JHTML::tooltip(JText::_('TT_MSG_BULKMAIL'), JText::_('TT_TITLE_MSG_BULKMAIL'),
                        '', JText::_('INPUT_MSG_BULKMAIL'));
                        ?>
                    </td>
                    <td>
                        <?php
                        $msg_bulkmail = $message->getType();
                        echo JHTML::_('select.booleanlist', 'msg_bulkmail', null, $msg_bulkmail, JText::_( 'INPUT_MSG_BULKMAIL_YES' ), JText::_( 'INPUT_MSG_BULKMAIL_NO' ));
                        ?>
                    </td>

                </tr>
                <tr>
                    <td align="right" class="key">
                        <?php echo JText::_( 'INPUT_NEWS_NAME' ); ?>:
                    </td>
                    <td>
                        <?php
                        $msg_news_id = $message->get('news_id');
                        echo JHTML::_('select.genericlist', isset($this->newsletters)?$this->newsletters:null, 'msg_news_id', "onchange=\"getDefaultTemplate();\"", 'news_id', 'news_name', $msg_news_id);
                        ?>
                        <span id="pr_news" style="visibility:hidden;">&nbsp;&nbsp;
                            <?php echo $ajax_loader; ?>
                        </span>
                    </td>
                    <td align="right" class="key">
                        <?php
                        echo JHTML::tooltip(JText::_('TT_MSG_CONTENTTYPE'), JText::_('TT_TITLE_MSG_CONTENTTYPE'),
                        '', JText::_('INPUT_MSG_CONTENTTYPE'));
                        ?>
                    </td>
                    <td>
                        <?php
                        $msg_plaintext = $message->get('plaintext');
                        echo JHTML::_('select.booleanlist', 'msg_plaintext', null, $msg_plaintext, "Plain Text", "HTML");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td align="left" class="key">
                        <span class="icon-add users">
                            <span class="icon-text">
                                <a href="javascript:addAttachment();">
                                    <?php echo JText::_('BTN_ADDATTACHMENT'); ?>
                                </a>
                            </span>
                        </span>
                    </td>
                    <td colspan="3">
                        <table cellpadding="5px" width="100%" id="attTable">
                            <tr>
                                <td valign="middle">
                                    <input class="text_area" type="text" name="msg_attachment[0]" id="msg_attachment_0" size="50" maxlength="250" value="<?php echo $msg_attachment->get('0');?>" />
                                    <a id="anchorAtt" name="anchorAtt" class="modal-button" style="text-decoration: underline;" title="File" href="index.php?option=com_jinc&view=mediaselection&tmpl=component&attach_id=0&folder=" rel="{handler: 'iframe', size: {x: 700, y: 500}}" >
                                        <input type="button" id="attButton" name="attButton" value="<?php echo JText::_('LABEL_ATTACHMENT_CHOOSE'); ?>">
                                    </a>
                                </td>
                            </tr>
                            <?php
                            for ($i = 1 ; $i < $count_attachment ; $i++) {
                                echo '<tr><td>';
                                echo '<input class="text_area" type="text" name="msg_attachment[' . $i . ']" id="msg_attachment_' . $i . '" size="50" maxlength="250" value="' . $msg_attachment->get($i) . '" />&nbsp;';
                                echo '<a id="anchorAtt" name="anchorAtt" class="modal-button" style="text-decoration: underline;" title="File" href="index.php?option=com_jinc&view=mediaselection&tmpl=component&attach_id=' . $i . '&folder=" rel="{handler: \'iframe\', size: {x: 700, y: 500}}" >';
                                echo '<input type="button" id="attButton" name="attButton" value=" ' . JText::_('LABEL_ATTACHMENT_CHOOSE'). '">';
                                echo '</a>';
                                echo '</td></tr>';
                            }
                            ?>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="left" class="key">
                        <?php echo JText::_( 'INPUT_MSG_BODY' ); ?>
                    </td>
                    <td colspan="2">
                        <?php
                        echo $msg_editor->display("msg_body", str_replace('&','&amp;',$message->get('body')), "90%", "", 75, 20);
                        ?>
                    </td>
                    <td>
                        <?php
                        if (isset ($this->taglist))
                            echo JINCHTMLHelper::showTags($this->taglist, array('CON', 'ATTR'));
                        ?>
                    </td>
                </tr>
            </table>
        </fieldset>
    </div>
    <div class="clr"></div>

    <input type="hidden" name="msg_id" value="<?php echo $message->get('id'); ?>" />
    <input type="hidden" name="option" value="com_jinc" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="controller" value="message" />
    <input type="hidden" name="view" value="messages" />
</form>