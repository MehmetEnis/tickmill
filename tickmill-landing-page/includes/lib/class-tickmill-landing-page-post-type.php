<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Tickmill_Landing_Page_Post_Type {

	/**
	 * The name for the custom post type.
	 * @var 	string
	 * @access  public
	 */
	public $post_type;

	/**
	 * The plural name for the custom post type posts.
	 * @var 	string
	 * @access  public
	 */
	public $plural;

	/**
	 * The singular name for the custom post type posts.
	 * @var 	string
	 * @access  public
	 */
	public $single;

	/**
	 * The options of the custom post type.
	 * @var 	array
	 * @access  public
	 */
	public $options;

	public function __construct ( $post_type = '', $plural = '', $single = '', $options = array() ) {

		if ( ! $post_type || ! $plural || ! $single ) return;

		// Post type name and labels
		$this->post_type = $post_type;
		$this->plural = $plural;
		$this->single = $single;
		$this->options = $options;

		// Regsiter post type
		add_action( 'init' , array( $this, 'register_post_type' ) );
	}

	/**
	 * Register new post type
	 * @return void
	 */
	public function register_post_type () {

		$labels = array(
			'name' => $this->plural,
			'singular_name' => $this->single,
			'name_admin_bar' => $this->single,
			'add_new' => _x( 'Add New', $this->post_type , 'tickmill-landing-page' ),
			'add_new_item' => sprintf('Add New %s', $this->single ),
			'edit_item' => sprintf('Edit %s', $this->single ),
			'new_item' => sprintf('New %s', $this->single ),
			'all_items' => sprintf('All %s', $this->plural ),
			'view_item' => sprintf('View %s', $this->single ),
			'search_items' => sprintf('Search %s', $this->plural ),
			'not_found' =>  sprintf('No %s Found', $this->plural ),
			'not_found_in_trash' => sprintf('No %s Found In Trash', $this->plural ),
			'parent_item_colon' => sprintf('Parent %s', $this->single ),
			'menu_name' => $this->plural,
		);

		$args = array(
			'labels' => apply_filters( $this->post_type . '_labels', $labels ),
			'description' => 'Tickmill Landing Pages',
			'public' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_nav_menus' => true,
			'query_var' => true,
			'can_export' => true,
			'rewrite' => true,
			'capability_type' => 'post',
			'has_archive' => true,
			'hierarchical' => true,
			'show_in_rest'       	=> true,
	  		'rest_base'          	=> $this->post_type,
	  		'rest_controller_class' => 'WP_REST_Posts_Controller',
			'supports' => array( 'title', 'editor', 'excerpt', 'comments', 'thumbnail' ),
			'menu_position' => 5,
			'menu_icon' => 'dashicons-megaphone',
		);

		$args = array_merge($args, $this->options);

		register_post_type( $this->post_type,  $args );
	}

}
