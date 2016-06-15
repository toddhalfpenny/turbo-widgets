<?php
/**
	@package turbo-widgets

	Plugin Name: Turbo Widgets
	Plugin URI: http://turbowidgets.net
	Description: The easiest way to add Widgets to Posts and Pages through the WYSIWYG or shortcodes.
	Author: Datamad Ltd, Todd Halfpenny
	Version: 1.0.4
	Author URI: http://turbowidgets.net
 */

/**
	Copyright 2015  DATAMAD LTD  (email : sales@turbowidgets.net)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/*
 * = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
 * 	I N S T A L L / U P G R A D E
 * = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
 */

function turbo_widgets_install() {
	// Nothing to do this time out.
}


/*
 * = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
 *  C O R E    C O D E
 * = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
 */

function turbo_widget( $atts ) {
	$turbo_sidebar = array(
		'name' => 'Turbo Widgets Sidebar',
		'id' => 'turbo-sidebar-1',
		'description' => '',
		'class' => '',
		'before_widget' => '<div class="widget turbo_widget">',
		'after_widget' => '</div><!-- .turbo_widge -->',
		'before_title' => '<h3 class="widget_title">',
		'after_title' => '</h2>',
	);

	// From WP 4.2.7+ (maybe) the $atts changed.
	global $wp_version;
	if ( $wp_version < 4.3 ) {
		parse_str( htmlspecialchars_decode( $atts[0] ), $output );
	} else {
		$tmp_arr = array( 0 => 'widget-prefix=' . $atts['widget-prefix'] );
		parse_str( htmlspecialchars_decode( $tmp_arr[0] ), $output );
	}
	$field_prefix = 'widget-' .  $output['widget-prefix'] . '--';

	foreach ( $output as $i => $value ) {
		$is_meta = strstr( $i, $field_prefix );
		if ( false <> $is_meta ) {
			$i = substr( $i,  strlen( $field_prefix ) );
			$instance[ $i ] = $value;
		}
	}
	if ( ! array_key_exists( 'obj-class', $output ) ) {
		// For some reason our array is cocked-up (semi-colon special char,)
		// only an issue on some vsn of PHP) we need to adjust the keys.
		foreach ( $output as $k => $v ) {
			unset( $output[ $k ] );
			$new_key = str_replace( '#038;', '', $k );
			$output[ $new_key ] = $v;
		}
	}
	$turbo_widget_class = $output['obj-class'];
	$widget = new $turbo_widget_class;

	ob_start();
	the_widget( $turbo_widget_class, $instance, $turbo_sidebar );
	$my_str = ob_get_contents();
	ob_end_clean();
	return $my_str;
}




register_activation_hook( __FILE__, 'turbo_widgets_install' );

add_shortcode( 'turbo_widget', 'turbo_widget' );



/*
 * = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
 * 	ADD CSS ?
 * = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
 */


/*
 * = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
 * 	T I N Y   M C E
 * = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
 */
function turbo_addbuttons() {
	if ( is_admin() ) {
		if ( get_user_option( 'rich_editing' ) ) {
			add_filter( 'mce_external_plugins', 'add_turbo_tinymce_plugin' );
			add_filter( 'mce_buttons', 'register_turbo_tinymce_button' );
		}
	}
}

function register_turbo_tinymce_button( $buttons ) {
	array_push( $buttons, 'separator', 'turboplugin' );
	return $buttons;
}

// Load the TinyMCE plugin.
function add_turbo_tinymce_plugin( $plugin_array ) {
	$plugin_url = plugins_url( 'js/turbo_editor_plugin.js', __FILE__  );
	$plugin_array['turboplugin'] = $plugin_url;
	return $plugin_array;
}

// Init process for button control.
add_action( 'init', 'turbo_addbuttons' );

function turbo_editor_dialog() {
	$tw_attrs = isset( $_GET['tw_attrs'] ) ? $_GET['tw_attrs'] : '';
	require_once( 'include/turbo-editor-dialog.php' );
	die;
}

if ( is_admin() ) {
	add_action( 'wp_ajax_turbo_editor_dialog', 'turbo_editor_dialog' );
}

add_action( 'admin_head', 'turbo_admin_css' );

function turbo_admin_css() {
	?>
	<style>
	.wp_themeSkin span.mce_turboplugin, .mce-i-turboplugin {
		background: url( '<?php echo esc_url( plugins_url( 'images/turbo_tiny_mce.png', __FILE__  ) ); ?>' ) no-repeat 0 -20px !important;
	}

	span.mce_turboplugin:hover,  .mce-i-turboplugin:hover {
		background-position: 0 0;
	}
	</style>
	<?php
}

?>
