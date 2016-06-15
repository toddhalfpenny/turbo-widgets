<?php
/**
 * @package turbo-swidgets
 * WOP tinyMCE editor dialog
 * /

/**
 * [turbo_list_all_widgets description]
 */
function turbo_list_all_widgets() {
	global $wp_registered_widgets, $wp_registered_widget_controls;

	$turbo_widget_name = '';
	$select_disabled = '';
	$sort = $wp_registered_widgets;
	$turbo_widget_name_arr = array();
	foreach ( $sort as $i => $value ) {
		if ( array_key_exists( $value['name'], $turbo_widget_name_arr ) ) {
			unset( $sort[ $i ] );
		} else {
			$turbo_widget_name_arr[ $value['name'] ] = true;
		}
	}
	echo '<select id="widget_select"' . esc_html( $select_disabled ) .'><option value="Null"></option>';
	foreach ( $sort as $i => $value ) {
		$callback = $value['callback'];

		$turbo_object = $callback[0];
		if ( is_array( $callback ) ) {
			$turbo_object = $callback[0];
			if ( isset( $turbo_object->id_base ) ) {
				$select_value = $turbo_object->id_base;
			} else {
				$select_value = $value['id'];
			}
		} else {
			$select_value = $value['id'];
		}
		$selected = '';
		if ( $select_value == $turbo_widget_name ) {
			$selected = 'SELECTED';
		}
		echo '<option value="' . esc_html( $select_value ) . '" '. esc_html( $selected ) . '>' . esc_html( $value['name'] ) . '</option>';
	}
	echo '</select>';

	// Output all the hidden widget forms.
	// If the Widget does not conform to what we need then we display some info.
	echo '<div class="isNull"></div>';
	foreach ( $sort as $i => $value ) {
		$callback = $value['callback'];
		if ( is_array( $callback ) ) {
			$turbo_object = $callback[0];
			$widget = new $turbo_object;
			if ( isset( $turbo_object->id_base ) ) {
				$form_class = $turbo_object->id_base;
			} else {
				$form_class = $value['id'];
			}
			echo '<div class="is' . esc_html( $form_class ) . '">';
			echo '<input type="hidden" id="widget-prefix" name="widget-prefix" value="' . esc_html( $form_class ) . '" />';
			echo '<input type="hidden" id="obj-class" name="obj-class" value="' . esc_html( get_class( $turbo_object ) ) . '" />';
			if ( method_exists( $widget, 'form' ) ) {
				$widget->form( array() );
			} else { // This widget's admin is not available from the 'form' method, currently unsupported.
				non_supported_str( $value['name'] );
			}
		} else { // This is an old widget.
			echo '<div class="is' . esc_html( $value['id'] ) . '">';
			echo '<input type="hidden" id="widget-prefix" name="widget-prefix" value="' . esc_html( $value['id'] ) . '" />';
			non_supported_str( $value['name'] );
		}
		echo '</div>';
	}
}

/**
 * Outputs a "not supported" message for the widget
 *
 * @param string $widget_name The human readable name of the widget.
 */
function non_supported_str( $widget_name ) {
	echo '<div class="wp-ui-notification tw-notification"><h2><em>' . esc_html( $widget_name ) . '</em> widget not currently supported, sorry</h2><p>It is likely that this widget does not make use of the <a href="https://codex.wordpress.org/Widgets_API"  target="_blank">documented process for widget creation</a>.</p></div>';
}


?>

<style>
	.turbo-widget-title {font-family: "Open Sans",sans-serif; color: #5A5A5A; font-size: 1.7em;}
	.current-div input[type=text], .current-div select {  border-spacing: 0; width: 100%;clear: both;margin: 0;}
	.submit_btn {clear: both;}
	div#TB_ajaxContent {width: 95% !important; height: 90% !important;}
	.tw-notification {color: #fff; padding: 1em; margin-top: 0.8em;}
	.tw-notification h2 {color: #fff;}
	.tw-notification a {color: #fff; font-style: italic;}
</style>
<script>
	jQuery(document).ready(function(){
		// Hide the default MCE image toolbar
		jQuery(".mce-inline-toolbar-grp").hide();

		jQuery('[class^=is]').hide();
		// This log shows that we can get currently selected widget info.
		// And thus we can edit existing widgets.
		var currWidget =  tinymce.activeEditor.selection.getContent();
		//console.log('getContent' +  currWidget);
		var value = jQuery("#widget_select option:selected").val();
		var theDiv = jQuery(".is" + value);
		theDiv.slideDown();
		theDiv.addClass('current-div');
		theDiv.siblings('[class^=is]').slideUp();
		theDiv.siblings('[class^=is]').removeClass("current-div");
		jQuery("#current-widget").val(".is" + value);

	});
	jQuery('#widget_select').change(function() {
		var value = jQuery("#widget_select option:selected").val();
		var theDiv = jQuery(".is" + value);
		theDiv.slideDown();
		theDiv.addClass('current-div');
		theDiv.siblings('[class^=is]').slideUp();
		theDiv.siblings('[class^=is]').removeClass("current-div");
		jQuery("#current-widget").val(".is" + value);
	});
</script>
		<?php
		$update_existing = 0;
		echo "<h3 class='turbo-widget-title'>Select a Widget to add</h3>";
		?>
		<form id='widget-options-form'>
			<input type='hidden' id='current-widget' value=''/>
		<?php
		turbo_list_all_widgets( );
		?>
		<p class='submit_btn'>
			<input type="button" class="button-secondary" value="<?php esc_html_e( 'Cancel', 'wop' )?>" onclick="tb_remove();" />
			<input type="button" class="button-primary" value="<?php esc_html_e( 'Insert', 'wop' )?>" onclick="insertShortCode(<?php echo esc_html( $update_existing );?>);" />
		</p>
		</form>
		<hr />
		<h3>Rate this plugin</h3><p><a href="http://wordpress.org/support/view/plugin-reviews/turbo-widgets?rate=5#postform" title="Rate me" target="_BLANK">If you like me, please rate me</a>... or maybe even <a href="http://turbo-widgets.net/donate/" title="Show you love">donate to the author</a>...</p> <p>or perhaps just spread the good word <a href="https://twitter.com/share" class="twitter-share-button" data-url="http://wordpress.org/extend/plugins/turbo-widgets/" data-text="Using the Turbo Widgets WordPress plugin and lovin' it" data-via="toddhalfpenny" data-count="none">Tweet</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></p>
</div>
