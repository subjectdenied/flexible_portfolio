<?php
/**
 * Plugin Name: Flexible Portfolio
 * Description: Filterable portfolio grid for posts/pages using WP categories/tags. Full Divi Builder integration with Visual Builder preview. Also works as a standalone shortcode [tag_portfolio].
 * Version: 1.0.0
 * Author: subjectdenied
 * Text Domain: flexible-portfolio
 * Requires at least: 5.0
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'FLEX_PORTFOLIO_VERSION', '1.0.0' );
define( 'FLEX_PORTFOLIO_DIR', plugin_dir_path( __FILE__ ) );
define( 'FLEX_PORTFOLIO_URL', plugin_dir_url( __FILE__ ) );

// Core shortcode — works with or without Divi
require_once FLEX_PORTFOLIO_DIR . 'includes/tag-portfolio-shortcode.php';

// Divi Extension — only when Divi is active
function flex_portfolio_init_extension() {
    require_once FLEX_PORTFOLIO_DIR . 'includes/FlexiblePortfolioExtension.php';
}
add_action( 'divi_extensions_init', 'flex_portfolio_init_extension' );

// AJAX endpoint for Visual Builder preview
function flex_portfolio_ajax_preview() {
    check_ajax_referer( 'flex_portfolio_preview', 'nonce' );

    $atts = array();
    $keys = array(
        'post_type', 'filter_by', 'include_categories', 'include_tags',
        'include_posts', 'posts_number', 'show_filter', 'show_title',
        'show_categories', 'fullwidth', 'columns', 'order',
    );

    foreach ( $keys as $key ) {
        if ( isset( $_POST[ $key ] ) && $_POST[ $key ] !== '' ) {
            $atts[ $key ] = sanitize_text_field( $_POST[ $key ] );
        }
    }

    $html = tag_portfolio_render( $atts );
    wp_send_json_success( $html );
}
add_action( 'wp_ajax_flex_portfolio_preview', 'flex_portfolio_ajax_preview' );

// Pass nonce to builder JS
function flex_portfolio_builder_data() {
    if ( function_exists( 'et_core_is_fb_enabled' ) && et_core_is_fb_enabled() ) {
        wp_localize_script( 'flexible-portfolio-builder-bundle', 'FlexiblePortfolioBuilder', array(
            'nonce' => wp_create_nonce( 'flex_portfolio_preview' ),
        ) );
    }
}
add_action( 'wp_enqueue_scripts', 'flex_portfolio_builder_data', 20 );

// Enqueue Divi portfolio CSS on frontend when our module is used
function flex_portfolio_enqueue_styles() {
    global $post;
    if ( ! is_a( $post, 'WP_Post' ) ) {
        return;
    }
    if ( ! has_shortcode( $post->post_content, 'et_pb_tag_portfolio' ) && ! has_shortcode( $post->post_content, 'tag_portfolio' ) ) {
        return;
    }

    if ( defined( 'ET_BUILDER_VERSION' ) ) {
        $assets_prefix = get_template_directory_uri() . '/includes/builder/feature/dynamic-assets/assets/css';
        wp_enqueue_style( 'flex-portfolio-base', $assets_prefix . '/portfolio.css', array(), ET_BUILDER_VERSION );
        wp_enqueue_style( 'flex-portfolio-filterable', $assets_prefix . '/filterable_portfolio.css', array(), ET_BUILDER_VERSION );
        wp_enqueue_style( 'flex-portfolio-overlay', $assets_prefix . '/overlay.css', array(), ET_BUILDER_VERSION );
    } else {
        wp_enqueue_style( 'flex-portfolio-standalone', FLEX_PORTFOLIO_URL . 'assets/css/portfolio-standalone.css', array(), FLEX_PORTFOLIO_VERSION );
    }
}
add_action( 'wp_enqueue_scripts', 'flex_portfolio_enqueue_styles' );
