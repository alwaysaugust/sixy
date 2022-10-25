<?php
/** no direct access * */
defined('_WPLEXEC') or die('Restricted access');

class wpl_summary_widget extends wpl_widget
{
    public $wpl_tpl_path = 'widgets.summary.tmpl';
    public $wpl_backend_form = 'widgets.summary.form';
    public $title;
    public $wpltarget;
    public $kind;
    public $listing;
    public $filter;

    /**
     * @var wpl_cache
     */
    public $wplcache;

    public function __construct()
    {
        parent::__construct('wpl_summary_widget', __('(WPL) Summary', 'real-estate-listing-realtyna-wpl'), array('description'=>__('Shows listings summary based on a certain Listing Type.', 'real-estate-listing-realtyna-wpl')));
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
        
        $this->widget_uq_name = 'wplsum'.$this->widget_id;
        
        $widget_id = $this->widget_id;
        $this->css_class = isset($instance['data']['css_class']) ? $instance['data']['css_class'] : '';
        
        echo $args['before_widget'];

        $this->title = apply_filters('widget_title', $instance['title']);
        
        $this->wpltarget = isset($instance['wpltarget']) ? $instance['wpltarget'] : 0;
        $this->data = $instance['data'];
        
        $this->kind = isset($this->data['kind']) ? $this->data['kind'] : 0;
        $this->listing = isset($this->data['listing']) ? $this->data['listing'] : NULL;
        $this->filter = isset($this->data['filter']) ? $this->data['filter'] : NULL;
        
        /** WPL Cache Instance **/
        $this->wplcache = wpl_global::get_wpl_cache();
        
        $layout = 'widgets.summary.tmpl.' . $instance['layout'];
        $layout = _wpl_import($layout, true, true);

        if(!wpl_file::exists($layout)) $layout = _wpl_import('widgets.summary.tmpl.default', true, true);

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
            $instance = array('title'=>__('Summary', 'real-estate-listing-realtyna-wpl'), 'layout'=>'default.php',
                'data'=>array(
                    'kind'=>0,
                    'listing'=>NULL,
                    'filter'=>NULL
            ));
			
			$defaults = array();
            $instance = wp_parse_args((array) $instance, $defaults);
        }
        
        $this->kind = isset($instance['data']['kind']) ? $instance['data']['kind'] : 0;
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
        /** Clear Cache File **/
        $this->widget_id = $this->number;
        if($this->widget_id < 0) $this->widget_id = abs($this->widget_id)+1000;
        
        $this->widget_uq_name = 'wplsum'.$this->widget_id;
        
        $this->wplcache = wpl_global::get_wpl_cache();
        $this->wplcache->delete($this->wplcache->path('widgets'.DS.$this->widget_uq_name.'.json'));
        
        $instance = array();
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['layout'] = $new_instance['layout'];
        $instance['wpltarget'] = $new_instance['wpltarget'];
        $instance['data'] = (array) $new_instance['data'];
        
        return $instance;
    }

    /**
     * @param string $key
     * @param string $value
     * @return string
     */
    public function get_link($key, $value)
    {
        $url = wpl_property::get_property_listing_link($this->wpltarget);
        $url = wpl_global::add_qs_var('sf_select_listing', $this->listing, $url);
        $url = wpl_global::add_qs_var('sf_select_'.$key, $value, $url);
        
        return $url;
    }
}