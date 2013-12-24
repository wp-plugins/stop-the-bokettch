<?php
/**
 * @package Stop the Bokettch
 * @version 0.5.1
 */
/*
Plugin Name: Stop the Bokettch
Plugin URI: http://www.warna.info/archives/2649/
Description: This is a plugin for displaying an alert notification to the ToolBar if you have checked "Discourage search engines from indexing this site" of Site Visibility Options. So This is very useful for missed check options.
Author: jim912
Version: 0.5.1
Author URI: http://www.warna.info/
Text Domain: stop-the-bokettch
Domain Path: /languages/
*/

class Stop_the_Bokettch {
	
	private $icon_url;
	private $genericons_dir_url;
	
	public function __construct() {
		if ( ! get_option( 'blog_public' ) ) {
			add_action( 'init', array( &$this, 'init' ) );
			load_plugin_textdomain( 'stop-the-bokettch', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
			$this->icon_url = plugin_dir_url( __FILE__ ) . 'images/' . __( 'default.png', 'stop-the-bokettch' );
			$this->genericons_dir_url = plugins_url( 'fonts/genericons/', __FILE__ );
		}
	}
	
	
	public function init() {
		$capability = apply_filters( 'bokettch-notice-capability', 'publish_posts' );
		if ( current_user_can( $capability ) ) {
			add_action( 'admin_bar_menu'    , array( &$this, 'bokettch_notice' ), 9999 );
			add_action( 'admin_print_styles', array( &$this, 'bokettch_style' ) );
			add_action( 'wp_head'           , array( &$this, 'bokettch_style' ) );

			if ( $this->is_admin_responsive() && ( is_admin() || get_user_option( 'show_admin_bar_front', get_current_user_id() ) === 'true' ) ) {
				wp_enqueue_style( 'genericons', $this->genericons_dir_url . 'genericons.css', array(), '3.0.2' );
			}
		}
	}
	
	

	public function bokettch_notice( $wp_admin_bar ) {
		$title = '<span class="ab-icon"></span><span class="ab-label">' . __( 'NO INDEX', 'stop-the-bokettch' ) . '</span>';
		if ( current_user_can( 'manage_options' ) ) {
			$link = admin_url( 'options-reading.php#blog_public' );
		} else {
			$link = false;
		}
		$wp_admin_bar->add_menu(array(
			'id'    => 'bokettch-notice',
			'meta'  => array(),
			'title' => apply_filters( 'bokettch-notice-title', $title ),
			'href'  => apply_filters( 'bokettch-notice-link', $link )
		));
 	}


	public function bokettch_style() {
		if ( ! is_admin() && ! is_user_logged_in() ) { return; }
		$icon_url = apply_filters( 'bokettch-icon-url', $this->icon_url );
?>
	<style type="text/css">
		#wpadminbar #wp-admin-bar-bokettch-notice {
			background: red;
		}
<?php if ( $this->is_admin_responsive() ) : ?>
		#wpadminbar #wp-admin-bar-bokettch-notice .ab-icon:before {
			font-family: 'Genericons';
			content: '\f446';
			color: white;
			font-size: 1.2em;
			top: 1px;
		}
		@media screen and (max-width: 782px) {
			#wp-toolbar > ul > li#wp-admin-bar-bokettch-notice {
				display:inline;
			}
			#wpadminbar #wp-admin-bar-bokettch-notice .ab-icon:before {
				font-size: 1em;
				top: 4px;
			}
		}
<?php else : ?>
		#wpadminbar #wp-admin-bar-bokettch-notice .ab-icon {
			background-image: url( "<?php echo esc_url( $icon_url ); ?>" );
			width: 22px;
			height: 22px;
			margin-top: 3px;
		}
		#wpadminbar #wp-admin-bar-bokettch-notice .ab-label{
			color: white;
			text-shadow: none;
		}
<?php endif; ?>
	</style>
<?php
	}
	
	
	private function is_admin_responsive() {
		return version_compare( '3.8.*', get_bloginfo( 'version' ), '<=' );
	}
}
new Stop_the_Bokettch;