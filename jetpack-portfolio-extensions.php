<?php
/*
Plugin Name: Jetpack Portfolio Extensions
Plugin URI:
Description: Enhances the Jetpack Portfolio custom post type with support for excerpts, revisions, and two shortcodes: `list_project_tags` and `list_all_project_types`.
Version: 0.4
Author: Frankie Winters
Author Email: support@winters.design
*/

/**
 * Runs when the plugin is initialized
 */
add_action('init', 'init_wintersdesign_jetpack_portfolio_extensions');
function init_wintersdesign_jetpack_portfolio_extensions() {
	/**
	 * Add Excerpts to Jetpack Portfolio Items
	 */
		add_post_type_support( 'jetpack-portfolio', array(
			'excerpt'
		));}

/**
 * Register style on initialization
 */
function wd_jpe_register_script() {
    wp_register_style( 'jetpack-portfolio-extension', plugins_url('/jetpack-portfolio-extensions.css', __FILE__), false, '2.0', 'all');
}
add_action('init', 'wd_jpe_register_script');

/**
 * Add options to the Customizer
 *
 */
function wd_jpe_customize_register($wp_customize){
	$wp_customize->add_setting( 'wd_jpe_excerpt', array(
	  'capability' => 'edit_wd_jpe__options',
	  'sanitize_callback' => 'wd_jpe_slug_sanitize_checkbox',
	) );

	$wp_customize->add_control( 'wd_jpe_excerpt', array(
	  'type' => 'checkbox',
	  'section' => 'theme_options', // Add a default or your own section
	  'label' => __( 'Portfolio Excerpts' ),
	  'description' => __( 'On single project pages, show the custom excerpt between the project title and content.' ),
	) );
}
function wd_jpe_slug_sanitize_checkbox( $checked ) {
  return ( ( isset( $checked ) && true == $checked ) ? true : false );
}
add_action('customize_register', 'wd_jpe_customize_register');

/**
 * On single project pages, show the custom excerpt between the project title and content
 */
function wd_jpe_show_excerpt( $content ) {
	if ( get_theme_mod( 'wd_jpe_excerpt' ) == 0 ) { // true
    return $content;
	}
	if ( ('jetpack-portfolio' == get_post_type($post_id) ) && ( has_excerpt($post_id) )) {
		wp_enqueue_style( 'jetpack-portfolio-extension' );
		$excerpt = get_the_excerpt($post_id);
		$excerpt = "<p class='project-excerpt'>$excerpt<p>";
		$content = $excerpt . $content;
	}
	return $content;
}
add_filter( 'the_content', 'wd_jpe_show_excerpt' );

/**
 * Prints a list of Jetpack Project Tags associated with a single portfolio item.
 * Adds a shortcode `list_project_tags` to display them
 */
function wd_jpe_shortcode_list_project_tags() {
   wp_enqueue_style( 'jetpack-portfolio-extension' );
	echo get_the_term_list($post->ID, 'jetpack-portfolio-tag', '<ul class="portfolio-tag-list"><li>', '</li><li>', '</li></ul>' );
}
add_shortcode( 'list_project_tags', 'wd_jpe_list_project_tags' );

/**
 * Prints a list of Jetpack Project Tags
 * Adds a shortcode `list_all_project_types` to display them
 */
function wd_jpe_shortcode_list_all_project_types() {
	wp_enqueue_style( 'jetpack-portfolio-extension' );
	$terms = get_terms('jetpack-portfolio-type', array('hide_empty' => true));
	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
	    $term_list = '<ul class="project-type-list">';
	    foreach ( $terms as $term ) {
			$term_list_item_class = '';
		    if (is_tax( 'jetpack-portfolio-type', $term->slug ) ) {
		    	$term_list_item_class .= " current-type";
		    }
          $term_list .= '<li class="project-type' . $term_list_item_class . '"><a href="' . esc_url( get_term_link( $term ) ) . '" alt="' . esc_attr( sprintf( __( 'View all post filed under %s', 'twentysixteen' ), $term->name ) ) . '">' . $term->name . '</a></li>';
	    }
	    $term_list .= '</ul>';
	    return $term_list;
	}
}
add_shortcode( 'list_all_project_types', 'wd_jpe_shortcode_list_all_project_types' );

?>
