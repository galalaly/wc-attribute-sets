<?php

if( !defined( 'ABSPATH' ) ) exit; // do not access directly

/**
 * Manages ajax requests from the plugin's
 * management page.
 */
class WCAttributeSetsAjax extends WCAttributeSets
{
    
    private $db;

    function __construct()
    {
        parent::__construct( false );

        // Ajax
        add_action( 'wp_ajax_wc_attribute_sets_create_set', array( $this, 'createSet' ) ); // creating a new set
        add_action( 'wp_ajax_nopriv_wc_attribute_sets_create_set', array( $this, 'createSet' ) );

        // wc_attribute_sets_get_set
        add_action( 'wp_ajax_wc_attribute_sets_get_set', array( $this, 'getSet' ) ); // creating a new set
        add_action( 'wp_ajax_nopriv_wc_attribute_sets_get_set', array( $this, 'getSet' ) );

        add_action( 'wp_ajax_wc_attribute_sets_update_set', array( $this, 'updateSet' ) ); // creating a new set
        add_action( 'wp_ajax_nopriv_wc_attribute_sets_update_set', array( $this, 'updateSet' ) );

        // WooCommerce actual integration
        add_action( 'wp_ajax_wc_attribute_sets_add_to_product', array( $this, 'addAttributeSetToProduct' ) );
        add_action( 'wp_ajax_nopriv_wc_attribute_sets_add_to_product', array( $this, 'addAttributeSetToProduct' ) );

        add_action( 'wp_ajax_wc_attribute_sets_get_all_sets', array( $this, 'getAllSets' ) ); // get all sets

        require_once $this->dir . '/includes/class-wc-attribute-sets-db.php';

        $this->db = new WCAttributeSetsDb;
        
    }

    function createSet()
    {
        // Get the data
        $attributeSetName = $_POST[ 'set' ][ 'name' ];
        $attributeSetAttributes = $_POST[ 'set' ][ 'attributes' ];

        // Create a new option with our prefix
        $this->db->create( $attributeSetName, $attributeSetAttributes );
        
        echo 'Done';

        die();
    }

    function updateSet()
    {
        // Get the data
        $attributeSetName = $_POST[ 'set' ][ 'name' ];
        $attributeSetAttributes = $_POST[ 'set' ][ 'attributes' ];

        $key = $_POST[ 'key' ];

        // Create a new option with our prefix
        $this->db->update( $attributeSetName, $attributeSetAttributes, $key );
        
        echo 'Done';

        die();
    }

    function getAllSets()
    {
        // get all sets
        $data = $this->db->getAll();

        echo json_encode($data);

        die();
    }

    function getSet()
    {
        $key = $_POST[ 'setKey' ];

        $data = $this->db->get( $key );

        echo json_encode( $data );

        die();
    }

    function addAttributeSetToProduct()
    {
        WCAttributeSetsWooCommerce::addAttributeSetToProduct();
    }
}

?>