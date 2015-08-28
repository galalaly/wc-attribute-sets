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

    $attributeSetKey = $_POST[ 'key' ];

    $db = new WCAttributeSetsDb;

    $attributeSet = $db->get($attributeSetKey);

    if( empty( $attributeSet->attributes ) )
      die();

    foreach( $attributeSet->attributes as $attributeKey )
    {
      $thepostid     = 0;
      $taxonomy      = wc_attribute_taxonomy_name($attributeKey);
      $i             = absint( $_POST['i'] );
      $position      = 0;
      $metabox_class = array();
      $attribute     = array(
        'name'         => $taxonomy,
        'value'        => '',
        'is_visible'   => apply_filters( 'woocommerce_attribute_default_visibility', 1 ),
        'is_variation' => 0,
        'is_taxonomy'  => $taxonomy ? 1 : 0
      );

      if ( $taxonomy ) {
        $attribute_taxonomy = $wc_product_attributes[ $taxonomy ];
        $metabox_class[]    = 'taxonomy';
        $metabox_class[]    = $taxonomy;
        $attribute_label    = wc_attribute_label( $taxonomy );
      } else {
        $attribute_label = '';
      }

      include( WC()->plugin_path() . '/includes/admin/meta-boxes/views/html-product-attribute.php' );
    }

    die();

  }
}

?>