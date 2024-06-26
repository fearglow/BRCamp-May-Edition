<div class="st-calendar-wrapper">
    <?php
    /**
     * Created by PhpStorm.
     * User: Administrator
     * Date: 2/22/2019
     * Time: 8:13 AM
     */
    wp_dequeue_script('nicescroll.js');
    global $post, $wp_query;
    $oldpost = $post;
    $url = st()->url('plugins/ot-custom/fields/inventory');
    $lang = get_locale();
    wp_enqueue_script('moment.min', get_template_directory_uri() . '/js/moment.js', array('jquery'), NULL, TRUE);
    wp_enqueue_script('prettify', $url . '/js/prettify.js', array('moment.min'), NULL, TRUE);
    wp_enqueue_script('jquery.lang.gantt', $url . '/js/lang.js', array('jquery', 'prettify'), NULL, TRUE);
    wp_enqueue_script('gantt-js', $url . '/js/jquery.fn.gantt.js', array('moment.min'), NULL, TRUE);
    wp_enqueue_script('inventory-js-partner', get_template_directory_uri() . '/js/inventory.js', ['gantt-js'], null, true);
    wp_enqueue_style('gantt-css', $url . '/css/style.css');

    echo '<h2>' . __('Hotel Inventory', 'traveler') . '</h2>';

    $args = [
        'post_type' => 'st_hotel',
        'posts_per_page' => get_option('posts_per_page', 10),
        'paged' => get_query_var('paged', 1)
    ];
    if ( ! is_super_admin() ) {
        $args['author'] = get_current_user_id();
    }
    $queryhotel = new WP_Query($args);
    while ($queryhotel->have_posts()): $queryhotel->the_post();
        $hotel_id = get_the_ID();
        $args = [
            'post_type' => 'hotel_room',
            'posts_per_page' => -1,
            'meta_query' => [
                [
                    'key' => 'room_parent',
                    'value' => $hotel_id,
                    'compare' => '='
                ]
            ]
        ];
        if ( ! is_super_admin() ) {
            $args['author'] = get_current_user_id();
        }

        $rooms = [];
        $query = new WP_Query($args);
        while ($query->have_posts()): $query->the_post();
            $rooms[] = [
                'id' => get_the_ID(),
                'name' => get_the_title(),
                'price_by_per_person' => get_post_meta( get_the_ID(), 'price_by_per_person', true )
            ];

        endwhile;
        wp_reset_postdata();
        ?>
        <div class="calendar-wrapper" style="position: relative">
            <h4>
                <?php echo get_the_title($hotel_id);?>
            </h4>
            <div class="gantt wpbooking-gantt st-inventory" data-id="<?php echo esc_attr($hotel_id); ?>"
                 data-rooms="<?php echo esc_attr(json_encode($rooms)); ?>">
            </div>
            <input type="hidden" value="<?php echo esc_html__('Edit number of room', 'traveler'); ?>"
                   id="inventory-text-eidt-room"/>
            <div class="panel-room-number-wrapper">
                <div class="panel-room">
                    <input class="input-price" type="number" name="input-room-number" value="" placeholder="">
                    <input class="input-room-id" type="hidden" name="input-room-id" value="" placeholder="" min="0">
                    <a href="javascript: void(0);" class="button btn-add-number-room" style="margin-left: 10px;">Update
                        <i class="fa fa-spin fa-spinner loading-icon"></i></a>
                    <span class="close">
                                <i class="fa fa-times"></i>
                            </span>
                    <div class="message-box"></div>
                </div>
            </div>
        </div>
    <?php
    endwhile;
    st_paging_nav(null, $queryhotel);
    wp_reset_postdata();
    $post = $oldpost;

    ?>
</div>
<style>
    .inventory-edit-room-number{
        /* display: none !important; */

		color: #ed8323;
		display: inline-block;
		width: 20px;
		height: 20px;
		float: right;
		cursor: pointer;
		position: absolute;
		right: 0;
    }
</style>
