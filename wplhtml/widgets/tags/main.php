<?php
/** no direct access * */
defined('_WPLEXEC') or die('Restricted access');

class wpl_tags_widget extends wpl_widget
{
    public $wpl_tpl_path = 'widgets.tags.tmpl';
    public $wpl_backend_form = 'widgets.tags.form';
    public $title;
    public $wpltarget;
    public $kind;
    public $show_count;
    public $category;

    /**
     * @var wpl_cache
     */
    public $wplcache;
    public $current_property;

    public function __construct()
    {
        parent::__construct('wpl_tags_widget', __('(WPL) Tags', 'real-estate-listing-realtyna-wpl'), array('description'=>__('Shows Listing Tags', 'real-estate-listing-realtyna-wpl')));
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
        
        $this->widget_uq_name = 'wpltag'.$this->widget_id;
        
        echo $args['before_widget'];

        $this->title = apply_filters('widget_title', $instance['title']);
        
        $this->wpltarget = isset($instance['wpltarget']) ? $instance['wpltarget'] : 0;
        $this->data = $instance['data'];
        
        $this->kind = isset($this->data['kind']) ? $this->data['kind'] : 0;
        $this->css_class = isset($this->data['css_class']) ? $this->data['css_class'] : '';
        $this->show_count = isset($this->data['show_count']) ? $this->data['show_count'] : 1;
        $this->category = isset($this->data['category']) ? $this->data['category'] : '';
        
        /** WPL Cache Instance **/
        $this->wplcache = wpl_global::get_wpl_cache();
        
        $layout = 'widgets.tags.tmpl.' . $instance['layout'];
        $layout = _wpl_import($layout, true, true);

        if(!wpl_file::exists($layout)) $layout = _wpl_import('widgets.tags.tmpl.default', true, true);
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
            $instance = array('title'=>__('Listing Tags', 'real-estate-listing-realtyna-wpl'), 'layout'=>'default.php',
                'data'=>array(
                    'kind'=>0,
                    'css_class'=>'',
                    'show_count'=>'1'
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
        /** Clear Cache File **/
        $this->widget_id = $this->number;
        if($this->widget_id < 0) $this->widget_id = abs($this->widget_id)+1000;
        
        $this->widget_uq_name = 'wpltag'.$this->widget_id;
        
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
     * @param int $value
     * @return string
     */
    public function get_link($key, $value = 1)
    {
        $url = wpl_property::get_property_listing_link($this->wpltarget);
        $url = wpl_global::add_qs_var('sf_select_'.$key, $value, $url);
        
        return $url;
    }

    /**
     * @param array $tags
     */
    public function tags_styles($tags = NULL)
    {
        static $loaded = array();
        
        if(isset($loaded[$this->widget_id])) return;
        if(!isset($loaded[$this->widget_id])) $loaded[$this->widget_id] = true;
        
        if(is_null($tags))
        {
            $kind = $this->current_property['data']['kind'];
            $tags = wpl_flex::get_tag_fields($kind);
        }
        
        $styles_str = '';
        foreach($tags as $tag)
        {
            $options = json_decode($tag['data']['options'], true);
            if(!$options['widget']) continue;
            
            $styles_str .= '.wpl-tags-wp .'.$tag['data']['table_column'].'{background-color: #'.trim($options['color'], '# ').';} .wpl-tags-wp .'.$tag['data']['table_column'].' a{color: #'.trim($options['text_color'], '# ').'}';
        }
        
        $wplhtml = wpl_html::getInstance();
        $wplhtml->set_footer('<style type="text/css">'.$styles_str.'</style>');
    }
}