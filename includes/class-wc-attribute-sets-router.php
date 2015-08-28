<?php

class WCAttributeSetsRouter extends WCAttributeSets
{

    public $currentAction;

    function __construct()
    {
        parent::__construct( false );
    }

    function route( $act )
    {
        switch( $act ) 
        {
            
            case null:
            case 'home':
                $this->render( 'home' );
            break;
            
            default:
                $this->render( 'home' );
        }
    }

    function buildUrl( $act = 'home' )
    {
        $baseAdminUrl = admin_url( 'admin.php' );

        $url = add_query_arg( array(
            'page' => 'wc-attribute-sets',
            'act'  => $act
        ), $baseAdminUrl );

        return $url;
    }

    function render( $view, $args = null )
    {
        if( !is_null( $args ) )
            extract( $args );

        require $this->dir . '/views/' . $view . '.php';
    }
}

?>