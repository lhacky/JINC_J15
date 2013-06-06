<?php defined('_JEXEC') or die('Restricted access'); ?>
<div class="imgOutline">
    <div class="imgTotal">
        <div align="center" class="imgBorder">
            <?php
            $attach_id = isset($this->attach_id)?$this->attach_id:0;
            $js_submit = "window.parent.document.adminForm.msg_attachment_" . $attach_id . ".value = \"" . addslashes($this->_tmp_doc->path_relative) . "\"; window.parent.document.getElementById(\"sbox-window\").close();";
            ?>
            <a onclick='<?php echo $js_submit; ?>' style="display: block; width: 100%; height: 100%">
                <img border="0" src="<?php echo $this->_tmp_doc->icon_32 ?>" alt="<?php echo $this->_tmp_doc->name; ?>" />
            </a>
        </div>
    </div>
    <div class="controls">
    </div>
    <div class="imginfoBorder">
        <?php echo $this->_tmp_doc->name; ?>
    </div>
</div>