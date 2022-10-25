<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<script type="text/javascript">
    wplj(function () {
        var isMobile = {
            Android: function() {
                return navigator.userAgent.match(/Android/i);
            },
            BlackBerry: function() {
                return navigator.userAgent.match(/BlackBerry/i);
            },
            iOS: function() {
                return navigator.userAgent.match(/iPhone|iPad|iPod/i);
            },
            Opera: function() {
                return navigator.userAgent.match(/Opera Mini/i);
            },
            Windows: function() {
                return navigator.userAgent.match(/IEMobile/i);
            },
            any: function() {
                return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
            }
        };

        if(isMobile.any() && <?php echo $this->show_only_on_mobile; ?>) {
            wplj('#wpl_mobile_app_widget_cnt<?php echo $this->widget_id; ?>').removeClass('wpl-util-hidden');
            wplj('#wpl_mobile_app_widget-<?php echo $this->widget_id; ?>').addClass('wpl-mobile-app-on-header');
            wplj('body').prepend(wplj('#wpl_mobile_app_widget-<?php echo $this->widget_id; ?>'));
        }
    });
</script>
