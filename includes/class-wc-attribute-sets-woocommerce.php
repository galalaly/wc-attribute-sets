<?php

/**
 * Manages the ingegration with WooCommerce
 * interface and other stuff.
 */
class WCAttributeSetsWooCommerce extends WCAttributeSets
{
  function __construct()
  {
    parent::__construct( false );
    
    add_action( 'woocommerce_product_options_attributes', array( $this, 'addSelectBox' ) );

    require_once $this->dir . '/includes/class-wc-attribute-sets-db.php';

    $this->db = new WCAttributeSetsDb;
  }

  function addSelectBox()
  {
    global $wc_attribute_sets;
    ?>
    <p class="toolbar">
      <select name="attribute_set" id="attribute_set" class="attribute_set">
        <option value="">Choose an attribute set</option>
        <?php
          $attributeSets = $this->db->getAll();
          foreach( $attributeSets as $attributeSet ):
        ?>
          <option value="<?php echo $attributeSet['key']; ?>"><?php echo $attributeSet['name']; ?></option>
        <?php
          endforeach;
        ?>
      </select>
      <button type="button" class="button button-primary add_attribute_set">Set</button>
    </p>
    <?php
  }

  static function addAttributeSetToProduct()
  {
    ob_start();

    global $wc_product_attributes;

	check_ajax_referer( 'add-attribute', 'security' );
    
	$attributeSetKey = $_POST[ 'key' ];
    $db = new WCAttributeSetsDb;
    $attributeSet = $db->get($attributeSetKey);
    if( empty( $attributeSet->attributes ) )
      die();
	
	
	$i=absint( $_POST['i'] );
    foreach( $attributeSet->attributes as $attributeKey )
    {		
		$taxonomy      = wc_attribute_taxonomy_name($attributeKey);  
		$metabox_class = array();
		$attribute     = new WC_Product_Attribute();
		$attribute->set_id( wc_attribute_taxonomy_id_by_name( $taxonomy ) );
		$attribute->set_name( $taxonomy );
		$attribute->set_visible( apply_filters( 'woocommerce_attribute_default_visibility', 1 ) );
		$attribute->set_variation( apply_filters( 'woocommerce_attribute_default_is_variation', 0 ) );

		if ( $attribute->is_taxonomy() ) {
		  $metabox_class[] = 'taxonomy';
		  $metabox_class[] = $attribute->get_name();
		}
		
		if($attribute->get_name() == "pa_") continue;
		
		include( WC()->plugin_path() . '/includes/admin/meta-boxes/views/html-product-attribute.php' );
		$i++;
    }
    wp_die();

  }
}

?>
