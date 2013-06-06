<?php
isset($newsletter) || die('Newsletter not found');
$front_theme = $newsletter->get('front_theme');
$news_type = $newsletter->getType();

echo JHTML::stylesheet($front_theme, 'components/com_jinc/assets/themes/');
echo JHTML::script('jinc.js', 'components/com_jinc/assets/js/');
?>
<script language="JavaScript" type="text/javascript">
    function mod_checkPublic(form, alerttxt) {
        if (!mod_validate_mail(form.mod_user_mail, "<?php echo JText::_('ERROR_ERR039'); ?>" ))
        return false;
        return true;
    }

    function mod_checkForm(form_name) {
        var form = document.forms[form_name];
<?php
if ($news_type == NEWSLETTER_PUBLIC_NEWS)
    echo 'return mod_checkPublic(form);';
else
    echo 'return true;';
?>
        return false;
    }
</script>
<form action="index.php" method="post" onSubmit="return mod_checkForm('jinc_form_<?php echo $news_id; ?>');" id="jinc_form_<?php echo $news_id; ?>" name="jinc_form_<?php echo $news_id; ?>">
    <div class="jinc_mod_frm_subscription">
        <table width="100%" border="0">
            <?php
            $news_type = $newsletter->getType();
            if ($news_type == NEWSLETTER_PUBLIC_NEWS) {
            ?>
                <tr>
                    <td width="100%">
                    <?php echo JText::_('INPUT_USERMAIL') . '*'; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="user_mail" id="mod_user_mail" maxlength="127" size="10">
                </td>
            </tr>
            <?php
                }
            ?>

            </table>
            <br><br>
            <input type="submit" class="btn" value="<?php echo JText::_('BTN_UNSUBSCRIBE'); ?>">
            <input type="hidden" name="option" value="com_jinc">
            <input type="hidden" name="controller" value="newsletter">
            <input type="hidden" name="task" value="unsubscribe">
            <input type="hidden" name="news_id" value="<?php echo $news_id; ?>">
            <input type="hidden" name="mod_jinc" value="true">
        <?php echo JHTML::_('form.token'); ?>
    </div>
</form>