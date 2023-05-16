<?php
namespace Relevantly\Utils;

class Helpers {

    static function get_plugin_template_part( $template, $subdir, $args )
    {
        $templateDir = '/relevantly/templates/';
        $pluginDir   = '/templates/';

        if ( ! empty( $subdir ) ) {
            $templateDir = $templateDir . $subdir;
            $pluginDir   = $pluginDir . $subdir . '/';
        }

        // if template is overriden in theme, include it
        if ( file_exists( get_template_directory() . $templateDir . "$template.php" ) ) {
            get_template_part( $templateDir . $template, null, $args );
        
        } elseif ( file_exists( RELEVANTLY_PLUGIN_PATH . $pluginDir . "$template.php" ) ) {
            include RELEVANTLY_PLUGIN_PATH . $pluginDir . "$template.php";

        } else {
            return null;
        }
    }
}