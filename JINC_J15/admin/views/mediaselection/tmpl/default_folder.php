<?php defined('_JEXEC') or die('Restricted access'); ?>
<div class="imgOutline">
    <div class="imgTotal">
        <div align="center" class="imgBorder">
            <?php
            $attach_id = isset($this->attach_id)?$this->attach_id:0;
            ?>
            <a href="index.php?option=com_jinc&view=mediaselection&tmpl=component&attach_id=<?php echo $attach_id; ?>&folder=<?php echo $this->_tmp_folder->path_relative; ?>" target="_self">
                <img src="components/com_media/images/folder.png" width="80" height="80" border="0" /></a>
        </div>
    </div>
    <div class="controls">
    </div>
    <div class="imginfoBorder">
        <a href="index.php?option=com_jinc&view=mediaselection&tmpl=component&attach_id=<?php echo $attach_id; ?>&folder=<?php echo $this->_tmp_folder->path_relative; ?>" target="_self"><?php echo substr( $this->_tmp_folder->name, 0, 10 ) . ( strlen( $this->_tmp_folder->name ) > 10 ? '...' : ''); ?></a>
    </div>
</div>
