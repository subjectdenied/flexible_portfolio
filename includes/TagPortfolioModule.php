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
        $this->name = esc_html__( "Tag Portfolio", "flavor" );
        $this->plural = esc_html__( "Tag Portfolios", "flavor" );
        $this->icon = "P";

        $this->settings_modal_toggles = array(
            "general" => array(
                "toggles" => array(
                    "main_content" => esc_html__( "Content", "flavor" ),
                    "elements"     => esc_html__( "Elements", "flavor" ),
                    "ordering"     => esc_html__( "Reihenfolge", "flavor" ),
                ),
            ),
        );
    }

    function get_fields() {
        return array(
            "post_type" => array(
                "label"       => esc_html__( "Inhaltstyp", "flavor" ),
                "type"        => "select",
                "options"     => array(
                    "post"      => esc_html__( "Beiträge", "flavor" ),
                    "page"      => esc_html__( "Seiten", "flavor" ),
                    "post,page" => esc_html__( "Beiträge & Seiten", "flavor" ),
                ),
                "default"     => "post",
                "toggle_slug" => "main_content",
            ),
            "filter_by" => array(
                "label"       => esc_html__( "Filtern nach", "flavor" ),
                "type"        => "select",
                "options"     => array(
                    "category" => esc_html__( "Kategorie", "flavor" ),
                    "post_tag" => esc_html__( "Schlagwort", "flavor" ),
                    "both"     => esc_html__( "Kategorie & Schlagwort", "flavor" ),
                ),
                "default"     => "category",
                "toggle_slug" => "main_content",
            ),
            "include_categories" => array(
                "label"            => esc_html__( "Kategorien einschließen", "flavor" ),
                "type"             => "categories",
                "taxonomy_name"    => "category",
                "renderer_options" => array(
                    "use_terms" => false,
                ),
                "toggle_slug"      => "main_content",
            ),
            "include_tags" => array(
                "label"            => esc_html__( "Schlagwörter einschließen", "flavor" ),
                "type"             => "categories",
                "taxonomy_name"    => "post_tag",
                "renderer_options" => array(
                    "use_terms" => true,
                ),
                "toggle_slug"      => "main_content",
            ),
            "include_posts" => array(
                "label"       => esc_html__( "Bestimmte Beiträge/Seiten (IDs)", "flavor" ),
                "type"        => "text",
                "description" => esc_html__( "Kommagetrennte Post/Seiten IDs. Leer lassen für automatische Filterung.", "flavor" ),
                "toggle_slug" => "main_content",
            ),
            "posts_number" => array(
                "label"       => esc_html__( "Anzahl Beiträge", "flavor" ),
                "type"        => "text",
                "default"     => "12",
                "toggle_slug" => "main_content",
            ),
            "orderby" => array(
                "label"       => esc_html__( "Sortierung", "flavor" ),
                "type"        => "select",
                "options"     => array(
                    "date"       => esc_html__( "Datum", "flavor" ),
                    "title"      => esc_html__( "Titel", "flavor" ),
                    "menu_order" => esc_html__( "Manuelle Reihenfolge", "flavor" ),
                    "rand"       => esc_html__( "Zufällig", "flavor" ),
                ),
                "default"     => "date",
                "toggle_slug" => "ordering",
            ),
            "order" => array(
                "label"       => esc_html__( "Sortierrichtung", "flavor" ),
                "type"        => "select",
                "options"     => array(
                    "DESC" => esc_html__( "Absteigend", "flavor" ),
                    "ASC"  => esc_html__( "Aufsteigend", "flavor" ),
                ),
                "default"     => "DESC",
                "toggle_slug" => "ordering",
            ),
            "show_filter" => array(
                "label"       => esc_html__( "Filtertabs anzeigen", "flavor" ),
                "type"        => "yes_no_button",
                "options"     => array(
                    "on"  => esc_html__( "Ja", "flavor" ),
                    "off" => esc_html__( "Nein", "flavor" ),
                ),
                "default"     => "on",
                "toggle_slug" => "elements",
            ),
            "show_title" => array(
                "label"       => esc_html__( "Titel anzeigen", "flavor" ),
                "type"        => "yes_no_button",
                "options"     => array(
                    "on"  => esc_html__( "Ja", "flavor" ),
                    "off" => esc_html__( "Nein", "flavor" ),
                ),
                "default"     => "on",
                "toggle_slug" => "elements",
            ),
            "show_categories" => array(
                "label"       => esc_html__( "Kategorien/Tags anzeigen", "flavor" ),
                "type"        => "yes_no_button",
                "options"     => array(
                    "on"  => esc_html__( "Ja", "flavor" ),
                    "off" => esc_html__( "Nein", "flavor" ),
                ),
                "default"     => "on",
                "toggle_slug" => "elements",
            ),
            "fullwidth" => array(
                "label"       => esc_html__( "Layout", "flavor" ),
                "type"        => "select",
                "options"     => array(
                    "off" => esc_html__( "Raster", "flavor" ),
                    "on"  => esc_html__( "Volle Breite", "flavor" ),
                ),
                "default"     => "off",
                "toggle_slug" => "main_content",
            ),
            "columns" => array(
                "label"       => esc_html__( "Spalten", "flavor" ),
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
        $params = array();
        $keys = array(
            "post_type", "filter_by", "include_categories", "include_tags",
            "include_posts", "posts_number", "show_filter", "show_title",
            "show_categories", "fullwidth", "columns", "orderby", "order",
        );

        foreach ( $keys as $key ) {
            $val = isset( $this->props[ $key ] ) ? $this->props[ $key ] : "";
            if ( $val !== "" ) {
                $params[] = sprintf( "%s=\"%s\"", $key, esc_attr( $val ) );
            }
        }

        return do_shortcode( "[tag_portfolio " . implode( " ", $params ) . "]" );
    }
}

if ( function_exists( "et_builder_should_load_all_module_data" ) && et_builder_should_load_all_module_data() ) {
    new ET_Builder_Module_TagPortfolio();
}
