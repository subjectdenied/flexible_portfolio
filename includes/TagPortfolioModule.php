<?php
/**
 * Tag Portfolio — Divi Visual Builder Wrapper
 *
 * Thin wrapper that exposes shortcode settings in the Divi Builder UI.
 * All business logic lives in tag-portfolio-shortcode.php.
 * Delete this file when migrating away from Divi.
 */

if ( ! defined( "ABSPATH" ) ) exit;

class ET_Builder_Module_TagPortfolio extends ET_Builder_Module {

    public $slug       = "et_pb_tag_portfolio";
    public $vb_support = "partial";

    function init() {
        $this->name = esc_html__( "Tag Portfolio", "flexible-portfolio" );
        $this->plural = esc_html__( "Tag Portfolios", "flexible-portfolio" );
        $this->icon = "P";

        $this->settings_modal_toggles = array(
            "general" => array(
                "toggles" => array(
                    "main_content" => esc_html__( "Content", "flexible-portfolio" ),
                    "elements"     => esc_html__( "Elements", "flexible-portfolio" ),
                ),
            ),
        );
    }

    function get_fields() {
        return array(
            "post_type" => array(
                "label"       => esc_html__( "Inhaltstyp", "flexible-portfolio" ),
                "type"        => "select",
                "options"     => array(
                    "post"      => esc_html__( "Beiträge", "flexible-portfolio" ),
                    "page"      => esc_html__( "Seiten", "flexible-portfolio" ),
                    "post,page" => esc_html__( "Beiträge & Seiten", "flexible-portfolio" ),
                ),
                "default"     => "post",
                "toggle_slug" => "main_content",
            ),
            "filter_by" => array(
                "label"       => esc_html__( "Filtern nach", "flexible-portfolio" ),
                "type"        => "select",
                "options"     => array(
                    "category" => esc_html__( "Kategorie", "flexible-portfolio" ),
                    "post_tag" => esc_html__( "Schlagwort", "flexible-portfolio" ),
                    "both"     => esc_html__( "Kategorie & Schlagwort", "flexible-portfolio" ),
                ),
                "default"     => "category",
                "toggle_slug" => "main_content",
            ),
            "include_categories" => array(
                "label"            => esc_html__( "Kategorien einschließen", "flexible-portfolio" ),
                "type"             => "categories",
                "taxonomy_name"    => "category",
                "renderer_options" => array(
                    "use_terms" => false,
                ),
                "toggle_slug"      => "main_content",
            ),
            "include_tags" => array(
                "label"            => esc_html__( "Schlagwörter einschließen", "flexible-portfolio" ),
                "type"             => "categories",
                "taxonomy_name"    => "post_tag",
                "renderer_options" => array(
                    "use_terms" => false,
                ),
                "toggle_slug"      => "main_content",
            ),
            "include_posts" => array(
                "label"       => esc_html__( "Bestimmte Beiträge/Seiten (IDs)", "flexible-portfolio" ),
                "type"        => "text",
                "description" => esc_html__( "Kommagetrennte Post/Seiten IDs. Leer lassen für automatische Filterung.", "flexible-portfolio" ),
                "toggle_slug" => "main_content",
            ),
            "posts_number" => array(
                "label"       => esc_html__( "Anzahl Beiträge", "flexible-portfolio" ),
                "type"        => "text",
                "default"     => "12",
                "toggle_slug" => "main_content",
            ),
            "order" => array(
                "label"       => esc_html__( "Sortierung", "flexible-portfolio" ),
                "type"        => "select",
                "options"     => array(
                    "DESC" => esc_html__( "Neueste zuerst", "flexible-portfolio" ),
                    "ASC"  => esc_html__( "Älteste zuerst", "flexible-portfolio" ),
                ),
                "default"     => "DESC",
                "toggle_slug" => "elements",
            ),
            "show_filter" => array(
                "label"       => esc_html__( "Filtertabs anzeigen", "flexible-portfolio" ),
                "type"        => "yes_no_button",
                "options"     => array(
                    "on"  => esc_html__( "Ja", "flexible-portfolio" ),
                    "off" => esc_html__( "Nein", "flexible-portfolio" ),
                ),
                "default"     => "on",
                "toggle_slug" => "elements",
            ),
            "show_title" => array(
                "label"       => esc_html__( "Titel anzeigen", "flexible-portfolio" ),
                "type"        => "yes_no_button",
                "options"     => array(
                    "on"  => esc_html__( "Ja", "flexible-portfolio" ),
                    "off" => esc_html__( "Nein", "flexible-portfolio" ),
                ),
                "default"     => "on",
                "toggle_slug" => "elements",
            ),
            "show_categories" => array(
                "label"       => esc_html__( "Kategorien/Tags anzeigen", "flexible-portfolio" ),
                "type"        => "yes_no_button",
                "options"     => array(
                    "on"  => esc_html__( "Ja", "flexible-portfolio" ),
                    "off" => esc_html__( "Nein", "flexible-portfolio" ),
                ),
                "default"     => "on",
                "toggle_slug" => "elements",
            ),
            "fullwidth" => array(
                "label"       => esc_html__( "Layout", "flexible-portfolio" ),
                "type"        => "select",
                "options"     => array(
                    "off" => esc_html__( "Raster", "flexible-portfolio" ),
                    "on"  => esc_html__( "Volle Breite", "flexible-portfolio" ),
                ),
                "default"     => "off",
                "toggle_slug" => "main_content",
            ),
            "columns" => array(
                "label"       => esc_html__( "Spalten", "flexible-portfolio" ),
                "type"        => "range",
                "default"     => "4",
                "range_settings" => array(
                    "min"  => "1",
                    "max"  => "6",
                    "step" => "1",
                ),
                "toggle_slug" => "main_content",
                "show_if"     => array(
                    "fullwidth" => "off",
                ),
            ),
        );
    }

    public function render( $attrs, $content, $render_slug ) {
        $atts = array();
        $keys = array(
            "post_type", "filter_by", "include_categories", "include_tags",
            "include_posts", "posts_number", "show_filter", "show_title",
            "show_categories", "fullwidth", "columns", "order",
        );

        foreach ( $keys as $key ) {
            if ( isset( $this->props[ $key ] ) && $this->props[ $key ] !== "" ) {
                $atts[ $key ] = $this->props[ $key ];
            }
        }

        return tag_portfolio_render( $atts );
    }
}

new ET_Builder_Module_TagPortfolio();
