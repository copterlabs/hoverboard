<?php
/**
 * The template for displaying tag archives
 *
 * @package     WordPress
 * @subpackage  Hoverboard
 * @since       0.1.0
 */

get_header();
get_template_part('common/main-column', 'top');

$tag_format = 'Posts Tagged with "%s"';
$tag_title  = sprintf($tag_format, single_tag_title('', FALSE));

?>
    <h1><?php echo $tag_title; ?></h1>
<?php

$tag_description = category_description();
if (!empty($tag_description)) {
    echo $tag_description;
}

get_template_part('loop', 'tag');

get_template_part('common/main-column', 'bottom');
get_footer();
