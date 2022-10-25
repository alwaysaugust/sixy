<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

$prefilter_fields = wpl_db::select("SELECT * FROM `#__wpl_dbst` WHERE 1 AND `enabled`>='1' AND `kind`='0' AND `type` NOT IN ('addon_video','attachments','gallery','separator','googlemap','meta_desc','meta_key','openhouse_dates','ptcategory','rooms','more_details','url') AND `searchmod`='1' ORDER BY `category` ASC, `index` ASC", 'loadObjectList');
$existings_prefilters = json_decode($this->user_data->maccess_rets_prefilters, true);
?>
<div class="page fixed" id="option_container">
    <div class="fanc-row">
        <div class="prow">
            <label for="wpl_rets_prefilters_field"><?php echo __('Prefilter', 'real-estate-listing-realtyna-wpl'); ?></label>
            <select id="wpl_rets_prefilters_field" onchange="wpl_rets_prefilter_add_criteria(this.value);">
                <option value="">-----</option>
                <?php foreach($prefilter_fields as $prefilter_field): ?>
                <option value="<?php echo $prefilter_field->id; ?>"><?php echo __($prefilter_field->name, 'real-estate-listing-realtyna-wpl'); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="prow">
            <div id="wpl_rets_prefilter_criterias">
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
jQuery(document).ready(function()
{
    <?php
    if(is_array($existings_prefilters) and count($existings_prefilters))
    {
        foreach($existings_prefilters as $column=>$existings_prefilter) echo 'wpl_rets_prefilter_add_criteria('.$existings_prefilter['id'].', "'.$existings_prefilter['operator'].'", '.json_encode(explode(',', $existings_prefilter['values'])).', true);';
    }
    ?>
});
</script>