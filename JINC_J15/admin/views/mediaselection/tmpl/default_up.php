<?php defined('_JEXEC') or die('Restricted access'); ?>
<div class="imgOutline">
    <div class="imgTotal">
        <div align="center" class="imgBorder">
            <?php
            $attach_id = isset($this->attach_id)?$this->attach_id:0;
            ?>
            <a href="index.php?option=com_jinc&view=mediaselection&tmpl=component&attach_id=<?php echo $attach_id; ?>&folder=<?php echo $this->state->parent; ?>" target="_self">
                <img src="components/com_media/images/folderup_32.png" width="32" height="32" border="0" alt=".." /></a>
        </div>
    </div>
    <div class="controls">
        <span>&nbsp;</span>
    </div>
    <div class="imginfoBorder">
        <a href="index.php?option=com_jinc&view=mediaselection&tmpl=component&attach_id=<?php echo $attach_id; ?>&folder==<?php echo $this->state->parent; ?>" target="_self">..</a>
    </div>
</div>