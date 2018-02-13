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
	wintersdesign_upgrade_jetpack_portfolio();
}

/**
 * Register style on initialization
 */
function wintersdesign_portfolio_register_script() {
    wp_register_style( 'jetpack-portfolio-extension', plugins_url('/jetpack-portfolio-extensions.css', __FILE__), false, '2.0', 'all');
}
add_action('init', 'wintersdesign_portfolio_register_script');

// function winterdesign_portfolio_enqueue_style(){
// 	wp_enqueue_style('jetpack-portfolio-extension');
// }
// add_action('wp_enqueue_scripts', 'winterdesign_portfolio_enqueue_style', 40);


/* Add Excepts and Markdown to Jetpack Portfolio Items */
function wintersdesign_upgrade_jetpack_portfolio() {
	add_post_type_support( 'jetpack-portfolio', array(
		'excerpt'
	));
}

/**
 * Prints a list of Jetpack Project Tags associated with a single portfolio item.
 * Adds a shortcode `list_project_tags` to display them
 */
function wintersdesign_list_project_tags() {
   wp_enqueue_style( 'jetpack-portfolio-extension' );
	echo get_the_term_list($post->ID, 'jetpack-portfolio-tag', '<ul class="portfolio-tag-list"><li>', '</li><li>', '</li></ul>' );
}
add_shortcode( 'list_project_tags', 'wintersdesign_list_project_tags' );

/**
 * Prints a list of Jetpack Project Tags
 * Adds a shortcode `list_all_project_types` to display them
 */
function wintersdesign_list_all_project_types() {
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
add_shortcode( 'list_all_project_types', 'wintersdesign_list_all_project_types' );

?>
