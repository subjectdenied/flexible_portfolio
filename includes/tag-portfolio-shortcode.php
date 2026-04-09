<?php
/**
 * Tag Portfolio Shortcode
 *
 * Renders a filterable portfolio grid for posts/pages using WP categories/tags.
 * Outputs HTML compatible with Divi Filterable Portfolio CSS/JS for seamless integration.
 * Standalone — works without Divi after migration.
 */

if ( ! defined( "ABSPATH" ) ) exit;

function tag_portfolio_render( $atts ) {
    $atts = shortcode_atts( array(
        "post_type"          => "post",
        "filter_by"          => "category",
        "include_categories" => "",
        "include_tags"       => "",
        "include_posts"      => "",
        "posts_number"       => "12",
        "show_filter"        => "on",
        "show_title"         => "on",
        "show_categories"    => "on",
        "show_pagination"    => "on",
        "fullwidth"          => "off",
        "columns"            => "4",
        "order"              => "DESC",
    ), $atts, "tag_portfolio" );

    // Build post types array
    $post_types = array_map( "trim", explode( ",", $atts["post_type"] ) );

    // Build query args
    $query_args = array(
        "post_type"      => $post_types,
        "posts_per_page" => intval( $atts["posts_number"] ),
        "post_status"    => array( "publish", "private" ),
        "perm"           => "readable",
        "orderby"        => "date",
        "order"          => $atts["order"],
    );

    // Specific posts override
    if ( ! empty( $atts["include_posts"] ) ) {
        $query_args["post__in"] = array_map( "intval", explode( ",", $atts["include_posts"] ) );
        $query_args["orderby"]  = "post__in";
    }

    // Tax query
    $tax_query = array();

    if ( ! empty( $atts["include_categories"] ) ) {
        $tax_query[] = array(
            "taxonomy" => "category",
            "field"    => "term_id",
            "terms"    => array_map( "intval", explode( ",", $atts["include_categories"] ) ),
            "operator" => "IN",
        );
    }

    if ( ! empty( $atts["include_tags"] ) ) {
        $tax_query[] = array(
            "taxonomy" => "post_tag",
            "field"    => "term_id",
            "terms"    => array_map( "intval", explode( ",", $atts["include_tags"] ) ),
            "operator" => "IN",
        );
    }

    if ( count( $tax_query ) > 1 ) {
        $tax_query["relation"] = "OR";
    }

    if ( ! empty( $tax_query ) ) {
        $query_args["tax_query"] = $tax_query;
    }

    $query = new WP_Query( $query_args );

    if ( ! $query->have_posts() ) {
        wp_reset_postdata();
        return "<div class=\"et_pb_portfolio_items\"><p>" . esc_html__( "Keine Beiträge gefunden.", "flexible-portfolio" ) . "</p></div>";
    }

    // Determine filter taxonomy
    $filter_taxonomy = "category";
    if ( $atts["filter_by"] === "post_tag" ) {
        $filter_taxonomy = "post_tag";
    }

    // Collect items and their terms for filter tabs
    $items_html = "";
    $all_terms  = array();

    while ( $query->have_posts() ) {
        $query->the_post();

        // Get terms for this post (for filtering classes)
        $terms = array();
        if ( $atts["filter_by"] === "both" || $atts["filter_by"] === "category" ) {
            $cat_terms = get_the_terms( get_the_ID(), "category" );
            if ( $cat_terms && ! is_wp_error( $cat_terms ) ) {
                $terms = array_merge( $terms, $cat_terms );
            }
        }
        if ( $atts["filter_by"] === "both" || $atts["filter_by"] === "post_tag" ) {
            $tag_terms = get_the_terms( get_the_ID(), "post_tag" );
            if ( $tag_terms && ! is_wp_error( $tag_terms ) ) {
                $terms = array_merge( $terms, $tag_terms );
            }
        }

        // Build CSS classes for filtering (reuses Divi pattern)
        $term_classes = array();
        $term_labels  = array();
        foreach ( $terms as $term ) {
            $slug = urldecode( $term->slug );
            $term_classes[] = "project_category_" . $slug;
            $term_labels[]  = esc_html( $term->name );
            $all_terms[ $term->term_id ] = $term;
        }

        $item_classes = array( "et_pb_portfolio_item", "active" );
        if ( $atts["fullwidth"] === "off" ) {
            $item_classes[] = "et_pb_grid_item";
        }
        $item_classes = array_merge( $item_classes, $term_classes );

        // Thumbnail
        $thumb_html = "";
        if ( has_post_thumbnail() ) {
            $width  = ( $atts["fullwidth"] === "on" ) ? 1080 : 400;
            $height = ( $atts["fullwidth"] === "on" ) ? 9999 : 284;
            $thumb_html = get_the_post_thumbnail( get_the_ID(), array( $width, $height ) );
        }

        $permalink = esc_url( get_permalink() );

        $items_html .= sprintf(
            "<div id=\"post-%1\$s\" class=\"%2\$s\">" .
                "<a href=\"%3\$s\">" .
                    "<span class=\"et_portfolio_image\">" .
                        "%4\$s" .
                        "<span class=\"et_overlay\"></span>" .
                    "</span>" .
                "</a>",
            get_the_ID(),
            esc_attr( implode( " ", $item_classes ) ),
            $permalink,
            $thumb_html
        );

        if ( $atts["show_title"] === "on" ) {
            $items_html .= sprintf(
                "<h2 class=\"et_pb_module_header\"><a href=\"%s\">%s</a></h2>",
                $permalink,
                esc_html( get_the_title() )
            );
        }

        if ( $atts["show_categories"] === "on" && ! empty( $term_labels ) ) {
            $items_html .= sprintf(
                "<p class=\"post-meta\">%s</p>",
                implode( ", ", $term_labels )
            );
        }

        $items_html .= "</div>";
    }

    wp_reset_postdata();

    // Build filter tabs
    $filters_html = "";
    if ( $atts["show_filter"] === "on" && ! empty( $all_terms ) ) {
        $filters_html .= "<div class=\"et_pb_portfolio_filters clearfix\">";
        $filters_html .= "<ul class=\"clearfix\">";
        $filters_html .= sprintf(
            "<li class=\"et_pb_portfolio_filter et_pb_portfolio_filter_all\">" .
                "<a href=\"#\" class=\"active\" data-category-slug=\"all\">%s</a>" .
            "</li>",
            esc_html__( "Alle", "flexible-portfolio" )
        );

        // Sort terms by name
        usort( $all_terms, function( $a, $b ) {
            return strcasecmp( $a->name, $b->name );
        });

        foreach ( $all_terms as $term ) {
            $filters_html .= sprintf(
                "<li class=\"et_pb_portfolio_filter\">" .
                    "<a href=\"#\" data-category-slug=\"%s\">%s</a>" .
                "</li>",
                esc_attr( urldecode( $term->slug ) ),
                esc_html( $term->name )
            );
        }

        $filters_html .= "</ul></div>";
    }

    // Wrapper classes
    $wrapper_classes = array( "et_pb_module", "et_pb_filterable_portfolio", "clearfix" );
    if ( $atts["fullwidth"] === "off" ) {
        $wrapper_classes[] = "et_pb_filterable_portfolio_grid";
    } else {
        $wrapper_classes[] = "et_pb_filterable_portfolio_fullwidth";
    }

    $output = "<style>.flex-portfolio-active .et_pb_portfolio_item { display: block !important; }</style>";
    $output .= sprintf(
        "<div class=\"%s flex-portfolio-active\" data-posts-number=\"%s\">" .
            "%s" .
            "<div class=\"et_pb_portfolio_items_wrapper clearfix\">" .
                "<div class=\"et_pb_portfolio_items\">" .
                    "%s" .
                "</div>" .
            "</div>" .
        "</div>",
        esc_attr( implode( " ", $wrapper_classes ) ),
        esc_attr( $query->found_posts ),
        $filters_html,
        $items_html
    );

    return $output;
}

add_shortcode( "tag_portfolio", "tag_portfolio_render" );

function et_pb_tag_portfolio_fallback( $atts, $content = '' ) {
    $params = array();
    $keys = array(
        'post_type', 'filter_by', 'include_categories', 'include_tags',
        'include_posts', 'posts_number', 'show_filter', 'show_title',
        'show_categories', 'fullwidth', 'columns', 'order',
    );
    foreach ( $keys as $key ) {
        if ( isset( $atts[ $key ] ) && $atts[ $key ] !== '' ) {
            $params[] = sprintf( '%s="%s"', $key, esc_attr( $atts[ $key ] ) );
        }
    }
    return do_shortcode( '[tag_portfolio ' . implode( ' ', $params ) . ']' );
}
add_shortcode( 'et_pb_tag_portfolio', 'et_pb_tag_portfolio_fallback' );
