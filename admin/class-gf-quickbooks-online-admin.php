<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      0.1.0
 *
 * @package    GFQuickbooksOnline
 * @subpackage GFQuickbooksOnline/admin
 */
namespace AppSol\GFQuickbooksOnline\Admin;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    GFQuickbooksOnline
 * @subpackage GFQuickbooksOnline/admin
 * @author     Stuart Laverick <stuart@appropriatesolutions.co.uk>
 */
class Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    0.1.0
	 */
	public function enqueueStyles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in GFQuickbooksOnline_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The GFQuickbooksOnline_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/gf-quickbooks-online-admin.css', array(), $this->version, 'all');

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    0.1.0
	 */
	public function enqueueScripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in GFQuickbooksOnline_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The GFQuickbooksOnline_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/gf-quickbooks-online-admin.js', array('jquery'), $this->version, false);

	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function showFormsMenuPage()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/class-gf-quickbooks-online-admin-config.php';
	}

}
