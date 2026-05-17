<?php
defined( 'ABSPATH' ) || exit;

/**
 * Room Details meta box – all structured fields for a hotel room.
 *
 * Fields:
 *   vhr_price        – nightly price in GHS (numeric)
 *   vhr_price_prefix – label before price, e.g. "from"
 *   vhr_capacity     – e.g. "2 Adults"
 *   vhr_size         – e.g. "52 m²"
 *   vhr_location     – e.g. "Kabakaba Hills, Ho"
 *   vhr_bed_type     – e.g. "King bed"
 *   vhr_view         – e.g. "Hill view"
 *   vhr_badge        – short badge label, e.g. "For stay"
 *   vhr_gallery      – comma-separated attachment IDs for room gallery
 *   vhr_booking_url  – direct booking / enquiry URL for this room
 */

add_action( 'add_meta_boxes', 'vhr_add_meta_boxes' );
function vhr_add_meta_boxes(): void {
    add_meta_box(
        'vhr_room_details',
        __( 'Room Details', 'volta-hotel-rooms' ),
        'vhr_render_meta_box',
        'hotel_room',
        'normal',
        'high'
    );
}

function vhr_render_meta_box( WP_Post $post ): void {
    wp_nonce_field( 'vhr_save_meta', 'vhr_nonce' );

    $fields = vhr_get_meta_fields( $post->ID );
    ?>
    <style>
        .vhr-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px 24px; }
        .vhr-field { display:flex; flex-direction:column; gap:4px; }
        .vhr-field label { font-weight:600; font-size:13px; }
        .vhr-field input, .vhr-field select {
            border:1px solid #8c8f94; border-radius:4px; padding:6px 8px; font-size:14px;
        }
        .vhr-field--full { grid-column: span 2; }
    </style>
    <div class="vhr-grid">
        <?php
        $inputs = [
            [ 'id' => 'vhr_price',        'label' => 'Nightly Price (GHS)',  'type' => 'number', 'placeholder' => '2315' ],
            [ 'id' => 'vhr_price_prefix', 'label' => 'Price Prefix',         'type' => 'text',   'placeholder' => 'from' ],
            [ 'id' => 'vhr_capacity',     'label' => 'Capacity',             'type' => 'text',   'placeholder' => '2 Adults' ],
            [ 'id' => 'vhr_size',         'label' => 'Room Size',            'type' => 'text',   'placeholder' => '52 m²' ],
            [ 'id' => 'vhr_location',     'label' => 'Location / Wing',      'type' => 'text',   'placeholder' => 'Kabakaba Hills, Ho' ],
            [ 'id' => 'vhr_bed_type',     'label' => 'Bed Type',             'type' => 'text',   'placeholder' => 'King bed' ],
            [ 'id' => 'vhr_view',         'label' => 'View / Highlight',     'type' => 'text',   'placeholder' => 'Hill view' ],
            [ 'id' => 'vhr_badge',        'label' => 'Card Badge',           'type' => 'text',   'placeholder' => 'For stay' ],
            [ 'id' => 'vhr_booking_url',  'label' => 'Booking / Enquiry URL','type' => 'url',    'placeholder' => 'https://...', 'full' => true ],
            [ 'id' => 'vhr_gallery',      'label' => 'Gallery Attachment IDs (comma-separated)', 'type' => 'text', 'placeholder' => '101,102,103', 'full' => true ],
        ];

        foreach ( $inputs as $f ) {
            $class = isset( $f['full'] ) ? 'vhr-field vhr-field--full' : 'vhr-field';
            $val   = esc_attr( $fields[ $f['id'] ] ?? '' );
            echo '<div class="' . $class . '">';
            echo '<label for="' . $f['id'] . '">' . esc_html( $f['label'] ) . '</label>';
            echo '<input type="' . $f['type'] . '" id="' . $f['id'] . '" name="' . $f['id'] . '" value="' . $val . '" placeholder="' . esc_attr( $f['placeholder'] ) . '">';
            echo '</div>';
        }
        ?>
    </div>
    <?php
}

add_action( 'save_post_hotel_room', 'vhr_save_meta' );
function vhr_save_meta( int $post_id ): void {
    if (
        ! isset( $_POST['vhr_nonce'] ) ||
        ! wp_verify_nonce( $_POST['vhr_nonce'], 'vhr_save_meta' ) ||
        defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ||
        ! current_user_can( 'edit_post', $post_id )
    ) {
        return;
    }

    $fields = [ 'vhr_price', 'vhr_price_prefix', 'vhr_capacity', 'vhr_size',
                'vhr_location', 'vhr_bed_type', 'vhr_view', 'vhr_badge',
                'vhr_booking_url', 'vhr_gallery' ];

    foreach ( $fields as $key ) {
        if ( $key === 'vhr_price' ) {
            update_post_meta( $post_id, $key, absint( $_POST[ $key ] ?? 0 ) );
        } elseif ( $key === 'vhr_booking_url' ) {
            update_post_meta( $post_id, $key, esc_url_raw( $_POST[ $key ] ?? '' ) );
        } else {
            update_post_meta( $post_id, $key, sanitize_text_field( $_POST[ $key ] ?? '' ) );
        }
    }
}

function vhr_get_meta_fields( int $post_id ): array {
    $keys = [ 'vhr_price', 'vhr_price_prefix', 'vhr_capacity', 'vhr_size',
              'vhr_location', 'vhr_bed_type', 'vhr_view', 'vhr_badge',
              'vhr_booking_url', 'vhr_gallery' ];

    $data = [];
    foreach ( $keys as $k ) {
        $data[ $k ] = get_post_meta( $post_id, $k, true );
    }
    return $data;
}
