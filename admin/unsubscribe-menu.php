<?php

class IW_LTU_Unsubscribe_Menu extends IW_LTU_Admin_Page {

	/**
	 * Constructor
	 */
	public function __construct( $menu_slug, $cap, $args ) {
		parent::__construct( $menu_slug, $cap, $args );
		add_action( 'admin_init', array( &$this, 'unsubscribe' ) );
	}



	public function render_content() {
		$settings = iw_ltu_get_settings();

		$ays_text = esc_html( apply_filters( 'ltu_ays_text', __( 'Are you sure?', 'let-them-unsubscribe' ) ) );
		?>
			<form action="" method="post">
				<h3><?php echo nl2br( $settings['info-text'] ); ?></h3>
				<?php wp_nonce_field( 'ltu_unsubscribe' ); ?>
				<input type="submit" name="submit_ltu_delete_account" class="button-primary" value="<?php _e( 'Yes, delete my account', 'let-them-unsubscribe' ); ?>" onclick="return confirm('<?php echo $ays_text; ?>')">
			</form>
		<?php
	}

	public function unsubscribe() {

		if ( isset( $_POST['submit_ltu_delete_account'] ) ) {
			if ( ! iw_ltu_user_can_unsubscribe() )
				return;

			check_admin_referer( 'ltu_unsubscribe' );

			$user_id = get_current_user_id();
			do_action( 'ltu_before_unsubscribe_user', $user_id );

			wp_delete_user( $user_id );

			$delete_network_user = iw_ltu_get_settings();
			if ( is_multisite() ) {
				wpmu_delete_user( $user_id );
			}
			wp_logout();

			do_action( 'ltu_after_unsubscribe_user', $user_id );	

			$settings = iw_ltu_get_settings();

			if ( $settings['redirect-page'] && 'page' === get_post_type( $settings['redirect-page'] ) ) {
				wp_redirect( get_permalink( $settings['redirect-page'] ) );
				exit();
			}
			
			$die_text = '<h3>' . $settings['end-text'] . '</h3>';
			$die_text .= '<a href="' . home_url() . '">' . __( 'Go to home page', 'let-them-unsubscribe' ) . '</a>';
			
			wp_die( $die_text );
		}
	}

}