<?php
/**
* Child theme stylesheet einbinden in Abhaengigkeit vom Original-Stylesheet
*/

function child_theme_styles() {
wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
wp_enqueue_style( 'child-theme-css', get_stylesheet_directory_uri() .'/style.css' , array('parent-style'));

}
add_action( 'wp_enqueue_scripts', 'child_theme_styles' );

/* Enqueue Javascript in /child-theme-s/includes/js/ */
function child_theme_s_scripts(){
 wp_enqueue_script('child-theme-s-js', get_stylesheet_directory_uri() . '/custom.js');
}
 
add_action( 'wp_enqueue_scripts', 'child_theme_s_scripts' );

/**
 * SMTP via phpmailer
 * This function will connect wp_mail to our SMTP server.
 * 4/20/22 M
 */
add_action( 'phpmailer_init', 'send_smtp_email' );
if ( !function_exists('send_smtp_email') ) :
	function send_smtp_email( $phpmailer ) {
		$phpmailer->isSMTP();
		$phpmailer->Host       = SMTP_HOST;
		$phpmailer->SMTPAuth   = SMTP_AUTH;
		$phpmailer->Port       = SMTP_PORT;
		$phpmailer->Username   = SMTP_USER;
		$phpmailer->Password   = SMTP_PASS;
		$phpmailer->SMTPSecure = SMTP_SECURE;
		$phpmailer->From       = SMTP_FROM;
		$phpmailer->FromName   = SMTP_NAME;
	}
endif;

/**
 * Tag Portfolio - shortcode + Divi module
 */
require_once get_stylesheet_directory() . '/includes/tag-portfolio-shortcode.php';

function tag_portfolio_load_divi_module() {
    if ( class_exists( 'ET_Builder_Module' ) ) {
        require_once get_stylesheet_directory() . '/includes/TagPortfolioModule.php';
    }
}
add_action( 'et_builder_ready', 'tag_portfolio_load_divi_module' );

?>
