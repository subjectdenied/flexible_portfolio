<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class ET_Builder_Module_TagPortfolio extends ET_Builder_Module {

    public $slug       = 'et_pb_tag_portfolio';
    public $vb_support = 'on';

    function init() {
        $this->name   = esc_html__( 'Tag Portfolio', 'flexible-portfolio' );
        $this->plural = esc_html__( 'Tag Portfolios', 'flexible-portfolio' );
        $this->icon   = 'P';

        $this->settings_modal_toggles = array(
            'general' => array(
                'toggles' => array(
                    'main_content' => esc_html__( 'Content', 'flexible-portfolio' ),
                    'elements'     => esc_html__( 'Elements', 'flexible-portfolio' ),
                ),
            ),
        );
    }

    function get_fields() {
        return array(
            'post_type' => array(
                'label'       => esc_html__( 'Inhaltstyp', 'flexible-portfolio' ),
                'type'        => 'select',
                'options'     => array(
                    'post'      => esc_html__( 'Beiträge', 'flexible-portfolio' ),
                    'page'      => esc_html__( 'Seiten', 'flexible-portfolio' ),
                    'post,page' => esc_html__( 'Beiträge & Seiten', 'flexible-portfolio' ),
                ),
                'default'         => 'post',
                'toggle_slug'     => 'main_content',
                'computed_affects' => array( '__fp_items', '__fp_terms' ),
            ),
            'filter_by' => array(
                'label'       => esc_html__( 'Filtern nach', 'flexible-portfolio' ),
                'type'        => 'select',
                'options'     => array(
                    'category' => esc_html__( 'Kategorie', 'flexible-portfolio' ),
                    'post_tag' => esc_html__( 'Schlagwort', 'flexible-portfolio' ),
                    'both'     => esc_html__( 'Kategorie & Schlagwort', 'flexible-portfolio' ),
                ),
                'default'         => 'category',
                'toggle_slug'     => 'main_content',
                'computed_affects' => array( '__fp_items', '__fp_terms' ),
            ),
            'include_categories' => array(
                'label'            => esc_html__( 'Kategorien einschließen', 'flexible-portfolio' ),
                'type'             => 'categories',
                'taxonomy_name'    => 'category',
                'renderer_options' => array( 'use_terms' => false ),
                'toggle_slug'      => 'main_content',
                'computed_affects'  => array( '__fp_items', '__fp_terms' ),
            ),
            'include_tags' => array(
                'label'            => esc_html__( 'Schlagwörter einschließen', 'flexible-portfolio' ),
                'type'             => 'categories',
                'taxonomy_name'    => 'post_tag',
                'renderer_options' => array( 'use_terms' => false ),
                'toggle_slug'      => 'main_content',
                'computed_affects'  => array( '__fp_items', '__fp_terms' ),
            ),
            'include_posts' => array(
                'label'       => esc_html__( 'Bestimmte Beiträge/Seiten (IDs)', 'flexible-portfolio' ),
                'type'        => 'text',
                'description' => esc_html__( 'Kommagetrennte Post/Seiten IDs.', 'flexible-portfolio' ),
                'toggle_slug' => 'main_content',
                'computed_affects' => array( '__fp_items' ),
            ),
            'posts_number' => array(
                'label'       => esc_html__( 'Anzahl Beiträge', 'flexible-portfolio' ),
                'type'        => 'text',
                'default'     => '12',
                'toggle_slug' => 'main_content',
                'computed_affects' => array( '__fp_items' ),
            ),
            'order' => array(
                'label'   => esc_html__( 'Sortierung', 'flexible-portfolio' ),
                'type'    => 'select',
                'options' => array(
                    'DESC' => esc_html__( 'Neueste zuerst', 'flexible-portfolio' ),
                    'ASC'  => esc_html__( 'Älteste zuerst', 'flexible-portfolio' ),
                ),
                'default'     => 'DESC',
                'toggle_slug' => 'elements',
                'computed_affects' => array( '__fp_items' ),
            ),
            'show_filter' => array(
                'label'   => esc_html__( 'Filtertabs anzeigen', 'flexible-portfolio' ),
                'type'    => 'yes_no_button',
                'options' => array(
                    'on'  => esc_html__( 'Ja', 'flexible-portfolio' ),
                    'off' => esc_html__( 'Nein', 'flexible-portfolio' ),
                ),
                'default'     => 'on',
                'toggle_slug' => 'elements',
            ),
            'show_title' => array(
                'label'   => esc_html__( 'Titel anzeigen', 'flexible-portfolio' ),
                'type'    => 'yes_no_button',
                'options' => array(
                    'on'  => esc_html__( 'Ja', 'flexible-portfolio' ),
                    'off' => esc_html__( 'Nein', 'flexible-portfolio' ),
                ),
                'default'     => 'on',
                'toggle_slug' => 'elements',
            ),
            'show_categories' => array(
                'label'   => esc_html__( 'Kategorien/Tags anzeigen', 'flexible-portfolio' ),
                'type'    => 'yes_no_button',
                'options' => array(
                    'on'  => esc_html__( 'Ja', 'flexible-portfolio' ),
                    'off' => esc_html__( 'Nein', 'flexible-portfolio' ),
                ),
                'default'     => 'on',
                'toggle_slug' => 'elements',
            ),
            'fullwidth' => array(
                'label'   => esc_html__( 'Layout', 'flexible-portfolio' ),
                'type'    => 'select',
                'options' => array(
                    'off' => esc_html__( 'Raster', 'flexible-portfolio' ),
                    'on'  => esc_html__( 'Volle Breite', 'flexible-portfolio' ),
                ),
                'default'         => 'off',
                'toggle_slug'     => 'main_content',
                'computed_affects' => array( '__fp_items' ),
            ),
            'columns' => array(
                'label'          => esc_html__( 'Spalten', 'flexible-portfolio' ),
                'type'           => 'range',
                'default'        => '4',
                'range_settings' => array( 'min' => '1', 'max' => '6', 'step' => '1' ),
                'toggle_slug'    => 'main_content',
                'show_if'        => array( 'fullwidth' => 'off' ),
            ),
            // Computed fields — Divi calls these automatically for VB preview
            '__fp_items' => array(
                'type'                => 'computed',
                'computed_callback'   => array( 'ET_Builder_Module_TagPortfolio', 'get_items' ),
                'computed_depends_on' => array(
                    'post_type', 'filter_by', 'include_categories', 'include_tags',
                    'include_posts', 'posts_number', 'fullwidth', 'order',
                ),
            ),
            '__fp_terms' => array(
                'type'                => 'computed',
                'computed_callback'   => array( 'ET_Builder_Module_TagPortfolio', 'get_terms_data' ),
                'computed_depends_on' => array(
                    'filter_by', 'include_categories', 'include_tags',
                ),
            ),
        );
    }

    /**
     * Computed callback: returns portfolio items data for the VB.
     */
    static function get_items( $args = array(), $conditional_tags = array(), $current_page = array() ) {
        $defaults = array(
            'post_type'          => 'post',
            'filter_by'          => 'category',
            'include_categories' => '',
            'include_tags'       => '',
            'include_posts'      => '',
            'posts_number'       => '12',
            'fullwidth'          => 'off',
            'order'              => 'DESC',
        );
        $args = wp_parse_args( $args, $defaults );

        if ( empty( $args['include_categories'] ) && empty( $args['include_tags'] ) && empty( $args['include_posts'] ) ) {
            return array();
        }

        // Build selected term IDs
        $selected_ids = array();
        if ( ! empty( $args['include_categories'] ) ) {
            $selected_ids = array_merge( $selected_ids, array_map( 'intval', explode( ',', $args['include_categories'] ) ) );
        }
        if ( ! empty( $args['include_tags'] ) ) {
            $selected_ids = array_merge( $selected_ids, array_map( 'intval', explode( ',', $args['include_tags'] ) ) );
        }

        $post_types = array_map( 'trim', explode( ',', $args['post_type'] ) );

        $query_args = array(
            'post_type'      => $post_types,
            'posts_per_page' => intval( $args['posts_number'] ),
            'post_status'    => array( 'publish', 'private' ),
            'perm'           => 'readable',
            'orderby'        => 'date',
            'order'          => $args['order'],
        );

        if ( ! empty( $args['include_posts'] ) ) {
            $query_args['post__in'] = array_map( 'intval', explode( ',', $args['include_posts'] ) );
            $query_args['orderby']  = 'post__in';
        }

        $tax_query = array();
        if ( ! empty( $args['include_categories'] ) ) {
            $tax_query[] = array(
                'taxonomy' => 'category',
                'field'    => 'term_id',
                'terms'    => array_map( 'intval', explode( ',', $args['include_categories'] ) ),
                'operator' => 'IN',
            );
        }
        if ( ! empty( $args['include_tags'] ) ) {
            $tax_query[] = array(
                'taxonomy' => 'post_tag',
                'field'    => 'term_id',
                'terms'    => array_map( 'intval', explode( ',', $args['include_tags'] ) ),
                'operator' => 'IN',
            );
        }
        if ( count( $tax_query ) > 1 ) {
            $tax_query['relation'] = 'OR';
        }
        if ( ! empty( $tax_query ) ) {
            $query_args['tax_query'] = $tax_query;
        }

        $query = new WP_Query( $query_args );
        $items = array();

        if ( $query->have_posts() ) {
            $width  = ( 'on' === $args['fullwidth'] ) ? 1080 : 400;
            $height = ( 'on' === $args['fullwidth'] ) ? 9999 : 284;

            while ( $query->have_posts() ) {
                $query->the_post();

                $terms = array();
                $category_classes = array();

                $all_terms = array();
                if ( in_array( $args['filter_by'], array( 'category', 'both' ) ) ) {
                    $cat_terms = get_the_terms( get_the_ID(), 'category' );
                    if ( $cat_terms && ! is_wp_error( $cat_terms ) ) {
                        $all_terms = array_merge( $all_terms, $cat_terms );
                    }
                }
                if ( in_array( $args['filter_by'], array( 'post_tag', 'both' ) ) ) {
                    $tag_terms = get_the_terms( get_the_ID(), 'post_tag' );
                    if ( $tag_terms && ! is_wp_error( $tag_terms ) ) {
                        $all_terms = array_merge( $all_terms, $tag_terms );
                    }
                }

                foreach ( $all_terms as $term ) {
                    if ( ! in_array( $term->term_id, $selected_ids ) ) {
                        continue;
                    }
                    $category_classes[] = 'project_category_' . urldecode( $term->slug );
                    $terms[] = array(
                        'id'    => $term->term_id,
                        'slug'  => $term->slug,
                        'label' => $term->name,
                    );
                }

                $thumbnail = '';
                if ( has_post_thumbnail() ) {
                    $thumbnail = get_the_post_thumbnail( get_the_ID(), array( $width, $height ) );
                }

                $items[] = array(
                    'id'               => get_the_ID(),
                    'title'            => get_the_title(),
                    'permalink'        => get_permalink(),
                    'thumbnail'        => $thumbnail,
                    'post_categories'  => $terms,
                    'category_classes' => $category_classes,
                );
            }
            wp_reset_postdata();
        }

        return $items;
    }

    /**
     * Computed callback: returns filter terms data for the VB.
     */
    static function get_terms_data( $args = array(), $conditional_tags = array(), $current_page = array() ) {
        $defaults = array(
            'filter_by'          => 'category',
            'include_categories' => '',
            'include_tags'       => '',
        );
        $args  = wp_parse_args( $args, $defaults );
        $terms = array();

        if ( ! empty( $args['include_categories'] ) ) {
            foreach ( array_map( 'intval', explode( ',', $args['include_categories'] ) ) as $tid ) {
                $term = get_term( $tid, 'category' );
                if ( $term && ! is_wp_error( $term ) ) {
                    $terms[ $term->slug ] = array(
                        'id'    => $term->term_id,
                        'slug'  => $term->slug,
                        'label' => $term->name,
                    );
                }
            }
        }

        if ( ! empty( $args['include_tags'] ) ) {
            foreach ( array_map( 'intval', explode( ',', $args['include_tags'] ) ) as $tid ) {
                $term = get_term( $tid, 'post_tag' );
                if ( $term && ! is_wp_error( $term ) ) {
                    $terms[ $term->slug ] = array(
                        'id'    => $term->term_id,
                        'slug'  => $term->slug,
                        'label' => $term->name,
                    );
                }
            }
        }

        return $terms;
    }

    public function render( $attrs, $content, $render_slug ) {
        $atts = array();
        $keys = array(
            'post_type', 'filter_by', 'include_categories', 'include_tags',
            'include_posts', 'posts_number', 'show_filter', 'show_title',
            'show_categories', 'fullwidth', 'columns', 'order',
        );

        foreach ( $keys as $key ) {
            if ( isset( $this->props[ $key ] ) && $this->props[ $key ] !== '' ) {
                $atts[ $key ] = $this->props[ $key ];
            }
        }

        return tag_portfolio_render( $atts );
    }
}

new ET_Builder_Module_TagPortfolio();
