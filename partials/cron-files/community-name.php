<?php

    global $wpdb;
    $table_name = 'wp_wpl_properties';
    $neightbour_list_table_name = 'wp_neighbourhood_list';

    //get all neighbourhood name
    $neighbourhood_name = $wpdb->get_results(
        "SELECT `location5_name` FROM $table_name WHERE `location5_name` IS NOT NULL GROUP BY `location5_name`;"
    );
    

    $nbh_array = json_decode(json_encode($neighbourhood_name), true);
    $nbh_data = array_values($nbh_array);
    // print_r($nbh_data);die;
    
    $i=0;
    foreach ($nbh_data as $k => $val) {
        
        $location_name = $val['location5_name'];
        // print_r(string($location_name));die;
        $results = $wpdb->get_results(
            "SELECT `name` FROM $neightbour_list_table_name WHERE `name` = '$location_name' AND `type` = 'community';"
        );
        // print_r($results);die;
        
        $type = 'community';

        if (!$results) {
            $data = $wpdb->query("INSERT INTO $neightbour_list_table_name(id, type, name) VALUES(NULL, '$type', '$location_name' )");
        }

        ?>

        <br>
        <?php echo $k+1;?>.
        <?php echo $val['location5_name'];?>
        <?php
    $i++;
        // if($i==100) break;
    }
    // die;
?>
