<?php
/*
Plugin Name: Pronamic Issuu
Plugin URI: http://www.pronamic.eu/plugins/pronamic-issuu/
Description: Deprecated — Easily integrate Issuu documents into WordPress.
 
Version: 1.0.1
Requires at least: 3.0

Author: Pronamic
Author URI: http://www.pronamic.eu/

Text Domain: pronamic_issuu
Domain Path: /languages/

License: GPL

GitHub URI: https://github.com/pronamic/wp-pronamic-issuu
*/

class Pronamic_Issuu_Plugin {
	/**
	 * The plugin file
	 * 
	 * @var string
	 */
	public static $file;

	/**
	 * The plugin directory name
	 * 
	 * @var string
	 */
	public static $dirname;

	//////////////////////////////////////////////////

	/**
	 * Bootstrap
	 */
	public static function bootstrap( $file ) {
		self::$file = $file;
		self::$dirname = dirname( $file );

		add_action( 'init',              array( __CLASS__, 'init' ) );
		add_action( 'admin_init',        array( __CLASS__, 'admin_init' ) );
		add_action( 'admin_menu',        array( __CLASS__, 'admin_menu' ) );
		add_action( 'template_redirect', array( __CLASS__, 'template_redirect' ), 1 );

		register_activation_hook( $file, array( __CLASS__, 'activate' ) );
		register_activation_hook( $file, 'flush_rewrite_rules' );
	}

	//////////////////////////////////////////////////

	/**
	 * Initialize
	 */
	public static function init() {
		// Text domain
		$rel_path = dirname( plugin_basename( self::$file ) ) . '/languages/';
	
		load_plugin_textdomain( 'pronamic_issuu', false, $rel_path );

		// Require
		require_once self::$dirname . '/includes/functions.php';
		require_once self::$dirname . '/includes/taxonomy.php';
		require_once self::$dirname . '/includes/gravityforms.php';
		require_once self::$dirname . '/includes/template.php';
	
		// Post types
		$slug = get_option( 'pronamic_issuu_doc_base' );
		$slug = empty( $slug ) ? _x( 'documents', 'slug', 'pronamic_issuu' ) : $slug;
	
		register_post_type( 'pronamic_issuu_doc', array(
			'labels'             => array(
				'name'               => _x( 'Documents', 'post type general name', 'pronamic_issuu' ), 
				'singular_name'      => _x( 'Document', 'post type singular name', 'pronamic_issuu' ), 
				'add_new'            => _x( 'Add New', 'pronamic_issuu_doc', 'pronamic_issuu' ), 
				'add_new_item'       => __( 'Add New Document', 'pronamic_issuu' ), 
				'edit_item'          => __( 'Edit Document', 'pronamic_issuu' ), 
				'new_item'           => __( 'New Document', 'pronamic_issuu' ), 
				'view_item'          => __( 'View Document', 'pronamic_issuu' ), 
				'search_items'       => __( 'Search Document', 'pronamic_issuu' ), 
				'not_found'          => __( 'No documents found', 'pronamic_issuu' ), 
				'not_found_in_trash' => __( 'No documents found in Trash', 'pronamic_issuu' ),  
				'parent_item_colon'  => __( 'Parent Document:', 'pronamic_issuu' ), 
				'menu_name'          => __( 'Documents', 'pronamic_issuu' )
			) , 
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'capability_type'    => 'post',
			'has_archive'        => true,
			'rewrite'            => array( 'slug' => $slug ), 
			'menu_icon'          => plugins_url( 'includes/images/issuu.png', self::$file ), 
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'custom-fields' ) 
		));

		// Actions
		add_filter( 'the_content', array( __CLASS__, 'the_content' ) );
	}

	/**
	 * Admin initialize
	 */
	public static function admin_init() {
		// General
		add_settings_section(
			'pronamic_issuu_general', // id
			__( 'General', 'pronamic_issuu' ), // title
			array( __CLASS__, 'settings_section' ), // callback
			'pronamic_issuu' // page
		);

		add_settings_field( 
			'pronamic_issuu_username', // id
			__( 'Username', 'pronamic_issuu' ), // title
			array( __CLASS__, 'input_text' ),  // callback
			'pronamic_issuu', // page
			'pronamic_issuu_general', // section 
			array(  // args 
				'class' => 'regular-text',
				'label_for' => 'pronamic_issuu_username' 
			) 
		);

		add_settings_field( 
			'pronamic_issuu_api_key', // id
			__( 'API key', 'pronamic_issuu' ), // title
			array( __CLASS__, 'input_text' ),  // callback
			'pronamic_issuu', // page
			'pronamic_issuu_general', // section 
			array(  // args 
				'class' => 'regular-text',
				'label_for' => 'pronamic_issuu_api_key' 
			) 
		);

		add_settings_field( 
			'pronamic_issuu_api_secret', // id
			__( 'API secret', 'pronamic_issuu' ), // title
			array( __CLASS__, 'input_text' ),  // callback
			'pronamic_issuu', // page
			'pronamic_issuu_general', // section 
			array(  // args 
				'class' => 'regular-text',
				'label_for' => 'pronamic_issuu_api_secret' 
			) 
		);

		/*
		add_settings_field( 
			'pronamic_issuu_automatic_embed_issuu_viewer', // id
			__( 'Automatic Embed', 'pronamic_issuu' ), // title
			array( __CLASS__, 'input_checkbox' ),  // callback
			'pronamic_issuu', // page
			'pronamic_issuu_general', // section 
			array(  // args 
				'label' => __( 'Automatic embed PDF attachments Issuu viewer in the content.', 'pronamic_issuu' ),
				'label_for' => 'pronamic_issuu_automatic_embed_issuu_viewer' 
			) 
		);
		*/

		// Pages
		add_settings_section(
			'pronamic_issuu_pages', // id
			__( 'Pages', 'pronamic_issuu' ), // title
			array( __CLASS__, 'settings_section' ), // callback
			'pronamic_issuu' // page
		);
	
		add_settings_field( 
			'pronamic_issuu_no_access_page_id', // id
			__( 'No Access Page', 'pronamic_issuu' ), // title
			array( __CLASS__, 'input_page' ),  // callback
			'pronamic_issuu', // page
			'pronamic_issuu_pages', // section 
			array( 'label_for' => 'pronamic_issuu_no_access_page_id' ) // args 
		);

		// Permalinks
		// Un we can't add the permalink options to permalink settings page
		// @see http://core.trac.wordpress.org/ticket/9296
		add_settings_section(
			'pronamic_issuu_permalinks', // id
			__( 'Permalinks', 'pronamic_issuu' ), // title
			array( __CLASS__, 'settings_section' ), // callback
			'pronamic_issuu' // page
		);

		add_settings_field( 
			'pronamic_issuu_doc_base', // id
			__( 'Document base', 'pronamic_issuu' ), // title
			array( __CLASS__, 'input_text' ),  // callback
			'pronamic_issuu', // page
			'pronamic_issuu_permalinks', // section 
			array(  // args 
				'class' => 'regular-text code',
				'label_for' => 'pronamic_issuu_doc_base' 
			) 
		);

		// Register settings
		register_setting( 'pronamic_issuu', 'pronamic_issuu_username' );
		register_setting( 'pronamic_issuu', 'pronamic_issuu_api_key' );
		register_setting( 'pronamic_issuu', 'pronamic_issuu_api_secret' );
		register_setting( 'pronamic_issuu', 'pronamic_issuu_no_access_page_id' );
		register_setting( 'pronamic_issuu', 'pronamic_issuu_doc_base' );
	}
	
	/**
	 * Admin menu
	 */
	public static function admin_menu() {
		add_submenu_page( 
			'edit.php?post_type=pronamic_issuu_doc' , // parent_slug
			__( 'Issuu Settings', 'pronamic_issuu' ) , // page_title
			__( 'Settings', 'pronamic_issuu' ), // menu_title
			'read' , // capability
			'pronamic_issuu_settings' , // menu_slug
			array( __CLASS__, 'page_settings' ) // function 
		);
	}
	
	/**
	 * Page settings
	 */
	public static function page_settings() {
		include dirname( self::$file) . '/admin/settings.php';
	}

	/**
	 * Settings section
	 */
	public static function settings_section() {
		
	}

	/**
	 * Input tekst
	 * 
	 * @param array $args
	 */
	public static function input_text( $args ) {
		printf(
			'<input name="%s" id="%s" type="text" value="%s" class="%s" />', 
			esc_attr( $args['label_for'] ),
			esc_attr( $args['label_for'] ),
			esc_attr( get_option( $args['label_for'] ) ),
			$args['class']
		);
	}

	/**
	 * Input checkbox
	 * 
	 * @param array $args
	 */
	public static function input_checkbox( $args ) {
		$name = $args['label_for'];
		$label = $args['label'];
		$value = get_option( $name, true );

		printf(
			'<label for="%s">%s %s</label>',
			$name,
			sprintf(
				'<input type="checkbox" value="1" id="%s" name="%s" %s>', 
				$name,
				$name,
				checked( $value, true, false )
			),
			$label
		);
	}

	/**
	 * Input page
	 * 
	 * @param array $args
	 */
	public static function input_page( $args ) {
		$name = $args['label_for'];

		wp_dropdown_pages( array(
			'name'             => $name,
			'selected'         => get_option( $name, '' ),
			'show_option_none' => __( '&mdash; Select a page &mdash;', 'pronamic_issuu' ) 
		) );
	}

	/**
	 * Admin include file
	 * 
	 * @param string $file
	 */
	public static function admin_include( $file ) {
		include 'admin/' . $file;
	}

	/**
	 * Is mobile
	 */
	public static function is_mobile( ) {
		$user_agent = filter_input( INPUT_SERVER, 'HTTP_USER_AGENT', FILTER_SANITIZE_STRING );

		$is_ipad   = strpos( $user_agent, 'iPad' ) !== false;
		$is_iphone = strpos( $user_agent, 'iPhone' ) !== false;
		
		return $is_ipad || $is_iphone;
	}

	/**
	 * Template redirect
	 */
	public static function template_redirect( ) {
		if ( is_singular( 'pronamic_issuu_doc' ) ) {
			if ( !current_user_can( 'read_issuu_documents' ) ) {
				$page_id = get_option( 'pronamic_issuu_no_access_page_id', false );

				$redirect = get_permalink();

				if ( $page_id !== false ) {
					$url = get_permalink( $page_id );
					$url = add_query_arg( 'redirect_to', $redirect, $url );
				} else {
					$url = wp_login_url( $redirect );
				}

				wp_redirect( $url );
				
				exit;
			} else {
				if ( self::is_mobile() ) {
					$pdf = pronamic_issuu_get_pdf();

					if ( !empty( $pdf ) ) {
						$username = get_option( 'pronamic_issuu_username' );
						$name = get_post_meta( $pdf->ID, 'issuu_pdf_name', true );

						$url = pronamic_issuu_get_document_url( $username, $name );

						wp_redirect( $url );

						exit;
					}
				}
			}
		}
	}

	//////////////////////////////////////////////////

	/**
	 * Configure the specified roles
	 * 
	 * @param array $roles
	 */
	public static function set_roles( $roles ) {
		global $wp_roles;

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		foreach ( $roles as $role => $data ) {
			if ( isset( $data['display_name'], $data['capabilities'] ) ) {
				$display_name = $data['display_name'];
				$capabilities = $data['capabilities'];
	
				if ( $wp_roles->is_role( $role ) ) {
					foreach ( $capabilities as $cap => $grant ) {
						$wp_roles->add_cap( $role, $cap, $grant );
					}
				} else {
					$wp_roles->add_role( $role, $display_name, $capabilities );
				}
			}
		}
	}

	//////////////////////////////////////////////////

	/**
	 * Activate
	 */
	public static function activate() {
		// Capabilities
		$capabilities = array(
			'read_issuu_documents'         => true,
			'read_private_issuu_documents' => true
		);

		// Roles
		$roles = array(
			'editor' => array(
				'display_name' => __( 'Editor', 'pronamic_issuu' ) ,	
				'capabilities' => $capabilities
			), 
			'administrator' => array(
				'display_name' => __( 'Administrator', 'pronamic_issuu' ) ,	
				'capabilities' => $capabilities
			)
		);
			
		self::set_roles( $roles );
	}

	//////////////////////////////////////////////////

	/**
	 * The content
	 * 
	 * @param string $content
	 */
	public static function the_content( $content ) {
		if ( is_singular( 'pronamic_issuu_doc' ) ) {
			$shortcodes = pronamic_issuu_get_shortcodes();

			$addition = implode( "\r\n", $shortcodes );

			$content = $addition . $content;
		}

		return $content;
	}
}

Pronamic_Issuu_Plugin::bootstrap( __FILE__ );
