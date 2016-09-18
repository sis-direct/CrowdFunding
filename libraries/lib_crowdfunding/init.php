<?php
/**
 * @package      Crowdfunding
 * @subpackage   Initialization
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

if (!defined('CROWDFUNDING_PATH_COMPONENT_ADMINISTRATOR')) {
    define('CROWDFUNDING_PATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR . '/components/com_crowdfunding');
}

if (!defined('CROWDFUNDING_PATH_COMPONENT_SITE')) {
    define('CROWDFUNDING_PATH_COMPONENT_SITE', JPATH_SITE . '/components/com_crowdfunding');
}

if (!defined('CROWDFUNDING_PATH_LIBRARY')) {
    define('CROWDFUNDING_PATH_LIBRARY', JPATH_LIBRARIES . '/Crowdfunding');
}

JLoader::registerNamespace('Crowdfunding', JPATH_LIBRARIES);

// Register some helpers
JLoader::register('CrowdfundingHelper', CROWDFUNDING_PATH_COMPONENT_ADMINISTRATOR . '/helpers/crowdfunding.php');
JLoader::register('CrowdfundingHelperRoute', CROWDFUNDING_PATH_COMPONENT_SITE . '/helpers/route.php');

// Register some Joomla! classes
JLoader::register('JHtmlString', JPATH_LIBRARIES . '/joomla/html/html/string.php');
JLoader::register('JHtmlCategory', JPATH_LIBRARIES . '/joomla/html/html/category.php');

// Include HTML helpers path
JHtml::addIncludePath(CROWDFUNDING_PATH_COMPONENT_SITE . '/helpers/html');

// Register Observers
JLoader::register('CrowdfundingObserverReward', CROWDFUNDING_PATH_COMPONENT_ADMINISTRATOR . '/tables/observers/reward.php');
JObserverMapper::addObserverClassToClass('CrowdfundingObserverReward', 'CrowdfundingTableReward', array('typeAlias' => 'com_crowdfunding.reward'));

// Load library language
$lang = JFactory::getLanguage();
$lang->load('lib_crowdfunding', CROWDFUNDING_PATH_COMPONENT_SITE);

// Register class aliases.
JLoader::registerAlias('CrowdfundingCategories', '\\Crowdfunding\\Categories');

JLog::addLogger(
    array(
        // Sets file name
        'text_file' => 'com_crowdfunding.errors.php'
    ),
    // Sets messages of all log levels to be sent to the file
    JLog::CRITICAL + JLog::EMERGENCY + JLog::ERROR,
    // The log category/categories which should be recorded in this file
    // In this case, it's just the one category from our extension, still
    // we need to put it inside an array
    array('com_crowdfunding')
);
