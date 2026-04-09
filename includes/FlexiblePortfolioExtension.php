<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class FlexiblePortfolioExtension extends DiviExtension {

    public $gettext_domain = 'flexible-portfolio';
    public $name           = 'flexible-portfolio';
    public $version        = FLEX_PORTFOLIO_VERSION;

    public function __construct( $name = 'flexible-portfolio', $args = array() ) {
        $this->plugin_dir     = FLEX_PORTFOLIO_DIR;
        $this->plugin_dir_url = FLEX_PORTFOLIO_URL;

        parent::__construct( $name, $args );
    }
}

new FlexiblePortfolioExtension;
