<?php
/*
 * Plugin Name: Tickmill Landing Page
 * Version: 1.0
 * Plugin URI: http://www.tickmill.com/
 * Description: Provides custom landing pages for marketing with PDF downloads and download counts
 * Author: Mehmet Enis
 * Author URI: https://www.linkedin.com/in/mehmetenis/
 * Requires at least: 4.0
 * Tested up to: 4.9
 *
 * Text Domain: tickmill-landing-page
 * 
 *
 * @package TickMill
 * @author Mehmet Enis
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Load plugin class files
require_once( 'includes/class-tickmill-landing-page.php' );

// Load plugin libraries
require_once( 'includes/lib/class-tickmill-landing-page-post-type.php' );

/**
 * Returns the main instance of Tickmill_Landing_Page to prevent the need to use globals.
 *
 * @return object Tickmill_Landing_Page
 */
function Tickmill_Landing_Page () {
	$instance = Tickmill_Landing_Page::instance( __FILE__, '1.0.0' );
	return $instance;
}

Tickmill_Landing_Page()->register_post_type( 'landingpage', 'Landing Pages', 'Landing Page');