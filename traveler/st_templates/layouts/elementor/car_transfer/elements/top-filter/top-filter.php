<?php
$filters = get_post_meta(get_the_ID(), 'rs_filter_car', true);
if(!isset($format))
    $format = '';

$name_asc = 'name_a_z';
$name_desc = 'name_z_a';
?>
<div class="top-filter">
    <ul>
        <li><h3 class="title"><?php echo __('FILTER BY', 'traveler'); ?></h3> <span class="hidden-lg hidden-md close-filter"><?php echo TravelHelper::getNewIcon('Ico_close', '#A0A9B2', '20px', '20px'); ?></span></li>
        <?php
        if(!empty($filters)){
            foreach ($filters as $k => $v){
                echo st()->load_template('layouts/modern/car/elements/top-filter/' . esc_html($v['rs_filter_type']), '', array('title' => $v['title'], 'taxonomy' => $v['rs_filter_type_taxonomy']));
            }
        }
        ?>
    </ul>

    <div class="toolbar toolbar-intop">
        <ul class="toolbar-action hidden-xs hidden-sm">
            <li>
                <div class="form-extra-field dropdown">
                    <button class="btn btn-link dropdown" type="button" id="dropdownMenuSort" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php echo __('Sort', 'traveler'); ?> <i class="fa fa-angle-down arrow"></i>
                    </button>
                    <div class="dropdown-menu sort-menu" aria-labelledby="dropdownMenuSort">
                        <div class="sort-title">
                            <h3><?php echo __('SORT BY', 'traveler'); ?> <span class="hidden-lg hidden-md hidden-sm close-filter"><i class="fa fa-times" aria-hidden="true"></i></span></h3>
                        </div>
                        <div class="sort-item st-icheck">
                            <div class="st-icheck-item"><label> <?php echo __('New car', 'traveler'); ?><input class="service_order" type="radio" name="service_order_<?php echo esc_attr($format); ?>" data-value="new" /><span class="checkmark"></span></label></div>
                        </div>
                        <div class="sort-item st-icheck">
                            <span class="title"><?php echo __('Price', 'traveler'); ?></span>
                            <div class="st-icheck-item"><label> <?php echo __('Low to Hight', 'traveler'); ?><input class="service_order" type="radio" name="service_order_<?php echo esc_attr($format); ?>"  data-value="price_asc"/><span class="checkmark"></span></label></div>
                            <div class="st-icheck-item"><label> <?php echo __('Hight to Low', 'traveler'); ?><input class="service_order" type="radio" name="service_order_<?php echo esc_attr($format); ?>"  data-value="price_desc"/><span class="checkmark"></span></label></div>
                        </div>
                        <div class="sort-item st-icheck">
                            <span class="title"><?php echo __('Name', 'traveler'); ?></span>
                            <div class="st-icheck-item"><label> <?php echo __('a - z', 'traveler'); ?><input class="service_order" type="radio" name="service_order_<?php echo esc_attr($format); ?>"  data-value="<?php echo esc_attr($name_asc); ?>"/><span class="checkmark"></span></label></div>
                            <div class="st-icheck-item"><label> <?php echo __('z - a', 'traveler'); ?><input class="service_order" type="radio" name="service_order_<?php echo esc_attr($format); ?>"  data-value="<?php echo esc_attr($name_desc); ?>"/><span class="checkmark"></span></label></div>
                        </div>
                    </div>
                </div>
            </li>
            <li class="layout hidden">
            <span class="layout-item" data-value="list">
                <span class="icon-active"><?php echo TravelHelper::getNewIcon('ico_list-active', '#A0A9B2'); ?></span>
                <span class="icon-normal"><?php echo TravelHelper::getNewIcon('ico_list', '#A0A9B2'); ?></span>
            </span>
                <span class="layout-item active" data-value="grid">
                <span class="icon-active"><?php echo TravelHelper::getNewIcon('ico_grid_active', '#A0A9B2'); ?></span>
                <span class="icon-normal"><?php echo TravelHelper::getNewIcon('ico_grid', '#A0A9B2'); ?></span>
            </span>
            </li>
        </ul>
    </div>
</div>
