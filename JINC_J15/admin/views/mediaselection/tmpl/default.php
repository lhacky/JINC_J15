<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php
$user =& JFactory::getUser();
$attach_id = isset($this->attach_id)?$this->attach_id:0;
$canUpload= ($user->authorize('com_media', 'upload'));
if ($canUpload) :
    ?>
<form action="index.php?option=com_media&amp;task=file.upload&amp;tmpl=component" id="uploadForm" method="post" enctype="multipart/form-data">
    <fieldset>
            <?php $config =& JComponentHelper::getParams('com_media'); ?>
        <legend><?php echo JText::_( 'Upload File' ); ?> [ <?php echo JText::_( 'Max' ); ?>&nbsp;<?php echo ($config->get('upload_maxsize') / 1000000); ?>M ]</legend>
        <fieldset class="actions">
            <legend></legend>
            <input type="file" id="file-upload" name="Filedata" />
            <input type="submit" id="file-upload-submit" value="<?php echo JText::_('Start Upload'); ?>"/>
            <span id="upload-clear"></span>
        </fieldset>
        <ul class="upload-queue" id="upload-queue">
            <li style="display: none" />
        </ul>
    </fieldset>
    <input type="hidden" name="return-url" value="<?php echo base64_encode('index.php?option=com_jinc&view=mediaselection&tmpl=component&attach_id='. $attach_id . '&'); ?>" />
    <input type="hidden" name="folder" value="<?php echo $this->state->folder; ?>">
        <?php echo JHTML::_( 'form.token' ); ?>
</form>
<?php 
endif;
?>

<form action="index.php?option=com_media&amp;tmpl=component&amp;folder=<?php echo $this->state->folder; ?>" method="post" id="mediamanager-form" name="mediamanager-form">
    <div class="manager">
        <table width="100%" cellspacing="0">
            <tbody>
                <?php echo $this->loadTemplate('up'); ?>

                <?php for ($i=0,$n=count($this->folders); $i<$n; $i++) :
                    $this->setFolder($i);
                    echo $this->loadTemplate('folder');
                endfor; ?>

                <?php for ($i=0,$n=count($this->documents); $i<$n; $i++) :
                    $this->setDoc($i);
                    echo $this->loadTemplate('doc');
                endfor; ?>

                <?php for ($i=0,$n=count($this->images); $i<$n; $i++) :
                    $this->setImage($i);
                    echo $this->loadTemplate('img');
                endfor; ?>
            </tbody>
        </table>
    </div>
    <input type="hidden" name="task" value="list" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>