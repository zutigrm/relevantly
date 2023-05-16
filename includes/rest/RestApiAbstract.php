<?php
namespace Relevantly\Rest;

abstract class RestApiAbstract {
    protected $restNamespace = 'relevantly/';

    function init(){
        add_action( 'rest_api_init', [ $this, 'add_rest_route' ] );
    }
    function add_rest_route(){}
    function endpoint_callback( \WP_REST_Request $request ){}
}