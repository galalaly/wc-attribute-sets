<?php

/**
 * Basic interface for database operation
 * from plugin's management page.
 */
class WCAttributeSetsDb extends WCAttributeSets
{
  
  private $prefix;

  function __construct()
  {
    parent::__construct( false );

    $this->prefix = 'wc_attribute_set_';
  }

  function create( $name, $attributes )
  {
    $key = time();

    $attributes = empty( $attributes ) ? [] : $attributes;

    update_option( 'wc_attribute_set_' . time(), array(
      'name' => $name,
      'attributes' => $attributes,
      'key' => $key
    ), false );
  }

  function get( $key )
  {
    $name = 'wc_attribute_set_' . $key;

    return (object) get_option( $name, false );
  }

  function update( $name, $attributes, $key )
  {
    $attributes = empty( $attributes ) ? [] : $attributes;
    update_option( 'wc_attribute_set_' . $key, array(
      'name' => $name,
      'attributes' => $attributes,
      'key' => $key
    ), false );
  }

  function getAll()
  {
    global $wpdb;

    $result = $wpdb->get_results( 'SELECT option_value FROM ' . $wpdb->prefix . 'options WHERE option_name LIKE "' . $this->prefix . '%" ' );

    $data = array();

    foreach( $result as $r )
    {
      $data[] = maybe_unserialize( $r->option_value );
    }

    return $data;

  }
}

?>