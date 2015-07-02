<?php
/**
 * @package      Crowdfunding
 * @subpackage   Currencies
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

namespace Crowdfunding;

use Prism;
use Joomla\Utilities\ArrayHelper;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality that manage currencies.
 *
 * @package      Crowdfunding
 * @subpackage   Currencies
 */
class Currencies extends Prism\Database\ArrayObject
{
    /**
     * Initialize the object.
     *
     * <code>
     * $currencies   = new Crowdfunding\Currencies(\JFactory::getDbo());
     * </code>
     *
     * @param \JDatabaseDriver $db
     */
    public function __construct(\JDatabaseDriver $db)
    {
        $this->db       = $db;
    }

    /**
     * Load currencies data by ID from database.
     *
     * <code>
     * $ids = array(1,2,3,4,5);
     * $currencies   = new Crowdfunding\Currencies(\JFactory::getDbo());
     * $currencies->load($ids);
     *
     * foreach($currencies as $currency) {
     *   echo $currency["title"];
     *   echo $currency["code"];
     * }
     * </code>
     *
     * @param array $ids
     */
    public function load($ids = array())
    {
        // Load project data
        $query = $this->db->getQuery(true);

        $query
            ->select("a.id, a.title, a.code, a.symbol, a.position")
            ->from($this->db->quoteName("#__crowdf_currencies", "a"));

        if (!empty($ids)) {
            ArrayHelper::toInteger($ids);
            $query->where("a.id IN ( " . implode(",", $ids) . " )");
        }

        $this->db->setQuery($query);
        $results = $this->db->loadAssocList();

        if (!$results) {
            $results = array();
        }

        $this->items = $results;
    }

    /**
     * Load currencies data by abbreviation from database.
     *
     * <code>
     * $ids = array("GBP", "EUR", "USD");
     * $currencies   = new Crowdfunding\Currencies(\JFactory::getDbo());
     * $currencies->loadByCode($ids);
     *
     * foreach($currencies as $currency) {
     *   echo $currency["title"];
     *   echo $currency["code"];
     * }
     * </code>
     *
     * @param array $ids
     */
    public function loadByCode($ids = array())
    {
        // Load project data
        $query = $this->db->getQuery(true);

        $query
            ->select("a.id, a.title, a.code, a.symbol, a.position")
            ->from($this->db->quoteName("#__crowdf_currencies", "a"));

        if (!empty($ids)) {

            foreach ($ids as $key => $value) {
                $ids[$key] = $this->db->quote($value);
            }

            $query->where("a.code IN ( " . implode(",", $ids) . " )");
        }

        $this->db->setQuery($query);
        $results = $this->db->loadAssocList();

        if (!$results) {
            $results = array();
        }

        $this->items = $results;
    }

    /**
     * Create a currency object by abbreviation and return it.
     *
     * <code>
     * $ids = array(1,2,3,4,5);
     * $currencies   = new Crowdfunding\Currencies(\JFactory::getDbo());
     * $currencies->load($ids);
     *
     * $currency = $currencies->getCurrencyByCode("EUR");
     * </code>
     *
     * @param string $code
     *
     * @throws \UnexpectedValueException
     *
     * @return null|Currency
     */
    public function getCurrencyByCode($code)
    {
        if (!$code) {
            throw new \UnexpectedValueException(\JText::_("LIB_CROWDFUNDING_INVALID_CURRENCY_ABBREVIATION"));
        }

        $currency = null;

        foreach ($this->items as $item) {
            if (strcmp($code, $item["code"]) == 0) {
                $currency = new Currency();
                $currency->bind($item);
                break;
            }
        }

        return $currency;
    }

    /**
     * Create a currency object and return it.
     *
     * <code>
     * $ids = array(1,2,3,4,5);
     * $currencies   = new Crowdfunding\Currencies(\JFactory::getDbo());
     * $currencies->load($ids);
     *
     * $currencyId = 1;
     * $currency = $currencies->getCurrency($currencyId);
     * </code>
     *
     * @param int $id
     *
     * @throws \UnexpectedValueException
     *
     * @return null|Currency
     */
    public function getCurrency($id)
    {
        if (!$id) {
            throw new \UnexpectedValueException(\JText::_("LIB_CROWDFUNDING_INVALID_CURRENCY_ID"));
        }

        $currency = null;

        foreach ($this->items as $item) {
            if ($id == $item["id"]) {
                $currency = new Currency();
                $currency->bind($item);
                break;
            }
        }

        return $currency;
    }
}
