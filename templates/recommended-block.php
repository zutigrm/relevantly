<?php
$section_title = isset( $args[ 'section_title' ] ) ? $args[ 'section_title' ] : null;
$related_posts = isset( $args[ 'related_posts' ] ) ? $args[ 'related_posts' ] : null;
?>
<?php if ( ! empty( $related_posts ) && ! is_wp_error( $related_posts ) ): ?>
    <div class="relevantly-block__wrap">
        <?php if ( ! empty( $section_title ) ): ?>
            <strong class="relevantly-block__title"><?php echo esc_html( $section_title ); ?></strong>
        <?php endif; ?>

        <div class="relevantly-block__items">
            <?php foreach ( $related_posts as $_post ): ?>
                <?php Relevantly\Utils\Helpers::get_plugin_template_part( 'block-item', 'parts', [ 'post_id' => $_post ] ); ?>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>