<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

?>

<ul class="wpl-save-searches-container" style=" list-style:none;padding: 0; margin: 0;text-align: left;">
    <?php foreach($property_ids as $property_id): $property_data = wpl_property::get_property_raw_data($property_id); ?>
        <?php
        $bedroom = isset($property_data['bedrooms']) ? $property_data['bedrooms'].' '.__('bed(s)','real-estate-listing-realtyna-wpl').',' : '';
        $bathroom = isset($property_data['bathrooms']) ? $property_data['bathrooms'].' '.__('bath(s)','real-estate-listing-realtyna-wpl') : '';
        $wpl_properties = array('current' => wpl_property::full_render($property_id));
        ?>
        <li class="wpl-save-searches-listing" style="line-height: 20px;margin-bottom: 50px;font-size: 14px;vertical-align:top;">
            <table style="width:100%">
                <tr>
                    <td width="100" style="vertical-align:top;padding-right:20px;">
                        <?php wpl_activity::load_position('wpl_property_listing_image_email', array('wpl_properties'=>$wpl_properties)); ?>
                    </td>
                    <td>
                        <div class="address"><a style=" text-decoration: none;color:#29a9df;font-size:16px;" href="<?php echo wpl_property::get_property_link($property_data); ?>"><?php echo wpl_property::generate_location_text($property_data); ?></a></div>
                        <div class="price" style="font-weight: bold;font-size:20px;color:#333;margin:10px 0;"><?php echo $wpl_properties['current']['materials']['price']['value']; ?></div>
                        <div class="title"><?php echo wpl_property::update_property_title($property_data); ?></div>
                        <div class="features"><?php echo $bedroom.$bathroom; ?></div>
                        <a style=" display: block;background:#29a9df;padding:5px 10px;color:#fff;border-radius:5px;text-align: center;font-weight: normal;font-size:16px;text-decoration:none;text-transform: uppercase;margin-top:10px;width:100px" href="<?php echo wpl_property::get_property_link($property_data); ?>" class="btn"><?php echo __('More info','real-estate-listing-realtyna-wpl'); ?></a>
                    </td>
                </tr>
            </table>
        </li>
    <?php endforeach; ?>
</ul>