<?php

/**

 * Created by PhpStorm.

 * User: Dannie

 * Date: 8/24/2018

 * Time: 2:46 PM

 */

global $wpdb;



$ranges = [

    'year' => esc_html__('Years', 'traveler'),

    'last_month' => esc_html__('Last month', 'traveler'),

    'this_month' => esc_html__('This month', 'traveler'),

    'last_7_days' => esc_html__('Last 7 days', 'traveler'),

];





$post_types = [

    'st_hotel' => [

        'title' => esc_html__('Hotel', 'traveler'),

        'icon' => 'fa fa-building fa-5x',

        'amount' => 0,

        'bg' => '#337ab7'

    ],

    'hotel_room' => [

        'title' => esc_html__('Room', 'traveler'),

        'icon' => 'fa fa-building fa-5x',

        'amount' => 0,

        'bg' => '#337ab7'

    ],

    'st_rental' => [

        'title' => esc_html__('Rental', 'traveler'),

        'icon' => 'fa fa-home fa-5x',

        'amount' => 0,

        'bg' => '#e35b5a'

    ],

    'st_cars' => [

        'title' => esc_html__("Car", 'traveler'),

        'icon' => 'fa fa-car fa-5x',

        'amount' => 0,

        'bg' => '#44b6ae'

    ],

    'st_tours' => [

        'title' => esc_html__("Tour", 'traveler'),

        'icon' => 'fa fa-flag-o fa-5x',

        'amount' => 0,

        'bg' => '#8775a7'

    ],

    'st_activity' => [

        'title' => esc_html__("Activity", 'traveler'),

        'icon' => 'fa fa-bolt fa-5x',

        'amount' => 0,

        'bg' => '#27ae60'

    ],

    'st_flight' => [

        'title' => esc_html__("Flight", 'traveler'),

        'icon' => 'fa fa-plane fa-5x',

        'amount' => 0,

        'bg' => '#ff892d'

    ],

];

$sum_query = [];

//if(!empty($post_types))

//{

//    foreach ($post_types as $post_type=>$data){

//        $sum_query[]="SUM(CASE WHEN st_booking_post_type='{$post_type}' THEN total_order END) as ".$post_type;

//    }

//

//}

$st_status = STInput::get('st_status', []);

$current_range = STInput::get('range', 'last_7_days');

if (empty($current_range)) $current_range = 'last_7_days';

$chartData = [

    'labels' => [],

    'datasets' => [],

    'prefix'

];



$where = [];

$whereRaw = '';

if (!empty(STInput::get('partner_id'))) {

    $where['partner_id'] = (int)STInput::get('partner_id');

}

if ($st_post_type = STInput::get('st_post_type')) {



    $where['st_booking_post_type'] = $st_post_type;

}

if ($st_post_types = STInput::get('st_post_types') and is_array($st_post_types)) {

    $pl = array_fill(0, count($st_post_types), '%s');

    $whereRaw .= $wpdb->prepare(' st_booking_post_type in (' . implode(',', $pl) . ')', $st_post_types);

}



if (!empty($st_status) and is_array($st_status)) {

    if ($whereRaw) $whereRaw .= ' AND ';

    $pl = array_fill(0, count($st_status), '%s');

    $whereRaw .= $wpdb->prepare(' status in (' . implode(',', $pl) . ')', $st_status);

}





if ($partner_search = STInput::get('partner_search')) {

    if ($whereRaw) $whereRaw .= ' AND ';



    $partner_search = $wpdb->esc_like($partner_search);

    $partner_search = '%' . $partner_search . '%';



    $whereRaw .= $wpdb->prepare("({$wpdb->users}.id LIKE %s OR {$wpdb->users}.user_login LIKE %s OR {$wpdb->users}.display_name LIKE %s OR {$wpdb->users}.user_email LIKE %s )",

        [$partner_search, $partner_search, $partner_search, $partner_search]);

}



$listsUsers = [];

$totalUsers = 0;

$limitUsers = 20;

$currentPage = max(1, STInput::get('c_paged'));

$total_booking_fee_price = 0;

$total_price = 0;



switch ($current_range) {

    case "year":

    case "last_7_days":

    case "this_month":

    case "last_month":

    case "custom":

        $max_time = strtotime('today');

        $min_time = $max_time - DAY_IN_SECONDS * 6;



        if ($current_range == 'year') {

            $max_time = strtotime('today');

            $max_time = strtotime(date('Y-m-t', $max_time));

            $min_time = strtotime(date('Y-01-01', $max_time));

        }



        if ($current_range == 'this_month') {

            $min_time = strtotime(date('Y-m-01', $max_time));

        }



        if ($current_range == 'last_month') {

            $min_obj = new DateTime(date('Y-m-01', $max_time));

            $min_obj->modify('-1 day');

            $max_time = $min_obj->getTimestamp();

            $min_time = strtotime(date('Y-m-01', $max_time));

        }





        if ($current_range == 'custom') {

            $start_date = STInput::get('start_date');

            $end_date = STInput::get('end_date');



            if (!$start_date_obj = date_create_from_format('m-d-Y', $start_date)) $min_time = false;

            else $min_time = $start_date_obj->getTimestamp();



            if (!$end_date_obj = date_create_from_format('m-d-Y', $end_date)) $max_time = false;

            else $max_time = $end_date_obj->getTimestamp();





        }





        if ($max_time and $min_time) {



            if (!empty($whereRaw)) {

                ST_Order_Item_Model::inst()->where($whereRaw, null, true);

            }



            $listsUsers = $query = ST_Order_Item_Model::inst()

                ->where('created >=', date('Y-m-d', $min_time))

                ->where('created <=', date('Y-m-d', $max_time))

                ->where($where)

                ->orderby('id', 'desc')

                ->get($limitUsers, ($currentPage - 1) * $limitUsers)->result();



            $totalUsers = ST_Order_Item_Model::inst()->get_total();



        }





        $status = ST_Order_Item_Model::inst()->groupby('status')->get()->result();



        break;

}

?>

<div id="poststuff" class="st-reports-wrap">

    <!--    <div class="report-by-post_types">-->

    <!--        --><?php //if(!empty($post_types))

    //        {

    //            foreach ($post_types as $post_type=>$data)

    //            {

    //                ?>

    <!--                <div class="post-type-item-wrap">-->

    <!--                    <div class="post-type-item" style="background-color:-->

    <?php //echo esc_attr($data['bg']) ?><!--">-->

    <!--                        <i class="post-type-icon --><?php //echo esc_attr($data['icon']) ?><!--"></i>-->

    <!--                        <div class="post-type-info">-->

    <!--                            <span class="amount">-->

    <?php //echo TravelHelper::format_money($data['amount']) ?><!--</span>-->

    <!--                            <span class="post-type-name">-->

    <?php //echo esc_html($data['title']) ?><!--</span>-->

    <!--                        </div>-->

    <!--                    </div>-->

    <!--                </div>-->

    <!--                --><?php

    //            }

    //        }?>

    <!--    </div>-->

    <div class="postbox">

        <ul class="st_report_date_ranges">

            <?php foreach ($ranges as $id => $range) {

                $url = remove_query_arg(['start_date', 'end_date', 'c_paged']);

                ?>

                <li class="<?php if ($current_range == $id) echo 'active'; ?>">

                    <a href="<?php echo esc_url(add_query_arg(['range' => $id], $url)); ?>"><?php echo esc_html($range) ?></a>

                </li>

                <?php



            } ?>



            <li class="custom <?php if ($current_range == 'custom') echo 'active'; ?>">

                <form action="#" method="get">

                    <span class="txt-custom"><?php esc_html_e('Custom', 'traveler') ?></span>

                    <?php if (!empty($_GET)) {

                        foreach ($_GET as $k => $v) {

                            if (in_array($k, ['range', '_wpnonce', '_wp_http_referer', 'start_date', 'end_date'])) continue;

                            if (empty($v)) continue;

                            if (is_array($v) and !empty($v)) {

                                foreach ($v as $v2)

                                    printf('<input value="%s" name="%s[]" type="hidden"/>', esc_attr($v2), $k);

                                continue;

                            }

                            printf('<input value="%s" name="%s" type="hidden"/>', esc_attr($v), $k);

                        }

                    } ?>

                    <input type="hidden" name="range" value="custom">

                    <input type="text" placeholder="mm-dd-yyyy"

                           value="<?php if ($current_range == 'custom') echo esc_attr(STInput::get('start_date')); ?>"

                           autocomplete="off" name="start_date" class="form-control datepicker_start">

                    <span>–</span>

                    <input type="text" placeholder="mm-dd-yyyy"

                           value="<?php if ($current_range == 'custom') echo esc_attr(STInput::get('end_date')); ?>"

                           autocomplete="off" name="end_date" class="form-control datepicker_end">

                    <button type="submit" class="button"

                            value="Go"><?php esc_html_e('Filter', 'traveler') ?></button>

                    <?php wp_nonce_field('st_traveler_filter_reports') ?>

                </form>

            </li>

        </ul>

        <div class="inside st-reports-flex">

            <div class="report-sidebar">

                <form action="#" method="get">

                    <?php if (!empty($_GET)) {

                        foreach ($_GET as $k => $v) {

                            if (in_array($k, ['_wpnonce', '_wp_http_referer', 'partner_id', 'st_status', 'st_post_types'])) continue;

                            if (empty($v)) continue;

                            if (is_array($v) and !empty($v)) {

                                foreach ($v as $v2)

                                    printf('<input value="%s" name="%s[]" type="hidden"/>', esc_attr($v2), $k);

                                continue;

                            }

                            printf('<input value="%s" name="%s" type="hidden"/>', esc_attr($v), $k);

                        }

                    } ?>

                    <?php wp_nonce_field('st_traveler_filter_reports') ?>



                    <?php if (!empty($status)) { ?>

                        <div class="filter-item">

                            <label class="filter-label"><?php esc_html_e('Filter by Status', 'traveler') ?></label>

                            <div class="filter-checkbox-wrap">

                                <?php foreach ($status as $s) {

                                    $checked = (is_array($st_status) and in_array($s['status'], $st_status)) ? 'checked' : "";

                                    $str = $s['status'];

                                    $str = ucfirst($str);

                                    $str = str_replace('-', ' ', $str);

                                    printf('<label class="filter-checkbox" style="display: block;margin-bottom: 7px;"><input type="checkbox" value="%s" name="st_status[]" %s> %s</label>', $s['status'], $checked, $str);

                                } ?>

                            </div>

                        </div>

                    <?php } ?>

                    <div class="filter-item">

                        <label class="filter-label"><?php esc_html_e('Filter by Type', 'traveler') ?></label>

                        <!--                        <select name="st_post_type" class="filter-control">-->

                        <!--                            <option value="">--- Select ---</option>-->

                        <!--                            --><?php //foreach ($post_types as $post_type=>$data){

                        //                                printf('<option value="%s" %s>%s</option>',$post_type,selected($post_type,STInput::get('st_post_type'),false),$data['title']);

                        //                            } ?>

                        <!--                        </select>-->

                        <div class="filter-checkbox-wrap">

                            <?php foreach ($post_types as $post_type => $data) {

                                $checked = (is_array($st_post_types) and in_array($post_type, $st_post_types)) ? 'checked' : "";

                                printf('<label class="filter-checkbox" style="display: block;margin-bottom: 7px;"><input type="checkbox" value="%s" name="st_post_types[]" %s> %s</label>', $post_type, $checked, $data['title']);

                            } ?>

                        </div>

                    </div>

                    <div class="filter-item">

                        <button class="button" type="submit"

                                value="filter"><?php esc_html_e('Filter', 'traveler') ?></button>

                    </div>

                </form>

            </div>

            <div class="report-chart">
                <div class="table-responsive">
                    <table class="wp-list-table widefat striped pages">
                        <thead>
                            <tr>
                                <th class="col-name"><?php esc_html_e('Order', 'traveler'); ?></th>
                                <th class="col-name"><?php esc_html_e('Service', 'traveler'); ?></th>
                                <th class="col-name"><?php esc_html_e('Type', 'traveler'); ?></th>
                                <th class="col-name"><?php esc_html_e('Date', 'traveler'); ?></th>
                                <th class="col-name"><?php esc_html_e('Status', 'traveler'); ?></th>
                                <th class="col-name"><?php esc_html_e('Total', 'traveler'); ?></th>
                                <th class="col-name"><?php esc_html_e('Booking Fees', 'traveler'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total_booking_fee_price = 0; // Initialize total booking fees
                            $total_price = 0; // Initialize total price

                            if (!empty($listsUsers)) {
                                foreach ($listsUsers as $oneUser) {
                                    // Assuming there's a way to get the WooCommerce order ID from each $oneUser
                                    $wc_order_id = isset($oneUser['wc_order_id']) ? $oneUser['wc_order_id'] : null;

                                    if ($wc_order_id) {
                                        // Retrieve the booking fee price using get_post_meta()
                                        $booking_fee_price = get_post_meta($wc_order_id, 'booking_fee_price', true);
                                        $booking_fee_price = !empty($booking_fee_price) ? floatval($booking_fee_price) : 0; // Ensure it's a number and provide a default
                                    } else {
                                        // Handle cases where wc_order_id is not available
                                        $booking_fee_price = 0;
                                    }

                                    $total_price += floatval($oneUser['total_order']); // Accumulate totals
                                    $total_booking_fee_price += $booking_fee_price; // Add to total
                                    ?>
                                    <tr>
                                        <td>#<?php echo esc_url($oneUser['order_item_id']); ?>
                                            <?php if ($oneUser['user_id'] && $user = new WP_User($oneUser['user_id'])) {
                                                echo ($user->first_name) ? ($user->first_name) . ' ' . esc_attr($user->last_name) : esc_attr($user->display_name);
                                            } ?>
                                            <?php do_action('st_after_order_page_admin_information_table', $oneUser['order_item_id']); ?>
                                        </td>
                                        <td><a href="<?php echo esc_url(get_permalink($oneUser['st_booking_id'])); ?>" target="_blank"><?php echo get_the_title($oneUser['st_booking_id']); ?></a></td>
                                        <td><?php echo ucfirst(str_replace('st_', '', $oneUser['st_booking_post_type'])); ?></td>
                                        <td><?php echo date_i18n(get_option('date_format'), strtotime($oneUser['created'])); ?></td>
                                        <td><?php echo esc_html($oneUser['status']); ?></td>
                                        <td><?php echo TravelHelper::format_money($oneUser['total_order']); ?></td>
                                        <td><?php echo TravelHelper::format_money($booking_fee_price); ?></td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo '<tr><td colspan="7">' . esc_html__('--- Empty ---', 'traveler') . '</td></tr>';
                            }
                            ?>
                            <tr>
                                <td colspan="5" style="text-align: right;"><strong><?php esc_html_e('Total:', 'traveler'); ?></strong></td>
                                <td><strong><?php echo TravelHelper::format_money($total_price); ?></strong></td>
                                <td><strong><?php echo TravelHelper::format_money($total_booking_fee_price); ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>


                <div class="st-admin-paginate">

                    <?php

                    echo paginate_links([

                        'base' => remove_query_arg('c_paged') . '%_%',

                        'total' => ceil($totalUsers / $limitUsers),

                        'current' => $currentPage,

                        'format' => '&c_paged=%#%',

                    ])

                    ?>

                </div>

                <script>

                    (function ($) {



                        $(document).ready(function () {



                            var datepicker_end = $('.datepicker_end');

                            var datepicker_start = $('.datepicker_start');



                            datepicker_start.datepicker({

                                changeMonth: true,

                                dateFormat: 'mm-dd-yy'

                            }).on("change", function () {

                                datepicker_end.datepicker("option", "minDate", datepicker_start.datepicker('getDate'));

                            });



                            datepicker_end.datepicker({

                                changeMonth: true,

                                dateFormat: 'mm-dd-yy'

                            }).on("change", function () {

                                datepicker_start.datepicker("option", "maxDate", datepicker_end.datepicker('getDate'));

                            });

                            //

                            //

                            //var chartData=<?php //echo json_encode($chartData) ?>//;

                            //var ctx = document.getElementById('report-chart-content').getContext('2d');

                            //window.myBar = new Chart(ctx, {

                            //    type: 'line',

                            //    data: chartData,

                            //    options: {

                            //        // title: {

                            //        //     display: true,

                            //        //     text: 'Chart.js Bar Chart - Stacked'

                            //        // },

                            //        tooltips: {

                            //            mode: 'index',

                            //            intersect: false

                            //        },

                            //        responsive: true,

                            //        scales: {

                            //            yAxes: [{

                            //                ticks: {

                            //                    min: 0

                            //                }

                            //            }]

                            //        }

                            //    }

                            //});





                            // Select 2 Autocomplete Fields

                            if (typeof $.fn.select2 != 'undefined') {

                                $('.select2-field').each(function () {

                                    var p = $(this).data('params');

                                    var placeholder = $(this).data('placeholder');

                                    var init = $(this).data('init');



                                    $(this).select2({

                                        placeholder: placeholder,

                                        minimumInputLength: 1,

                                        ajax: {

                                            type: 'post',

                                            url: st_params.ajax_url,

                                            data: function (term) {

                                                p.action = 'st_traveler_autocomplete';

                                                p.search = term;

                                                p._s = st_params._s;

                                                // Query parameters will be ?search=[term]&type=public

                                                return p;

                                            },

                                            results: function (data, page) {

                                                return {results: data.items};

                                            },

                                            cache: true

                                        },

                                        initSelection: function (e, callback) {

                                            callback(init);

                                        }

                                    });



                                })

                            }



                        });

                    })(jQuery)

                </script>

            </div>

        </div>

    </div>

</div>

