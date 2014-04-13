<?php
/**
 * The template for displaying search results
 *
 * @package     WordPress
 * @subpackage  Hoverboard
 * @since       0.1.0
 */

get_header();
get_template_part('common/main-column', 'top');

if (have_posts()) {
    $template = 'Showing search results for &ldquo;%s&rdquo;.';
} else {
    $template = 'No search results for &ldquo;%s&rdquo;. Please try again.';
}

?>
        <p class="well lead">
            <?php printf($template, get_search_query()); ?> 
        </p>
<?php

have_posts() ? get_template_part('loop', 'search') : NULL;

get_template_part('common/main-column', 'bottom');
get_footer();
