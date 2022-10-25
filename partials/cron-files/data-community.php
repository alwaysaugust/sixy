<?php
    set_time_limit(0);
    global $wpdb;
    $table_name = 'wp_wpl_properties';
    $neighbourhood_community_table = 'wp_neighbourhood_community';

    //dynamic name
    $title = 'Toronto';

    //get all community location
    $community_name = $wpdb->get_results(
        "SELECT `location5_name` as community FROM $table_name WHERE `location5_name` != ''  GROUP BY `location5_name`;"
    );

    $nbh_array = json_decode(json_encode($community_name), true);
    $nbh_data = array_values($nbh_array);
    // print_r($nbh_data);die;
    
    $i=0;
    foreach ($nbh_data as $val) {
        if ($val['community']) {
            $title = $val['community']; 

            $results = $wpdb->get_results(
                "SELECT * FROM $table_name where location5_name like '%".$title."%'"
            );

            //wp_neighbourhood_community table
            $nbh_community_results = $wpdb->get_results(
                "SELECT `location_name` FROM $neighbourhood_community_table WHERE location_name like '%".$title."%'"
            );

            $pp_sqft_data = $wpdb->get_results(
                "SELECT (p/c)*100 as price_avg, avgsqft as sqft_avg FROM
                (SELECT SUM(price) AS p, COUNT(price) AS c, AVG(`avg_sqft`) as avgsqft FROM (SELECT `price`, `field_3041`,
                CASE
                    WHEN  INSTR(`field_3041`, '-') > 0 THEN ( (SUBSTRING_INDEX(`field_3041`,'-', -1) + SUBSTRING_INDEX(`field_3041`,'-', 1))/2)
                    WHEN INSTR(`field_3041`, 'sqft') > 0 THEN  SUBSTRING_INDEX(`field_3041`, ' sqft', 1)
                    ELSE `field_3041`
                END AS `avg_sqft`
                 FROM `wp_wpl_properties` WHERE location5_name like '%".$title."%' AND `field_3041` IS NOT NULL AND `field_3041` != '' AND `price` >= 100000 ) vtab) s;"
            );


            $pp_sqft_data_array = json_decode(json_encode($pp_sqft_data), true);
            $pp_sqft_data_avg = array_values($pp_sqft_data_array);

            if ($pp_sqft_data_avg[0]['price_avg'] && $pp_sqft_data_avg[0]['sqft_avg']) {
                $sqft_avg = $pp_sqft_data_avg[0]['price_avg'] / $pp_sqft_data_avg[0]['sqft_avg'];
            }else{
                $sqft_avg = '';
            }
            // print_r($sqft_avg);die;

            //current average price of all properties
            $property_value_avg = $wpdb->get_results(
                "SELECT AVG(`price`) as total_price FROM $table_name WHERE location5_name like '%".$title."%';"
            );
            $property_price_avg = $property_value_avg[0]->total_price;

            //property_type_count
            $property_type_count = $wpdb->get_results(
                "SELECT COUNT(*) as `no_of_property_type` FROM $table_name WHERE location5_name like '%".$title."%' AND `property_type` = 5 GROUP BY `property_type`;"
            );
            $no_of_property_type = $property_type_count[0]->no_of_property_type;

            //single_family_count
            $single_family_count = $wpdb->get_results(
                "SELECT COUNT(*) as `no_of_single_family` FROM $table_name WHERE location5_name like '%".$title."%' AND `property_type` = 3 GROUP BY `property_type`;"
            );
            
            $no_of_single_family = $single_family_count[0]->no_of_single_family;
            
            //detached_count
            $detached_count = $wpdb->get_results(
                "SELECT COUNT(*) as `no_of_detached` FROM $table_name WHERE location5_name like '%".$title."%' AND `property_type` = 15 GROUP BY `property_type`;"
            );
            
            $no_of_detached = $detached_count[0]->no_of_detached;
            // print_r($no_of_detached);die;
            
            //business_count
            $business_count = $wpdb->get_results(
                "SELECT COUNT(*) as `no_of_business` FROM $table_name WHERE location5_name like '%".$title."%' AND `property_type` = 4 GROUP BY `property_type`;"
            );
            
            $no_of_business = $business_count[0]->no_of_business;

            //condo_apt_count
            $condo_apt_count = $wpdb->get_results(
                "SELECT COUNT(*) as `no_of_condo_apt` FROM $table_name WHERE location5_name like '%".$title."%' AND `property_type` = 17 GROUP BY `property_type`;"
            );
            
            $no_of_condo_apt = $condo_apt_count[0]->no_of_condo_apt;

            //Att/row/twnhouse_count
            $art = $wpdb->get_results(
                "SELECT COUNT(*) as `no_of_art` FROM $table_name WHERE location5_name like '%".$title."%' AND `property_type` = 19 GROUP BY `property_type`;"
            );
            
            $no_of_art = $art[0]->no_of_art;

            //industrial_count
            $industrial_count = $wpdb->get_results(
                "SELECT COUNT(*) as `no_of_industrial` FROM $table_name WHERE location5_name like '%".$title."%' AND `property_type` = 1 GROUP BY `property_type`;"
            );
            
            $no_of_industrial = $industrial_count[0]->no_of_industrial;
            
            //
            $results_last_12_months = $wpdb->get_results(
                "SELECT * FROM $table_name where location5_name like '%".$title."%' and add_date> now() - INTERVAL 12 month"
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
                
                $list_vs_selling_price = (($sold_properties_prices - $selling_price)/$selling_price)*100;
                $total_count = count( $results );
                $days_on_the_market = round(($dateDiff)/$total_count);
            }

            $location_name = $val['community'];
            $type = 'community';
            $days_on_market = $days_on_the_market;
            $list_selling_price = round($list_vs_selling_price);
            $offer_competition = '';
            $turn_over = '';
            $property_value = round($property_price_avg);
            $price_ranking = '';
            $sold = $for_sale;
            $rented = $for_rent;
            $buyers_vs_sellers = '';
            $pp_square_foot = round($sqft_avg);
            $no_single_family = $no_of_single_family;
            $no_detached = $no_of_detached;
            $no_business = $no_of_business;
            $no_condo_apt = $no_of_condo_apt;
            $att_row_twn = $no_of_art;
            $no_industrial = $no_of_industrial;
            $no_other_property_type = $no_of_property_type;

            // if (!$nbh_community_results) {
                //$wpdb->query("INSERT INTO $neighbourhood_community_table(id, location_name, type, days_on_market, list_vs_selling_price, offer_competition, turn_over, property_value, price_ranking, sold, rented, buyers_vs_sellers, pp_square_foot, no_single_family, no_detached, no_business, no_condo_apt, att_row_twn, no_industrial, no_other_property_type) VALUES(NULL, '$location_name', '$type', '$days_on_market', '$list_selling_price', '$offer_competition', '$turn_over', '$property_value', '$price_ranking', '$sold', '$rented', '$buyers_vs_sellers', '$pp_square_foot', '$no_single_family', '$no_detached', '$no_business', '$no_condo_apt', '$att_row_twn', '$no_industrial', '$no_other_property_type' )");
            // }

            ?>

            Current Date Time: <?php echo get_the_date( 'Y-m-d H:i:s' ); ?>
            <br>
            Community name: <?php echo $val['community'];?>
            <br>
            Type: <?php echo 'community';?>
            <br>    
            Days on Market: <?php echo $days_on_the_market;?>
            <br>
            List vs. Selling Price: <?php echo round($list_vs_selling_price);?>
            <br>
            Offer Competition: <?php echo 'N/A';?>
            <br>
            Turn Over: <?php echo 'N/A';?>
            <br>
            Property Value over 12mth: <?php echo round($property_price_avg);?>
            <br>
            Price Ranking: <?php echo 'N/A';?>
            <br>
            Sold: <?php echo $for_sale;?>
            <br>
            Rented: <?php echo $for_rent;?>
            <br>
            Buyers VS Sellers: <?php echo 'N/A';?>
            <br>
            Price Per Square Foot neighbourhood avg: <?php echo round($sqft_avg);?>
            <br>
            no of single family: <?php echo $no_of_single_family; ?>
            <br>
            no of detached: <?php echo $no_of_detached; ?>
            <br>
            no of business: <?php echo $no_of_business; ?>
            <br>
            no of condo apt: <?php echo $no_of_condo_apt; ?>
            <br>
            att/row/twnhouse count: <?php echo $no_of_art; ?>
            <br>
            industrial: <?php echo $no_of_industrial; ?>
            <br>
            no of other property type: <?php echo $no_of_property_type; ?>
            <br>
            <br>

            <?php
        $i++;
            //if($i==10) break;
        }
    }
    // die;
?>
