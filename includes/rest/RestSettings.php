<?php
namespace Relevantly\Rest;

class RestSettings extends RestApiAbstract {
    private $version;
    private $endpoint;
    private $settings_key = 'relevantly';

    function setVersion( $version )
    {
        $this->version = $version;
    }

    function setEndpoint( $endpoint )
    {
        $this->endpoint = $endpoint;
    }
    
    function add_rest_route() 
    {
        $route = $this->restNamespace . $this->version;

        register_rest_route( $route, $this->endpoint, [
            'methods'  => \WP_REST_Server::READABLE,
            'callback' => [ $this, 'endpoint_callback' ],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route( $route, $this->endpoint, [
            'methods'  => \WP_REST_Server::CREATABLE,
            'callback' => [ $this, 'update_settings' ],
            'permission_callback' => function() {
                return current_user_can( 'manage_options' );
            },
            'args' => [
                'setting_key' => [
                    'required' => true,
                    'type'     => 'string',
                ],
                'setting_value' => [
                    'required' => true,
                    'type'     => 'string',
                ],
            ],
        ] );
    }

    function endpoint_callback( \WP_REST_Request $request )
    {
        $settings = get_option( $this->settings_key );
        
        if ( empty ( $settings ) ) {
            $settings = [];
        }

        return rest_ensure_response( [ 'data' => $settings ], 200 );
    }

    function update_settings( \WP_REST_Request $request )
    {
        $setting_key   = $request->get_param( 'setting_key' );
        $setting_value = $request->get_param( 'setting_value' );

        $settings = get_option( $this->settings_key );
        $settings[ $setting_key ] = $setting_value;
    
        $updated = update_option( $this->settings_key, $settings );

        $message = esc_html__( 'Setting update failed.', 'Relevantly' );

        if ( ! empty( $updated ) ) {
            // re-fetch updated settings
            $settings = get_option( $this->settings_key );

            $message = esc_html__( 'Setting updated successfully.', 'Relevantly' );
        }

        return rest_ensure_response( [ 'message' => $message, 'data' => $settings ], 200 );
    }
}