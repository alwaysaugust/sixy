<?php
/** no direct access * */
defined('_WPLEXEC') or die('Restricted access');
$units = wpl_units::get_units(4);
?>
<div class="fanc-row">
    <label for="wpl_o_minimum_charge"><?php echo __('Minimum Charge', 'real-estate-listing-realtyna-wpl'); ?></label>
    <input class="text_box" name="option[minimum_charge]" type="text" id="wpl_o_minimum_charge" value="<?php echo isset($this->options->minimum_charge) ? $this->options->minimum_charge : '1'; ?>" />
</div>
<div class="fanc-row">
    <label for="wpl_o_default_amount"><?php echo __('Default Amount', 'real-estate-listing-realtyna-wpl'); ?></label>
    <input class="text_box" name="option[default_amount]" type="text" id="wpl_o_default_amount" value="<?php echo isset($this->options->default_amount) ? $this->options->default_amount : '10'; ?>" />
</div>
<div class="fanc-row">
    <label for="wpl_o_unit_id"><?php echo __('Currency', 'real-estate-listing-realtyna-wpl'); ?></label>
    <select class="text_box" name="option[unit_id]" id="wpl_o_unit_id">
        <?php foreach($units as $unit): ?>
        <option value="<?php echo $unit['id']; ?>" <?php if(isset($this->options->unit_id) and $this->options->unit_id == $unit['id']) echo 'selected="selected"'; ?>><?php echo __($unit['name'], 'real-estate-listing-realtyna-wpl'); ?></option>
        <?php endforeach; ?>
    </select>
</div>
<div class="fanc-row">
    <label for="wpl_o_show_charge_form"><?php echo __('Charge Form', 'real-estate-listing-realtyna-wpl'); ?></label>
    <select class="text_box" name="option[show_charge_form]" id="wpl_o_show_charge_form">
        <option value="1" <?php if(isset($this->options->show_charge_form) and $this->options->show_charge_form) echo 'selected="selected"'; ?>><?php echo __('Show', 'real-estate-listing-realtyna-wpl'); ?></option>
        <option value="0" <?php if(isset($this->options->show_charge_form) and !$this->options->show_charge_form) echo 'selected="selected"'; ?>><?php echo __('Hide', 'real-estate-listing-realtyna-wpl'); ?></option>
    </select>
</div>