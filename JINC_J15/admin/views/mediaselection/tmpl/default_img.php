<?php defined('_JEXEC') or die('Restricted access'); ?>
<div class="imgOutline">
    <div class="imgTotal">
        <div align="center" class="imgBorder">
            <?php
            $attach_id = isset($this->attach_id)?$this->attach_id:0;
            $js_submit = "window.parent.document.adminForm.msg_attachment_" . $attach_id . ".value = \"" . addslashes($this->_tmp_img->path_relative) . "\"; window.parent.document.getElementById(\"sbox-window\").close();";
            ?>
            <a onclick='<?php echo $js_submit; ?>' class="img-preview"  style="display: block; width: 100%; height: 100%">
                <div class="image">
                    <img src="<?php echo COM_MEDIA_BASEURL.'/'.$this->_tmp_img->path_relative; ?>" width="<?php echo $this->_tmp_img->width_60; ?>" height="<?php echo $this->_tmp_img->height_60; ?>" alt="<?php echo $this->_tmp_img->name; ?> - <?php echo MediaHelper::parseSize($this->_tmp_img->size); ?>" border="0" />
                </div></a>
        </div>
    </div>
    <div class="controls">
    </div>
    <div class="imginfoBorder">
        <a onclick='<?php echo $js_submit; ?>' class="preview"><?php echo $this->escape( substr( $this->_tmp_img->name, 0, 10 ) . ( strlen( $this->_tmp_img->name ) > 10 ? '...' : '')); ?></a>
    </div>
</div>
