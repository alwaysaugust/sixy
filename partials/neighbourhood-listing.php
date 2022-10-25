<?php
    // override default attributes with user attributes
    // $atts_data = shortcode_atts( array(
    //     'title' => '',
    // ), $atts );
    // $title =  $atts_data['title'];
    // echo $title;die;
    
    //dynamic name
    $title = $args['title'] ?? '';
    
    
    global $wpdb;
    $table_name = 'wp_wpl_properties';
    $results = $wpdb->get_results(
        // "SELECT id,add_date,price FROM $table_name where location4_name like '%Toronto%' limit 1"
        "SELECT * FROM $table_name where location4_name = '$title' OR location5_name = '$title'"
    );
    
    $results_last_12_months = $wpdb->get_results(
        // "SELECT id,add_date,price FROM $table_name where location4_name like '%Toronto%' limit 1"
        "SELECT * FROM $table_name where location4_name = '$title' OR location5_name = '$title' and add_date> now() - INTERVAL 12 month"
    );
    $for_rent = 0;
    $for_sale = 0;
    $sold_properties_prices = 0;
    $selling_price = 0;
    //last 12 months
    if ($results){
        foreach($results_last_12_months as $value)
        {
             $rendered =  json_decode($value->rendered,true);
             $status = str_replace( ',', '', $value->f_3066_options );
            //  echo $rendered['rendered'][6]['raw'];
            if($rendered['materials']['listing']['value'] == 'For Rent'){
                $for_rent++;
            }
            if($status =='11'){
                $for_sale++;
            }
        }
        foreach($results as $value)
        {
             $dateDiff += dateDiff($value->add_date,date('Y-m-d H:i:s'));
             $status = str_replace( ',', '', $value->f_3066_options );
             if($status =='11'){
                $sold_properties_prices += $value->field_3069;
            }
            $selling_price += $value->price;
        }
        // echo "listing price ".$selling_price."<br>";
        // echo "sold price ".$sold_properties_prices;
        
        //$list_vs_selling_price = (($selling_price - $sold_properties_prices)/$selling_price)*100;
        $list_vs_selling_price = (($sold_properties_prices - $selling_price)/$selling_price)*100;
        $total_count = count( $results );
        $days_on_the_market = round(($dateDiff)/$total_count);
    }
?>
<style>
    .col-12.col-md-3.nbh-counter-main {
	    border: 1px solid #ebebeb;
	    border-right: 0;
	    padding: 28px;
	}
	.col-12.col-md-3.nbh-counter-main:last-child {
		border-right: 1px solid #ebebeb;
	}
	.col-12.col-md-3.nbh-counter-main-sec {
	    border: 1px solid #ebebeb;
	    border-right: 0;
	    border-top: 0;
	    padding: 28px;
	}
	.col-12.col-md-3.nbh-counter-main-sec:last-child {
	    border-right: 1px solid #ebebeb;
	}
	span.count-descripts {
		font-size: 12px;
	    font-weight: 600;
	    font-family: 'Inter';
	    line-height: 0.12;
	    color: #63717D;
	}
	span.nbh-bold {
	    font-size: 18px;
	    font-family: 'Inter';
	    font-weight: 600;
	    line-height: 1.768;
	    color: #191919;
	}
    .wpl_property_listing_container .grid_box .wpl_prp_listing_icon_box>div {
        font-family: 'Inter';
    }
    .wpl_search_from_box .wpl_search_field_container .chosen-container .chosen-single>span {
        color: #191919;
        font-size: 14px;
        font-weight: 100;
        letter-spacing: -0.14px;
        line-height: 39px;
        font-family: 'Inter' !important;
    }
    .block-hp .search-neighbourhoods .wpl_search_from_box .search_submit_box:after {
        line-height: 2.9em;
        letter-spacing: -0.14px;
    }
    .block-hp-featured .wpl_property_listing_container .grid_box .wpl_prp_listing_location {
        font-family: 'Inter' !important;
        font-size: 14px !important;
        font-weight: 400 !important;
        opacity: 1;
        color: #191919 !important;
        font-style: normal;
        letter-spacing: -0.14px;
        text-align: left;
        line-height: 21px;
    }
    span#wpl_total_results19 {
        letter-spacing: -0.14px;
        text-transform: capitalize;
    }
    .block-hp-featured .wpl_property_listing_container .grid_box .price_box span {
        letter-spacing: -0.18px;
    }
</style>
<br>

<?php if ($results) : ?>
<section class="custom-neighboor">
	<div class="inner-custom-neighboor">
		<div class="container-fluid p-5">
<div class="block-hp" style="">
<div class="search-neighbourhoods">
    <div class="section-search-mapview">
    <?php echo do_shortcode('[wpl_widget_instance id="wpl_search_widget-19"]') ?>
    </div>
<div style="text-align:center;"><style>.fusion-button.button-3{border-radius:4px 4px 4px 4px;}</style>
	<a class="fusion-button button-flat fusion-button-default-size button-default button-3 fusion-button-default-span fusion-button-default-type button-all" target="_self" href="/tosell/mp/?widget_id=7&kind=0&sf_locationtextsearch=<?php echo $title ?>&sf_unit_price=260"><span class="fusion-button-text">Map Search</span></a></div>
</div>
<?php echo do_shortcode('[WPL kind="0" sf_min_price="200000" sf_unit_price="260" limit="16" wplpagination="scroll" wplorderby="p.sp_featured" wplorder="ASC" wplcolumns="4" tpl="default" wplpcc="grid_box" sf_locationtextsearch="'.$title.'"]'); ?>

<div style="text-align:center;">
    <style>.fusion-button.button-4{border-radius:4px 4px 4px 4px;}</style>
    <a class="fusion-button button-flat fusion-button-default-size button-default button-4 fusion-button-default-span fusion-button-default-type button-all load" target="_self"><span class="fusion-button-text">Load more</span>
    </a>
</div>
</div>
</div>
</div>
</section>
<?php else : ?>
<style>
.custom-neighboor {
    bottom: 75px !important;
    left: 10px !important;
}
</style>
<section class="custom-neighboor">
    <h3>No information found.</h3>
    <!--<h3>Data not found</h3>-->
</section>
<?php endif;  ?>

<style>
    .fusion-button.button-flat.fusion-button-default-size.button-default.button-3.fusion-button-default-span.fusion-button-default-type.button-all {
  position: relative;
  bottom: 138px;
  left: 492px;
}
.fusion-body .fusion-flex-container.fusion-builder-row-4 {
  padding-top: 0px !important;
  margin-top: 0px !important;
  padding-right: 0px !important;
  padding-bottom: 0px !important;
  margin-bottom: 0px !important;
  padding-left: 0px !important;
}
.block-hp .search-neighbourhoods, .block-hp .search-neighbourhoods, .block-hp .search-neighbourhoods .wpl_search_from_box_top {
  margin-top: -37px !important;
}
.fusion-button.button-4 {
  border-radius: 4px 4px 4px 4px;
  top: -85px;
}
.wpl-row.wpl-expanded.wpl-small-up-1.wpl-medium-up-2.wpl-large-up-4.wpl_property_listing_listings_container.clearfix {
  position: relative;
  bottom: 57px;
}
.title-heading-left.fusion-responsive-typography-calculated {
  position: relative;
  right: 20px;
}
.fusion-button-default-span.fusion-button-default-type.button-all {
  padding: 10px 14px;
  font-size: 14px;
  color: #15161a;
  font-size: 14px;
  font-weight: 600;
  border: 1px solid #191919;
  background: #ffffff;
}
.fusion-button-default-span.fusion-button-default-type.button-all:hover {
  color: #ffffff;
  background: #191919;
}
.page-id-19460 div#wpl_googlemap_container14 {
    display: none !important;
}
</style>