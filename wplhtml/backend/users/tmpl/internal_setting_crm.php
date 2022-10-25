<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
_wpl_import('libraries.addon_crm');
?>
<div class="page fixed" id="option_container">
    <div class="fanc-row">
        <div class="mark-controls">
            <input type="button" class="wpl-button button-2" value="<?php echo __('Toggle', 'real-estate-listing-realtyna-wpl'); ?>" onclick="rta.util.checkboxes.toggle(wpl_slide_container_id_crm);" />
            <input type="button" class="wpl-button button-2" value="<?php echo __('All', 'real-estate-listing-realtyna-wpl'); ?>" onclick="rta.util.checkboxes.selectAll(wpl_slide_container_id_crm);" />
            <input type="button" class="wpl-button button-2" value="<?php echo __('None', 'real-estate-listing-realtyna-wpl'); ?>" onclick="rta.util.checkboxes.deSelectAll(wpl_slide_container_id_crm);" />
        </div>
        <div class="access-checkbox-wp">
            <?php
            $crm = new wpl_addon_crm($this->user_data->id);
            foreach($crm->access as $access)
            {
                echo '<div class="checkbox-wp"><input type="checkbox" id="maccess_crm' . $access['id'] . '" value="' . $access['value'] . '"' . ($access['value'] == 1 ? 'checked="checked"' : '');
                echo ' /> <label for="maccess_crm' . $access['id'] . '">' . __($access['title'], 'real-estate-listing-realtyna-wpl') . '</label></div>';
            }
            ?>
        </div>
    </div>
</div>