<?php
$related_posts = isset( $args[ 'related_posts' ] ) ? $args[ 'related_posts' ] : null;
?>
<?php if ( ! empty( $related_posts ) && ! is_wp_error( $related_posts ) ): ?>
    <div class="relevantly-related__wrap">
        <div class="relevantly-related__items">
            <?php foreach ( $related_posts as $_post ): ?>
                <?php Relevantly\Utils\Helpers::get_plugin_template_part( 'post-item', 'parts', [ 'post_id' => $_post ] ); ?>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>