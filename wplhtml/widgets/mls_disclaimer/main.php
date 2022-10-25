<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/**
 * @author Howard R. <howard@realtyna.com>
 * WPL (MLS Disclaimer) Widge
 */
class wpl_mls_disclaimer_widget extends wpl_widget
{
	public $wpl_tpl_path = 'widgets.mls_disclaimer.tmpl';
	public $wpl_backend_form = 'widgets.mls_disclaimer.form';
	public $title;
    
	public function __construct()
	{
        parent::__construct('wpl_mls_disclaimer_widget', __('(WPL) MLS Disclaimer', 'real-estate-listing-realtyna-wpl'), array('description'=>__('Show MLS disclaimer', 'real-estate-listing-realtyna-wpl')));
	}

    /**
     * @param array $args
     * @param array $instance
     */
	public function widget($args, $instance)
	{
        $this->widget_id = $this->number;
        if($this->widget_id < 0) $this->widget_id = abs($this->widget_id)+1000;

        // Fix Widget ID in some cases
        if($this->widget_id === false) $this->widget_id = mt_rand(100, 999);
        
        $this->widget_uq_name = 'wplmdis'.$this->widget_id;
        
		echo $args['before_widget'];

        $this->title = apply_filters('widget_title', $instance['title']);
        $this->data = $instance['data'];
		$this->css_class = isset($this->data['css_class']) ? $this->data['css_class'] : '';

		$layout = 'widgets.mls_disclaimer.tmpl.' . $instance['layout'];
        $layout = _wpl_import($layout, true, true);

        if(!wpl_file::exists($layout)) $layout = _wpl_import('widgets.mls_disclaimer.tmpl.default', true, true);
        if(wpl_file::exists($layout)) require $layout;
        else echo __('Widget Layout Not Found!', 'real-estate-listing-realtyna-wpl');
        
		echo $args['after_widget'];
	}

    /**
     * @param array $instance
     * @return string|void
     */
	public function form($instance)
	{
		$this->widget_id = $this->number;
        
        /** Set up some default widget settings. **/
        if(!isset($instance['layout']))
        {
            $instance = array('title'=>__('Disclaimer', 'real-estate-listing-realtyna-wpl'), 'layout'=>'default',
                'data'=>array(
                    'css_class'=>'',
            ));
			
			$defaults = array();
            $instance = wp_parse_args((array) $instance, $defaults);
        }
        
        $path = _wpl_import($this->wpl_backend_form, true, true);

        ob_start();
        include $path;
        echo $output = ob_get_clean();
	}

    /**
     * @param array $new_instance
     * @param array $old_instance
     * @return array
     */
	public function update($new_instance, $old_instance)
	{
		$instance = array();
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['layout'] = $new_instance['layout'];
		$instance['data'] = (array) $new_instance['data'];

		return $instance;
	}
}