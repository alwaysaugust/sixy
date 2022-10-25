<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<script id="wpl-rets-prefilter-criteria" type="text/x-handlebars-template">
    <div class="wpl-rets-prefilter-criteria-wp" id="wpl_rets_prefilter_criteria_wp_{{field.id}}" data-column="{{field.table_column}}">
        <span class="wpl-rets-prefilter-criteria-remove action-btn icon-recycle" data-id="{{field.id}}" data-column="{{field.table_column}}"></span>
        <input type="hidden" id="rets_prefilter[{{field.table_column}}][id]" value="{{field.id}}" />
        <input type="hidden" class="wpl-rets-prefilter-criteria-remove-{{field.table_column}}" id="rets_prefilter[{{field.table_column}}][removed]" value="0" />
        <label>{{field.name}}</label>
        {{#if operators}}
        <span class="wpl-rets-prefilter-criteria-operators">
            <select id="rets_prefilter[{{field.table_column}}][operator]">
                {{{wploptions operators selected_operator}}}
            </select>
        </span>
        {{/if}}
        
        <span class="wpl-rets-prefilter-criteria-options">
        {{#if options}}
            <select id="rets_prefilter[{{field.table_column}}][values]" multiple="multiple" data-chosen-opt="width:40%">
                {{{wploptions options values}}}
            </select>
        {{else}}
            <input type="text" id="rets_prefilter[{{field.table_column}}][values]" value="{{values}}" />
        {{/if}}
        </span>
    </div>
</script>
<script type="text/javascript">
jQuery(document).ajaxComplete(function()
{
    jQuery('#access_rets').off('change.rets').on('change.rets', function()
    {
        if(jQuery('#access_rets').is(':checked')) jQuery('#wpl_edit_user_rets_tab').removeClass('wpl-util-hidden');
        else jQuery('#wpl_edit_user_rets_tab').addClass('wpl-util-hidden');
    });
    
    jQuery('.wpl-rets-prefilter-criteria-remove').off('click').on('click', function()
    {
        var field_id = jQuery(this).data('id');
        var column = jQuery(this).data('column');
        
        wpl_rets_prefilter_criteria_remove(field_id, column);
    });
});

jQuery(document).ready(function()
{
    Handlebars.registerHelper('wploptions', function(options, selected)
    {
        var output = '';
        for(var i = 0, len = options.length; i < len; i++)
        {
            var option = '<option value="' + options[i].key+'"';
            if(typeof selected === 'string' && selected.toLowerCase() == options[i].key.toLowerCase()) option += ' selected="selected"';
            if(selected !== null && typeof selected === 'object')
            {
                var key;
                for(key in selected)
                {
                    if(options[i].key === selected[key]) option += ' selected="selected"';
                }
            }
            
            option += '>'+ Handlebars.Utils.escapeExpression(options[i].name) + '</option>';
            output += option;
        }

        return new Handlebars.SafeString(output);
    });
});

function wpl_rets_prefilter_add_criteria(field_id, operator, values, onload)
{
    // Field ID is empty
    if(!field_id) return false;
    
    if(typeof onload === 'undefined') onload = false;
    if(wplj("#wpl_rets_prefilter_criteria_wp_"+field_id).length)
    {
        var column = wplj("#wpl_rets_prefilter_criteria_wp_"+field_id).data('column');
        
        wplj(".wpl-rets-prefilter-criteria-remove-"+column).val('0');
        wplj("#wpl_rets_prefilter_criteria_wp_"+field_id).show();
        
        return false;
    }
    
    var ajax_loader_element = '#wpl_rets_prefilters_field_chosen';
    
    /** Show AJAX loader **/
    if(!onload) var wpl_ajax_loader = Realtyna.ajaxLoader.show(ajax_loader_element, 'tiny', 'rightOut');
    
    wplj.when(wpl_rets_prefilter_get_field_data(field_id))
    .fail(function()
    {
        /** Remove AJAX loader **/
        if(!onload) Realtyna.ajaxLoader.hide(wpl_ajax_loader);
    })
    .done(function(response)
    {
        if(response.success)
        {
            var criteria = Handlebars.compile(wplj("#wpl-rets-prefilter-criteria").html())
            ({
                field: response.data.field,
                operators: response.data.operators,
                selected_operator: operator,
                options: response.data.options,
                values: values
            });
        
            wplj("#wpl_rets_prefilter_criterias").append(criteria);
        }
        else
        {
        }
        
        /** Remove AJAX loader **/
        if(!onload) Realtyna.ajaxLoader.hide(wpl_ajax_loader);
    });
}

function wpl_rets_prefilter_get_field_data(field_id)
{
    var request_str = 'wpl_format=b:flex:ajax&wpl_function=get_field_options&id='+field_id+'&_wpnonce=<?php echo wpl_security::create_nonce('wpl_flex'); ?>';

    return wplj.ajax(
    {
        type: "POST",
        url: '<?php echo wpl_global::get_full_url(); ?>',
        data: request_str,
        dataType: 'JSON',
        cache: false,
        success: function(data)
        {
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
        }
    });
}

function wpl_rets_prefilter_criteria_remove(field_id, column)
{
    wplj(".wpl-rets-prefilter-criteria-remove-"+column).val('1');
    wplj("#wpl_rets_prefilter_criteria_wp_"+field_id).hide();
    
    return false;
}
</script>