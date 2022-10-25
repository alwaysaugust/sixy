<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/** set params **/
$wpl_properties = isset($params['wpl_properties']) ? $params['wpl_properties'] : array();
$property_id = isset($wpl_properties['data']['id']) ? $wpl_properties['data']['id'] : NULL;

/** get image params **/
$image_width = isset($params['image_width']) ? $params['image_width'] : 230;
$image_height = isset($params['image_height']) ? $params['image_height'] : 350;
$image_class = isset($params['image_class']) ? $params['image_class'] : '';
$category = (isset($params['category']) and trim($params['category']) != '') ? $params['category'] : '';
$resize = (isset($params['resize']) and trim($params['resize']) != '') ? $params['resize'] : 1;
$rewrite = (isset($params['rewrite']) and trim($params['rewrite']) != '') ? $params['rewrite'] : 0;
$watermark = (isset($params['watermark']) and trim($params['watermark']) != '') ? $params['watermark'] : 0;

/** render gallery **/
$raw_gallery = isset($wpl_properties['items']['gallery']) ? $wpl_properties['items']['gallery'] : array();

// Filter images by category
if(trim($category) != '') $raw_gallery = $this->categorize($raw_gallery, $category);

$gallery = wpl_items::render_gallery($raw_gallery, wpl_property::get_blog_id($property_id));
$index = 0;

if(count($gallery) && isset($gallery[$index]['url']))
{
    $image_url = $gallery[$index]['url'];
    if($resize and $image_width and $image_height and $gallery[$index]['category'] != 'external')
    {
        /** set resize method parameters **/
        $params = array();
        $params['image_name'] = $gallery[$index]['raw']['item_name'];
        $params['image_parentid'] = $gallery[$index]['raw']['parent_id'];
        $params['image_parentkind'] = $gallery[$index]['raw']['parent_kind'];
        $params['image_source'] = $gallery[$index]['path'];

        /** resize image if does not exist **/
        $image_url = wpl_images::create_gallery_image($image_width, $image_height, $params, $watermark, $rewrite);
    }
    
    echo htmlentities('<div class="wpl_gallery_images" style="width: '.$image_width.'px; height: '.$image_height.'px;"><img src="'.$image_url.'" class="'.$image_class.'" width="'.$image_width.'" height="'.$image_height.'" /></div>');
}