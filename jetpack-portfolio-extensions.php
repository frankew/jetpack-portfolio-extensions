<?php
/*	Plugin Name: Jetpack Portfolio Extensions
	Plugin URI: https://bitbucket.org/wintersdesign/jetpack-portfolio-extensions/
	Description: Enhances Jetpack Portfolio with Isotope layout and live filtering, and two shortcodes: [the_project_tags] and [list_project_types].
	Version: 0.4
	Author: Frankie Winters
	Author URI: https://frankie.winters.design
	Author Email: frankie@winters.design
*/

/**
 * Runs when the plugin is initialized
 */
function wd_jetpack_portfolio_extensions_init() {

}
add_action('init', 'wd_jetpack_portfolio_extensions_init');

/**
 * Register styles on 'init'
 */
function wd_jpe_register_script() {
    wp_register_style( 'jetpack-portfolio-extensions', plugins_url('/jetpack-portfolio-extensions.css', __FILE__), false, '3.0', 'all');
    wp_register_script( 'isotope', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.isotope/3.0.5/isotope.pkgd.min.js', array( 'jquery' ), null, true );
    wp_register_script( 'jetpack-portfolio-extensions-isotope', plugin_dir_url( __FILE__ ) . 'jetpack-portfolio-extensions-isotope.js', array( 'isotope' ), 1.2, true );
}
add_action('init', 'wd_jpe_register_script');

/**
 * Block default Jetpack shortcode styles
 */
 function wd_jpe_remove_jetpack_styles($href) {
  //  wp_deregister_style( 'jetpack-portfolio-style' ); // Could not get these to work consistently. :(
  //  wp_dequeue_style( 'jetpack-portfolio-style' );
  if(strpos($href, "portfolio-shortcode.css") == false) {
    return $href;
  }
  return false;
}
add_filter( 'style_loader_src', 'wd_jpe_remove_jetpack_styles' );

/**
 * Include js to active isotope if theme option is enabled
 */
function wd_jpe_enqueue_scripts() {
  if ( get_theme_mod( 'wd_jpe_use_isotope' ) == 1 ) { // true
    wp_enqueue_script('jetpack-portfolio-extensions-isotope');
	}
	wp_enqueue_style( 'jetpack-portfolio-extensions' );
}
add_action('wp_enqueue_scripts', 'wd_jpe_enqueue_scripts');

/**
 * Add options to the Customizer
 * - wd_jpe_show_excerpt?
 * - wd_jpe_use_isotope?
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
	  'label' => __( 'Show Excerpt on Single Project Pages' ),
	  'description' => __( 'On single project pages, show the custom excerpt between the project title and content.' ),
	) );
	// Use Isotope?
	$wp_customize->add_setting( 'wd_jpe_use_isotope', array(
		'sanitize_callback' => 'wd_jpe_slug_sanitize_checkbox',
	) );
	$wp_customize->add_control( 'wd_jpe_use_isotope', array(
	  'type' => 'checkbox',
	  'section' => 'wd_jpe_options',
	  'label' => __( 'Use Isotope' ),
	  'description' => __( 'Use jQuery <a href="https://isotope.metafizzy.co">Isotope</a> for tiled portfolio layout and filtering. (Filterable when used with the [list_project_types] shortcode.)' ),
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
	if ( get_theme_mod( 'wd_jpe_show_excerpt' ) == 0 ) { //
	    return $content;
	}
	global $post;
	$post_id = get_the_ID($post);
	if ( ('jetpack-portfolio' == get_post_type($post_id) ) && ( has_excerpt($post_id) )) {
		$excerpt = get_the_excerpt($post_id);
		$excerpt = "<p class='project-excerpt'>$excerpt<p>";
		$content = $excerpt . $content;
	}
	return $content;
}
add_filter( 'the_content', 'wd_jpe_show_excerpt' );

/**
 * Print a list of Jetpack Project Tags associated with a single portfolio item.
 * Add a shortcode `the_project_tags` to display them
 */
function wd_jpe_shortcode_list_project_tags() {
	return get_the_term_list($post->ID, 'jetpack-portfolio-tag', '<ul class="portfolio-tag-list"><li>', '</li><li>', '</li></ul>' );
}
add_shortcode( 'the_project_tags', 'wd_jpe_shortcode_list_project_tags' );

/**
 * Print a list of Jetpack Project Types
 * Add a shortcode `list_project_types` to display them
 */
function wd_jpe_shortcode_list_all_project_types() {
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
	        $term_list .= '<li class="project-type ' . $term_list_item_class . '" data-filter=".type-' . $term->slug . '"><a href="' . esc_url( get_term_link( $term ) ) . '" title="' . esc_attr( sprintf( __( 'View all post filed under %s', 'twentysixteen' ), $term->name ) ) . '">' . $term->name . '</a></li>';
	    }
	    $term_list .= '</ul>';
	    return $term_list;
	}
}
add_shortcode( 'list_project_types', 'wd_jpe_shortcode_list_all_project_types' );

/**
 * Add Jetpack Tags to Jetpack Shortcode Entry Markup
 */
function wd_jpe_shortcode_entry_class($class) {
	global $post;
    $classes = explode( " ", $class);
	$classes = array_diff($classes, ['portfolio-entry-column-1', 'portfolio-entry-first-item-row']);
    $jetpack_tags = get_the_terms($post, 'jetpack-portfolio-tag');
	if (is_array($jetpack_tags)) {
		foreach ($jetpack_tags as $term) {
			$classes[] = 'tag-'.$term->slug;
		}
	}
	$classes[] = $post->post_name;
    return implode( " ", $classes);
}
add_filter('portfolio-project-post-class', 'wd_jpe_shortcode_entry_class');

?>
