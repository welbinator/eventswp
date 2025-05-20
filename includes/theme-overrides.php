<?php

// OceanWP
add_action( 'after_setup_theme', 'your_plugin_fix_oceanwp_font_size' );

function your_plugin_fix_oceanwp_font_size() {
	if ( class_exists( 'OCEANWP_Theme_Class' ) ) {
		add_action( 'wp_head', 'your_plugin_add_oceanwp_font_size_override', 100 );
	}
}

function your_plugin_add_oceanwp_font_size_override() {
	echo '<style>html { font-size: 100% !important; }</style>';
}



