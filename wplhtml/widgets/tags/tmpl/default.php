<?php   
defined('_WPLEXEC') or die('Restricted access');

/** Cache File **/
$cache_file = $this->wplcache->path('widgets'.DS.$this->widget_uq_name.'.json');

/** Check if cache file is valid **/
if($this->wplcache->valid($cache_file))
{
    $JSON = $this->wplcache->read($cache_file);
    $tags = json_decode($JSON, true);
}
else
{
    $tag_fields = wpl_flex::get_tag_fields($this->kind);
    $tags = array();
    
    $conditions = array('sf_select_confirmed'=>1, 'sf_select_finalized'=>1, 'sf_select_deleted'=>0, 'sf_select_expired'=>0, 'sf_select_kind'=>$this->kind);
    $where = wpl_db::create_query($conditions);
    
    foreach($tag_fields as $tag_field)
    {
        $query = "SELECT COUNT(`id`) FROM `#__wpl_properties` WHERE 1 ".$where." AND `".$tag_field->table_column."`='1'";
        
        $tags[$tag_field->id] = array();
        $tags[$tag_field->id]['data'] = (array) $tag_field;
        $tags[$tag_field->id]['count'] = wpl_db::select($query, 'loadResult');
    }
    
    $JSON = json_encode($tags);
    
    /** Write to Cache File **/
    $this->wplcache->write($cache_file, $JSON);
}

/** Load Styles **/
$this->tags_styles($tags);
?>
<div id="wpl_tags_cnt<?php echo $this->widget_id; ?>" class="wpl-tags-widget <?php echo $this->css_class; ?>">

    <?php echo $args['before_title'].__($this->title, 'real-estate-listing-realtyna-wpl').$args['after_title']; ?>
    
    <ul class="wpl-tags-wp<?php echo ($this->show_count ? '' : ' wpl-tags-without-count'); ?>">
        <?php foreach($tags as $tag): $options = json_decode($tag['data']['options'], true); ?>
        <?php if(isset($options['widget']) and $options['widget'] == 0) continue; ?>
        <?php if(trim($this->category) != '' and (!isset($options['category']) or (isset($options['category']) and $options['category'] != $this->category))) continue; ?>
        <li class="<?php echo $tag['data']['table_column']; ?>">
            <a href="<?php echo $this->get_link($tag['data']['table_column'], 1); ?>">
                <?php echo ($this->show_count ? sprintf(__('%s <span class="wpl-tags-item-count">%s</span>', 'real-estate-listing-realtyna-wpl'), $tag['data']['name'], $tag['count']) : __($tag['data']['name'], 'real-estate-listing-realtyna-wpl')); ?>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>

</div>