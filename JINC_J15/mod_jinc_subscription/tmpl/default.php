<?php
isset($newsletter) || die('Newsletter not found');
isset($input_style) || die('Input Style not defined');

$front_theme = $newsletter->get('front_theme');
$news_type = $newsletter->getType();

JHTML::_('behavior.modal');
echo JHTML::stylesheet($front_theme, 'components/com_jinc/assets/themes/');
echo JHTML::script('jinc.js', 'components/com_jinc/assets/js/');
?>
<script language="JavaScript" type="text/javascript">
    function mod_checkPublic(form, alerttxt) {
        if (!mod_validate_mail(form.mod_user_mail, "<?php echo JText::_('ERROR_ERR039'); ?>" ))
        return false;
        return true;
    }

    function mod_checkAttributes(form) {
<?php
$attrs = $newsletter->get('attributes');
$attrs_array = $attrs->toArray();
foreach ($attrs_array as $attr_name => $attr_value) {
    if ($attr = $ninstance->loadAttribute($attr_name)) {
        if ($attr_value == ATTRIBUTE_MANDATORY) {
            echo 'var alert_msg = "' . JText::_($attr->get('name_i18n')) . ' is a mandatory argument";';
            echo 'if (!mod_validate_required(form.mod_' . $attr_name . ', alert_msg)) ';
            echo 'return false;';
        }
        if ($attr->get('type') == ATTRIBUTE_TYPE_INTEGER) {
            echo 'var alert_msg = "' . JText::_($attr->get('name_i18n')) . ' must be and integer value";';
            echo 'if (!mod_validate_integer(form.mod_' . $attr_name . ', alert_msg)) ';
            echo 'return false;';
        }
    } else {
        die('Error loading attribute');
    }
}
?>
        return true;
    }

    function mod_checkForm(form_name) {
        var form = document.forms[form_name];
        var notice_accept = form.notice_accept;
        if (notice_accept != undefined) {
            if (!(notice_accept.checked)) {
                alert("<?php echo JText::_('ERROR_FER027_JS') ?>");
                return false;
            }
        }

        if (mod_checkAttributes(form)) {

<?php
if ($news_type == NEWSLETTER_PUBLIC_NEWS)
    echo 'return mod_checkPublic(form);';
else
    echo 'return true;';
?>
        }
        return false;
    }
</script>
<form action="index.php" method="post" onSubmit="return mod_checkForm('jinc_form_<?php echo $news_id; ?>');" id="jinc_form_<?php echo $news_id; ?>" name="jinc_form_<?php echo $news_id; ?>">
    <div class="jinc_mod_frm_subscription">
        <table width="100%" border="0">
            <?php
            jincimport('frontend.jincinputstandard');
            jincimport('frontend.jincinputminimal');

            $renderer = ($input_style == NEWSLETTER_INPUT_STYLE_MINIMAL) ?
                    new JINCInputMinimal() : new JINCInputStandard();

            $renderer->preRender();
            if ($news_type == NEWSLETTER_PUBLIC_NEWS) {
                $attribute = new Attribute(-1);
                $attribute->set('name', 'user_mail');
                $attribute->set('type', ATTRIBUTE_TYPE_EMAIL);
                $attribute->set('name_i18n', 'INPUT_USERMAIL');

                $renderer->modRender($attribute, TRUE);
            }
            foreach ($attrs_array as $attr_name => $attr_value) {
                $attr = $ninstance->loadAttribute($attr_name);
                $renderer->modRender($attr, $attr_value == ATTRIBUTE_MANDATORY);
            }
            ?>

            <?php
            if ($newsletter->get('captcha') > CAPTCHA_NO) {
                $renderer->modCaptchaRender();
            }
            ?>

            <?php
            if (isset($notice)) {
                $notice_id = $notice->get('id');
                ?>
                <tr>
                    <td align="left" class="jinc_notice" id="notice_accept">
                        <input type="checkbox" name="notice_accept" />
                        <a class="modal" rel="{handler: 'iframe', size: {x: 700, y: 500}, onClose: function() {}}"
                           href="<?php echo JRoute::_('index.php?option=com_jinc&view=notice&tmpl=component&noti_id=' . $notice_id); ?>" >
                               <?php
                               echo $notice->get('title');
                               ?>
                        </a>
                        .
                        <?php
                        echo $notice->get('bdesc');
                        ?>
                    </td>
                </tr>
                <?php
            }
            ?>

        </table>
        <br><br>
        <input type="submit" class="btn" value="<?php echo JText::_('BTN_SUBSCRIBE'); ?>">
        <input type="hidden" name="option" value="com_jinc">
        <input type="hidden" name="controller" value="newsletter">
        <input type="hidden" name="task" value="subscribe">
        <input type="hidden" name="news_id" value="<?php echo $news_id; ?>">
        <input type="hidden" name="mod_jinc" value="true">
        <?php echo JHTML::_('form.token'); ?>
    </div>
</form>