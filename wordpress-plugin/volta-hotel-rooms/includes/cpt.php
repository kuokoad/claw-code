<?php
defined( 'ABSPATH' ) || exit;

/**
 * Register the Hotel Room custom post type and Room Category taxonomy.
 */
add_action( 'init', 'vhr_register_cpt' );
function vhr_register_cpt(): void {

    // ── Taxonomy: Room Category (Villa / Chalet / Suite / Deluxe / Executive …) ──
    register_taxonomy( 'room_category', 'hotel_room', [
        'labels' => [
            'name'          => __( 'Room Categories', 'volta-hotel-rooms' ),
            'singular_name' => __( 'Room Category',   'volta-hotel-rooms' ),
            'add_new_item'  => __( 'Add New Category', 'volta-hotel-rooms' ),
            'edit_item'     => __( 'Edit Category',    'volta-hotel-rooms' ),
        ],
        'public'            => true,
        'hierarchical'      => false,  // tag-style, not category-style
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'room-category' ],
    ] );

    // ── Post Type: Hotel Room ──
    register_post_type( 'hotel_room', [
        'labels' => [
            'name'               => __( 'Rooms & Suites',    'volta-hotel-rooms' ),
            'singular_name'      => __( 'Room',              'volta-hotel-rooms' ),
            'add_new_item'       => __( 'Add New Room',      'volta-hotel-rooms' ),
            'edit_item'          => __( 'Edit Room',         'volta-hotel-rooms' ),
            'new_item'           => __( 'New Room',          'volta-hotel-rooms' ),
            'view_item'          => __( 'View Room',         'volta-hotel-rooms' ),
            'search_items'       => __( 'Search Rooms',      'volta-hotel-rooms' ),
            'not_found'          => __( 'No rooms found',    'volta-hotel-rooms' ),
            'not_found_in_trash' => __( 'No rooms in trash', 'volta-hotel-rooms' ),
            'menu_name'          => __( 'Rooms',             'volta-hotel-rooms' ),
        ],
        'public'             => true,
        'has_archive'        => true,
        'show_in_rest'       => true,
        'menu_icon'          => 'dashicons-building',
        'menu_position'      => 20,
        'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
        'taxonomies'         => [ 'room_category' ],
        'rewrite'            => [ 'slug' => 'rooms' ],
    ] );
}

/**
 * Seed default room categories on first activation.
 */
register_activation_hook( VHR_DIR . '../volta-hotel-rooms.php', 'vhr_seed_categories' );
function vhr_seed_categories(): void {
    $defaults = [ 'villa', 'chalet', 'suite', 'deluxe', 'executive', 'honeymoon' ];
    foreach ( $defaults as $slug ) {
        if ( ! term_exists( $slug, 'room_category' ) ) {
            wp_insert_term( ucfirst( $slug ), 'room_category', [ 'slug' => $slug ] );
        }
    }
    flush_rewrite_rules();
}
