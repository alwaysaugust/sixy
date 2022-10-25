<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

$this->_wpl_import('widgets.mobile_app.scripts.js', true, true);
?>
<div id="wpl_mobile_app_widget_cnt<?php echo $this->widget_id; ?>" class="wpl-mobile_app-widget clearfix  <?php echo $this->css_class;  ?><?php if($this->show_only_on_mobile):?> wpl-util-hidden<?php endif;?>">
    <?php echo $args['before_title'].__($this->title, 'real-estate-listing-realtyna-wpl').$args['after_title']; ?>

    <div class="wpl-mobile-app-notif clearfix">
        <div>
            <div class="wpl-mobile-app-btn">
                <a href="<?php echo $this->google_play_url ?>">
                    <img src="<?php echo wpl_global::get_wpl_url(); ?>assets/img/mobile_app/google-play.png" alt="<?php echo __('Get it on Google play'); ?>" />
                </a>
            </div>
            <div class="wpl-mobile-app-btn">
                <a href="<?php echo $this->app_store_url ?>">
                    <img src="<?php echo wpl_global::get_wpl_url(); ?>assets/img/mobile_app/app-store.png" alt="<?php echo __('Available on the app store'); ?>" />
                </a>
            </div>
        </div>
    </div>
</div>
