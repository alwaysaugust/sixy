<?php
    $title = $args['title'] ?? '';
    global $wpdb;
    $table_name = 'wp_wpl_properties';
    $results = $wpdb->get_results(
        // "SELECT id,add_date,price FROM $table_name where location4_name like '%Toronto%' limit 1"
        "SELECT * FROM $table_name where location4_name like '%".$title."%'"
    );
    
    $results_last_12_months = $wpdb->get_results(
        // "SELECT id,add_date,price FROM $table_name where location4_name like '%Toronto%' limit 1"
        "SELECT * FROM $table_name where location4_name like '%".$title."%' and add_date> now() - INTERVAL 12 month"
    );
    $for_rent = 0;
    $for_sale = 0;
    $sold_properties_prices = 0;
    $selling_price = 0;
    //last 12 months
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
?>
<style>
    .col-12.col-md-3.nbh-counter-main {
	    border: 1px solid #EBEBEB;
	    border-right: 0;
	    padding: 28px;
	}
	.col-12.col-md-3.nbh-counter-main:last-child {
		border-right: 1px solid #EBEBEB;
	}
	.col-12.col-md-3.nbh-counter-main-sec {
	    border: 1px solid #EBEBEB;
	    border-right: 0;
	    border-top: 0;
	    padding: 28px;
	}
	.col-12.col-md-3.nbh-counter-main-sec:last-child {
	    border-right: 1px solid #EBEBEB;
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
</style>
<br>
<section class="neighboor">
	<div class="inner-neighbor">
		<div class="container-fluid p-5">
			<div class="row">
				<!-- <div class="col-12">
					<div class="heading">
						<h1>Neighborhood</h1>
					</div>
				</div> -->
				<div class="col-12 col-md-3 nbh-counter-main">
					<div class="neighborBox">
						<div class="nbhBox-top">
							<div class="nbhBox-contet">
								<span class="count-descripts">Days on Market</span>
							</div>
						</div>
						<div class="nbhBox-bottom">
							<span class="nbh-bold"><?php echo $days_on_the_market;?> Days</span>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-3 nbh-counter-main">
					<div class="neighborBox">
						<div class="nbhBox-top">
							<div class="nbhBox-contet">
								<span class="count-descripts">List vs. Selling Price</span>
							</div>
						</div>
						<div class="nbhBox-bottom">
							<span class="nbh-bold"><?php echo round($list_vs_selling_price);?>% <?php if($list_vs_selling_price > 0){
                              echo "Up";
                            }
                            elseif($list_vs_selling_price < 0){
                              echo "Below";
                            } ?></span>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-3 nbh-counter-main">
					<div class="neighborBox">
						<div class="nbhBox-top">
							<div class="nbhBox-contet">
								<span class="count-descripts">Offer Competition</span>
							</div>
						</div>
						<div class="nbhBox-bottom">
							<span class="nbh-bold">NA</span>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-3 nbh-counter-main">
					<div class="neighborBox">
						<div class="nbhBox-top">
							<div class="nbhBox-contet">
								<span class="count-descripts">Turnover</span>
							</div>
						</div>
						<div class="nbhBox-bottom">
							<span class="nbh-bold">NA</span>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-12 col-md-3 nbh-counter-main-sec">
					<div class="neighborBox">
						<div class="nbhBox-top">
							<div class="nbhBox-contet">
								<span class="count-descripts">Property Value (12mo)</span>
							</div>
						</div>
						<div class="nbhBox-bottom">
							<span class="nbh-bold">NA<img src="https://sixyrealestatewebsites.com/tosell/wp-content/uploads/sites/49/2022/05/icons8-sort-right-30.png"/>
							</span>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-3 nbh-counter-main-sec">
					<div class="neighborBox">
						<div class="nbhBox-top">
							<div class="nbhBox-contet">
								<span class="count-descripts">Price Ranking</span>
							</div>
						</div>
						<div class="nbhBox-bottom">
							<span class="nbh-bold">NA</span>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-3 nbh-counter-main-sec">
					<div class="neighborBox">
						<div class="nbhBox-top">
							<div class="nbhBox-contet">
								<span class="count-descripts">Sold (12mo)</span>
							</div>
						</div>
						<div class="nbhBox-bottom">
							<span class="nbh-bold"><?php echo $for_sale;?></span>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-3 nbh-counter-main-sec">
					<div class="neighborBox">
						<div class="nbhBox-top">
							<div class="nbhBox-contet">
								<span class="count-descripts">Rented (12mo)</span>
							</div>
						</div>
						<div class="nbhBox-bottom">
							<span class="nbh-bold"><?php echo $for_rent;?></span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>