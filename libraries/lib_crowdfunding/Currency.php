<?php
/**
 * @package      Crowdfunding
 * @subpackage   Currencies
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

namespace Crowdfunding;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing currency.
 *
 * @package      Crowdfunding
 * @subpackage   Currencies
 */
class Currency
{
    protected $id;
    protected $title;
    protected $code;
    protected $symbol;
    protected $position;

    /**
     * Database driver.
     *
     * @var \JDatabaseDriver
     */
    protected $db;

    protected static $instances = array();

    /**
     * Initialize the object.
     *
     * <code>
     * $currencyId = 1;
     * $currency   = new Crowdfunding\Currency(\JFactory::getDbo());
     * $currency->load($currencyId);
     * </code>
     *
     * @param \JDatabaseDriver $db
     */
    public function __construct(\JDatabaseDriver $db = null)
    {
        $this->db = $db;
    }

    /**
     * Create an object or return existing one.
     *
     * <code>
     * $currencyId = 1;
     *
     * $currency   = Crowdfunding\Currency::getInstance(\JFactory::getDbo(), $currencyId);
     * </code>
     *
     * @param \JDatabaseDriver $db
     * @param int             $id
     *
     * @return null|self
     */
    public static function getInstance(\JDatabaseDriver $db, $id)
    {
        if (!isset(self::$instances[$id])) {
            $item = new Currency($db);
            $item->load($id);

            self::$instances[$id] = $item;
        }

        return self::$instances[$id];
    }

    /**
     * Load currency data from database by ID.
     *
     * <code>
     * $currencyId = 1;
     *
     * $currency   = new Crowdfunding\Currency(\JFactory::getDbo());
     * $currency->load($currencyId);
     * </code>
     *
     * @param int $id
     */
    public function load($id)
    {
        $query = $this->db->getQuery(true);
        $query
            ->select("a.id, a.title, a.code, a.symbol, a.position")
            ->from($this->db->quoteName("#__crowdf_currencies", "a"))
            ->where("a.id = " . (int)$id);

        $this->db->setQuery($query);
        $result = $this->db->loadAssoc();

        if (!$result) {
            $result = array();
        }

        $this->bind($result);
    }

    /**
     * Load currency data from database.
     *
     * <code>
     * $currencyCode = "EUR";
     *
     * $currency   = new Crowdfunding\Currency(\JFactory::getDbo());
     * $currency->loadByCode($currencyCode);
     * </code>
     *
     * @param string $code
     */
    public function loadByCode($code)
    {
        $query = $this->db->getQuery(true);
        $query
            ->select("a.id, a.title, a.code, a.symbol, a.position")
            ->from($this->db->quoteName("#__crowdf_currencies", "a"))
            ->where("a.code = " . $this->db->quote($code));

        $this->db->setQuery($query);
        $result = $this->db->loadAssoc();

        if (!$result) {
            $result = array();
        }

        $this->bind($result);
    }

    /**
     * Set data about currency to object parameters.
     *
     * <code>
     * $data = array(
     *  "title"  => "Pound sterling",
     *  "symbol" => "£"
     * );
     *
     * $currency   = new Crowdfunding\Currency(\JFactory::getDbo());
     * $currency->bind($data);
     * </code>
     *
     * @param array $data
     * @param array $ignored
     *
     */
    public function bind($data, $ignored = array())
    {
        foreach ($data as $key => $value) {
            if (!in_array($key, $ignored)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Return currency ID.
     *
     * <code>
     * $currencyId  = 1;
     *
     * $currency    = new Crowdfunding\Currency(\JFactory::getDbo());
     * $currency->load($currencyId);
     *
     * if (!$currency->getId()) {
     * ....
     * }
     * </code>
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return currency code (abbreviation).
     *
     * <code>
     * $currencyId  = 1;
     *
     * $currency    = new Crowdfunding\Currency(\JFactory::getDbo());
     * $currency->load($currencyId);
     *
     * // Return GBP
     * $code = $currency->getCode();
     * </code>
     *
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Return currency symbol.
     *
     * <code>
     * $currencyId  = 1;
     *
     * $currency    = new Crowdfunding\Currency(\JFactory::getDbo());
     * $currency->load($currencyId);
     *
     * // Return £
     * $symbol = $currency->getSymbol();
     * </code>
     *
     * @return int
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * Return the position of currency symbol.
     *
     * <code>
     * $currencyId  = 1;
     *
     * $currency    = new Crowdfunding\Currency(\JFactory::getDbo());
     * $currency->load($currencyId);
     *
     * // Return 0 = beginning; 1 = end;
     * if (0 == $currency->getPosition()) {
     * ...
     * }
     * </code>
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }
}
