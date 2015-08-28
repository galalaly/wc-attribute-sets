<?php

/*
Plugin Name: WooCommerce Attribute Sets
Description: Adds attribute sets for WooCommerce
Plugin URI: http://galalaly.me
Author: Galal Aly
Author URI: http://www.galalaly.me
Version: 1.0
License: GPL2
Text Domain: wc-attribute-sets
Domain Path: wc-attribute-sets
*/

/**
 * The glue that holds the entire plugin together
 */
class WCAttributeSets
{
    public $url;
    public $dir;

    private $pageHandler;
    private $router;
    private $ajax;
    private $woocommerce;

    function __construct( $build = true )
    {
        $this->url = plugin_dir_url( __FILE__ );
        $this->dir = plugin_dir_path( __FILE__ );

        if( $build )
            $this->build();
    }

    function build()
    {
        $this->includeFiles();
        $this->init();
        $this->actions();
    }

    function includeFiles()
    {
        require_once $this->dir . '/includes/class-wc-attribute-sets-router.php';
        require_once $this->dir . '/includes/class-wc-attribute-sets-ajax.php';
        require_once $this->dir . '/includes/class-wc-attribute-sets-woocommerce.php';
    }

    function init()
    {
        $this->router = new WCAttributeSetsRouter();
        $this->ajax = new WCAttributeSetsAjax();
        $this->woocommerce = new WCAttributeSetsWooCommerce;
    }

    function actions()
    {
        add_action( 'admin_menu', array( $this, 'adminMenu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'adminAssets' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'woocommerceAssets' ) );
    }

    function adminAssets()
    {
        if( !$this->ourPage() )
            return;

        wp_enqueue_style( 'wc-attribute-sets', $this->url . '/assets/css/wc-attribute-sets.css' );

        // Registers and adds the required assets to admin pages
        wp_enqueue_script( 'wc-attribute-sets-angularjs', $this->url . '/assets/js/angular.min.js', null, null, false );
        wp_enqueue_script( 'wc-attribute-sets-angularjs-route', $this->url . '/assets/js/angular-route.min.js', array( 'wc-attribute-sets-angularjs' ), null, false );

        wp_enqueue_script( 'wc-attribute-sets-app', $this->url . '/assets/js/wc-attribute-sets-app.js', array( 'wc-attribute-sets-angularjs-route' ), 1.0, false );
        wp_localize_script( 'wc-attribute-sets-app', 'wcAttributeSets', array(
            'productsAttributes' => wc_get_attribute_taxonomies(),
            'viewsUrl' => $this->url . '/views/app/',
            'ajaxUrl' => admin_url( 'admin-ajax.php' )
        ) );
    }

    function woocommerceAssets()
    {
        wp_enqueue_script( 'wc-attribute-sets-woocommerce', $this->url . '/assets/js/woocommerce.js', array( 'jquery', 'wc-admin-product-meta-boxes' ), null, true );
        wp_localize_script( 'wc-attribute-sets-woocommerce', 'wcAttributeSets', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),

        ) );
    }

    function adminMenu()
    {
        // Adds the admin menu page
        $this->pageHandler = add_submenu_page( 'woocommerce', 'WooCommerce Attribute Sets', 'Attribute Sets', 'manage_options', 'wc-attribute-sets', array( $this, 'adminRouter' ) );
    }

    function adminRouter()
    {
        // Routes the requests of the plugin's pages
        
        // Make sure that this is actually the plugin's page, extra careful
        if( !$this->ourPage() )
            return;

        // Switch and Act
        $act = $_GET[ 'act' ];

        $this->router->route( $act );

    }

    function ourPage()
    {
        return get_current_screen()->id == $this->pageHandler;
    }

    // other hooks here
}

add_action( 'init', 'initWCAttributeSets', 99999 );

function initWCAttributeSets()
{
    global $wc_attribute_sets;
    $wc_attribute_sets = new WCAttributeSets;
}


?>