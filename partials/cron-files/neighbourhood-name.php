<?php

    global $wpdb;
    $table_name = 'wp_wpl_properties';
    $neightbour_list_table_name = 'wp_neighbourhood_list';

    //get all neighbourhood name
    $neighbourhood_name = $wpdb->get_results(
        "SELECT `location4_name` FROM $table_name WHERE `location4_name` IS NOT NULL AND `location4_name` != '.'  GROUP BY `location4_name`;"
    );
    

    $nbh_array = json_decode(json_encode($neighbourhood_name), true);
    $nbh_data = array_values($nbh_array);
    
    $i=0;
    foreach ($nbh_data as $k => $val) {
        
        $location_name = $val['location4_name'];
        $results = $wpdb->get_results(
            "SELECT `name` FROM $neightbour_list_table_name WHERE `name` = '$location_name' AND `type` = 'neighbourhood';"
        );
        
        $type = 'neighbourhood';

        if (!$results) {
            $data = $wpdb->query("INSERT INTO $neightbour_list_table_name(id, type, name) VALUES(NULL, '$type', '$location_name' )");
            // print_r($data);die;
        }

        ?>

        <br>
        <?php echo $k+1;?>.
        <?php echo $val['location4_name'];?>
        <?php
    $i++;
        //if($i==500) break;
    }
    // die;
?>
