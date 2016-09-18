<?php
/**
 * @package      Crowdfunding
 * @subpackage   Currencies
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Crowdfunding;

use Prism\Database;
use Joomla\Utilities\ArrayHelper;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality that manage currencies.
 *
 * @package      Crowdfunding
 * @subpackage   Currencies
 */
class Currencies extends Database\Collection
{
    /**
     * Load currencies data by ID from database.
     *
     * <code>
     * $options = array(
     *     "ids" => array(1,2,3,4,5),  // Use this option to load the currencies by IDs.
     *     "codes" => array("USD", "GBP") // Use this option to load the currencies by code.
     * );
     * $currencies   = new Crowdfunding\Currencies(\JFactory::getDbo());
     * $currencies->load($options);
     *
     * foreach($currencies as $currency) {
     *   echo $currency["title"];
     *   echo $currency["code"];
     * }
     * </code>
     *
     * @param array $options
     */
    public function load(array $options = array())
    {
        // Get IDs.
        $ids = (array_key_exists('ids', $options) and is_array($options['ids'])) ? $options['ids'] : array();
        $ids = ArrayHelper::toInteger($ids);

        // Get codes.
        $codes = (array_key_exists('codes', $options) and is_array($options['codes'])) ? $options['codes'] : array();

        $query = $this->db->getQuery(true);

        $query
            ->select('a.id, a.title, a.code, a.symbol, a.position')
            ->from($this->db->quoteName('#__crowdf_currencies', 'a'));

        // Filter by IDs
        if (count($ids) > 0) {
            $query->where('a.id IN ( ' . implode(',', $ids) . ' )');
        }

        // Filter by currency code.
        if (count($codes) > 0) {
            foreach ($codes as $key => $value) {
                $codes[$key] = $this->db->quote($value);
            }

            $query->where('a.code IN ( ' . implode(',', $codes) . ' )');
        }

        $this->db->setQuery($query);
        $this->items = (array)$this->db->loadAssocList();
    }

    /**
     * Create a currency object and return it.
     *
     * <code>
     * $options = array(
     *     "ids" => array(1,2,3,4,5),
     *     "codes" => array("USD", "GBP")
     * );
     *
     * $currencies   = new Crowdfunding\Currencies(\JFactory::getDbo());
     * $currencies->load($options);
     *
     * $currencyId = 1;
     * $currency = $currencies->getCurrency($currencyId);
     * </code>
     *
     * @param int|string $id Currency ID or Currency code.
     *
     * @throws \UnexpectedValueException
     *
     * @return Currency
     */
    public function getCurrency($id)
    {
        if (!$id) {
            throw new \UnexpectedValueException(\JText::_('LIB_CROWDFUNDING_INVALID_CURRENCY_ID'));
        }

        $currency = null;

        foreach ($this->items as $item) {

            if (is_numeric($id) and (int)$id === (int)$item['id']) {
                $currency = new Currency($this->db);
                $currency->bind($this->items[$id]);
                break;

            } elseif (strcmp($id, $item['code']) === 0) {
                $currency = new Currency($this->db);
                $currency->bind($item);
                break;
            }
        }

        return $currency;
    }

    /**
     * Return the currencies as array with objects.
     *
     * <code>
     * $options = array(
     *     "ids" => array(1,2,3,4,5),
     *     "codes" => array("USD", "GBP")
     * );
     *
     * $currencies   = new Crowdfunding\Currencies(\JFactory::getDbo());
     * $currencies->load($options);
     *
     * $currencies = $currencies->getCurrencies();
     * </code>
     *
     * @return array
     */
    public function getCurrencies()
    {
        $results = array();

        $i = 0;
        foreach ($this->items as $item) {
            $currency[$i] = new Currency($this->db);
            $currency[$i]->bind($item);
            $i++;
        }

        return $results;
    }
}
