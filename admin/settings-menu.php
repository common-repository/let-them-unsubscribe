<?php

class IW_LTU_Settings_Menu extends IW_LTU_Admin_Page {

	public $options_name = 'lt_unsubscribe_options';
	public $settings;
	/**
	 * Constructor
	 */
	public function __construct( $menu_slug, $cap, $args ) {
		parent::__construct( $menu_slug, $cap, $args );

		add_action( 'admin_init', array( $this, 'init' ) );
	}

	/**
	 * Register the settings
	 */
	public function init() {
		register_setting( $this->options_name, $this->options_name, array( $this, 'sanitize_options' ) );

		add_settings_section( 'ltu_general_options', __( 'Users who can delete their profiles', 'let-them-unsubscribe' ), null, $this->options_name );
		add_settings_field( 'ltu_general_options_roles', __( 'Select one or more roles', 'let-them-unsubscribe' ), array( $this, 'render_roles_field' ), $this->options_name, 'ltu_general_options' );

		add_settings_section( 'ltu_caption_options', __( 'Options', 'let-them-unsubscribe' ), null, $this->options_name );
		add_settings_field( 'ltu_caption_options_info_text', __( 'Text displayed on Delete Profile Page', 'let-them-unsubscribe' ), array( $this, 'ltu_caption_options_info_text' ), $this->options_name, 'ltu_caption_options' );
		add_settings_field( 'ltu_caption_options_end_text', __( 'Text displayed once the user has deleted his profile', 'let-them-unsubscribe' ), array( $this, 'ltu_caption_options_end_text' ), $this->options_name, 'ltu_caption_options' );
		
		add_settings_section( 'ltu_misc_options', __( 'Misc', 'let-them-unsubscribe' ), null, $this->options_name );
		add_settings_field( 'ltu_misc_options_redirect_page', __( 'Redirect to page', 'let-them-unsubscribe' ), array( $this, 'ltu_misc_options_redirect_page' ), $this->options_name, 'ltu_misc_options' );

	}

	public function render_content() {
		$this->settings = iw_ltu_get_settings();

		?>	
			<form action="options.php" method="post">
				<?php settings_fields( $this->options_name ); ?>
				<?php do_settings_sections( $this->options_name ); ?>
				<?php submit_button( false, 'primary', $this->options_name . '[submit_tlu_settings]' ); ?>
			</form>
		<?php
	}

	public function render_roles_field() {
		$roles = iw_ltu_get_roles();

		echo '<ul>';
		foreach ( $roles as $role => $role_name ) {
			?>
				<li>
					<label>
						<input type="checkbox" name="<?php echo $this->options_name; ?>[roles][<?php echo $role; ?>]" <?php checked( in_array( $role, $this->settings['roles'] ) ); ?>> <?php echo $role_name; ?>
					</label>
				</li>
			<?php
		}
		echo '</ul>';
	}

	public function ltu_caption_options_info_text() {

		?>
			<textarea name="<?php echo $this->options_name; ?>[info-text]" class="large-text" id="info-text" rows="5" cols="30"><?php echo esc_textarea( $this->settings['info-text'] ); ?></textarea>
		<?php
	}


	public function ltu_caption_options_end_text() {
		$disabled = $this->settings['redirect-page'] ? 'disabled="disabled"' : '';

		?>
			<textarea name="<?php echo $this->options_name; ?>[end-text]" <?php echo $disabled; ?> class="large-text" id="end-text" rows="5" cols="30"><?php echo esc_textarea( $this->settings['end-text'] ); ?></textarea>
		<?php
	}

	public function ltu_misc_options_redirect_page() {
		?>
			<?php wp_dropdown_pages( array( 
				'selected' => $this->settings['redirect-page'], 
				'option_none_value' => '', 
				'show_option_none' => __( '-- Select a page --', 'let-them-unsubscribe' ),
				'name' => $this->options_name . '[redirect_page]',
				'id' => 'redirect-page'
			) ); ?>
			
			<span class="description"> <?php _e( 'Redirect to this page instead of display the text.', 'let-them-unsubscribe' ); ?></span>
			<script>
				jQuery(document).ready(function($) {
					$('#redirect-page').change( function(e) {
						var $this = $(this);
							value = $this.val();

						if ( ! value ) {
							$('#end-text').attr('disabled',false);
						}
						else {
							$('#end-text').attr('disabled',true);
						}
					})
				});
			</script>
		<?php
	}



	public function sanitize_options( $input ) {
		if ( isset( $input['submit_tlu_settings'] ) ) {

			$settings = iw_ltu_get_settings();
			$_settings = $settings;

			if ( ! current_user_can( $this->get_capability() ) )
				return $settings;

			$all_roles = iw_ltu_get_roles();

			$roles = array();
			if ( ! empty( $input['roles'] ) ) {
				foreach ( $input['roles'] as $role_slug => $value ) {
					if ( array_key_exists( $role_slug, $all_roles ) )
						$roles[] = $role_slug;
				}	
			}
			
			$settings['roles'] = $roles;

			if ( empty( $input['info-text'] ) )
				add_settings_error( $this->options_name, 'empty-info-text', __( '<strong>Info text</strong> field must not be empty', 'let-them-unsubscribe' ) );
			else
				$settings['info-text'] = stripslashes( $input['info-text'] );

			if ( ! empty( $input['redirect_page'] ) && 'page' === get_post_type( absint( $input['redirect_page'] ) ) ) {
				$settings['redirect-page'] = absint( $input['redirect_page'] );
			}
			else {
				$settings['redirect-page'] = false;
			}

			if ( empty( $input['end-text'] ) && $settings['redirect-page'] === false )
				add_settings_error( $this->options_name, 'empty-end-text', __( '<strong>End text</strong> field must not be empty', 'let-them-unsubscribe' ) );
			elseif( empty( $input['end-text'] ) && $settings['redirect-page'] !== false )
				$settings['end-text'] = $_settings['end-text'];
			elseif ( ! empty( $input['end-text'] ) )
				$settings['end-text'] = stripslashes( $input['end-text'] );

			

			$errors = get_settings_errors( $this->options_name );
			if ( empty( $errors ) )
				return $settings;
			else
				return $_settings;
			
		}
		
	}
}