<?php
defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', 'vhr_enqueue_assets' );
function vhr_enqueue_assets(): void {
    if ( ! vhr_page_has_rooms_block() ) {
        return;
    }

    wp_enqueue_style(
        'vhr-rooms',
        VHR_URL . 'assets/css/rooms.css',
        [],
        VHR_VERSION
    );

    wp_enqueue_script(
        'vhr-rooms',
        VHR_URL . 'assets/js/rooms.js',
        [],
        VHR_VERSION,
        true
    );
}

function vhr_page_has_rooms_block(): bool {
    global $post;
    return $post && (
        has_shortcode( $post->post_content, 'volta_rooms' ) ||
        str_contains( $post->post_content, '"widgetType":"vhr_rooms"' )
    );
}

// Always load in Elementor editor preview
add_action( 'elementor/preview/enqueue_styles', 'vhr_enqueue_editor_assets' );
add_action( 'elementor/editor/enqueue_scripts', 'vhr_enqueue_editor_assets' );
function vhr_enqueue_editor_assets(): void {
    wp_enqueue_style( 'vhr-rooms', VHR_URL . 'assets/css/rooms.css', [], VHR_VERSION );
    wp_enqueue_script( 'vhr-rooms', VHR_URL . 'assets/js/rooms.js', [], VHR_VERSION, true );
}
