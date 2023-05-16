<?php
$post_id = isset( $args[ 'post_id' ] ) ? sanitize_key( $args[ 'post_id' ] ) : null;

if ( ! empty( $post_id ) ):
    $post_obj = get_post( $post_id );

    if ( ! is_wp_error( $post_obj ) && $post_obj instanceof WP_Post ):
        $title   = $post_obj->post_title;
        $excerpt = ! empty( $post_obj->post_excerpt ) ? $post_obj->post_excerpt : wp_trim_words( $post_obj->post_content, 20 );;
?>
        <div class="relevantly-block__item">
            <?php if ( has_post_thumbnail( $post_obj->ID ) ): ?>
                <div class="relevantly-block__item-img">
                    <?php print get_the_post_thumbnail( $post_obj->ID ); ?>
                </div>
            <?php endif; ?>

            <?php if ( ! empty( $title ) ): ?>
                <h3 class="relevantly-block__item-title">
                    <a href="<?php echo esc_url( get_the_permalink( $post_obj->ID ) ); ?>">
                        <?php echo esc_html( $title ); ?>
                    </a>
                </h3>
            <?php endif; ?>

            <?php if ( ! empty( $excerpt ) ): ?>
                <p class="relevantly-block__item-content"><?php echo esc_html( $excerpt ); ?></p>
            <?php endif; ?>
        </div>
    <?php endif;
endif;