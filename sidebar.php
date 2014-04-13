<?php
/**
 * The Sidebar containing the primary widget area.
 *
 * @package     WordPress
 * @subpackage  Hoverboard
 * @since       0.1.0
 */

get_template_part('common/sidebar', 'top');

?>
    <ul class="list-unstyled">
<?php if (!dynamic_sidebar('main-sidebar')): ?>
        <li>
            <h3>Archives</h3>
            <ul>
                <?php wp_get_archives('type=daily'); ?> 
            </ul>
        </li>
<?php endif; ?>
    </ul>
<?php

get_template_part('common/sidebar', 'bottom');
