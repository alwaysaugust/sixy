<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

$this->_wpl_import($this->tpl_path.'.scripts.form.user', true, ($this->wplraw ? false : true));
?>
<div class="wpl-save-search-addon" id="wpl_addon_save_searches_container">

    <div id="wpl_save_searches_form_container">

        <form id="wpl_save_searches_form" class="wpl-gen-form-wp" method="POST" onsubmit="wpl_save_search(); return false;">

            <div class="wpl-gen-form-row">
                <label for="wpl_ss_name"><?php echo __('Search Name', 'real-estate-listing-realtyna-wpl'); ?>: </label>
                <input type="text" name="wplname" id="wpl_ss_name" value="<?php echo __('My Search Name', 'real-estate-listing-realtyna-wpl'); ?>" autocomplete="off" required autofocus />
            </div>

            <?php if($this->users->is_administrator($this->user_id)): ?>
                <div class="wpl-gen-form-row">
                    <label for="wpl_ss_alias"><?php echo __('Alias', 'real-estate-listing-realtyna-wpl'); ?>: </label>
                    <input type="text" name="alias" id="wpl_ss_alias" placeholder="<?php echo __('Only lower-case characters, dashes, and numbers!', 'real-estate-listing-realtyna-wpl'); ?>" autocomplete="off" />
                </div>
            <?php endif; ?>

            <div class="wpl-gen-form-row wpl-util-right">
                <button type="submit" class="wpl-gen-btn-1" id="wpl_save_searches_submit"><?php echo __('Save', 'real-estate-listing-realtyna-wpl'); ?></button>
            </div>

            <input type="hidden" name="kind" value="<?php echo $this->kind; ?>" />
            <input type="hidden" name="token" id="wpl_save_searches_token" value="<?php echo $this->wpl_security->token(); ?>" />
            <input type="hidden" name="criteria" value="<?php echo base64_encode(json_encode($this->criteria)); ?>" />
            <input type="hidden" name="url" value="<?php echo urlencode($this->search_url); ?>" />
        </form>

        <div id="wpl_save_searches_form_show_messages" class="wpl-addon-save-search-msg"></div>

    </div>

</div>