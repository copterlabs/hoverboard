<?php
/**
 * The template for displaying Archive pages
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package     WordPress
 * @subpackage  Hoverboard
 * @since       0.1.0
 */

get_header();
get_template_part('common/main-column', 'top');

/* Grab the first post to determine what type of 
 * post is being displayed.
 */
if (have_posts()) {
    the_post();
}

if (is_post_type_archive()) {
    $archive_title = post_type_archive_title(NULL, FALSE);
} else {
    if (is_day()) {
        $archive_title = sprintf('Daily Archives: %s',   get_the_date());
    } elseif (is_month()) {
        $archive_title = sprintf('Monthly Archives: %s', get_the_date('F Y'));
    } elseif (is_year()) {
        $archive_title = sprintf('Yearly Archives: %s',  get_the_date('Y'));
    } else {
        $archive_title = 'Blog Archives';
    }
}

// Rewinds the loop
rewind_posts();

?>
        <h1 class="page-title"><?php echo $archive_title; ?></h1>
<?php

get_template_part( 'loop', 'archive' );

get_template_part('common/main-column', 'bottom');
get_footer();
