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
        "SELECT * FROM $table_name where location4_name = '$title' OR location5_name = '$title'"
    );
    
    // $results_last_12_months = $wpdb->get_results(
    //     // "SELECT id,add_date,price FROM $table_name where location4_name like '%Toronto%' limit 1"
    //     "SELECT * FROM $table_name where location4_name like '%".$title."%' and add_date> now() - INTERVAL 12 month"
    // );
    // $for_rent = 0;
    // $for_sale = 0;
    // $sold_properties_prices = 0;
    // $selling_price = 0;
    // //last 12 months
    // if ($results){
    //     foreach($results_last_12_months as $value)
    //     {
    //          $rendered =  json_decode($value->rendered,true);
    //          $status = str_replace( ',', '', $value->f_3066_options );
    //         //  echo $rendered['rendered'][6]['raw'];
    //         if($rendered['materials']['listing']['value'] == 'For Rent'){
    //             $for_rent++;
    //         }
    //         if($status =='11'){
    //             $for_sale++;
    //         }
    //     }
    //     foreach($results as $value)
    //     {
    //          $dateDiff += dateDiff($value->add_date,date('Y-m-d H:i:s'));
    //          $status = str_replace( ',', '', $value->f_3066_options );
    //          if($status =='11'){
    //             $sold_properties_prices += $value->field_3069;
    //         }
    //         $selling_price += $value->price;
    //     }
    //     // echo "listing price ".$selling_price."<br>";
    //     // echo "sold price ".$sold_properties_prices;
        
    //     //$list_vs_selling_price = (($selling_price - $sold_properties_prices)/$selling_price)*100;
    //     $list_vs_selling_price = (($sold_properties_prices - $selling_price)/$selling_price)*100;
    //     $total_count = count( $results );
    //     $days_on_the_market = round(($dateDiff)/$total_count);
    // }
?>
<style>
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
	.nbh-counter-main{
		margin-bottom: 20px;
	}
	.neighborMainBox {
    border: 1px solid #EBEBEB;
	}
		.nbh-counter-main .neighborBox {
	    padding: 20px;
	    border-bottom: 1px solid #EBEBEB;
	}
	.nbh-counter-main .neighborBox:last-child {
	    border-bottom: 0;
	}
	.neighborBoxTitle {
	    color: #191919;
	    font-size: 14px;
	    font-weight: 600;
	    font-family: 'Inter';
	    margin-bottom: 20px;
	}
	.fusion-fullwidth.fullwidth-box.fusion-builder-row-2.fusion-flex-container.banner-neighbourhoods.nonhundred-percent-fullwidth.non-hundred-percent-height-scrolling {
      /*display: none;*/
    }
    span.neighbourhood-trends.fusion-responsive-typography-calculated {
        opacity: 1;
        color: #191919;
        font-family: "Inter";
        font-size: 18px;
        font-weight: 700;
        font-style: normal;
        letter-spacing: -0.18px;
        text-align: left;
        position: relative;
    }
    section.neighboor {
        padding-top: 35px;
        padding-bottom: 18px;
    }
    .neighbor-brtag {
        padding-top: 10px;
    }
    .title-heading-left.fusion-responsive-typography-calculated {
        position: relative;
        right: 11px !important;
    }
    span.inner-muted {
        font-weight: 100;
    }
    .block-hp-featured .wpl_property_listing_container .grid_box .wpl_prp_listing_icon_box>div span {
        margin-left: 0px;
        font-size: 14px !important;
        line-height: 1.6em !important;
    }
    .col-12.col-md-4.nbh-counter-main.pad-left {
        padding-left: 0;
    }
    .col-12.col-md-4.nbh-counter-main.pad-right {
        padding-right: 0;
    }
</style>


<div class="neighbor-brtag">&nbsp;</div>
<?php if ($results) : ?>
<span class="neighbourhood-trends fusion-responsive-typography-calculated" id="community-page" style="">Neighbourhood Trends</span>
<br>
<?php else : ?>
        <div class="alert alert-dark" style="background: #8080801f;" role="alert">
      <span><strong>Location not available or data not found</strong></span>
      <br>
      <p>The location entered was not available, a valid City name is required i.e. <a href="/tosell/neighbourhood/?oakville" style="text-decoration: underline;">Oakville</a>.</p>
    </div>
    <style>
        .fusion-fullwidth.fullwidth-box.fusion-builder-row-4.fusion-flex-container.nonhundred-percent-fullwidth.non-hundred-percent-height-scrolling {
            display: none;
        }
    </style>
<?php endif;  ?>
