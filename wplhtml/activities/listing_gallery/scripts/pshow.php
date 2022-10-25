<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<?php if(count($this->gallery) > 1): ?>
<script type="text/javascript">
(function($)
{
    $(function()
    {
        <?php if($this->lazyload): ?>
        Realtyna.options.ajaxloader.coverStyle.backgroundColor = '#eeeeee';
        Realtyna.options.ajaxloader.coverStyle.zIndex = '-9';
        var loader = Realtyna.ajaxLoader.show('.wpl_gallery_container', 'normal', 'left', true);
        
        <?php endif; ?>

    var swidth = screen.width;
    
    //Impact data based on screen width
    
    var sTotal = "3";
    var slWidth = swidth/3;
    
    if (swidth < 600) {
            sTotal = "1";
            slWidth = swidth;
    } else if ((swidth > 600) && (swidth < 700)){
            sTotal = "2";
            slWidth = swidth/2;
    } else if (swidth > 700){
            sTotal = "3";
            slWidth = swidth/3;
    }
    
    bannerWidth = document.querySelector('[id^="wpl_gallery_container"]').offsetWidth;
    if ((bannerWidth > 1400) && (bannerWidth < 1920)){
         sTotal = "6";
         slWidth = swidth/6;
    }
    
    //alert (bannerWidth);
    // alert (slWidth*3);

        $('#bxslider_<?php echo $this->property_id.'_'.$this->activity_id; ?>').bxSlider({
            mode: 'fade',
            pause : 6000,
            auto: <?php echo (($this->autoplay) ? 'true' : 'false'); ?>,
            captions: false,
            controls: true,
            responsive: true,
            adaptiveHeight: true,
            minSlides: 0,
            maxSlides: sTotal, 
            auto: false,   
            mode: 'horizontal',
            captions: false,
            slideWidth: slWidth,
            pagerCustom: '#bx-pager-<?php echo $this->activity_id; ?>',
            onSliderLoad: function()
            {
            $( ".bx-viewport li img" ).each(function() {
             	 if ($( this ).height()<=360){
                	$( this ).css("max-width","115%");
                 }else if ($( this ).height()>361){
                 	$( this ).css("max-width","105%");
                 }
                
            });
                <?php if($this->lazyload): ?>
                Realtyna.ajaxLoader.hide(loader);
                <?php endif; ?>
            }
        });
        
    });
})(jQuery);
</script>
<?php endif;