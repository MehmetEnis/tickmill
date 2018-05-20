<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Tickmill_Landing_Page {

	/**
	 * The single instance of Tickmill_Landing_Page.
	 * @var 	object
	 * @access  private
	 */
	private static $_instance = null;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 */
	public $_version;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 */
	public $_token;

	/**
	 * The main plugin file.
	 * @var     string
	 * @access  public
	 */
	public $file;

	/**
	 * The main plugin directory.
	 * @var     string
	 * @access  public
	 */
	public $dir;

	/**
	 * The plugin assets directory.
	 * @var     string
	 * @access  public
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 * @var     string
	 * @access  public
	 */
	public $assets_url;

	/**
	 * Constructor function.
	 * @access  public
	 * @return  void
	 */
	public function __construct ( $file = '', $version = '1.0.0' ) {
		$this->_version = $version;
		$this->_token = 'tickmill_landing_page';

		// Load plugin environment variables
		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );


		// Load frontend JS & CSS
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );

		// Add Download Counter metabox to admin
		add_action('add_meta_boxes', array( $this, 'add_meta_box_admin') );

		// Load custom template
		add_filter( 'template_include', array( $this, 'include_template') );

		// Enable admin ajax
		add_action( 'wp_ajax_nopriv_download_e_book', array( $this, 'download_e_book' ) );
		add_action( 'wp_ajax_download_e_book', array( $this, 'download_e_book' ) );

		// Load custom fields to attachment
		add_filter( 'attachment_fields_to_edit', array($this, 'add_total_downloads_field'), 10, 2 );


	}

	/**
	 * Wrapper function to register a new post type
	 * @param  string $post_type   Post type name
	 * @param  string $plural      Post type item plural name
	 * @param  string $single      Post type item single name
	 * @param  string $description Description of post type
	 * @return object              Post type class object
	 */
	public function register_post_type ( $post_type = '', $plural = '', $single = '', $options = array() ) {

		if ( ! $post_type || ! $plural || ! $single ) return;

		$post_type = new Tickmill_Landing_Page_Post_Type( $post_type, $plural, $single, $options );

		return $post_type;
	}

	
	/**
	 * Add meta box to the dashboard
	 * @return void
	 */
	public function add_meta_box_admin () {
	    	add_meta_box(
				'total_downloads_location',
				'Total Downloads',
				array($this,'render_downloads_meta_box_content'),
				'landingpage',
				'side',
				'default'
			);
	}


	/**
	 * Display metabox content
	 * @param  object $post Post object
	 * @return void
	 */
	public function render_downloads_meta_box_content ( $post ) {

		// Get the pdf attachment
		// This assumes that there is only 1 PDF attached to these marketing pages
		// If more than 1 PDF is attached than this codes needs refactoring
    	$pdf = get_field( 'pdf', $post->ID );

    	// Get total download numner
    	$value = get_post_meta($pdf['id'], 'total_downloads', true);

    	echo '<span id="download_total" name="download_total">Total No of Downloads: ' . $value . '</span>';

	}

	/**
	 * Include custom template
	 * @param  object $template Template object
	 * @return Template
	 */
	public function include_template( $template )
	{	
	 
		if( is_singular( 'landingpage' ) ) {
	        $template = WP_PLUGIN_DIR .'/'. plugin_basename( dirname(__FILE__) ) .'/template/single-landingpage.php';
		}
	 
	    return $template;
	}

	/**
	 * Load frontend CSS.
	 * @access  public
	 * @since   1.0.0
	 */
	public function enqueue_styles () {
		wp_register_style( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'css/frontend.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-frontend' );
	}

	/**
	 * Load frontend Javascript.
	 * @access  public
	 * @return  void
	 */
	public function enqueue_scripts () {
		wp_register_script( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'js/frontend.js', array( 'jquery' ), $this->_version );
		wp_enqueue_script( $this->_token . '-frontend' );
		// set variables for script
		wp_localize_script( $this->_token . '-frontend', 'settings', array(
			'ajaxurl'	 => admin_url( 'admin-ajax.php' ),
			'send_label' => __( 'Downlaod E Book', 'downloadebook' ),
			'error'		 => __( 'Sorry, something went wrong. Please try again', 'reportabug' )
		) );
	}

	/**
	 * Handles Ajax from front-end and increments download counter
	 * @return void
	 */
	public static function download_e_book() {
		// Get post data from ajax
		$data = $_POST;
   
		// check the nonce
		if ( false == wp_verify_nonce( $data['nonce_token'], 'download_e_book_' . $data['page_id'] ) ) {
			wp_send_json_error();
		}
		
		// Get and increment total download metadata
		$count = get_post_meta($data['file_id'], 'total_downloads', true);
		// echo $count;
		$count++;
		// Save the incremented total download metadata
		$result = update_post_meta( $data['file_id'], 'total_downloads', $count );

		if ( $result ) {
			// Prepare response
			$response = array(
		        'message'   => 'Counter increased succesfully',
		        'file_url'        => wp_get_attachment_url($data['file_id'])
		    ); 
			wp_send_json_success( $response );
		} else {
			wp_send_json_error();
		}

	}


	/**
	 * Add custom fields to attachments
	 * @param  array $form_fields  Post type name
	 * @param  array $post         Post 
	 * @return object              Form Fields
	 */
	public function add_total_downloads_field( $form_fields, $post ) {
	    $form_fields['total_downloads'] = array(
	        'label' => 'Total Download',
	        'input' => 'number',
	        'value' => get_post_meta( $post->ID, 'total_downloads', true )
	    );
	 
	    return $form_fields;
	}

	/**
	 * Main Tickmill_Landing_Page Instance
	 *
	 * Ensures only one instance of Tickmill_Landing_Page is loaded or can be loaded.
	 *
	 * @static
	 * @see Tickmill_Landing_Page()
	 * @return Main Tickmill_Landing_Page instance
	 */
	public static function instance ( $file = '', $version = '1.0.0' ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}
		return self::$_instance;
	}

}
