<?php

add_action( 'show_user_profile', 'iw_ltu_unsubscribe_link', 99 );
function iw_ltu_unsubscribe_link() {
	global $iw_ltu;

	if ( iw_ltu_user_can_unsubscribe() ) {
		?>
			<a href="<?php echo $iw_ltu->unsubscribe_menu->get_permalink(); ?>"><?php _e( 'Delete my account', 'let-them-unsubscribe' ); ?></a>
		<?php
	}
}