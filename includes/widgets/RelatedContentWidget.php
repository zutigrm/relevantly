<?php
namespace Relevantly\Widgets;

use WP_Widget;

class RelatedContentWidget extends WP_Widget {
	public function __construct() {
		parent::__construct(
			'relevantly_related_content_widget',
			__( 'Relevantly - Related Content', 'relevantly' ),
			array( 'description' => __( 'Display related content based on the post content.', 'relevantly' ) )
		);
	}

	public function widget( $args, $instance ) {
		$limit = isset( $instance[ 'limit' ] ) ? $instance[ 'limit' ] : RELEVANTLY_DEFAULT_LIMIT;

		echo do_shortcode( '[relevantly limit="' . esc_attr( $limit ) . '"]' );
	}

	public function form( $instance ) {
		$limit = ! empty( $instance['limit'] ) ? $instance['limit'] : '';
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Limit:', 'relevantly' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="number" value="<?php echo esc_attr( $limit ); ?>">
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['limit'] = ( ! empty( $new_instance['limit'] ) ) ? strip_tags( $new_instance['limit'] ) : '';

		return $instance;
	}

	public static function init() {
		add_action(
			'widgets_init',
			function () {
				register_widget( __CLASS__ );
			}
		);
	}
}
