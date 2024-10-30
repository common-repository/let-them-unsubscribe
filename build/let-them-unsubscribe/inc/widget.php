<?php

class LTU_Widget extends WP_Widget {

    /**
     * Constructor
     *
     * @return void
     **/
    function __construct() {
        $widget_ops = array( 'classname' => 'ltu-widget', 'description' => __( 'Displays a link that leads to the Unsubscribe Admin Screen', 'let-them-unsubscribe' ) );
        parent::__construct( 'ltu-widget', 'Let Them Unsubscribe', $widget_ops );
    }

    /**
     * Outputs the HTML for this widget.
     *
     * @param array  An array of standard parameters for widgets in this theme
     * @param array  An array of settings for this widget instance
     * @return void Echoes it's output
     **/
    function widget( $args, $instance ) {

    	if ( ! is_user_logged_in() )
    		return;
    	extract( $args );

    	extract( $instance );

    	echo $args['before_widget'];
		
		echo $args['before_title']. apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		
		$class = ! empty( $args['css_class'] ) ? 'class="' . esc_attr( $args['css_class'] ) . '"' : '';
		$url = admin_url( 'profile.php' );
		if ( iw_ltu_user_can_unsubscribe() ) {
			$url = add_query_arg( 'page', 'ltu_unsubscribe', $url );
			?><a href="<?php echo esc_url( $url ); ?>" <?php echo $class; ?> title="<?php echo esc_attr__( 'Delete your account', 'let-them-unsubscribe' ); ?>"><?php echo esc_attr__( 'Delete your account', 'let-them-unsubscribe' ); ?></a><?php
		}
		else {
			?><a href="<?php echo esc_url( $url ); ?>" <?php echo $class; ?> title="<?php echo esc_attr__( 'Your profile', 'let-them-unsubscribe' ); ?>"><?php echo esc_attr__( 'Your profile', 'let-them-unsubscribe' ); ?></a><?php
		}

		echo $args['after_widget'];
    }

    /**
     * Deals with the settings when they are saved by the admin. Here is
     * where any validation should be dealt with.
     *
     * @param array  An array of new settings as submitted by the admin
     * @param array  An array of the previous settings
     * @return array The validated and (if necessary) amended settings
     **/
    function update( $new_instance, $old_instance ) {

        $instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['css_class'] = ( ! empty( $new_instance['css_class'] ) ) ? sanitize_text_field( $new_instance['css_class'] ) : '';

		return $instance;
    }

    /**
     * Displays the form for this widget on the Widgets page of the WP Admin area.
     *
     * @param array  An array of the current settings for this widget
     * @return void Echoes it's output
     **/
    function form( $instance ) {
        $instance = wp_parse_args( 
        	$instance, 
        	array( 
        		'title' => __( 'Delete your account', 'let-them-unsubscribe' ), 
        		'css_class' => ''
        	) 
        );
        extract( $instance );
        ?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'css_class' ); ?>"><?php _e( 'Link CSS class:' ); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id( 'css_class' ); ?>" name="<?php echo $this->get_field_name( 'css_class' ); ?>" type="text" value="<?php echo esc_attr( $css_class ); ?>">
			</p>
        	<p class="description"><?php _e( 'If the user is not allowed to delete its account, the link will lead to its profile page.', 'let-them-unsubscribe' ); ?></p>
        <?php
    }
}

add_action( 'widgets_init', 'ltu_register_widget' );
function ltu_register_widget() {
	register_widget( 'LTU_Widget' );
}