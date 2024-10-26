<?php

defined( 'ABSPATH' ) or die( 'You shall not pass!' );

function scripts_1clicksuite() {
    wp_register_style( '1clicksuite_style', plugin_dir_url( __FILE__ ) . 'assets/1click_style.css' );
		wp_enqueue_style( '1clicksuite_style' );
}
add_action('wp_enqueue_scripts', 'scripts_1clicksuite');
