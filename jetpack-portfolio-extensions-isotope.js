jQuery(document).ready(function() {
    jQuery(window).load(function() {
        // Activate Isotope for jetpack portfolio shortcode markup.
        var $container = jQuery('.jetpack-portfolio-shortcode');
        $container.isotope({
            itemSelector: '.portfolio-entry',
            stagger: 30,
            masonry: {
                columnWidth: '.portfolio-entry'
            }
        });


        // When the a link in the [list_all_project_types] shortcode is
        // clicked, filter the adjoining [jetpack_portfolio].
        jQuery('.project-type-list li').click(function() {
            // Find the next jetpack portfolio container in the document.
            // Note! There's an assumption about markup here for the sake of flexiblity.
            var $filterableContainer = jQuery(this).parent().next('.jetpack-portfolio-shortcode');
            var selector = jQuery(this).attr('data-filter');
            $filterableContainer.isotope({
                filter: selector,
            });
            // Manage .active class
            jQuery('.project-type-list li').removeClass('active');
            jQuery(this).addClass('active');
            return false;
        });
        // Set featured type initially active.
        jQuery('.project-type-list li[data-filter=".type-featured"]').click();
    });
});
