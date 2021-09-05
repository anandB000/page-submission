<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://profiles.wordpress.org/anand000/
 * @since      1.0.0
 *
 * @package    Page_Submission
 * @subpackage Page_Submission/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Page_Submission
 * @subpackage Page_Submission/admin
 * @author     Anandaraj Balu <anandrajbalu00@gmail.com>
 */
class Page_Submission_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/page-submission-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/page-submission-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register a custom post type "guest_posts".
	 */
	public function wpps_reg_cpt_guest_posts() {
		$labels = array(
			'name'                  => _x( 'Guest posts', 'Post type general name', 'page-submission' ),
			'singular_name'         => _x( 'Post', 'Post type singular name', 'page-submission' ),
			'menu_name'             => _x( 'Guest posts', 'Admin Menu text', 'page-submission' ),
			'name_admin_bar'        => _x( 'Post', 'Add New on Toolbar', 'page-submission' ),
			'add_new'               => __( 'Add New', 'page-submission' ),
			'add_new_item'          => __( 'Add New Post', 'page-submission' ),
			'new_item'              => __( 'New Post', 'page-submission' ),
			'edit_item'             => __( 'Edit Post', 'page-submission' ),
			'view_item'             => __( 'View Post', 'page-submission' ),
			'all_items'             => __( 'All Guest posts', 'page-submission' ),
			'search_items'          => __( 'Search Guest posts', 'page-submission' ),
			'parent_item_colon'     => __( 'Parent Guest posts:', 'page-submission' ),
			'not_found'             => __( 'No Guest posts found.', 'page-submission' ),
			'not_found_in_trash'    => __( 'No Guest posts found in Trash.', 'page-submission' ),
			'featured_image'        => _x( 'Post Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'page-submission' ),
			'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'page-submission' ),
			'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'page-submission' ),
			'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'page-submission' ),
			'archives'              => _x( 'Post archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'page-submission' ),
			'insert_into_item'      => _x( 'Insert into Post', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'page-submission' ),
			'uploaded_to_this_item' => _x( 'Uploaded to this Post', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'page-submission' ),
			'filter_items_list'     => _x( 'Filter Guest posts list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'page-submission' ),
			'items_list_navigation' => _x( 'Guest posts list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'page-submission' ),
			'items_list'            => _x( 'Guest posts list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'page-submission' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'guest-posts' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt' ),
		);

		register_post_type( 'guest_posts', $args );
	}

}
