<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 13-11-2018
 * Time: 5:10 PM
 * Since: 1.0.0
 * Updated: 1.0.0
 */
global $post;
$old_post = $post;

$args = [
    'post_type' => $service,
    'posts_per_page' => $posts_per_page,
    'order' => 'ASC',
    'orderby' => 'name',
];
if ($ids) {
    $args['post__in'] = explode(',', $ids);
    $args['orderby'] = 'post__in';
}

?>
<div class="st-related-service-new">
    <?php if (!empty($title)) { ?>
        <div class="e-title-wrapper">
            <h3 class="e-title"><?php echo esc_attr($title); ?></h3>
        </div>
    <?php } ?>
    <?php

    switch ($service) {
        case 'st_hotel':
            if (!st_check_service_available('st_hotel')) {
                break;
            }
            global $wp_query , $st_search_query;
            $current_lang = TravelHelper::current_lang();
            $main_lang = TravelHelper::primary_lang();
            if (TravelHelper::is_wpml()) {
                global $sitepress;
                $sitepress->switch_lang($main_lang, true);
            }
            $query = new WP_Query($args);
            while ($query->have_posts()):
                $query->the_post();
                echo st()->load_template('layouts/modern/hotel/loop/related');
            endwhile;
            wp_reset_postdata();
            $post = $old_post;
            break;
        case 'st_tours':
            if (!st_check_service_available('st_tours')) {
                break;
            }

            $query = new WP_Query($args);
            if ($query->have_posts()) {
                while ($query->have_posts()):
                    $query->the_post();
                    echo st()->load_template('layouts/modern/tour/elements/loop/related');
                endwhile;
            }
            wp_reset_postdata();
            $post = $old_post;
            break;
        case 'st_activity':
            if (!st_check_service_available('st_activity')) {
                break;
            }

            $query = new WP_Query($args);
            if ($query->have_posts()) {
                while ($query->have_posts()):
                    $query->the_post();
                    echo st()->load_template('layouts/modern/activity/elements/loop/related');
                endwhile;
            }
            wp_reset_postdata();
            $post = $old_post;
            break;
        case 'st_cars':
            if (!st_check_service_available('st_cars')) {
                break;
            }
			$args['meta_query'] = [
				'relation' => 'AND',
				[
					'key'     => 'car_type',
					'value'   => 'normal',
					'type'    => 'CHAR',
					'compare' => '=',
				],
			];
            $query = new WP_Query($args);
            if ($query->have_posts()) {
                while ($query->have_posts()):
                    $query->the_post();
                    echo st()->load_template('layouts/modern/car/elements/loop/related');
                endwhile;
            }
            wp_reset_postdata();
            $post = $old_post;
            break;
        case 'st_rental':
            if (!st_check_service_available('st_rental')) {
                break;
            }

            $query = new WP_Query($args);
            if ($query->have_posts()) {
                while ($query->have_posts()):
                    $query->the_post();
                    echo st()->load_template('layouts/modern/rental/elements/loop/related');
                endwhile;
            }
            $post = $old_post;
            break;
    }
    ?>
</div>
