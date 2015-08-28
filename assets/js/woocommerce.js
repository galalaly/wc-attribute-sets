jQuery( function( $ ){

  function wcAttributeSetsAddAttributeSet( attributeSet, size, obj )
  {
    data = {
      'action' : 'wc_attribute_sets_add_to_product',
      'key' : attributeSet,
      'i' : size
    };

    var $wrapper = $( obj ).closest( '#product_attributes' ).find( '.product_attributes' );
    var product_type = $( 'select#product-type' ).val();

    $.post( wcAttributeSets.ajaxUrl, data, function(response) {
      $wrapper.append( response );
      if ( product_type !== 'variable' ) {
        $wrapper.find( '.enable_variation' ).hide();
      }
      $('body').trigger( 'wc-enhanced-select-init' );
      // attribute_row_indexes();
      $wrapper.unblock();
      $('body').trigger( 'woocommerce_added_attribute' );
    } );
  }

  $(document).ready( function(){

    $( 'body' ).on( 'click', 'button.add_attribute_set', function(){
      attributeSet = $('select#attribute_set').val();
      size = $( '.product_attributes .woocommerce_attribute' ).size();
      wcAttributeSetsAddAttributeSet(attributeSet, size, this);
    } );

  } );

});