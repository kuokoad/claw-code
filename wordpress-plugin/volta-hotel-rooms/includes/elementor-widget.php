<?php
defined( 'ABSPATH' ) || exit;

/**
 * Register Elementor widget only when Elementor is loaded.
 */
add_action( 'elementor/widgets/register', 'vhr_register_elementor_widget' );
function vhr_register_elementor_widget( \Elementor\Widgets_Manager $manager ): void {
    $manager->register( new VHR_Rooms_Widget() );
}

class VHR_Rooms_Widget extends \Elementor\Widget_Base {

    public function get_name(): string        { return 'vhr_rooms'; }
    public function get_title(): string       { return __( 'Hotel Rooms Grid', 'volta-hotel-rooms' ); }
    public function get_icon(): string        { return 'eicon-posts-grid'; }
    public function get_categories(): array   { return [ 'general' ]; }
    public function get_keywords(): array     { return [ 'rooms', 'hotel', 'volta', 'suites', 'grid' ]; }

    protected function register_controls(): void {

        // ── Content tab ──────────────────────────────────────────────────────
        $this->start_controls_section( 'section_content', [
            'label' => __( 'Rooms Query', 'volta-hotel-rooms' ),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'show_filter', [
            'label'        => __( 'Show Category Filter', 'volta-hotel-rooms' ),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'label_on'     => __( 'Yes', 'volta-hotel-rooms' ),
            'label_off'    => __( 'No', 'volta-hotel-rooms' ),
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );

        $this->add_control( 'columns', [
            'label'   => __( 'Columns', 'volta-hotel-rooms' ),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => '3',
            'options' => [
                '2' => '2',
                '3' => '3',
            ],
        ] );

        $this->add_control( 'limit', [
            'label'   => __( 'Max Rooms', 'volta-hotel-rooms' ),
            'type'    => \Elementor\Controls_Manager::NUMBER,
            'default' => -1,
            'min'     => -1,
        ] );

        $this->add_control( 'categories', [
            'label'       => __( 'Filter by Categories', 'volta-hotel-rooms' ),
            'type'        => \Elementor\Controls_Manager::TEXT,
            'placeholder' => 'villa, suite',
            'description' => __( 'Comma-separated room_category slugs. Leave empty for all.', 'volta-hotel-rooms' ),
        ] );

        $this->add_control( 'orderby', [
            'label'   => __( 'Order By', 'volta-hotel-rooms' ),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'date',
            'options' => [
                'date'           => __( 'Date', 'volta-hotel-rooms' ),
                'title'          => __( 'Title (A–Z)', 'volta-hotel-rooms' ),
                'meta_value_num' => __( 'Price', 'volta-hotel-rooms' ),
                'menu_order'     => __( 'Menu Order', 'volta-hotel-rooms' ),
            ],
        ] );

        $this->add_control( 'order', [
            'label'   => __( 'Order Direction', 'volta-hotel-rooms' ),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'ASC',
            'options' => [
                'ASC'  => __( 'Ascending',  'volta-hotel-rooms' ),
                'DESC' => __( 'Descending', 'volta-hotel-rooms' ),
            ],
        ] );

        $this->end_controls_section();

        // ── Style tab – Filter pills ─────────────────────────────────────────
        $this->start_controls_section( 'section_style_filter', [
            'label' => __( 'Filter Pills', 'volta-hotel-rooms' ),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'pill_bg', [
            'label'     => __( 'Pill Background (Active)', 'volta-hotel-rooms' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .vhr-pill--active' => 'background-color: {{VALUE}};' ],
        ] );

        $this->add_control( 'pill_color', [
            'label'     => __( 'Pill Text (Active)', 'volta-hotel-rooms' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .vhr-pill--active' => 'color: {{VALUE}};' ],
        ] );

        $this->end_controls_section();

        // ── Style tab – Card ─────────────────────────────────────────────────
        $this->start_controls_section( 'section_style_card', [
            'label' => __( 'Room Card', 'volta-hotel-rooms' ),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'card_bg', [
            'label'     => __( 'Card Background', 'volta-hotel-rooms' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .vhr-card' => 'background-color: {{VALUE}};' ],
        ] );

        $this->add_control( 'card_radius', [
            'label'      => __( 'Border Radius', 'volta-hotel-rooms' ),
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
            'selectors'  => [ '{{WRAPPER}} .vhr-card' => 'border-radius: {{SIZE}}{{UNIT}};' ],
        ] );

        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'title_typography',
            'label'    => __( 'Room Title', 'volta-hotel-rooms' ),
            'selector' => '{{WRAPPER}} .vhr-card__title',
        ] );

        $this->add_control( 'price_color', [
            'label'     => __( 'Price Colour', 'volta-hotel-rooms' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .vhr-price__amount' => 'color: {{VALUE}};' ],
        ] );

        $this->add_control( 'cta_bg', [
            'label'     => __( 'CTA Button Background', 'volta-hotel-rooms' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .vhr-card__cta' => 'background-color: {{VALUE}};' ],
        ] );

        $this->add_control( 'cta_color', [
            'label'     => __( 'CTA Button Text', 'volta-hotel-rooms' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .vhr-card__cta' => 'color: {{VALUE}};' ],
        ] );

        $this->end_controls_section();
    }

    protected function render(): void {
        $s = $this->get_settings_for_display();

        echo do_shortcode( sprintf(
            '[volta_rooms show_filter="%s" columns="%s" limit="%s" categories="%s" orderby="%s" order="%s"]',
            esc_attr( $s['show_filter'] ),
            esc_attr( $s['columns'] ),
            intval( $s['limit'] ),
            esc_attr( $s['categories'] ?? '' ),
            esc_attr( $s['orderby'] ),
            esc_attr( $s['order'] )
        ) );
    }
}
