<?php
/**
 * Plugin Name: Flexible Portfolio
 * Description: Filterable portfolio grid for posts/pages using WP categories/tags. Full Divi Builder integration with Visual Builder preview. Also works as a standalone shortcode [tag_portfolio].
 * Version: 1.2.3
 * Author: subjectdenied
 * Text Domain: flexible-portfolio
 * Requires at least: 5.0
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'FLEX_PORTFOLIO_VERSION', '1.2.3' );
define( 'FLEX_PORTFOLIO_DIR', plugin_dir_path( __FILE__ ) );
define( 'FLEX_PORTFOLIO_URL', plugin_dir_url( __FILE__ ) );

// Core shortcode — works with or without Divi
require_once FLEX_PORTFOLIO_DIR . 'includes/tag-portfolio-shortcode.php';

// Divi Extension — only when Divi is active
function flex_portfolio_init_extension() {
    require_once FLEX_PORTFOLIO_DIR . 'includes/FlexiblePortfolioExtension.php';
}
add_action( 'divi_extensions_init', 'flex_portfolio_init_extension' );

// True when the current singular page actually uses the portfolio module/shortcode.
function flex_portfolio_page_has_portfolio() {
    global $post;
    if ( ! is_a( $post, 'WP_Post' ) ) {
        return false;
    }
    return has_shortcode( $post->post_content, 'et_pb_tag_portfolio' )
        || has_shortcode( $post->post_content, 'tag_portfolio' );
}

// DiviExtension's base class enqueues styles/style.min.css ("<name>-styles") and
// scripts/frontend-bundle.min.js ("<name>-frontend-bundle") on *every* frontend
// page. Both are tiny, but there's no reason to render-block pages that have no
// portfolio on them, so dequeue them there. Runs after Divi's own enqueue (pri 10).
function flex_portfolio_dequeue_global_assets() {
    // Never touch builder/admin contexts — the frontend bundle is needed for
    // Visual Builder module-picker registration (see v1.1.0 changelog).
    if ( is_admin() ) {
        return;
    }
    if ( isset( $_GET['et_fb'] ) ) {
        return;
    }
    if ( function_exists( 'et_core_is_fb_enabled' ) && et_core_is_fb_enabled() ) {
        return;
    }
    if ( flex_portfolio_page_has_portfolio() ) {
        return;
    }
    wp_dequeue_style( 'flexible-portfolio-styles' );
    wp_dequeue_script( 'flexible-portfolio-frontend-bundle' );
}
add_action( 'wp_enqueue_scripts', 'flex_portfolio_dequeue_global_assets', 20 );

// Enqueue Divi portfolio CSS on frontend when our module is used
function flex_portfolio_enqueue_styles() {
    if ( ! flex_portfolio_page_has_portfolio() ) {
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

    // Divi's dynamic-assets portfolio.css only has width rules for
    // tablet (<=980) and phone (<=767); desktop 4-col widths live in
    // Divi's main style.css which dynamic-assets doesn't load. Without
    // these, items stack full-width on desktop ("list view" bug).
    $grid_css = <<<CSS
.et_pb_portfolio_item .et_pb_portfolio_excerpt { font-size: 14px; color: #666; margin: 0.7em 0 0; line-height: 1.5; }
@media (min-width: 981px) {
    .et_pb_filterable_portfolio_grid .et_pb_grid_item.et_pb_portfolio_item {
        margin: 0 2.75% 5.5% 0;
        width: 22.9375%;
        float: left;
        clear: none;
    }
    .et_pb_filterable_portfolio_grid .et_pb_grid_item.et_pb_portfolio_item:nth-child(4n) { margin-right: 0; }
    .et_pb_filterable_portfolio_grid .et_pb_grid_item.et_pb_portfolio_item:nth-child(4n+1) { clear: both; }
}
CSS;
    wp_add_inline_style( defined( 'ET_BUILDER_VERSION' ) ? 'flex-portfolio-base' : 'flex-portfolio-standalone', $grid_css );
}
add_action( 'wp_enqueue_scripts', 'flex_portfolio_enqueue_styles' );
