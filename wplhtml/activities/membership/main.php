<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

_wpl_import('libraries.payments.payments');
_wpl_import('libraries.addon_membership');

/** activity class **/
class wpl_activity_main_membership extends wpl_activity
{
    public $tpl_path = 'views.activities.membership.tmpl';
    
	public function start($layout, $params)
	{
	    
	    die;
		/** include layout **/
		$layout_path = _wpl_import($layout, true, true);
		include $layout_path;
	}
}