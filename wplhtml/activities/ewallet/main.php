<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

_wpl_import('libraries.payments.payments');

/** activity class **/
class wpl_activity_main_ewallet extends wpl_activity
{
    public $tpl_path = 'views.activities.ewallet.tmpl';
    
	public function start($layout, $params)
	{
		/** include layout **/
		$layout_path = _wpl_import($layout, true, true);
		include $layout_path;
	}
}