<?php
$currency = get_post_meta( $order_id, 'currency', true );
$order_data = STUser_f::get_booking_meta($order_id);
$date_format = TravelHelper::getDateFormat();
$booking_fee_price = get_post_meta($order_id , 'booking_fee_price',true);
?>

<div class="st_tab st_tab_order tabbable">
    <ul class="nav nav-tabs tab_order">
        <li class="active">
            <?php
            $post_type = get_post_type( $service_id );
            $obj = get_post_type_object( $post_type ); ?>
            <a data-toggle="tab" href="#tab-booking-detail" aria-expanded="true"> <?php echo sprintf(esc_html__("%s Details",'traveler'),$obj->labels->singular_name) ?></a>
        </li>
        <li class="">
            <a data-toggle="tab" href="#tab-customer-detail" aria-expanded="false"> <?php esc_html_e("Customer Details",'traveler') ?></a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent973">
        <div id="tab-booking-detail" class="tab-pane fade active in">
            <div class="info">
                    <div class="row">
                        <div class="col-md-6">
                            <strong><?php esc_html_e("Booking ID",'traveler') ?>:  </strong>
                            #<?php echo esc_html($order_id) ?>
                        </div>
                        <div class="col-md-6"><strong><?php esc_html_e("Payment Method: ",'traveler') ?> </strong>
                            <?php echo STPaymentGateways::get_gatewayname(get_post_meta($order_id, 'payment_method', true)); ?>
                        </div>
                        <div class="col-md-6"><strong><?php esc_html_e("Order Date",'traveler') ?>:  </strong>
                            <?php echo esc_html(date_i18n($date_format, strtotime($order_data['created']))) ?>
                        </div>
                        <div class="col-md-6">
                            <strong><?php esc_html_e("Booking Status",'traveler') ?>:  </strong>
                            <?php
                            $data_status =  STUser_f::_get_order_statuses();
                            $status = $order_data['status'];
                            if(!empty($status_string = $data_status[$status])){
                                //$status_string = $data_status[$status];
	                            $status_string = $data_status[get_post_meta($order_id, 'status', true)];
                                if( isset( $order_data['cancel_refund_status'] ) && $order_data['cancel_refund_status'] == 'pending'){
                                    $status_string = __('Cancelling', 'traveler');
                                }
                            }
                            ?>
                            <span class=""> <?php  echo esc_html($status_string); ?></span>
                        </div>
                        <?php
                        $depart_data_location = get_post_meta($order_id, 'depart_data_location', true);
                        $flight_type = get_post_meta($order_id, 'flight_type', true);
                        ?>
                        <div class="col-md-12"><strong><?php esc_html_e("Flight",'traveler') ?>:  </strong>
                            <?php
                            if(!empty($depart_data_location['origin_location_full'])) {
                                echo '<span>' . esc_html($depart_data_location['origin_location_full'] . ' (' . $depart_data_location['origin_iata'] . ')' . ' - ' . $depart_data_location['destination_location_full'] . ' (' . $depart_data_location['destination_iata']) . ')</span>';
                            }
                            ?>
                        </div>

                        <div class="col-md-12"><strong><?php esc_html_e("Flight Type: ",'traveler') ?>:  </strong>
                            <?php
                            $type = array(
                                'one_way' => esc_html__('One Way', 'traveler'),
                                'return' => esc_html__('Round Trip', 'traveler'),
                            )
                            ?>
                            <?php echo esc_attr($type[$flight_type]); ?>
                        </div>
                        <?php
                        $depart_data_time = get_post_meta($order_id, 'depart_data_time', true);
                        $return_data_time = get_post_meta($order_id, 'return_data_time', true);
                        ?>
                        <div class="col-md-6">
                            <strong><?php esc_html_e("Departure Time:",'traveler') ?> </strong>
                            <?php
                            $check_in = $depart_data_time['depart_time'].' '.$depart_data_time['depart_date'];
                            echo esc_attr($check_in);
                            ?>
                        </div>
                        <?php if($flight_type == 'return'){?>
                            <div class="col-md-6">
                                <strong><?php esc_html_e("Return Time:",'traveler') ?> </strong>
                                <?php
                                $check_out = $return_data_time['depart_time'].' '.$return_data_time['depart_date'];
                                ?>
                                <?php echo esc_html($check_out); ?>
                            </div>
                        <?php } ?>
                        <div class="col-md-12">
                            <strong><?php esc_html_e("No. Passenger:",'traveler') ?> </strong>
                            <?php echo get_post_meta( $order_id, 'passenger', true ); ?>
                        </div>
                        <div class="col-md-12"><?php st_print_order_item_guest_name(json_decode($order_data['raw_data'],true)) ?></div>
                        <div class="line col-md-12"></div>
                        <div class="col-md-12">
                            <strong><?php esc_html_e("Departure Price:",'traveler') ?> </strong>
                            <div class="pull-right">
                                <strong>
                                    <?php
                                    $depart_price = (int) get_post_meta( $order_id, 'depart_price', true );
                                    echo TravelHelper::format_money($depart_price);
                                    ?>
                                </strong>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <strong><?php esc_html_e("Departure Tax:",'traveler') ?> </strong>
                            <div class="pull-right">
                                <strong>
                                    <?php
                                    $tax_price_depart = (int) get_post_meta( $order_id, 'tax_price_depart', true );
                                    echo TravelHelper::format_money($tax_price_depart);
                                    ?>
                                </strong>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <strong><?php esc_html_e("Return Price:",'traveler') ?> </strong>
                            <div class="pull-right">
                                <strong>
                                    <?php
                                    $return_price = (int) get_post_meta( $order_id, 'return_price', true );
                                    echo TravelHelper::format_money($return_price);
                                    ?>
                                </strong>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <strong><?php esc_html_e("Return Tax:",'traveler') ?> </strong>
                            <div class="pull-right">
                                <strong>
                                    <?php
                                    $tax_price_return = (int) get_post_meta( $order_id, 'tax_price_return', true );
                                    echo TravelHelper::format_money($tax_price_return);
                                    ?>
                                </strong>
                            </div>
                        </div>
                        <?php if(!empty($booking_fee_price)){
                            ?>
                            <div class="col-md-12">
                                <strong><?php esc_html_e("Fee: ",'traveler') ?></strong>
                                <div class="pull-right">
                                    <strong><?php echo TravelHelper::format_money($booking_fee_price); ?></strong>
                                </div>
                            </div>
                        <?php } ?>
                        <?php  $total_price = floatval( get_post_meta( $order_id, 'total_price', true ) ); ?>
                        <div class="col-md-12">
                            <strong><?php esc_html_e("Total: ",'traveler') ?></strong>
                            <div class="pull-right">
                                <strong><?php echo TravelHelper::format_money($total_price); ?></strong>
                            </div>
                        </div>
                        <div class="line col-md-12"></div>
                        <div class="col-md-12">
                            <strong><?php esc_html_e("Pay Amount: ",'traveler') ?></strong>
                            <div class="pull-right">
                                <strong><?php echo TravelHelper::format_money($total_price); ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        <div id="tab-customer-detail" class="tab-pane fade">
            <div class="container-customer">
                <?php echo apply_filters( 'st_customer_info_booking_history', st()->load_template('user/detail-booking-history/customer',false,array("order_id"=>$order_id)),$order_id ); ?>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?php do_action("st_after_body_order_information_table",$order_id); ?>
    <button data-dismiss="modal" class="btn btn-default" type="button"><?php esc_html_e("Close",'traveler') ?></button>
</div>