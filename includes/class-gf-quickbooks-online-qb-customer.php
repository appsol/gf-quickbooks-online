<?php
/**
 * Interface class for the Intuit Quickbooks API SDK
 *
 * @link       http://example.com
 * @since      0.1.0
 *
 * @package    GFQuickbooksOnline
 * @subpackage GFQuickbooksOnline/includes
 */
namespace AppSol\GFQuickbooksOnline\Includes;

use AppSol\GFQuickbooksOnline\Admin as Admin;
use AppSol\GFQuickbooksOnline\Pub as Pub;
use Guzzle\Http\Client;
use Guzzle\Plugin\Oauth\OauthPlugin;

/**
 * Provides access to the functionality of the Intuit Quickbooks API SDK
 *
 * @since      0.1.0
 * @package    GFQuickbooksOnline
 * @subpackage GFQuickbooksOnline/includes
 * @author     Stuart Laverick <stuart@appropriatesolutions.co.uk>
 **/
class QBCustomer
{
    /**
     * Quickbooks API Request object
     *
     * @var AppSol\GFQuickbooksOnline\Includes\QBRequest
     **/
    private $qbApi;

    /**
     * Class constructor
     *
     * @param AppSol\GFQuickbooksOnline\Includes\QBRequest $qbApi
     * @return void
     **/
    public function __construct($qbApi)
    {
        $this->qbApi = $qbApi;


    }

    /**
     * Get a full result for an individual Customer
     *
     * @return void
     * @author 
     **/
    public function getById($id)
    {
        return $this->qbApi->read('customer', $id);
    }

    /**
     * Get a summary result for all Customers
     *
     * @return array
     **/
    public function getAll()
    {
        $query = 'SELECT Id, DisplayName, PrimaryEmailAddr, PrimaryPhone FROM Customer WHERE Active = true';
        return $this->qbApi->query($query);
    }

    /**
     * Add a custom button to the Advanced Fields area
     * Called by Filter gform_add_field_buttons
     *
     * @param $fieldGroups array
     * @return array
     **/
    public function addQuickbooksCustomerField($fieldGroups)
    {
        foreach ($fieldGroups as &$group) {
            if ($group['name'] == 'advanced_fields') {
                $group['fields'][] = [
                    'class' => 'button',
                    'data-type' => 'qbcustomer',
                    'value' => __('QB Customer', 'gf-quickbooks-online'),
                    'onclick' => "StartAddField('qbcustomer')"
                ];
                break;
            }
        }
        return $fieldGroups;
    }

    /**
     * Add the Quickbooks Customer Field title
     * Called by Filter gform_field_type_title
     *
     * @return string
     **/
    public function setFieldTitle($type)
    {
        if ($type == 'qbcustomer') {
            return __('Quickbooks Customer', 'gf-quickbooks-online');
        }
    }

    /**
     * Add the Quickbooks Customer Field title
     * Called by Filter gform_field_input
     *
     * @return string
     **/
    public function setFieldInput($input, $field, $value, $lead_id, $form_id)
    {
        $customers = $this->getAll();
        error_log(print_r($customers, true));
        if ($field->type == 'qbcustomer') {
            $tabIndex = \GFCommon::get_tabindex();
            $class = isset($field->cssClass)? $field->cssClass : '';
            $qbCustomers = json_encode([]);
            ob_start();
            require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/gf-quickbooks-online-public-customer-autocomplete.php';
            $input = ob_get_clean();
        }
        return $input;
    }

    /**
     * Add the jQueryUI Autocomplete script to the public form
     * Called by Filter gform_enqueue_scripts
     *
     * @return void
     **/
    public function addAutoCompleteScript($form, $isAjax)
    {
        foreach ($form['fields'] as $field) {
            if ($field->type == 'qbcustomer' && isset($field->field_qbcustomer)) {
                wp_enqueue_script('jquery-ui-autocomplete');
                $jsUrl = plugin_dir_url(dirname(__FILE__)) . 'public/js/gf-quickbooks-online-public.js';
                wp_enqueue_script('qb_customer_autocomplete', $jsUrl ['jquery-ui-autocomplete']);
                break;
            }
        }
    }

    /**
     * Add the Javascript required to render the admin field UI
     * Called by Filter gform_editor_js
     *
     * @return void
     **/
    public function addEditorScript()
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/gf-quickbooks-online-admin-customer-autocomplete.php';
    }
} // END class 