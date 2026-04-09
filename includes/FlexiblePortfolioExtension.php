<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class FlexiblePortfolioExtension extends DiviExtension {

    public $gettext_domain = 'flexible-portfolio';
    public $name           = 'flexible-portfolio';
    public $version        = FLEX_PORTFOLIO_VERSION;

    public function __construct( $name = 'flexible-portfolio', $args = array() ) {
        $this->plugin_dir     = plugin_dir_path( __FILE__ );
        $this->plugin_dir_url = plugin_dir_url( __FILE__ );

        parent::__construct( $name, $args );
    }
}

new FlexiblePortfolioExtension;
