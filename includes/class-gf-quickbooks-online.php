<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
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

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      0.1.0
 * @package    GFQuickbooksOnline
 * @subpackage GFQuickbooksOnline/includes
 * @author     Stuart Laverick <stuart@appropriatesolutions.co.uk>
 */
class GFQuickbooksOnline {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      GFQuickbooksOnline_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      string    $pluginName    The string used to uniquely identify this plugin.
	 */
	protected $pluginName;

	/**
	 * The current version of the plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function __construct() {

		$this->pluginName = 'gf-quickbooks-online';
		$this->version = '0.1.0';

		$this->loadDependencies();
		$this->setLocale();
		$this->defineAdminHooks();
		$this->definePublicHooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - GFQuickbooksOnline_Loader. Orchestrates the hooks of the plugin.
	 * - GFQuickbooksOnline_i18n. Defines internationalization functionality.
	 * - GFQuickbooksOnline_Admin. Defines all hooks for the admin area.
	 * - GFQuickbooksOnline_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function loadDependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-gf-quickbooks-online-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-gf-quickbooks-online-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-gf-quickbooks-online-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-gf-quickbooks-online-public.php';

		/**
		 * The class that extends the Gravity Forms Framework
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-gf-quickbooks-online-addon.php';

		$this->loader = new Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the GFQuickbooksOnline_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function setLocale() {

		$plugin_i18n = new i18n();
		$plugin_i18n->setDomain($this->getPluginName());

		$this->loader->addAction('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function defineAdminHooks() {

		$plugin_admin = new Admin\Admin($this->getPluginName(), $this->getVersion());

		$this->loader->addAction('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->addAction('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

		$gfAddOn = GFQuickbooksOnlineAddOn::getInstance();

		$this->loader->addAction('gform_loaded', $gfAddOn, 'load', 5);

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function definePublicHooks() {

		$plugin_public = new Pub\Pub($this->getPluginName(), $this->getVersion());

		$this->loader->addAction('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->addAction('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

		$gfAddOn = GFQuickbooksOnlineAddOn::getInstance();

		// $this->loader->addAction('gform_loaded', $gfAddOn, 'init', 5);

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    0.1.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     0.1.0
	 * @return    string    The name of the plugin.
	 */
	public function getPluginName() {
		return $this->pluginName;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     0.1.0
	 * @return    GFQuickbooksOnline_Loader    Orchestrates the hooks of the plugin.
	 */
	public function getLoader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     0.1.0
	 * @return    string    The version number of the plugin.
	 */
	public function getVersion() {
		return $this->version;
	}
}
