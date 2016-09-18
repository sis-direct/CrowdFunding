<?php
/**
 * @package      Crowdfunding
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * It is Crowdfunding helper class
 */
abstract class CrowdfundingHelper
{
    protected static $extension = 'com_crowdfunding';

    protected static $statistics = array();

    /**
     * Configure the Linkbar.
     *
     * @param    string $vName The name of the active view.
     *
     * @since    1.6
     */
    public static function addSubmenu($vName = 'dashboard')
    {
        JHtmlSidebar::addEntry(
            JText::_('COM_CROWDFUNDING_DASHBOARD'),
            'index.php?option=' . self::$extension . '&view=dashboard',
            $vName === 'dashboard'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_CROWDFUNDING_PROJECTS'),
            'index.php?option=' . self::$extension . '&view=projects',
            $vName === 'projects'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_CROWDFUNDING_TRANSACTIONS'),
            'index.php?option=' . self::$extension . '&view=transactions',
            $vName === 'transactions'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_CROWDFUNDING_CATEGORIES'),
            'index.php?option=com_categories&extension=' . self::$extension . '',
            $vName === 'categories'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_CROWDFUNDING_COMMENTS'),
            'index.php?option=' . self::$extension . '&view=comments',
            $vName === 'comments'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_CROWDFUNDING_COUNTRIES'),
            'index.php?option=' . self::$extension . '&view=countries',
            $vName === 'countries'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_CROWDFUNDING_CURRENCIES'),
            'index.php?option=' . self::$extension . '&view=currencies',
            $vName === 'currencies'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_CROWDFUNDING_LOCATIONS'),
            'index.php?option=' . self::$extension . '&view=locations',
            $vName === 'locations'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_CROWDFUNDING_LOGS'),
            'index.php?option=' . self::$extension . '&view=logs',
            $vName === 'logs'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_CROWDFUNDING_REPORTS'),
            'index.php?option=' . self::$extension . '&view=reports',
            $vName === 'reports'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_CROWDFUNDING_TYPES'),
            'index.php?option=' . self::$extension . '&view=types',
            $vName === 'types'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_CROWDFUNDING_USERS'),
            'index.php?option=' . self::$extension . '&view=users',
            $vName === 'users'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_CROWDFUNDING_UPDATES'),
            'index.php?option=' . self::$extension . '&view=updates',
            $vName === 'updates'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_CROWDFUNDING_TOOLS'),
            'index.php?option=' . self::$extension . '&view=tools',
            $vName === 'tools'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_CROWDFUNDING_PLUGINS'),
            'index.php?option=com_plugins&view=plugins&filter_search=' . rawurlencode('crowdfunding'),
            $vName === 'plugins'
        );

    }

    public static function getProjectTitle($projectId)
    {
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('a.title')
            ->from($db->quoteName('#__crowdf_projects', 'a'))
            ->where('a.id = ' . (int)$projectId);

        $db->setQuery($query);

        return $db->loadResult();
    }

    public static function getProject($projectId, array $fields = array('id'))
    {
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        $selectFields = array();
        foreach ($fields as $field) {
            $selectFields[] = $db->quoteName($field);
        }

        $query
            ->select($selectFields)
            ->from('#__crowdf_projects')
            ->where($db->quoteName('id') . ' = ' . (int)$projectId);

        $db->setQuery($query);

        return $db->loadObject();

    }

    public static function getUserIdByRewardId($rewardId)
    {
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('b.user_id')
            ->from($db->quoteName('#__crowdf_rewards', 'a'))
            ->innerJoin($db->quoteName('#__crowdf_projects', 'b') . ' ON a.project_id = b.id')
            ->where('a.id = ' . (int)$rewardId);

        $db->setQuery($query);
        $result = $db->loadResult();

        return (int)$result;
    }

    /**
     * This module collects statistical data about project - number of updates, comments, funders,...
     *
     * @param integer $projectId
     *
     * @return array
     */
    public static function getProjectData($projectId)
    {
        $db = JFactory::getDbo();

        /// Updates
        if (!isset(self::$statistics[$projectId])) {
            self::$statistics[$projectId] = array(
                'updates'  => null,
                'comments' => null,
                'funders'  => null
            );

        }

        // Count updates
        if (self::$statistics[$projectId]['updates'] === null) {

            $query = $db->getQuery(true);
            $query
                ->select('COUNT(*) AS updates')
                ->from($db->quoteName('#__crowdf_updates'))
                ->where('project_id = ' . (int)$projectId);

            $db->setQuery($query);

            self::$statistics[$projectId]['updates'] = $db->loadResult();
        }

        // Count comments
        if (self::$statistics[$projectId]['comments'] === null) {

            $query = $db->getQuery(true);
            $query
                ->select('COUNT(*) AS comments')
                ->from($db->quoteName('#__crowdf_comments'))
                ->where('project_id = ' . (int)$projectId)
                ->where('published = 1');

            $db->setQuery($query);

            self::$statistics[$projectId]['comments'] = $db->loadResult();
        }

        // Count funders
        if (self::$statistics[$projectId]['funders'] === null) {

            $query = $db->getQuery(true);
            $query
                ->select('COUNT(*) AS funders')
                ->from($db->quoteName('#__crowdf_transactions', 'a'))
                ->where('a.project_id  = ' . (int)$projectId)
                ->where('(a.txn_status = ' . $db->quote('completed') . ' OR a.txn_status = '. $db->quote('pending') . ')');

            $db->setQuery($query);

            self::$statistics[$projectId]['funders'] = $db->loadResult();
        }

        return self::$statistics[$projectId];
    }

    /**
     * Generate a path to the folder, where the images are stored.
     *
     * @param int    $userId User Id.
     * @param string $path   A base path to the folder. It can be JPATH_BASE, JPATH_ROOT, JPATH_SITE,...
     *
     * @return string
     */
    public static function getImagesFolder($userId = 0, $path = '')
    {
        $params = JComponentHelper::getParams(self::$extension);
        /** @var $params Joomla\Registry\Registry */

        $folder = $path . DIRECTORY_SEPARATOR . $params->get('images_directory', 'images/crowdfunding');

        if ((int)$userId > 0) {
            $folder .= DIRECTORY_SEPARATOR . 'user' . (int)$userId;
        }

        return JPath::clean($folder);
    }

    /**
     * Generate a path to the temporary images folder.
     *
     * @param string $path   A base path to the folder. It can be JPATH_BASE, JPATH_ROOT, JPATH_SITE,...
     *
     * @return string
     */
    public static function getTemporaryImagesFolder($path = '')
    {
        $params = JComponentHelper::getParams(self::$extension);
        /** @var $params Joomla\Registry\Registry */

        return JPath::clean($path .DIRECTORY_SEPARATOR. $params->get('images_directory', 'images/crowdfunding') .DIRECTORY_SEPARATOR. 'temporary');
    }

    /**
     * Generate a URI path to the folder, where the images are stored.
     *
     * @param int $userId User Id.
     *
     * @return string
     */
    public static function getImagesFolderUri($userId = 0)
    {
        $params = JComponentHelper::getParams(self::$extension);
        /** @var $params Joomla\Registry\Registry */

        $uriImages = $params->get('images_directory', 'images/crowdfunding');

        if ((int)$userId > 0) {
            $uriImages .= '/user' . (int)$userId;
        }

        return $uriImages;
    }

    /**
     * Generate a URI path to the temporary images folder.
     *
     * @return string
     */
    public static function getTemporaryImagesFolderUri()
    {
        $params = JComponentHelper::getParams(self::$extension);
        /** @var $params Joomla\Registry\Registry */

        return $params->get('images_directory', 'images/crowdfunding') . '/temporary';
    }

    /**
     * Create a folder and index.html file.
     *
     * @param string $folder
     *
     * @return string
     */
    public static function createFolder($folder)
    {
        JFolder::create($folder);

        $folderIndex = JPath::clean($folder . DIRECTORY_SEPARATOR . 'index.html');
        $buffer      = "<!DOCTYPE html><title></title>";

        jimport('joomla.filesystem.file');
        JFile::write($folderIndex, $buffer);
    }

    /**
     * Generate a URI string by a given list of parameters.
     *
     * @param array $params
     *
     * @return string
     */
    public static function generateUrlParams($params)
    {
        $result = '';
        foreach ($params as $key => $param) {
            $result .= '&' . rawurlencode($key) . '=' . rawurlencode($param);
        }

        return $result;
    }

    /**
     * Prepare date format.
     *
     * @param bool $calendar
     *
     * @return string
     *
     * @deprecated since v2.5
     */
    public static function getDateFormat($calendar = false)
    {
        $params = JComponentHelper::getParams('com_crowdfunding');
        /** @var  $params Joomla\Registry\Registry */

        $dateFormat = $params->get('project_date_format', 'Y-m-d');

        if ($calendar) {
            switch($dateFormat) {
                case 'Y-m-d':
                    $dateFormat = 'YYYY-MM-DD';
                    break;
                case 'd-m-Y':
                    $dateFormat = 'DD-MM-YYYY';
                    break;
                case 'm-d-Y':
                    $dateFormat = 'MM-DD-YYYY';
                    break;
            }
        }

        return $dateFormat;
    }

    /**
     * Prepare an amount, parsing it from formatted string to decimal value.
     * This is most used when a user post a data via form.
     *
     * @param float $value
     *
     * @return string|float
     */
    public static function parseAmount($value)
    {
        $params = JComponentHelper::getParams('com_crowdfunding');
        /** @var  $params Joomla\Registry\Registry */

        // Get currency
        $currency       = Crowdfunding\Currency::getInstance(JFactory::getDbo(), $params->get('project_currency'));

        // Parse the goal amount.
        $amount = new Crowdfunding\Amount($params, $value);
        $amount->setCurrency($currency);

        return $amount->parse();
    }

    public static function prepareCategories($items)
    {
        $result = array();

        if (!empty($items)) {

            foreach ($items as $key => $item) {

                // Decode parameters
                if (!empty($item->params)) {
                    $item->params = json_decode($item->params, true);

                    // Generate a link to the picture.
                    if (is_array($item->params)) {
                        $image = Joomla\Utilities\ArrayHelper::getValue($item->params, 'image');
                        if (!empty($image)) {
                            $item->image_link = JUri::base().$image;
                        }
                    }
                }

                // Generate lines by number of items in a row.
                $result[$key] = $item;
            }
        }

        return $result;
    }

    public static function prepareItems($items)
    {
        $result = array();

        if (!empty($items)) {
            foreach ($items as $key => $item) {

                // Calculate funding end date
                if (!empty($item->funding_days)) {

                    $fundingStartDate = new Crowdfunding\Date($item->funding_start);
                    $endDate = $fundingStartDate->calculateEndDate($item->funding_days);
                    $item->funding_end = $endDate->format('Y-m-d');

                }

                // Calculate funded percentage.
                $percent = new Prism\Math();
                $percent->calculatePercentage($item->funded, $item->goal, 0);
                $item->funded_percents = (string)$percent;

                // Calculate days left
                $today = new Crowdfunding\Date();
                $item->days_left = $today->calculateDaysLeft($item->funding_days, $item->funding_start, $item->funding_end);

                $result[$key] = $item;
            }
        }

        return $result;
    }

    public static function fetchIds(array $items = array(), $column = 'id')
    {
        $result = array();

        foreach ($items as $key => $item) {
            if (is_object($item) and isset($item->$column)) {
                $result[] = (int)$item->$column;
            } elseif (is_array($item) and array_key_exists($column, $item)) {
                $result[] = (int)$item[$column];
            } else {
                continue;
            }
        }

        $result = array_filter(array_unique($result));
        sort($result);

        return $result;
    }

    /**
     * @param array $items
     *
     * @return array
     *
     * @deprecated 2.5
     */
    public static function fetchUserIds(array $items = array())
    {
        $result = array();

        if (count($items) > 0) {
            foreach ($items as $key => $item) {
                if (is_object($item) and isset($item->user_id)) {
                    $result[] = (int)$item->user_id;
                } elseif (is_array($item) and array_key_exists('user_id', $item)) {
                    $result[] = (int)$item['user_id'];
                } else {
                    continue;
                }
            }
        }

        $result = array_unique($result);
        sort($result);

        return $result;
    }

    public static function prepareIntegrations($socialPlatform, array $userIds)
    {
        $options = new \Joomla\Registry\Registry(array(
            'platform' => $socialPlatform,
            'user_ids' => $userIds
        ));

        $socialProfilesBuilder = new Prism\Integration\Profiles\Factory($options);
        return $socialProfilesBuilder->create();
    }

    public static function isRewardsEnabled($projectId)
    {
        // Check for enabled rewards by component options.
        $componentParams = JComponentHelper::getParams('com_crowdfunding');
        if (!$componentParams->get('rewards_enabled', 1)) {
            return false;
        }

        // Check for enabled rewards by project type.
        $project = Crowdfunding\Project::getInstance(JFactory::getDbo(), $projectId);
        $type    = $project->getType();
        if ($type === null) {
            return true;
        }

        return (bool)(($type instanceof Crowdfunding\Type) and $type->isRewardsEnabled());
    }

    /**
     * Check for possibility user to accesses a project.
     * If there are no user ID or item, it will be able to create project.
     *
     * @param int $userId
     * @param stdClass $item
     * @param string $layout
     *
     * @return bool
     */
    public static function isAuthorized($userId, $item, $layout)
    {
        if ((strcmp('basic', $layout) === 0) and !(int)$item->id) {
            return true;
        }

        return (bool)((int)$item->user_id === (int)$userId);
    }

    /**
     * Route URI to front-end.
     *
     * @param string  $url
     *
     * @return string
     */
    public static function siteRoute($url)
    {
        $appSite    = JApplicationCms::getInstance('site');
        $routerSite = $appSite->getRouter('site');

        $routedUri  = $routerSite->build($url);
        if ($routedUri instanceof JUri) {
            $routedUri = $routedUri->toString();
        }

        if (false !== strpos($routedUri, '/administrator')) {
            $routedUri = str_replace('/administrator', '', $routedUri);
        }

        return JUri::root().$routedUri;
    }
}
