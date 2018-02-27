# Jetpack Portfolio Extensions
    Tags: jetpack, portfolio, isotope, masonry, filterable, jetpack-project-types, jetpack-project-tags,
    Requires at least: 4.9
    Tested up to: 4.9.4
    Requires PHP: 7.0
    License: GPLv3
    License URI: https://www.gnu.org/licenses/gpl-3.0.html

## Description

Enhances Jetpack Portfolio with [Isotope](https://isotope.metafizzy.co) layout, live filtering,  and two shortcodes: [the_project_tags] and [list_project_types].

When installed, this plugin will override the default layout for jetpack portfolio shortcodes. This means the `columns` option of the shortcode will be ignored. Projects will be displayed in responsive columns with the following breakpoints:

    min-width:40em  /* 2 columns */
    min-width:60em  /* 3 columns */
    min-width:90em  /* 4 columns */
    min-width:120em /* 5 columns */

To enable a tiled, masonry-style layout, tick the Use Isotope box in the options. For live filtering, place a `[list_project_types]` shortcode just above your `[jetpack_portfolio]` shortcode.

The plugin also adds classes for Jetpack Project Tags to the `[jetpack_portfolio]` project entry markup. 

### Options

Find in the Customizer -> Portfolio Options.

- **Show Excerpt on Single Project Pages**: On single project pages, show the custom excerpt between the project title and content.
- **Use Isotope**: Use jQuery [Isotope](https://isotope.metafizzy.co) for tiled portfolio layout and filtering. (Filterable when used with the [list_project_types] shortcode.)

### Shortcodes

 - `[list_project_types]` Prints a list of all Jetpack 'Project Types'. CSS `.project-type-list`.
 - `[the_project_tags]` Prints a list of Jetpack 'Project Tags' associated with a single portfolio item. CSS `.project-tag-list`.
