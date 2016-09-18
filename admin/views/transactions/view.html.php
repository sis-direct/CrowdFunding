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

class CrowdfundingViewTransactions extends JViewLegacy
{
    /**
     * @var JDocumentHtml
     */
    public $document;

    /**
     * @var Joomla\Registry\Registry
     */
    protected $state;

    /**
     * @var Joomla\Registry\Registry
     */
    protected $params;

    protected $items;
    protected $pagination;

    protected $amount;
    protected $currencies;
    protected $enabledSpecificPlugins;

    protected $option;

    protected $listOrder;
    protected $listDirn;
    protected $saveOrder;
    protected $saveOrderingUrl;
    protected $sortFields;

    protected $sidebar;

    /**
     * Payment plugins, which provides capture and void functionality.
     *
     * @var array
     */
    protected $specificPlugins = array('paypalexpress', 'paypaladaptive', 'stripeconnect');

    public function display($tpl = null)
    {
        $this->option = JFactory::getApplication()->input->get('option');

        $this->state      = $this->get('State');
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        $this->params = $this->state->get('params');

        // Get currencies
        $currencies = array();
        foreach ($this->items as $item) {
            $currencies[] = $item->txn_currency;
        }
        $currencies   = array_filter(array_unique($currencies));
        
        if (count($currencies) > 0) {
            $options = new Joomla\Registry\Registry;
            $options->set('locale_intl', $this->params->get('locale_intl'));
            $options->set('amount_format', $this->params->get('amount_format'));

            $this->currencies = new Crowdfunding\Currencies(JFactory::getDbo(), $options);
            $this->currencies->load(array('codes' => $currencies));
        }

        $this->amount = new Crowdfunding\Amount($this->params);

        // Get enabled specific plugins.
        $extensions                   = new Prism\Extensions(JFactory::getDbo(), $this->specificPlugins);
        $this->enabledSpecificPlugins = $extensions->getEnabled();

        
        // Prepare sorting data
        $this->prepareSorting();

        // Prepare actions
        $this->addToolbar();
        $this->addSidebar();
        $this->setDocument();

        parent::display($tpl);
    }

    /**
     * Prepare sortable fields, sort values and filters.
     */
    protected function prepareSorting()
    {
        // Prepare filters
        $this->listOrder = $this->escape($this->state->get('list.ordering'));
        $this->listDirn  = $this->escape($this->state->get('list.direction'));
        $this->saveOrder = (strcmp($this->listOrder, 'a.ordering') === 0);

        if ($this->saveOrder) {
            $this->saveOrderingUrl = 'index.php?option=' . $this->option . '&task=' . $this->getName() . '.saveOrderAjax&format=raw';
            JHtml::_('sortablelist.sortable', $this->getName() . 'List', 'adminForm', strtolower($this->listDirn), $this->saveOrderingUrl);
        }

        $this->sortFields = array(
            'b.name'             => JText::_('COM_CROWDFUNDING_BENEFICIARY'),
            'e.name'             => JText::_('COM_CROWDFUNDING_BACKER'),
            'c.title'            => JText::_('COM_CROWDFUNDING_PROJECT'),
            'a.txn_amount'       => JText::_('COM_CROWDFUNDING_AMOUNT'),
            'a.txn_date'         => JText::_('COM_CROWDFUNDING_DATE'),
            'a.service_provider' => JText::_('COM_CROWDFUNDING_PAYMENT_GETAWAY'),
            'a.txn_status'       => JText::_('COM_CROWDFUNDING_PAYMENT_STATUS'),
            'a.id'               => JText::_('JGRID_HEADING_ID')
        );
    }

    /**
     * Add a menu on the sidebar of page
     */
    protected function addSidebar()
    {
        // Add submenu
        CrowdfundingHelper::addSubmenu($this->getName());
        
        // Create object Filters and load some filters options.
        $filters = Crowdfunding\Filters::getInstance(JFactory::getDbo());

        // Get payment services.
        $paymentServices = $filters->getPaymentServices();
        JHtmlSidebar::addFilter(
            JText::_('COM_CROWDFUNDING_SELECT_PAYMENT_SERVICES'),
            'filter_payment_service',
            JHtml::_('select.options', $paymentServices, 'value', 'text', $this->state->get('filter.payment_service'), true)
        );

        // Get payment statuses.
        $paymentStatuses = $filters->getPaymentStatuses();
        JHtmlSidebar::addFilter(
            JText::_('COM_CROWDFUNDING_SELECT_PAYMENT_STATUS'),
            'filter_payment_status',
            JHtml::_('select.options', $paymentStatuses, 'value', 'text', $this->state->get('filter.payment_status'), true)
        );

        // Get reward states.
        $rewardDistributionStatuses = $filters->getRewardDistributionStatuses();
        JHtmlSidebar::addFilter(
            JText::_('COM_CROWDFUNDING_SELECT_REWARD_STATUS'),
            'filter_reward_state',
            JHtml::_('select.options', $rewardDistributionStatuses, 'value', 'text', $this->state->get('filter.reward_state'), true)
        );

        $this->sidebar = JHtmlSidebar::render();
    }

    /**
     * Add the page title and toolbar.
     *
     * @since   1.6
     */
    protected function addToolbar()
    {
        // Set toolbar items for the page
        JToolbarHelper::title(JText::_('COM_CROWDFUNDING_TRANSACTIONS_MANAGER'));
        JToolbarHelper::addNew('transaction.add');
        JToolbarHelper::editList('transaction.edit');

        // Add actions used for specific payment plugins.
        if (count($this->enabledSpecificPlugins) > 0) {
            JToolbarHelper::divider();

            // Add custom buttons
            $bar = JToolbar::getInstance('toolbar');
            $bar->appendButton('Confirm', JText::_('COM_CROWDFUNDING_QUESTION_CAPTURE'), 'checkin', JText::_('COM_CROWDFUNDING_CAPTURE'), 'payments.doCapture', true);
            $bar->appendButton('Confirm', JText::_('COM_CROWDFUNDING_QUESTION_VOID'), 'cancel-circle', JText::_('COM_CROWDFUNDING_VOID'), 'payments.doVoid', true);
        }

        JToolbarHelper::divider();
        JToolbarHelper::deleteList(JText::_('COM_CROWDFUNDING_DELETE_ITEMS_QUESTION'), 'transactions.delete');
        JToolbarHelper::divider();
        JToolbarHelper::custom('transactions.backToDashboard', 'dashboard', '', JText::_('COM_CROWDFUNDING_DASHBOARD'), false);
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $this->document->setTitle(JText::_('COM_CROWDFUNDING_TRANSACTIONS_MANAGER'));

        // Scripts
        JHtml::_('behavior.multiselect');
        JHtml::_('bootstrap.tooltip');
        JHtml::_('formbehavior.chosen', 'select');

        JHtml::_('Prism.ui.joomlaList');
        JHtml::_('Prism.ui.pnotify');
        JHtml::_('Prism.ui.joomlaHelper');

        $this->document->addScript('../media/' . $this->option . '/js/admin/' . JString::strtolower($this->getName()) . '.js');
    }
}
