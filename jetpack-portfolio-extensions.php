<?php
/*
Plugin Name: Jetpack Portfolio Extensions
Plugin URI:
Description: Enhances the Jetpack Portfolio custom post type with support for excerpts, markdown, revisions, and more.
Version: 0.3
Author: Frankie Winters
Author Email: support@frankie.winters.fyi
License:

  Copyright 2011  (support@danieleckhart.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

class JetpackPortfolioExtensions {

	/*--------------------------------------------*
	 * Constants
	 *--------------------------------------------*/
	const name = 'Jetpack Portfolio Extensions';
	const slug = 'few_jetpack_portfolio_extensions';

	/**
	 * Constructor
	 */
	function __construct() {
		//Hook up to the init action
		add_action( 'init', array( &$this, 'init_few_jetpack_portfolio_extensions' ) );
	}

	/**
	 * Runs when the plugin is activated
	 */
	function install_few_jetpack_portfolio_extensions() {
		// do not generate any output here
	}

	/**
	 * Runs when the plugin is initialized
	 */
	function init_few_jetpack_portfolio_extensions() {
		// Load JavaScript and stylesheets
		$this->register_scripts_and_styles();


		if ( is_admin() ) {
			//this will run when in the WordPress admin
		} else {
			//this will run when on the frontend
		}

		// add_action( 'init', 'few_upgrade_jetpack_portfolio' );
		$this->few_upgrade_jetpack_portfolio();
	}

	  /* Add Excepts and Markdown to Jetpack Portfolio Items */
	function few_upgrade_jetpack_portfolio() {
		add_post_type_support( 'jetpack-portfolio', array(
			'excerpt',
			'revisions',
			'wpcom-markdown'
		));
	}

	/**
	 * Registers and enqueues stylesheets for the administration panel and the
	 * public facing site.
	 */
	private function register_scripts_and_styles() {
		if ( is_admin() ) {

		} else {

		} // end if/else
	} // end register_scripts_and_styles

	/**
	 * Helper function for registering and enqueueing scripts and styles.
	 *
	 * @name	The 	ID to register with WordPress
	 * @file_path		The path to the actual file
	 * @is_script		Optional argument for if the incoming file_path is a JavaScript source file.
	 */
	private function load_file( $name, $file_path, $is_script = false ) {

		$url = plugins_url($file_path, __FILE__);
		$file = plugin_dir_path(__FILE__) . $file_path;

		if( file_exists( $file ) ) {
			if( $is_script ) {
				wp_register_script( $name, $url, array('jquery') ); //depends on jquery
				wp_enqueue_script( $name );
			} else {
				wp_register_style( $name, $url );
				wp_enqueue_style( $name );
			} // end if
		} // end if

	} // end load_file

} // end class
new JetpackPortfolioExtensions();

?>
