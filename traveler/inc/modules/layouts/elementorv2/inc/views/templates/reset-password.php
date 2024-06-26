<?php get_header();
$login_page = get_the_permalink(st()->get_option("page_user_login"));
?>
<div id="st-content-wrapper" class="st-style-elementor">
    <?php
    $menu_transparent = st()->get_option('menu_transparent', '');
    if($menu_transparent === 'on'){
        $thumb_id = get_post_thumbnail_id(get_the_ID());
        
        if($thumb_id){
            $img_url = wp_get_attachment_image_url($thumb_id, 'full');
            echo stt_elementorv2()->loadView('components/banner', ['img_url' => $img_url]);
        }
        
    }?>
</div>
<div class="container">
    <div id="st-forgot-form-page" class="st-login-class-wrapper">
        <div class="modal-dialog" role="document" style="max-width: 450px;">
            <div class="modal-content">
                <?php echo st()->load_template('layouts/modern/common/loader'); ?>
                <div class="modal-header d-sm-flex d-md-flex justify-content-between align-items-center">
                    <div class="modal-title"><?php echo __('Reset Password', 'traveler') ?></div>
                </div>
                <div class="modal-body">
                    <form action="#" class="form" method="post">
                        <input type="hidden" name="st_theme_style" value="modern"/>
                        <input type="hidden" name="action" value="st_reset_password">
                        <p class="c-grey f14">
                            <?php echo __('Enter the e-mail address associated with the account.', 'traveler') ?>
                            <br/>
                            <?php echo __('We\'ll e-mail a link to reset your password.', 'traveler') ?>
                        </p>
                        <div class="form-group">
                            <input type="email" class="form-control" name="email"
                                   placeholder="<?php echo esc_html__('Email', 'traveler') ?>">
                            <?php echo TravelHelper::getNewIcon('ico_email_login_form'); ?>
                        </div>
                        <div class="form-group">
                            <input type="submit" name="submit" class="form-submit"
                                   value="<?php echo esc_html__('Send Reset Link', 'traveler') ?>">
                        </div>
                        <div class="message-wrapper mt20"></div>
                        <div class="text-center mt20">
                            <a href="<?php echo esc_url($login_page); ?>" class="st-link font-medium open-login"><?php echo esc_html__('Back to Log In', 'traveler') ?></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php get_footer(); ?>
