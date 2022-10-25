<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<div class="page fixed" id="option_container">
	<div class="odd">
		<span class="maccess_name"><?php echo __('Total number of properties', 'real-estate-listing-realtyna-wpl'); ?></span>
		<input type="text" name="maccess_num_prop" id="maccess_num_prop" value="<?php echo $this->data->maccess_num_prop; ?>" />
	</div>
	<div>
		<span class="maccess_name"><?php echo __('Total number of hot properties', 'real-estate-listing-realtyna-wpl'); ?></span>
		<input type="text" name="maccess_num_hot" id="maccess_num_hot" value="<?php echo $this->data->maccess_num_hot; ?>" />
	</div>
	<div class="odd">
		<span class="maccess_name"><?php echo __('Total number of featured properties', 'real-estate-listing-realtyna-wpl'); ?></span>
		<input type="text" name="maccess_num_feat" id="maccess_num_feat" value="<?php echo $this->data->maccess_num_feat; ?>" />
	</div>
	<div>
		<span class="maccess_name"><?php echo __('Listing Restriction (PWizard)?', 'real-estate-listing-realtyna-wpl'); ?></span>
		<select id="maccess_lrestrict" onchange="wpl_other_option_show(this.value , 'maccess_lrestrict_div');">
        	<option value="0" <?php if(isset($this->data->maccess_lrestrict) and $this->data->maccess_lrestrict == 0) echo 'selected="selected"'; ?>><?php echo __("No", 'real-estate-listing-realtyna-wpl'); ?></option>
        	<option value="1" <?php if(isset($this->data->maccess_lrestrict) and $this->data->maccess_lrestrict == 1) echo 'selected="selected"'; ?>><?php echo __("Yes", 'real-estate-listing-realtyna-wpl'); ?></option>
        </select>
	</div>
	<div>
		<div id="maccess_lrestrict_div" style="display:<?php echo((isset($this->data->maccess_lrestrict) and $this->data->maccess_lrestrict == 1) ? 'block' : 'none'); ?>;" class="maccess_reddiv">
			<select id="maccess_listings" multiple="multiple" data-chosen-opt="width:100%">
				<?php
				$options = (array) explode(',', (isset($this->data->maccess_listings) ? $this->data->maccess_listings : ''));
				foreach($this->listings as $listing)
				{
					if(in_array($listing['id'], $options)) $selected = 'selected="selected"'; else $selected = '';
					echo '<option value="'.$listing['id'].'" '.$selected.'>'.__($listing['name'], 'real-estate-listing-realtyna-wpl').'</option>';
				}
				?>
			</select>
		</div>
	</div>
	<div class="odd">
		<span class="maccess_name"><?php echo __('Property Type Restriction (PWizard)?', 'real-estate-listing-realtyna-wpl'); ?></span>
		<select id="maccess_ptrestrict" onchange="wpl_other_option_show(this.value , 'ptrestrict_div');">
        	<option value="0" <?php if(isset($this->data->maccess_ptrestrict) and $this->data->maccess_ptrestrict == 0) echo 'selected="selected"'; ?>><?php echo __("No", 'real-estate-listing-realtyna-wpl'); ?></option>
        	<option value="1" <?php if(isset($this->data->maccess_ptrestrict) and $this->data->maccess_ptrestrict == 1) echo 'selected="selected"'; ?>><?php echo __("Yes", 'real-estate-listing-realtyna-wpl'); ?></option>
        </select>
	</div>
	<div class="odd">
		<div style="display:<?php echo((isset($this->data->maccess_ptrestrict) and $this->data->maccess_ptrestrict == 1) ? 'block' : 'none'); ?>;" id="ptrestrict_div" class="maccess_reddiv">
            <select id="maccess_property_types" multiple="multiple" data-chosen-opt="width:100%">
                <?php
                $options = (array) explode(',', (isset($this->data->maccess_property_types) ? $this->data->maccess_property_types : ''));
                foreach($this->property_types as $property_type)
                {
                    if(in_array($property_type['id'], $options)) $selected = 'selected="selected"'; else $selected = '';
                    echo '<option value="'.$property_type['id'].'" '.$selected.'>'.__($property_type['name'], 'real-estate-listing-realtyna-wpl').'</option>';
                }
                ?>
            </select>

        </div>
	</div>
	<div>
		<span class="maccess_name"><?php echo __('Listing Restriction (PListing)?', 'real-estate-listing-realtyna-wpl'); ?></span>
		<select id="maccess_lrestrict_plisting" onchange="wpl_other_option_show(this.value , 'maccess_lrestrict_plisting_div');">
        	<option value="0" <?php if(isset($this->data->maccess_lrestrict_plisting) and $this->data->maccess_lrestrict_plisting == 0) echo 'selected="selected"'; ?>><?php echo __("No", 'real-estate-listing-realtyna-wpl'); ?></option>
        	<option value="1" <?php if(isset($this->data->maccess_lrestrict_plisting) and $this->data->maccess_lrestrict_plisting == 1) echo 'selected="selected"'; ?>><?php echo __("Yes", 'real-estate-listing-realtyna-wpl'); ?></option>
        </select>
	</div>
	<div>
		<div id="maccess_lrestrict_plisting_div" style="display:<?php echo((isset($this->data->maccess_lrestrict_plisting) and $this->data->maccess_lrestrict_plisting == 1) ? 'block' : 'none'); ?>;" class="maccess_reddiv">
			<select id="maccess_listings_plisting" multiple="multiple" data-chosen-opt="width:100%">
				<?php
				$options = (array) explode(',', (isset($this->data->maccess_listings_plisting) ? $this->data->maccess_listings_plisting : ''));
				foreach($this->listings as $listing)
				{
					if(in_array($listing['id'], $options)) $selected = 'selected="selected"'; else $selected = '';
					echo '<option value="'.$listing['id'].'" '.$selected.'>'.__($listing['name'], 'real-estate-listing-realtyna-wpl').'</option>';
				}
				?>
			</select>
		</div>
	</div>
	<div class="odd">
		<span class="maccess_name"><?php echo __('Property Type Restriction (PListing)?', 'real-estate-listing-realtyna-wpl'); ?></span>
		<select id="maccess_ptrestrict_plisting" onchange="wpl_other_option_show(this.value , 'ptrestrict_plisting_div');">
        	<option value="0" <?php if(isset($this->data->maccess_ptrestrict_plisting) and $this->data->maccess_ptrestrict_plisting == 0) echo 'selected="selected"'; ?>><?php echo __("No", 'real-estate-listing-realtyna-wpl'); ?></option>
        	<option value="1" <?php if(isset($this->data->maccess_ptrestrict_plisting) and $this->data->maccess_ptrestrict_plisting == 1) echo 'selected="selected"'; ?>><?php echo __("Yes", 'real-estate-listing-realtyna-wpl'); ?></option>
        </select>
	</div>
	<div class="odd">
		<div style="display:<?php echo((isset($this->data->maccess_ptrestrict_plisting) and $this->data->maccess_ptrestrict_plisting == 1) ? 'block' : 'none'); ?>;" id="ptrestrict_plisting_div" class="maccess_reddiv">
            <select id="maccess_property_types_plisting" multiple="multiple" data-chosen-opt="width:100%">
                <?php
                $options = (array) explode(',', (isset($this->data->maccess_property_types_plisting) ? $this->data->maccess_property_types_plisting : ''));
                foreach($this->property_types as $property_type)
                {
                    if(in_array($property_type['id'], $options)) $selected = 'selected="selected"'; else $selected = '';
                    echo '<option value="'.$property_type['id'].'" '.$selected.'>'.__($property_type['name'], 'real-estate-listing-realtyna-wpl').'</option>';
                }
                ?>
            </select>

        </div>
	</div>
	<div>
		<span class="maccess_name"><?php echo __('Listing Restriction (PShow)?', 'real-estate-listing-realtyna-wpl'); ?></span>
		<select id="maccess_lrestrict_pshow" onchange="wpl_other_option_show(this.value , 'maccess_lrestrict_pshow_div');">
        	<option value="0" <?php if(isset($this->data->maccess_lrestrict_pshow) and $this->data->maccess_lrestrict_pshow == 0) echo 'selected="selected"'; ?>><?php echo __("No", 'real-estate-listing-realtyna-wpl'); ?></option>
        	<option value="1" <?php if(isset($this->data->maccess_lrestrict_pshow) and $this->data->maccess_lrestrict_pshow == 1) echo 'selected="selected"'; ?>><?php echo __("Yes", 'real-estate-listing-realtyna-wpl'); ?></option>
        </select>
	</div>
	<div>
		<div id="maccess_lrestrict_pshow_div" style="display:<?php echo((isset($this->data->maccess_lrestrict_pshow) and $this->data->maccess_lrestrict_pshow == 1) ? 'block' : 'none'); ?>;" class="maccess_reddiv">
			<select id="maccess_listings_pshow" multiple="multiple" data-chosen-opt="width:100%">
				<?php
				$options = (array) explode(',', (isset($this->data->maccess_listings_pshow) ? $this->data->maccess_listings_pshow : ''));
				foreach($this->listings as $listing)
				{
					if(in_array($listing['id'], $options)) $selected = 'selected="selected"'; else $selected = '';
					echo '<option value="'.$listing['id'].'" '.$selected.'>'.__($listing['name'], 'real-estate-listing-realtyna-wpl').'</option>';
				}
				?>
			</select>
		</div>
	</div>
	<div class="odd">
		<span class="maccess_name"><?php echo __('Property Type Restriction (PShow)?', 'real-estate-listing-realtyna-wpl'); ?></span>
		<select id="maccess_ptrestrict_pshow" onchange="wpl_other_option_show(this.value , 'ptrestrict_pshow_div');">
        	<option value="0" <?php if(isset($this->data->maccess_ptrestrict_pshow) and $this->data->maccess_ptrestrict_pshow == 0) echo 'selected="selected"'; ?>><?php echo __("No", 'real-estate-listing-realtyna-wpl'); ?></option>
        	<option value="1" <?php if(isset($this->data->maccess_ptrestrict_pshow) and $this->data->maccess_ptrestrict_pshow == 1) echo 'selected="selected"'; ?>><?php echo __("Yes", 'real-estate-listing-realtyna-wpl'); ?></option>
        </select>
	</div>
	<div class="odd">
		<div style="display:<?php echo((isset($this->data->maccess_ptrestrict_pshow) and $this->data->maccess_ptrestrict_pshow == 1) ? 'block' : 'none'); ?>;" id="ptrestrict_pshow_div" class="maccess_reddiv">
            <select id="maccess_property_types_pshow" multiple="multiple" data-chosen-opt="width:100%">
                <?php
                    $options = (array) explode(',', (isset($this->data->maccess_property_types_pshow) ? $this->data->maccess_property_types_pshow : ''));
                    foreach($this->property_types as $property_type)
                    {
                        if(in_array($property_type['id'], $options)) $selected = 'selected="selected"'; else $selected = '';
                        echo '<option value="'.$property_type['id'].'" '.$selected.'>'.__($property_type['name'], 'real-estate-listing-realtyna-wpl').'</option>';
                    }
                ?>
            </select>

        </div>
	</div>
	<?php if(wpl_global::check_addon('membership')): ?>
    <div>
        <span class="maccess_name"><?php echo __('Receive Contact Emails Directly', 'real-estate-listing-realtyna-wpl'); ?></span>
        <select  id="maccess_direct_contact" onchange="wpl_toggle_direct_contact_users()">
            <option value="1" <?php if(isset($this->data->maccess_direct_contact) and $this->data->maccess_direct_contact == 1) echo 'selected="selected"'; ?> >Yes</option>
            <option value="0" <?php if(isset($this->data->maccess_direct_contact) and $this->data->maccess_direct_contact == 0) echo 'selected="selected"'; ?> >No</option>
        </select>
    </div>
    <div id="maccess_direct_contact_users" style="display:<?php if(isset($this->data->maccess_direct_contact) and $this->data->maccess_direct_contact == 0) echo 'block;'; else echo 'none;'; ?>">
        <span class="maccess_name"><?php echo __('Substitute Recipient', 'real-estate-listing-realtyna-wpl'); ?></span>
        <select id="maccess_direct_contact_user_id">
            <?php foreach($this->users as $user): ?>
            <option <?php if(isset($this->data->maccess_direct_contact_user_id) and $this->data->maccess_direct_contact_user_id == $user->id) echo 'selected="selected"'; ?> value="<?php echo $user->id; ?>" > <?php echo $user->first_name; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="odd">
        <span class="maccess_name"><?php echo __('Force Profile Completion', 'real-estate-listing-realtyna-wpl'); ?></span>
        <select id="maccess_fpc">
            <option value="0" <?php if(isset($this->data->maccess_fpc) and $this->data->maccess_fpc == 0) echo 'selected="selected"'; ?>><?php echo __("No", 'real-estate-listing-realtyna-wpl'); ?></option>
            <option value="1" <?php if(isset($this->data->maccess_fpc) and $this->data->maccess_fpc == 1) echo 'selected="selected"'; ?>><?php echo __("Yes", 'real-estate-listing-realtyna-wpl'); ?></option>
        </select>
    </div>
	<?php endif; ?>
</div>