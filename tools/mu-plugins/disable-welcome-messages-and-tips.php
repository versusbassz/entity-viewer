<?php
/*
  Plugin Name: Disable Welcome Messages and Tips
  Description: Hide Welcome Messages and Tips in the Gutenberg Block Editor
  Version: 1.0.8
  Author: Jules Colle
  Author URI: https://bdwm.be
 */
add_action('admin_head', 'dwat_hide_popovers');

/**
 * Inspired by https://orbisius.com/p4620
 */
function dwat_hide_popovers() {
	?>
	<style>
		.wp-admin .components-popover.nux-dot-tip {
			display: none !important;
		}
	</style>

	<script>
		jQuery(window).load( function(){
			wp.data && wp.data.select( 'core/edit-post' ).isFeatureActive( 'welcomeGuide' ) && wp.data.dispatch( 'core/edit-post' ).toggleFeature( 'welcomeGuide' );
			wp.data && wp.data.select( 'core/edit-post' ).isFeatureActive( 'fullscreenMode' ) && wp.data.dispatch( 'core/edit-post' ).toggleFeature( 'fullscreenMode' );
		});
	</script>
	<?php
}
