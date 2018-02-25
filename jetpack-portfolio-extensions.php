<?php
/*
Plugin Name: Jetpack Portfolio Extensions
Plugin URI:
Description: Enhances the Jetpack Portfolio custom post type with support for two shortcodes: `list_project_tags` and `list_all_project_types`.
Version: 0.4
Author: Frankie Winters
Author Email: support@winters.design
*/

/**
 * Register styles on 'init'
 */
function wd_jpe_register_script() {
    wp_register_style( 'jetpack-portfolio-extensions', plugins_url('/jetpack-portfolio-extensions.css', __FILE__), false, '3.0', 'all');
    wp_register_script( 'isotope', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.isotope/3.0.5/isotope.pkgd.min.js', array( 'jquery' ), null, true );
    wp_register_script( 'jetpack-portfolio-extensions-isotope', plugin_dir_url( __FILE__ ) . 'jetpack-portfolio-extensions-isotope.js', array( 'isotope' ), 1.0, true );
}
add_action('init', 'wd_jpe_register_script');

/**
 * Include js to active isotope if theme option is enabled
 */
function wd_jpe_enqueue_scripts() {
  if ( get_theme_mod( 'wd_jpe_use_isotope' ) == 1 ) { // true
    wp_enqueue_script('jetpack-portfolio-extensions-isotope');
  }
}
add_action('wp_enqueue_scripts', 'wd_jpe_enqueue_scripts');

/**
 * Add options to the Customizer
 *
 */
function wd_jpe_customize_register($wp_customize){
	$wp_customize->add_section( 'wd_jpe_options' , array(
    'title'      => __('Portfolio Options',''),
    'priority'   => 200,
  ) );
  // Show Excerpt?
	$wp_customize->add_setting( 'wd_jpe_show_excerpt', array(
	  'sanitize_callback' => 'wd_jpe_slug_sanitize_checkbox',
	) );
	$wp_customize->add_control( 'wd_jpe_show_excerpt', array(
	  'type' => 'checkbox',
	  'section' => 'wd_jpe_options',
	  'label' => __( 'Show Excerpts' ),
	  'description' => __( 'On single project pages, show the custom excerpt between the project title and content.' ),
	) );
  // Use Isotope?
  $wp_customize->add_setting( 'wd_jpe_use_isotope', array(
	  'sanitize_callback' => 'wd_jpe_slug_sanitize_checkbox',
	) );
	$wp_customize->add_control( 'wd_jpe_use_isotope', array(
	  'type' => 'checkbox',
	  'section' => 'wd_jpe_options',
	  'label' => __( 'Isotope Portfolio Shortcode' ),
	  'description' => __( 'Use jQuery Isotope to upgrade the [jetpack_portfolio] shortcode with responsive masonry-style layout (filterable when used with the [list_all_project_types] shortcode.)' ),
	) );

}
function wd_jpe_slug_sanitize_checkbox( $checked ) {
	// enforce boolean value
  return ( ( isset( $checked ) && true == $checked ) ? true : false );
}
add_action('customize_register', 'wd_jpe_customize_register');

/**
 * On single project pages, show the custom excerpt between the project title and content
 */
function wd_jpe_show_excerpt( $content ) {
	if ( get_theme_mod( 'wd_jpe_show_excerpt' ) == 0 ) { //
    return $content;
	}
	if ( ('jetpack-portfolio' == get_post_type($post_id) ) && ( has_excerpt($post_id) )) {
		wp_enqueue_style( 'jetpack-portfolio-extensions' );
		$excerpt = get_the_excerpt($post_id);
		$excerpt = "<p class='project-excerpt'>$excerpt<p>";
		$content = $excerpt . $content;
	}
	return $content;
}
add_filter( 'the_content', 'wd_jpe_show_excerpt' );

/**
 * Print a list of Jetpack Project Tags associated with a single portfolio item.
 * Add a shortcode `list_project_tags` to display them
 */
function wd_jpe_shortcode_list_project_tags() {
  wp_enqueue_style( 'jetpack-portfolio-extensions' );
	echo get_the_term_list($post->ID, 'jetpack-portfolio-tag', '<ul class="portfolio-tag-list"><li>', '</li><li>', '</li></ul>' );
}
add_shortcode( 'list_project_tags', 'wd_jpe_list_project_tags' );

/**
 * Print a list of Jetpack Project Tags
 * Add a shortcode `list_all_project_types` to display them
 */
function wd_jpe_shortcode_list_all_project_types() {
	wp_enqueue_style( 'jetpack-portfolio-extensions' );
	$terms = get_terms( array(
		'taxonomy' => 'jetpack-portfolio-type',
		'hide_empty' => true
	) );
	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
	    $term_list = '<ul class="project-type-list">';
      if ( get_theme_mod( 'wd_jpe_use_isotope' ) == 1 ) { // Use filterable isotope layout
        $term_list .= '<li class="project-type project-type-filter-all active" data-filter="*"><a href="#">All</a></li>';
    	}
	    foreach ( $terms as $term ) {
  			$term_list_item_class = $term->slug;
				// Add CSS hook to mark current type as active
		    if (is_tax( 'jetpack-portfolio-type', $term->slug ) ) {
		    	$term_list_item_class .= "current-type";
		    }
        $term_list .= '<li class="project-type ' . $term_list_item_class . '" data-filter=".type-' . $term->slug . '"><a href="' . esc_url( get_term_link( $term ) ) . '" alt="' . esc_attr( sprintf( __( 'View all post filed under %s', 'twentysixteen' ), $term->name ) ) . '">' . $term->name . '</a></li>';
	    }
	    $term_list .= '</ul>';
	    return $term_list;
	}
}
add_shortcode( 'list_all_project_types', 'wd_jpe_shortcode_list_all_project_types' );

?>
