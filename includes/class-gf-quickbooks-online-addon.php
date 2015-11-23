<?php
/**
 * Extension class for the Gravity Forms AddOn Framework
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

\GFForms::include_addon_framework();

/**
 * Extends GFAddOn
 *
 * This class provides access to the Gravity Forms AddOn Framework
 *
 * @since      0.1.0
 * @package    GFQuickbooksOnline
 * @subpackage GFQuickbooksOnline/includes
 * @author     Stuart Laverick <stuart@appropriatesolutions.co.uk>
 */
class GFQuickbooksOnlineAddOn extends \GFAddOn {

    protected $_version = GF_QUICKBOOKS_ONLINE_VERSION;
    protected $_min_gravityforms_version = "1.9.14";
    protected $_slug = "gfquickbooksonline";
    protected $_path = "gf-quickbooks-online/gf-quickbooks-online.php";
    protected $_full_path = GF_QUICKBOOKS_ONLINE_PATH;
    protected $_title = "Gravity Forms to Quickbooks Online Add-On";
    protected $_short_title = "Quickbooks";

    /**
     * Static class instance
     *
     * @var AppSol\GFQuickbooksOnline\Includes\GFQuickbooksOnlineAddOn
     **/
    private static $_instance = null;

    /**
     * Return the single instance of the class
     *
     * @return AppSol\GFQuickbooksOnline\Includes\GFQuickbooksOnlineAddOn
     **/
    public static function get_instance() {
        if ( self::$_instance == null ) {
            self::$_instance = new GFQuickbooksOnlineAddOn();
        }

        return self::$_instance;
    }

    /**
     * Initialise the context
     *
     * Long Description.
     *
     * @since    0.1.0
     */
    public function load() {
        \GFAddOn::register('AppSol\GFQuickbooksOnline\Includes\GFQuickbooksOnlineAddOn');
    }

    /**
     * Show the Forms menu page overrides GFAddOn::plugin_page
     *
     * @return void
     **/
    public function plugin_page() {
        print __FILE__;
    }

    /**
     * Show the Settings page tab
     *
     * @return void
     **/
    public function plugin_settings_fields()
    {
        return [
            [
                'title' => 'Quickbooks Online API Settings',
                'fields' => [
                    [
                        'name' => 'qbapptoken',
                        'type' => 'text',
                        'label' => 'App Token'
                    ],
                    [
                        'name' => 'qbconsumerkey',
                        'type' => 'text',
                        'label' => 'Consumer Key'
                    ],
                    [
                        'name' => 'qbconsumersecret',
                        'type' => 'text',
                        'label' => 'Consumer Secret'
                    ],
                    [
                        'name' => 'qbrealmid',
                        'type' => 'text',
                        'label' => 'Realm ID'
                    ],
                    [
                        'name' => 'qbapistatus',
                        'type' => 'radio',
                        'label' => 'API version',
                        'tooltip' => 'Send the data to the live or sandbox account',
                        'horizontal' => true,
                        'default_value' => 'sandbox',
                        'choices' => [
                            [
                                'label' => 'Sandbox',
                                'value' => 'sandbox'
                            ],
                            [
                                'label' => 'Production',
                                'value' => 'production'
                            ]
                        ]
                    ],
                    [
                        'name' => 'qbconnect',
                        'type' => 'qbintuitconnect',
                        'label' => 'Connection'
                    ]
                ]
            ]
        ];
    }

    /**
     * Load the scripts required by the plugin
     *
     * @return void
     * @author 
     **/
    public function scripts()
    {
        $scripts = [
            [
                'handle' => 'intuit_ipp_anywhere',
                'src' => 'https://appcenter.intuit.com/Content/IA/intuit.ipp.anywhere.js',
                'version' => null,
                'in_footer' => false,
                'callback' => [],
                'enqueue' => [
                    ['admin_page' => 'plugin_settings']
                ]
            ],
            [
                'handle' => 'gf_quickbooks_online_intuit',
                'src' => $this->get_base_url() . '/admin/js/gf-quickbooks-online-intuit.js',
                'deps' => ['intuit_ipp_anywhere'],
                'strings' => [
                    'grantUrl' => admin_url('admin.php?page=' . $_GET['page'] . (isset($_GET['subview'])? '&subview=' . $_GET['subview'] : '') . '&qbconnect')
                ],
                'enqueue' => [
                    ['admin_page' => 'plugin_settings']
                ]
            ]
        ];

        return array_merge( parent::scripts(), $scripts );
    }

    /**
     * Show the connection status or the connect button
     *
     * @return void
     **/
    public function settings_qbintuitconnect()
    {
        error_log('GFQuickbooksOnlineAddOn::settings_qbintuitconnect');
        if ($this->get_plugin_setting('qbconsumerkey') && $this->get_plugin_setting('qbconsumersecret') && $this->get_plugin_setting('qbrealmid')) {
            $qbApi = QBRequest::get_instance();
            if ($company = $qbApi->getCompanyInfo()) {
                print '<p>' . __('Connected to Company ', 'gf-quickbooks-online') . ':<br />' . $company['CompanyInfo']['CompanyName'] . '</p>';
            } else {
                require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/gf-quickbooks-online-admin-connect.php';
            }
        } else {
            print '<p>Complete all fields before connecting</p>';
        }
    }

    /**
     * Display validation or response errors in a Wordpress alert
     *
     * @return void
     **/
    public function displayErrors()
    {
        error_log('GFQuickbooksOnlineAddOn::displayErrors');
        $html = [];
        if (count(QBOAuth::get_instance()->connectionErrors)) {
            $html[] = '<div class="error">';
            foreach (QBOAuth::get_instance()->connectionErrors as $error) {
                $html[] = '<p>' . $error . '</p>';
            }
            $html[] = '</div>';
        }
        if (count(QBRequest::get_instance()->responseErrors)) {
            $html[] = '<div class="error">';
            foreach (QBRequest::get_instance()->responseErrors as $error) {
                $html[] = '<p>' . $error['message'] . '</p>';
            }
            $html[] = '</div>';
        }
        if (count(QBRequest::get_instance()->validationErrors)) {
            $html[] = '<div class="error">';
            foreach (QBRequest::get_instance()->validationErrors as $error) {
                $html[] = '<p>' . implode("<br />", $error) . '</p>';
            }
            $html[] = '</div>';
        }
        if (count($html)) {
            print implode("\n", $html);
        }
    }

}
