<?php
    //dynamic name
    $title = $args['title'] ?? '';
    
    global $wpdb;
    $table_name = 'wp_wpl_properties';
    $results = $wpdb->get_results(
        // "SELECT id,add_date,price FROM $table_name where location4_name like '%Toronto%' limit 1"
        "SELECT * FROM $table_name where location4_name like '%".$title."%' OR location5_name like '%".$title."%'"
    );
    
    if($results){
        $title = $args['title'] ?? '';
    }else{
        // $title = "The location was not found.";
        $title = "Location not available";
    }
?>

<div class="fusion-fullwidth fullwidth-box fusion-builder-row-2 fusion-flex-container fusion-parallax-none banner-neighbourhoods nonhundred-percent-fullwidth non-hundred-percent-height-scrolling" style="background-color: #63717d;background-image: url(&quot;/tosell/wp-content/uploads/sites/49/2022/04/banner_neighbourhoods.jpg&quot;);background-position: center center;background-repeat: no-repeat;border-width: 0px 0px 0px 0px;border-color:var(--awb-color3);border-style:solid;-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover;">
    <div class="fusion-builder-row fusion-row fusion-flex-align-items-flex-start" style="max-width:1144px;margin-left: calc(-4% / 2 );margin-right: calc(-4% / 2 );">
        <div class="fusion-layout-column fusion_builder_column fusion-builder-column-4 fusion_builder_column_1_2 1_2 fusion-flex-column fusion-flex-align-self-center banner-messaging">
            <div class="fusion-column-wrapper fusion-flex-justify-content-center fusion-content-layout-column" style="background-position:left top;background-repeat:no-repeat;-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover;padding: 0px 0px 0px 0px;">
                <style type="text/css">
                    @media only screen and (max-width:1024px) {.fusion-title.fusion-title-1{margin-top:10px!important; margin-right:0px!important;margin-bottom:15px!important;margin-left:0px!important;}}@media only screen and (max-width:640px) {.fusion-title.fusion-title-1{margin-top:10px!important; margin-right:0px!important;margin-bottom:10px!important; margin-left:0px!important;}}</style><div class="fusion-title title fusion-title-1 fusion-sep-none fusion-title-text fusion-title-size-one" style="margin-top:10px;margin-right:0px;margin-bottom:15px;margin-left:0px;"><h1 class="title-heading-left fusion-responsive-typography-calculated neighbourhoods-banner" style="font-family: &quot;Inter&quot;; font-weight: 400; margin: 0px; color: rgb(255, 255, 255); --fontSize: 64; line-height: 1.2;" data-fontsize="64" data-lineheight="76.8px"><?php echo $title ?></h1></div></div><style type="text/css">.fusion-body .fusion-builder-column-4{width:50% !important;margin-top : 0px;margin-bottom : 0px;}.fusion-builder-column-4 > .fusion-column-wrapper {padding-top : 0px !important;padding-right : 0px !important;margin-right : 4.608%;padding-bottom : 0px !important;padding-left : 0px !important;margin-left : 3.84%;}@media only screen and (max-width:1024px) {.fusion-body .fusion-builder-column-4{width:50% !important;order : 0;}.fusion-builder-column-4 > .fusion-column-wrapper {margin-right : 4.608%;margin-left : 3.84%;}}@media only screen and (max-width:640px) {.fusion-body .fusion-builder-column-4{width:100% !important;order : 0;}.fusion-builder-column-4 > .fusion-column-wrapper {margin-right : 1.92%;margin-left : 1.92%;}}
                    </style>
                    </div>
                    <div class="fusion-layout-column fusion_builder_column fusion-builder-column-5 fusion_builder_column_1_2 1_2 fusion-flex-column">
                        <div class="fusion-column-wrapper fusion-flex-justify-content-flex-start fusion-content-layout-column" style="background-position:left top;background-repeat:no-repeat;-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover;padding: 0px 0px 0px 0px;">
                            
                        </div>
                        <style type="text/css">.fusion-body .fusion-builder-column-5{width:50% !important;margin-top : 0px;margin-bottom : 0px;}.fusion-builder-column-5 > .fusion-column-wrapper {padding-top : 0px !important;padding-right : 0px !important;margin-right : 3.84%;padding-bottom : 0px !important;padding-left : 0px !important;margin-left : 3.072%;}@media only screen and (max-width:1024px) {.fusion-body .fusion-builder-column-5{width:50% !important;order : 0;}.fusion-builder-column-5 > .fusion-column-wrapper {margin-right : 3.84%;margin-left : 3.072%;}}@media only screen and (max-width:640px) {.fusion-body .fusion-builder-column-5{width:100% !important;order : 0;}.fusion-builder-column-5 > .fusion-column-wrapper {margin-right : 1.92%;margin-left : 1.92%;}}</style></div></div><style type="text/css">.fusion-body .fusion-flex-container.fusion-builder-row-2{ padding-top : 0px;margin-top : 0px;padding-right : 30px;padding-bottom : 0px;margin-bottom : 0px;padding-left : 30px;}</style></div>
<style>
    .title-heading-left.fusion-responsive-typography-calculated {
  top: 48px;
}
.fusion-fullwidth.fullwidth-box.fusion-builder-row-2.fusion-flex-container.fusion-parallax-none.banner-neighbourhoods.nonhundred-percent-fullwidth.non-hundred-percent-height-scrolling {
  width: 120em;
  right: 401px;
}
.fusion-builder-row.fusion-row.fusion-flex-align-items-flex-start{
    max-width: 1162px !important;
}
.title-heading-left.fusion-responsive-typography-calculated {
  position: relative;
  right: 9px;
}
.custom-neighboor {
  position: relative;
  bottom: 48px;
}
</style>