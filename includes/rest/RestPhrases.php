<?php
namespace Relevantly\Rest;

use Relevantly\Repositories\KeywordRepository;

class RestPhrases extends RestApiAbstract {
    private $version;
    private $endpoint;
    private $keywordRepository;
    

    function setVersion( $version )
    {
        $this->version = $version;
    }

    function setEndpoint( $endpoint )
    {
        $this->endpoint = $endpoint;
    }

    function __construct( KeywordRepository $keywordRepository )
    {
        $this->keywordRepository = $keywordRepository;
    }
    
    function add_rest_route() 
    {
        $route = $this->restNamespace . $this->version;

        register_rest_route( $route, $this->endpoint, [
            'methods'  => \WP_REST_Server::READABLE,
            'callback' => [ $this, 'endpoint_callback' ],
            'permission_callback' => '__return_true',
            'args' => [
                'page' => [
                    'description' => 'Page number',
                    'type'        => 'integer',
                    'default'     => 1,
                    'validate_callback' => function( $value, $request, $param ) {
                        return is_numeric( $value ) && $value > 0;
                    },
                    'sanitize_callback' => 'absint',
                ],
                'limit' => [
                    'description' => 'Number of items per page',
                    'type'        => 'integer',
                    'default'     => 10,
                    'validate_callback' => function( $value, $request, $param ) {
                        return is_numeric( $value ) && $value > 0;
                    },
                    'sanitize_callback' => 'absint',
                ],
            ],
        ]);
    }

    function endpoint_callback( \WP_REST_Request $request )
    {
        $page  = $request->get_param( 'page' );
        $limit = $request->get_param( 'limit' );

        $data = $this->keywordRepository->get_all_keywords( $page, $limit );
        
        if ( empty ( $data ) ) {
            $data = [];
        }

        return rest_ensure_response( [ 'data' => $data ], 200 );
    }
}