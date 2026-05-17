<?php
defined( 'ABSPATH' ) || exit;

/**
 * [volta_rooms] shortcode
 *
 * Attributes:
 *   categories   – comma-separated room_category slugs to include (default: all)
 *   show_filter  – "yes" | "no"  show the filter pill tabs (default: yes)
 *   columns      – 2 | 3  (default: 3)
 *   limit        – max rooms to show (default: -1 = all)
 *   orderby      – meta_value_num | date | title  (default: date)
 *   order        – ASC | DESC (default: ASC)
 *
 * Example:
 *   [volta_rooms show_filter="yes" columns="3"]
 *   [volta_rooms categories="villa,suite" show_filter="no" limit="4"]
 */
add_shortcode( 'volta_rooms', 'vhr_rooms_shortcode' );

function vhr_rooms_shortcode( array $atts ): string {
    $atts = shortcode_atts( [
        'categories'  => '',
        'show_filter' => 'yes',
        'columns'     => '3',
        'limit'       => '-1',
        'orderby'     => 'date',
        'order'       => 'ASC',
    ], $atts, 'volta_rooms' );

    $args = [
        'post_type'      => 'hotel_room',
        'posts_per_page' => intval( $atts['limit'] ),
        'orderby'        => sanitize_key( $atts['orderby'] ),
        'order'          => in_array( strtoupper( $atts['order'] ), [ 'ASC', 'DESC' ] ) ? $atts['order'] : 'ASC',
        'post_status'    => 'publish',
    ];

    if ( $atts['orderby'] === 'meta_value_num' ) {
        $args['meta_key'] = 'vhr_price';
    }

    if ( ! empty( $atts['categories'] ) ) {
        $args['tax_query'] = [ [
            'taxonomy' => 'room_category',
            'field'    => 'slug',
            'terms'    => array_map( 'trim', explode( ',', $atts['categories'] ) ),
        ] ];
    }

    $query = new WP_Query( $args );
    if ( ! $query->have_posts() ) {
        return '<p class="vhr-no-rooms">' . esc_html__( 'No rooms found.', 'volta-hotel-rooms' ) . '</p>';
    }

    // Collect all categories present in this result set for the filter tabs
    $cats_in_results = [];
    foreach ( $query->posts as $post ) {
        $terms = get_the_terms( $post->ID, 'room_category' );
        if ( $terms && ! is_wp_error( $terms ) ) {
            foreach ( $terms as $term ) {
                $cats_in_results[ $term->slug ] = $term->name;
            }
        }
    }

    ob_start();
    $show_filter = $atts['show_filter'] === 'yes';
    $cols        = in_array( $atts['columns'], [ '2', '3' ] ) ? $atts['columns'] : '3';
    ?>
    <div class="vhr-rooms-block" data-cols="<?php echo esc_attr( $cols ); ?>">

        <?php if ( $show_filter && count( $cats_in_results ) > 1 ) : ?>
        <div class="vhr-filter" role="tablist" aria-label="<?php esc_attr_e( 'Filter rooms by category', 'volta-hotel-rooms' ); ?>">
            <button class="vhr-pill vhr-pill--active" data-filter="all" role="tab" aria-selected="true">
                <?php esc_html_e( 'All', 'volta-hotel-rooms' ); ?>
            </button>
            <?php foreach ( $cats_in_results as $slug => $name ) : ?>
            <button class="vhr-pill" data-filter="<?php echo esc_attr( $slug ); ?>" role="tab" aria-selected="false">
                <?php echo esc_html( $name ); ?>
            </button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="vhr-grid vhr-grid--<?php echo esc_attr( $cols ); ?>">
            <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                <?php echo vhr_render_room_card( get_the_ID() ); ?>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Build a single room card matching the HTML design's .card structure.
 */
function vhr_render_room_card( int $post_id ): string {
    $meta   = vhr_get_meta_fields( $post_id );
    $terms  = get_the_terms( $post_id, 'room_category' );
    $cats   = [];
    $labels = [];

    if ( $terms && ! is_wp_error( $terms ) ) {
        foreach ( $terms as $t ) {
            $cats[]   = $t->slug;
            $labels[] = $t->name;
        }
    }

    $data_cat    = implode( ' ', $cats );
    $cat_label   = ! empty( $labels ) ? $labels[0] : '';
    $badge       = $meta['vhr_badge']        ?: __( 'For stay', 'volta-hotel-rooms' );
    $price       = $meta['vhr_price']        ? number_format( (float) $meta['vhr_price'], 0 ) : '';
    $prefix      = $meta['vhr_price_prefix'] ?: __( 'from', 'volta-hotel-rooms' );
    $capacity    = $meta['vhr_capacity']     ?: '';
    $size        = $meta['vhr_size']         ?: '';
    $location    = $meta['vhr_location']     ?: '';
    $bed_type    = $meta['vhr_bed_type']     ?: '';
    $view        = $meta['vhr_view']         ?: '';
    $booking_url = $meta['vhr_booking_url']  ?: get_permalink( $post_id );
    $img_url     = get_the_post_thumbnail_url( $post_id, 'large' ) ?: '';

    $specs = array_filter( [
        $capacity ? [ 'icon' => 'capacity', 'label' => $capacity ] : null,
        $size     ? [ 'icon' => 'size',     'label' => $size ]     : null,
        $view     ? [ 'icon' => 'view',     'label' => $view ]     : null,
        $bed_type ? [ 'icon' => 'bed',      'label' => $bed_type ] : null,
    ] );

    ob_start();
    ?>
    <article class="vhr-card" data-cat="<?php echo esc_attr( $data_cat ); ?>" aria-label="<?php echo esc_attr( get_the_title( $post_id ) ); ?>">

        <div class="vhr-card__media">
            <?php if ( $img_url ) : ?>
            <img src="<?php echo esc_url( $img_url ); ?>"
                 alt="<?php echo esc_attr( get_the_title( $post_id ) ); ?>"
                 loading="lazy"
                 class="vhr-card__img">
            <?php else : ?>
            <div class="vhr-card__img vhr-card__img--placeholder"></div>
            <?php endif; ?>

            <div class="vhr-card__tags">
                <span class="vhr-tag vhr-tag--primary"><?php echo esc_html( $badge ); ?></span>
                <?php if ( $cat_label ) : ?>
                <span class="vhr-tag vhr-tag--secondary"><?php echo esc_html( $cat_label ); ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="vhr-card__body">
            <div class="vhr-card__meta">
                <h3 class="vhr-card__title"><?php echo esc_html( get_the_title( $post_id ) ); ?></h3>
                <?php if ( $location ) : ?>
                <p class="vhr-card__location">
                    <svg class="vhr-icon" aria-hidden="true" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8 1.5A4.5 4.5 0 0 0 3.5 6c0 2.887 3.686 6.963 4.148 7.47a.5.5 0 0 0 .704 0C8.814 12.963 12.5 8.887 12.5 6A4.5 4.5 0 0 0 8 1.5ZM8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4Z" fill="currentColor"/>
                    </svg>
                    <?php echo esc_html( $location ); ?>
                </p>
                <?php endif; ?>
            </div>

            <?php if ( $price ) : ?>
            <div class="vhr-card__price">
                <span class="vhr-price__prefix"><?php echo esc_html( $prefix ); ?></span>
                <span class="vhr-price__amount">₵<?php echo esc_html( $price ); ?></span>
                <span class="vhr-price__period"><?php esc_html_e( '/ night', 'volta-hotel-rooms' ); ?></span>
            </div>
            <?php endif; ?>

            <?php if ( ! empty( $specs ) ) : ?>
            <ul class="vhr-card__specs">
                <?php foreach ( $specs as $spec ) : ?>
                <li class="vhr-spec">
                    <?php echo vhr_spec_icon( $spec['icon'] ); ?>
                    <span><?php echo esc_html( $spec['label'] ); ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>

            <a href="<?php echo esc_url( $booking_url ); ?>" class="vhr-card__cta">
                <?php esc_html_e( 'View room', 'volta-hotel-rooms' ); ?>
                <svg class="vhr-icon" aria-hidden="true" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </div>

    </article>
    <?php
    return ob_get_clean();
}

function vhr_spec_icon( string $type ): string {
    $icons = [
        'capacity' => '<svg class="vhr-icon" aria-hidden="true" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.5 7a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5ZM5.5 7a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5ZM1 13.5A4.5 4.5 0 0 1 5.5 9h5A4.5 4.5 0 0 1 15 13.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
        'size'     => '<svg class="vhr-icon" aria-hidden="true" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="2" y="2" width="12" height="12" rx="1" stroke="currentColor" stroke-width="1.5"/><path d="M5 5h6v6H5z" stroke="currentColor" stroke-width="1"/></svg>',
        'view'     => '<svg class="vhr-icon" aria-hidden="true" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 8s2.5-5 7-5 7 5 7 5-2.5 5-7 5-7-5-7-5Z" stroke="currentColor" stroke-width="1.5"/><circle cx="8" cy="8" r="2" stroke="currentColor" stroke-width="1.5"/></svg>',
        'bed'      => '<svg class="vhr-icon" aria-hidden="true" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 11V6a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v5M1 11h14M1 13v-2M15 13v-2M3 5V3.5A.5.5 0 0 1 3.5 3h9a.5.5 0 0 1 .5.5V5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
    ];
    return $icons[ $type ] ?? '';
}
