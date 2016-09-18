<?php
/**
 * @package      Crowdfunding
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

if (strcmp('five_steps', $this->wizardType) === 0) {
    $layout      = new JLayoutFile('project_wizard');
} else {
    $layout      = new JLayoutFile('project_wizard_six_steps');
}
echo $layout->render($this->layoutData);
?>

<div class="row">
    <div class="col-md-6">
        <form action="<?php echo JRoute::_('index.php?option=com_crowdfunding'); ?>" method="post" name="projectForm" id="js-cf-project-form" novalidate="novalidate" autocomplete="off" enctype="multipart/form-data" >

            <div class="form-group">
                <?php echo $this->form->getLabel('title'); ?>
                <?php echo $this->form->getInput('title'); ?>
            </div>

            <div class="form-group">
            <?php echo $this->form->getLabel('short_desc'); ?>
            <?php echo $this->form->getInput('short_desc'); ?>
            </div>

            <div class="form-group">
            <?php echo $this->form->getLabel('catid'); ?>
            <?php echo $this->form->getInput('catid'); ?>
            </div>

            <div class="form-group">
            <?php echo $this->form->getLabel('location_preview'); ?>
            <?php echo $this->form->getInput('location_preview'); ?>
            </div>

            <?php if ($this->numberOfTypes === 1) {
                $type = $this->types[0];
            ?>
            <input type="hidden" name="jform[type_id]" value="<?php echo ($type instanceof Crowdfunding\Type) ? $type->getId() : 0; ?>" />
            <?php } elseif ($this->numberOfTypes > 1) {?>
                <div class="form-group">
                <?php echo $this->form->getLabel('type_id'); ?>
                <?php echo $this->form->getInput('type_id'); ?>
                </div>
            <?php } else { ?>
                <input type="hidden" name="jform[type_id]" value="0" />
            <?php }?>
            
            <?php 
			if($this->params->get('project_terms', 0) and $this->isNew) {
			    $termsUrl = $this->params->get('project_terms_url', '');
			?>
			<div class="checkbox">
                <label>
                    <input type="checkbox" name="jform[terms]" value="1" required="required"> <?php echo (!$termsUrl) ? JText::_('COM_CROWDFUNDING_TERMS_AGREEMENT') : JText::sprintf('COM_CROWDFUNDING_TERMS_AGREEMENT_URL', $termsUrl);?>
                </label>
            </div>
            <?php }?>
            
            <?php echo $this->form->getInput('id'); ?>
            <?php echo $this->form->getInput('location_id'); ?>
            
            <input type="hidden" name="task" value="project.save" />
            <?php echo JHtml::_('form.token'); ?>
            
            <button type="submit" class="btn btn-primary mtb-15-0" <?php echo $this->disabledButton;?>>
            	<span class="fa fa-check"></span>
                <?php echo JText::_('COM_CROWDFUNDING_SAVE_AND_CONTINUE')?>
            </button>
        </form>
    </div>

    <div class="col-md-6">
        <?php if (!$this->debugMode) {?>
        <div class="mb-15">
            <span class="btn btn-default fileinput-button">
                <span class="fa fa-upload"></span>
                <span><?php echo JText::_('COM_CROWDFUNDING_UPLOAD_IMAGE');?></span>
                <!-- The file input field used as target for the file upload widget -->
                <input id="js-thumb-fileupload" type="file" name="project_image" data-url="<?php echo JRoute::_('index.php?option=com_crowdfunding&task=project.uploadImage&format=raw');?>" />
            </span>

            <a href="<?php echo JRoute::_('index.php?option=com_crowdfunding&task=project.removeImage&id='.$this->item->id.'&'.JSession::getFormToken().'=1');?>" id="js-btn-remove-image" class="btn btn-danger" style="display: <?php echo $this->displayRemoveButton; ?>">
                <span class="fa fa-trash"></span>
                <?php echo JText::_('COM_CROWDFUNDING_REMOVE_IMAGE');?>
            </a>

            <span class="btn hasTooltip ml-10" title="<?php echo JText::sprintf('COM_CROWDFUNDING_MEDIA_FILES_ALLOWED_S', $this->imageWidth, $this->imageHeight, $this->maxFilesize);?>">
                <span class="fa fa-question-circle" title="" ></span>
            </span>

            <img src="media/com_crowdfunding/images/ajax-loader.gif" width="16" height="16" id="js-thumb-fileupload-loader" style="display: none;" />

            <div id="js-image-tools" class="mt-10" style="display: none;">
                <a href="javascript: void(0);" class="btn btn-primary" id="js-crop-btn">
                    <span class="fa fa-check-circle"></span>
                    <?php echo JText::_('COM_CROWDFUNDING_CROP_IMAGE');?>
                </a>

                <a href="javascript: void(0);" class="btn btn-default" id="js-crop-btn-cancel">
                    <span class="fa fa-ban"></span>
                    <?php echo JText::_('COM_CROWDFUNDING_CANCEL');?>
                </a>
            </div>

        </div>
        <form action="<?php echo JRoute::_('index.php?option=com_crowdfunding');?>" method="post" id="js-image-tools-form">
            <input type="hidden" name="<?php echo JSession::getFormToken(); ?>" value="1" />
        </form>
        <?php }?>

        <div id="js-fixed-dragger-cropper">
            <img src="<?php echo $this->imagePath; ?>" class="img-polaroid" id="js-thumb-img" />
        </div>


    </div>

</div>