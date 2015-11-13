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
    public static function getInstance() {
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
        \GFAddOn::register('GFQuickbooksOnline');
    }

    /**
     * Show the Forms menu page overrides GFAddOn::plugin_page
     *
     * @return void
     **/
    public function plugin_page()
    {
        $admin = new Admin\Admin();
        $admin->showFormsMenuPage();
    }

}
