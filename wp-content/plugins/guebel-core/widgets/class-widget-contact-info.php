<?php
/**
 * Contact Info Widget.
 *
 * @package Guebel_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Guebel_Widget_Contact_Info extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'guebel_contact_info',
			__( 'Guebel: Contact Info', 'guebel-core' ),
			array(
				'description' => __( 'Display store contact information.', 'guebel-core' ),
			)
		);
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];

		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		if ( $title ) {
			echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
		}

		$email   = get_theme_mod( 'guebel_email', '' );
		$phone   = get_theme_mod( 'guebel_phone', '' );
		$address = get_theme_mod( 'guebel_address', '' );

		echo '<ul class="contact-info-widget">';

		if ( $email ) {
			echo '<li class="contact-info-item">';
			echo '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>';
			echo '</li>';
		}

		if ( $phone ) {
			echo '<li class="contact-info-item">';
			echo '<a href="tel:' . esc_attr( preg_replace( '/[^+0-9]/', '', $phone ) ) . '">' . esc_html( $phone ) . '</a>';
			echo '</li>';
		}

		if ( $address ) {
			echo '<li class="contact-info-item">' . esc_html( $address ) . '</li>';
		}

		echo '</ul>';

		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( 'Title:', 'guebel-core' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
				type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p class="description">
			<?php esc_html_e( 'Contact details are pulled from Customizer > Guebel > Contact Info.', 'guebel-core' ); ?>
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance          = array();
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		return $instance;
	}
}
