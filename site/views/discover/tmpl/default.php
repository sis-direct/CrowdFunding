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
$numberOfItems = count($this->items);
?>
<div class="cfdiscover<?php echo $this->pageclass_sfx; ?>">
    <?php if ($this->params->get('show_page_heading', 1)) { ?>
        <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php } ?>

    <?php if ($numberOfItems === 0) { ?>
        <p class="alert alert-warning"><?php echo JText::_('COM_CROWDFUNDING_NO_ITEMS_MATCHING_QUERY'); ?></p>
    <?php } ?>

    <?php if ($numberOfItems > 0) {
        $layout      = new JLayoutFile($this->params->get('grid_layout', 'items_grid'));
        echo $layout->render($this->layoutData);
    }?>

    <?php if (((int)$this->params->def('show_pagination', 1) === 1 or ((int)$this->params->get('show_pagination') === 2)) and ((int)$this->pagination->get('pages.total') > 1)) { ?>
        <div class="pagination">
        <?php if ($this->params->def('show_pagination_results', 1)) { ?>
            <p class="counter pull-right"> <?php echo $this->pagination->getPagesCounter(); ?> </p>
        <?php } ?>
        <?php echo $this->pagination->getPagesLinks(); ?> </div>
    <?php } ?>
</div>