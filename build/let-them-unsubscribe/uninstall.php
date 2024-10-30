<?php

if( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit ();

delete_option( 'lt_unsubscribe_options' );
delete_option( 'iw_ltu_version' );