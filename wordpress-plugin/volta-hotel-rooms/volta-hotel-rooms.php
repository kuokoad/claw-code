<?php
/**
 * Plugin Name:       Volta Hotel – Rooms Manager
 * Plugin URI:        https://voltaserenehotel.com
 * Description:       Custom Post Type and Elementor widget for the Volta Serene Hotel Rooms section. Provides [volta_rooms] shortcode and an Elementor widget with live category filtering.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            Volta Serene Hotel
 * License:           GPL-2.0-or-later
 * Text Domain:       volta-hotel-rooms
 */

defined( 'ABSPATH' ) || exit;

define( 'VHR_VERSION',  '1.0.0' );
define( 'VHR_DIR',      plugin_dir_path( __FILE__ ) );
define( 'VHR_URL',      plugin_dir_url( __FILE__ ) );

require VHR_DIR . 'includes/cpt.php';
require VHR_DIR . 'includes/meta-boxes.php';
require VHR_DIR . 'includes/shortcode.php';
require VHR_DIR . 'includes/elementor-widget.php';
require VHR_DIR . 'includes/assets.php';
