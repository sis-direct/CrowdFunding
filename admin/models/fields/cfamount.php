<?php
/**
 * @package      Crowdfunding
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

jimport('Prism.init');
jimport('Crowdfunding.init');

class JFormFieldCfAmount extends JFormField
{
    /**
     * The form field type.
     *
     * @var    string
     *
     * @since  11.1
     */
    protected $type = 'cfamount';

    /**
     * Method to get the field input markup.
     *
     * @return  string  The field input markup.
     *
     * @since   11.1
     */
    protected function getInput()
    {
        // Initialize some field attributes.
        $size      = $this->element['size'] ? ' size="' . (int)$this->element['size'] . '"' : '';
        $maxLength = $this->element['maxlength'] ? ' maxlength="' . (int)$this->element['maxlength'] . '"' : '';
        $readonly  = ((string)$this->element['readonly'] === 'true') ? ' readonly="readonly"' : '';
        $disabled  = ((string)$this->element['disabled'] === 'true') ? ' disabled="disabled"' : '';
        $class     = (!empty($this->element['class'])) ? ' class="' . (string)$this->element['class'] . '"' : '';
        $required  = $this->required ? ' required aria-required="true"' : '';

        $cssLayout = (!empty($this->element['css_layout'])) ? (string)$this->element['css_layout'] : 'Bootstrap 2';

        // Initialize JavaScript field attributes.
        $onchange = $this->element['onchange'] ? ' onchange="' . (string)$this->element['onchange'] . '"' : '';

        $params = JComponentHelper::getParams('com_crowdfunding');
        /** @var  $params Joomla\Registry\Registry */

        $currency = Crowdfunding\Currency::getInstance(JFactory::getDbo(), $params->get('project_currency'));

        // Prepare amount object.
        $amount = new Crowdfunding\Amount($params, $this->value);
        $amount->setCurrency($currency);

        $html   = array();
        if ($cssLayout === 'Bootstrap 3') {

            $html[] = '<div class="input-group">';

            if ($currency->getSymbol()) { // Prepended
                $html[] = '<div class="input-group-addon">' . $currency->getSymbol() . '</div>';
            }

            $html[] = '<input type="text" name="' . $this->name . '" id="' . $this->id . '"' . ' value="' . $amount->format() . '"' . $class . $size . $disabled . $readonly . $maxLength . $onchange . $required . '/>';

            // Prepend
            $html[] = '<div class="input-group-addon">' . $currency->getCode() . '</div>';

            $html[] = '</div>';

        } else { // Bootstrap 2

            if ($currency->getSymbol()) { // Prepended
                $html[] = '<div class="input-prepend input-append"><span class="add-on">' . $currency->getSymbol() . '</span>';
            } else { // Append
                $html[] = '<div class="input-append">';
            }

            $html[] = '<input type="text" name="' . $this->name . '" id="' . $this->id . '"' . ' value="' . $amount->format() . '"' . $class . $size . $disabled . $readonly . $maxLength . $onchange . $required . '/>';

            // Appended
            $html[] = '<span class="add-on">' . $currency->getCode() . '</span></div>';
        }

        return implode("\n", $html);
    }
}
