<?php   
defined('_WPLEXEC') or die('Restricted access');
?>
<div id="wpl_unit_switcher_widget_cnt<?php echo $this->widget_id; ?>" class="wpl-unit-switcher-widget <?php echo $this->css_class; ?>">
    <?php echo $args['before_title'].__($this->title, 'real-estate-listing-realtyna-wpl').$args['after_title']; ?>
    <?php wpl_activity::import('unit_switcher:'.$this->layout, mt_rand(1000, 9999), array('unit_type'=>$this->unit_type)); ?>
</div>