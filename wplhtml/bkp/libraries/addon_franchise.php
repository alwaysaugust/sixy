<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

_wpl_import('libraries.sql_parser.PHPSQLParser');
_wpl_import('libraries.sql_parser.PHPSQLCreator');
_wpl_import('libraries.addon_membership');

/**
 * Franchise Library
 * @author Howard <howard@realtyna.com>
 * @package Franchise Add-on
 */
class wpl_addon_franchise
{
    /**
     * WPL tables with shared options
     * @var array
     */
    private static $tables = NULL;
    
    /**
     * WPL tables with primary key option
     * @var array
     */
    private static $primary_keys = NULL;
    
    /**
     * Franchise Settings of Current child website
     * @var array
     */
    public static $fs_settings = NULL;

    /**
     * WPL Database Class
     * @var wpl_db
     */
    public $db;
    
    /**
     * @author Howard <howard@realtyna.com>
     */
	public function __construct()
	{
        $this->db = new wpl_db();
	}
    
    public function cronjob()
    {
        $tables = $this->db->select('SHOW TABLES');
        $wpl_tables = array();
        $database = $this->db->get_DBO();
        
		foreach($tables as $table_name=>$table)
		{
			if(strpos($table_name, '_wpl_') === false) continue;
            $table_name = str_replace($database->base_prefix, '', $table_name);
            
			$wpl_tables[$table_name] = $table_name;
		}
        
        $fs_tables = $this->tables();
        
        foreach($wpl_tables as $wpl_table)
        {
            if(!isset($fs_tables[$wpl_table]) or (isset($fs_tables[$wpl_table]) and $fs_tables[$wpl_table] != '1')) continue;
            if($this->db->columns($wpl_table, 'blog_id')) continue;
            
            $this->db->q("ALTER TABLE `#__$wpl_table` ADD `blog_id` INT(10) NOT NULL DEFAULT '1'");
            
            if($this->db->get('`primary_key`', 'wpl_addon_franchise_tables', 'table', $wpl_table))
            {
                $this->db->q("ALTER TABLE `#__$wpl_table` DROP PRIMARY KEY, CHANGE `id` `id` INT(11) NOT NULL");
                $this->db->q("ALTER TABLE `#__$wpl_table` ADD PRIMARY KEY (`id`, `blog_id`)");
                $this->db->q("ALTER TABLE `#__$wpl_table` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT");

                // Add extra indexes for main table
                if($wpl_table == 'wpl_properties')
                {
                    $this->db->q("ALTER TABLE `#__wpl_properties` ADD INDEX blog_id (`blog_id`, `source_blog_id`)");
                    $this->db->q("ALTER TABLE `#__wpl_properties` ADD INDEX franchise_index (`blog_id`, `source_blog_id`, `kind`)");
                    $this->db->q("ALTER TABLE `#__wpl_properties` ADD INDEX franchise_index_source_blog_id (`source_blog_id`, `kind`)");
                }
            }
        }
    }
    
    public function tables($force = false)
    {
        // Get from static cache
        if(is_array(self::$tables) and !$force) return self::$tables;
        
        $results = $this->db->select("SELECT * FROM `#__wpl_addon_franchise_tables`");
        
        $tables = array();
        foreach($results as $result) $tables[$result->table] = $result->shared;
        
        // Set to static cache
        self::$tables = $tables;
        return $tables;
    }
    
    public function primary_keys()
    {
        // Get from static cache
        if(is_array(self::$primary_keys)) return self::$primary_keys;
        
        $results = $this->db->select("SELECT * FROM `#__wpl_addon_franchise_tables` WHERE `primary_key`='1'");
        
        $tables = array();
        foreach($results as $result) $tables[$result->table] = $result->table;
        
        // Set to static cache
        self::$primary_keys = $tables;
        return $tables;
    }
    
    public function fs_settings($blog_id)
    {
        // Get from static cache
        if(is_array(self::$fs_settings) and isset(self::$fs_settings[$blog_id])) return self::$fs_settings[$blog_id];
        
        $raw = $this->db->select("SELECT `setting_value` FROM `#__wpl_settings` WHERE `setting_name`='wpl_franchise' AND `blog_id`='".$blog_id."'", 'loadResult');
        if(trim($raw) == '') $raw = '[]';
        
        $fs_settings = json_decode($raw, true);
        
        // Set Default Values
        if(!isset($fs_settings['listing']))
        {
            $fs_settings = array
            (
                'listing'=>array
                (
                    'insert'=>array('target'=>'-1'),
                    'share'=>array('access'=>'0', 'shared'=>'1', 'childs'=>array()),
                    'select'=>array('targets'=>array('-1'))
                ),
                'user'=>array
                (
                    'select'=>array('targets'=>array('-1')),
                    'maximum_agents'=>'',
                )
            );
        }
        
        // Set to static cache
        if(!is_array(self::$fs_settings)) self::$fs_settings = array();
        self::$fs_settings[$blog_id] = $fs_settings;

        return $fs_settings;
    }
    
    /**
     * Check if a plugin is network activated or not
     * @author Howard <howard@realtyna.com>
     * @param string $plugin
     * @return boolean
     */
    public function is_network_activate($plugin = '')
    {
        if(trim($plugin) == '') $plugin = WPL_BASENAME.'/WPL.php';
        
        /** Makes sure the plugin is defined before trying to use it **/
        if(!function_exists('is_plugin_active_for_network'))
        {
            /** Import WordPress Plugin Library **/
            _wp_import('wp-admin.includes.plugin');
        }

        return is_plugin_active_for_network($plugin);
    }
    
    public function get_admin_url($blog_id = NULL)
    {
        /** Get blog ID **/
        if(is_null($blog_id)) $blog_id = wpl_global::get_current_blog_id();
        
        return esc_url(get_admin_url($blog_id));
    }
    
    public function get_network_option($option, $default = false, $cache = false)
    {
        return get_site_option($option, $default, $cache);
    }
    
    /**
     * Get WordPress multisite signup method from network settings
     * @author Howard <howard@realtyna.com>
     * @return string The value can be 'all', 'none', 'blog' or 'user'.
     */
    public function get_signup_method()
    {
        return apply_filters('wpmu_active_signup', $this->get_network_option('registration', 'none'));
    }
    
    public static function get_admin_id($blog_id)
    {
        return wpl_users::get_id_by_email(wpl_global::get_blog_option($blog_id, 'admin_email', NULL));
    }
    
    public function package_updated($params)
    {
        $addon_id = $params[0]['package_id'];
        $addon_data = wpl_global::get_addon($addon_id);
        
        $fswpl = new wpl_addon_franchise_wpl();
        return $fswpl->addon('update', $addon_data['addon_name']);
    }
    
    public function package_installed($params)
    {
        $addon_id = $params[0]['package_id'];
        $addon_data = wpl_global::get_addon($addon_id);
        
        $fswpl = new wpl_addon_franchise_wpl();
        return $fswpl->addon('install', $addon_data['addon_name']);
    }
    
    public function get_child_websites()
    {
        $blogs = $this->db->select("SELECT * FROM `#__blogs` WHERE `archived`='0' AND `spam`='0' AND `deleted`='0'", 'loadAssocList');
        
        $childs = array();
        foreach($blogs as $blog)
		{
			$label = wpl_global::get_blog_option($blog['blog_id'], 'blogname');
			if(trim($label) == '') $label = wpl_global::get_blog_option($blog['blog_id'], 'siteurl');
			
			$childs[$blog['blog_id']] = $label;
		}
        
        return $childs;
    }
    
    /**
     * Check if we're on property manager or add/edit listings view in both of backend and frontend
     * @author Howard <howard@realtyna.com>
     * @static
     * @return boolean
     */
    public static function run_criteria()
    {
        $results = true;
        
        if(wpl_global::get_client()) $results = false;
        elseif(wpl_request::getVar('wplpage', NULL) == 'property_manager') $results = false;
        
        return $results;
    }
    
    /**
     * Check if a table shared in all the network or not
     * @author Howard <howard@realtyna.com>
     * @param string $table
     * @return boolean
     */
    public function is_network_shared($table)
    {
        $tables = $this->tables();
                
        if(isset($tables[$table]) and $tables[$table] == '-1') return true;
        return false;
    }

    /**
     * Check if a child website can add more agents or not
     * @author Howard <howard@realtyna.com>
     * @param int $blog_id
     * @return bool
     */
    public function is_agents_limit_reached($blog_id = NULL)
    {
        // Current Blog
        if(!$blog_id) $blog_id = wpl_global::get_current_blog_id();

        $fs_settings = $this->fs_settings($blog_id);
        if(isset($fs_settings['user']) and isset($fs_settings['user']['maximum_agents']) and trim($fs_settings['user']['maximum_agents']) != '')
        {
            $maximum_agents = (int) $fs_settings['user']['maximum_agents'];
            $current_total_agents = wpl_db::num("SELECT COUNT(id) FROM `#__wpl_users` WHERE `id` > 0");

            // Maximum Agents Reached!
            if($current_total_agents >= $maximum_agents) return true;
        }

        return false;
    }
}

/**
 * Franchise Blogs Library
 * @author Howard <howard@realtyna.com>
 * @package Franchise Add-on
 */
class wpl_addon_franchise_blogs
{
    public $db;
    public $fs;

    /**
     * @author Howard <howard@realtyna.com>
     */
	public function __construct()
	{
        $this->db = new wpl_db();
        $this->fs = new wpl_addon_franchise();
	}
    
    public function delete($blog_id, $drop = false)
    {
        $current_blog_id = wpl_global::get_current_blog_id();
        if($current_blog_id != $blog_id) switch_to_blog($blog_id);
        
        // Uninstall the blog
        $this->uninstall($blog_id);
        
        if($current_blog_id != $blog_id) switch_to_blog($current_blog_id);
    }
    
    public function add($blog_id, $user_id)
    {
        $current_blog_id = wpl_global::get_current_blog_id();
        if($current_blog_id != $blog_id) switch_to_blog($blog_id);
        
        $this->initialize($blog_id, $user_id);
        $this->upgrade($blog_id);
        $this->addons($blog_id);
        
        if($current_blog_id != $blog_id) switch_to_blog($current_blog_id);
    }
    
    public function archive()
    {
    }
    
    public function spam()
    {
    }
    
    public function activate($blog_id)
    {
        $current_blog_id = wpl_global::get_current_blog_id();
        if($current_blog_id != $blog_id) switch_to_blog($blog_id);
        
        $this->initialize($blog_id);
        $this->upgrade($blog_id);
        $this->addons($blog_id);
        
        if($current_blog_id != $blog_id) switch_to_blog($current_blog_id);
    }
    
    public function deactivate($blog_id)
    {
    }
    
    public function uninstall($blog_id)
    {
        // Remove Upload Directory of Blog
        $upload_path = wpl_global::get_upload_base_path($blog_id);
        if(wpl_folder::exists($upload_path)) wpl_folder::delete($upload_path);
        
        // Remove Cache Directory of Blog
        $wplcache = wpl_global::get_wpl_cache();
        $cache_path = $wplcache->get_path();
        
        if(wpl_folder::exists($cache_path)) wpl_folder::delete($cache_path);
        
        // Remove notification directory
        $notification_path = wpl_global::get_wpl_root_path().'libraries'.DS.'notifications'.DS.'templates'.$blog_id;
        if(wpl_folder::exists($notification_path)) wpl_folder::delete($notification_path);
        
        // Remove UI Customizer CSS file
        $css_path = wpl_global::get_wpl_root_path().'assets'.DS.'css'.DS.'ui_customizer'.DS.'wpl'.$blog_id.'.css';
        if(wpl_file::exists($css_path)) wpl_file::delete($css_path);

        // Remove Properties
        $fs = wpl_sql_parser::getInstance();
        $fs->disable();

        $properties = wpl_db::select("SELECT `id` FROM `#__wpl_properties` WHERE `source_blog_id`='$blog_id'", 'loadAssocList');
        foreach($properties as $property) wpl_property::purge($property['id']);

        $fs->enable();
        
        // clear the blog data
        $this->clear($blog_id);
    }
    
    public function duplicate($source_blog_id, $destination_blog_id)
    {
        $current_blog_id = wpl_global::get_current_blog_id();
        if($current_blog_id != $destination_blog_id) switch_to_blog($destination_blog_id);
        
        // Copy destination blog data from source blog data
        $this->copy($source_blog_id, $destination_blog_id);
        
        if($current_blog_id != $destination_blog_id) switch_to_blog($current_blog_id);
    }
    
    public function upgrade($blog_id)
    {
        $assets_path = WPL_ABSPATH.'libraries'.DS.'addon_franchise'.DS.'assets'.DS;
        
        $upgrade_files = wpl_folder::files($assets_path.'upgrades'.DS, '.sql', false, false);
        foreach($upgrade_files as $upgrade_file)
        {
            wpl_global::do_file_queries($assets_path.'upgrades'.DS.$upgrade_file, false, false);
        }
        
        /** update WPL version in db **/
		update_option('wpl_version', wpl_global::wpl_version());
    }
    
    public function initialize($blog_id, $user_id = NULL)
    {
        if(is_null($user_id)) $user_id = $this->fs->get_admin_id($blog_id);
        
        $assets_path = WPL_ABSPATH.'libraries'.DS.'addon_franchise'.DS.'assets'.DS;
        
        $mr_query_files = wpl_folder::files($assets_path.'mr_queries'.DS, '.sql', false, false); // mr means Must Run
        foreach($mr_query_files as $mr_query_file)
        {
            wpl_global::do_file_queries($assets_path.'mr_queries'.DS.$mr_query_file, false, false);
        }
        
        $query_files = wpl_folder::files($assets_path.'queries'.DS, '.sql', false, false);
        foreach($query_files as $query_file)
        {
            wpl_global::do_file_queries($assets_path.'queries'.DS.$query_file, false, false);
        }
        
        $script_files = wpl_folder::files($assets_path.'scripts'.DS, '.php', false, false);
        foreach($script_files as $script_file)
        {
            include $assets_path.'scripts'.DS.$script_file;
        }
        
        /** Create property listing page **/
        $pages = array('Properties'=>'[WPL]', 'For Sale'=>'[WPL sf_select_listing="9"]', 'For Rent'=>'[WPL sf_select_listing="10"]', 'Vacation Rental'=>'[WPL sf_select_listing="12"]');
        foreach($pages as $title=>$content)
        {
            if($this->db->select("SELECT COUNT(post_content) FROM `#__posts` WHERE `post_content` LIKE '%$content%' AND `post_status` IN ('publish', 'private')", 'loadResult') != 0) continue;

            $post = array('post_title'=>$title, 'post_content'=>$content, 'post_type'=>'page', 'post_status'=>'publish', 'comment_status'=>'closed', 'ping_status'=>'closed', 'post_author'=>1);
            $post_id = wp_insert_post($post);

            if($content == '[WPL]') wpl_settings::save_setting('main_permalink', $post_id);
        }
        
        /** Add Blog Admin to WPL **/
        wpl_users::add_user_to_wpl($user_id);
    }
    
    public function addons($blog_id)
    {
        $addons = $this->db->select("SELECT * FROM `#__wpl_addons` ORDER BY `id` ASC", 'loadAssocList');
        foreach($addons as $addon) $this->update($blog_id, $addon['addon_name']);
    }
    
    public function install($blog_id, $addon = '')
    {
        /** First Validation **/
        if(trim($addon) == '') return false;
        
        // The addon already installed on this blog
        if(wpl_global::get_wp_option('wpl_'.$addon.'_version', 0)) return true;
        
        $addon_data = wpl_global::get_addon(0, $addon);
        
        /** Add-on is not installed on network **/
        if(!isset($addon_data['id'])) return false;
        
        $assets_path = WPL_ABSPATH.'libraries'.DS.'addon_franchise'.DS.'assets'.DS;
        
        // Add-on Directory doesn't exists
        if(!wpl_folder::exists($assets_path.'addons'.DS.$addon.DS)) return false;
        
        $install_addon_files = wpl_folder::files($assets_path.'addons'.DS.$addon.DS, '.sql', false, false);
        foreach($install_addon_files as $install_addon_file)
        {
            wpl_global::do_file_queries($assets_path.'addons'.DS.$addon.DS.$install_addon_file, false, false);
        }
        
        $upgrade_addon_files = wpl_folder::files($assets_path.'addons'.DS.$addon.DS.'upgrades'.DS, '.sql', false, false);
        foreach($upgrade_addon_files as $upgrade_addon_file)
        {
            wpl_global::do_file_queries($assets_path.'addons'.DS.$addon.DS.'upgrades'.DS.$upgrade_addon_file, false, false);
        }
        
        /** update WPL addon version in db **/
		update_option('wpl_'.$addon.'_version', $addon_data['version']);
		return true;
    }
    
    public function update($blog_id, $addon = '')
    {
        /** First Validation **/
        if(trim($addon) == '') return false;
        
        $addon_data = wpl_global::get_addon(0, $addon);
        
        /** Add-on is not installed on network **/
        if(!isset($addon_data['id'])) return false;
        
        $current_version = wpl_global::get_wp_option('wpl_'.$addon.'_version', 0);
        
        // The addon does not installed on this blog
        if(!$current_version) return $this->install($blog_id, $addon);
        
        $assets_path = WPL_ABSPATH.'libraries'.DS.'addon_franchise'.DS.'assets'.DS;
        
        $upgrade_addon_files = wpl_folder::files($assets_path.'addons'.DS.$addon.DS.'upgrades'.DS, '.sql', false, false);
        foreach($upgrade_addon_files as $upgrade_addon_file)
        {
            $file_version = trim($upgrade_addon_file, ' .sql');
            if(version_compare($file_version, $current_version, '<=')) continue;
            
            wpl_global::do_file_queries($assets_path.'addons'.DS.$addon.DS.'upgrades'.DS.$upgrade_addon_file, false, false);
        }
        
        /** update WPL addon version in db **/
		update_option('wpl_'.$addon.'_version', $addon_data['version']);

		return true;
    }
    
    public function clear($blog_id, $criteria = 'shared')
    {
        $tables = $this->db->select('SHOW TABLES');
        $database = $this->db->get_DBO();
        
        $wpl_tables = array();
		foreach($tables as $table_name=>$table)
		{
            $table_name = str_replace($database->base_prefix, '', $table_name);
			$wpl_tables[$table_name] = $table_name;
		}
        
        // Remove WPL Data from WPL Tables
        $shared_tables = $this->db->select("SELECT `table` FROM `#__wpl_addon_franchise_tables` WHERE `$criteria`='1'", 'loadColumn');
        
        foreach($shared_tables as $shared_table)
        {
            if(!in_array($shared_table, $wpl_tables)) continue;

            if($shared_table == 'wpl_properties') $this->db->q("DELETE FROM `#__".$shared_table."` WHERE `source_blog_id`='$blog_id'");
            else $this->db->q("DELETE FROM `#__".$shared_table."` WHERE 1");
        }
    }
    
    public function copy($source_blog_id, $destination_blog_id)
    {
        $tables = $this->db->select('SHOW TABLES');
        $database = $this->db->get_DBO();
        
        $wpl_tables = array();
		foreach($tables as $table_name=>$table)
		{
            $table_name = str_replace($database->base_prefix, '', $table_name);
			$wpl_tables[$table_name] = $table_name;
		}
        
        // Copy WPL Data from WPL Tables
        $shared_tables = $this->db->select("SELECT `table` FROM `#__wpl_addon_franchise_tables` WHERE `shared`='1' AND `primary_key`='1'", 'loadColumn');
        
        $sqlParser = wpl_sql_parser::getInstance();
        $sqlParser->disable();
        
        foreach($shared_tables as $shared_table)
        {
            if(!in_array($shared_table, $wpl_tables)) continue;
            
            $this->db->q("CREATE TEMPORARY TABLE `tmp_".$shared_table."` SELECT * FROM `#__".$shared_table."` WHERE `blog_id`='$source_blog_id'");
            $this->db->q("UPDATE `tmp_".$shared_table."` SET `blog_id`='$destination_blog_id' WHERE `blog_id`='$source_blog_id'");
			
			$this->db->q("DELETE FROM `#__".$shared_table."` WHERE `blog_id`='$destination_blog_id'");
            $this->db->q("INSERT INTO `#__".$shared_table."` SELECT * FROM `tmp_".$shared_table."` WHERE `blog_id`='$destination_blog_id'");
			
            $this->db->q("DROP TABLE `tmp_".$shared_table."`");
        }
        
        $sqlParser->enable();

        // Copy UI Customizer File
        $source_file = WPL_ABSPATH.'assets'.DS.'css'.DS.'ui_customizer'.DS.'wpl.css';
        if($source_blog_id != 1) $source_file = WPL_ABSPATH.'assets'.DS.'css'.DS.'ui_customizer'.DS.'wpl'.$source_blog_id.'.css';

        $destination_file = WPL_ABSPATH.'assets'.DS.'css'.DS.'ui_customizer'.DS.'wpl'.$destination_blog_id.'.css';
        if(wpl_file::exists($source_file)) wpl_file::copy($source_file, $destination_file);

        // Copy Notification Files
        $notification_dir = wpl_global::get_wpl_root_path().'libraries'.DS.'notifications'.DS.'templates'.DS;
        if($source_blog_id != 1) $notification_dir = wpl_global::get_wpl_root_path().'libraries'.DS.'notifications'.DS.'templates'.$source_blog_id.DS;

        $destination_dir = wpl_global::get_wpl_root_path().'libraries'.DS.'notifications'.DS.'templates'.$destination_blog_id.DS;
        if(!wpl_folder::exists($destination_dir)) wpl_folder::copy($notification_dir, $destination_dir);
    }
    
    public function table($blog_id, $table)
    {
        $current_blog_id = wpl_global::get_current_blog_id();
        if($current_blog_id != $blog_id) switch_to_blog($blog_id);
        
        $tables_path = WPL_ABSPATH.'libraries'.DS.'addon_franchise'.DS.'assets'.DS.'tables';
        if(wpl_file::exists($tables_path.DS.$table.'.sql')) wpl_global::do_file_queries($tables_path.DS.$table.'.sql', false, false);
        
        if($current_blog_id != $blog_id) switch_to_blog($current_blog_id);
    }
}

/**
 * Franchise WPL Library
 * @author Howard <howard@realtyna.com>
 * @package Franchise Add-on
 */
class wpl_addon_franchise_wpl
{
    public $db;

    /**
     * @author Howard <howard@realtyna.com>
     */
	public function __construct()
	{
        $this->db = new wpl_db();
	}
    
    public function uninstall()
    {
        $current_blog_id = wpl_global::get_current_blog_id();
        $fsblogs = new wpl_addon_franchise_blogs();
        
        // Get all blogs
        $blogs = $this->db->select("SELECT `blog_id` FROM `#__blogs`", 'loadColumn');

        foreach($blogs as $blog_id)
        {
            switch_to_blog($blog_id);
            $fsblogs->uninstall($blog_id);
        }

        switch_to_blog($current_blog_id);
    }
    
    public function activate($network = false)
    {
        $current_blog_id = wpl_global::get_current_blog_id();
        $fsblogs = new wpl_addon_franchise_blogs();
        
        // WPL activated only for one blog
        if(!$network)
        {
            $fsblogs->activate($current_blog_id);
            return;
        }

        // WPL activated for all blogs
        $blogs = $this->db->select("SELECT `blog_id` FROM `#__blogs`", 'loadColumn');
        foreach($blogs as $blog_id)
        {
            switch_to_blog($blog_id);
            $fsblogs->activate($blog_id);
        }

        switch_to_blog($current_blog_id);
    }
    
    public function deactivate($network = false)
    {
        $current_blog_id = wpl_global::get_current_blog_id();
        $fsblogs = new wpl_addon_franchise_blogs();
        
        // WPL deactivated only for one blog
        if(!$network)
        {
            $fsblogs->deactivate($current_blog_id);
            return;
        }

        // WPL deactivated for all blogs
        $blogs = $this->db->select("SELECT `blog_id` FROM `#__blogs`", 'loadColumn');
        foreach($blogs as $blog_id)
        {
            switch_to_blog($blog_id);
            $fsblogs->deactivate($blog_id);
        }

        switch_to_blog($current_blog_id);
    }
    
    public function upgrade()
    {
        $current_blog_id = wpl_global::get_current_blog_id();
        $fsblogs = new wpl_addon_franchise_blogs();

        // Upgrade all blogs
        $blogs = $this->db->select("SELECT `blog_id` FROM `#__blogs`", 'loadColumn');
        foreach($blogs as $blog_id)
        {
            /** WPL is not activated for this blog **/
            if(!wpl_global::get_blog_option($blog_id, 'wpl_version', 0)) continue;
            
            switch_to_blog($blog_id);
            $fsblogs->upgrade($blog_id);
        }

        switch_to_blog($current_blog_id);
    }
    
    public function addon($type = 'install', $addon = '')
    {
        /** First Validation **/
        if(trim($addon) == '') return false;
        
        $current_blog_id = wpl_global::get_current_blog_id();
        $fsblogs = new wpl_addon_franchise_blogs();

        // Upgrade all blogs
        $blogs = $this->db->select("SELECT `blog_id` FROM `#__blogs`", 'loadColumn');
        foreach($blogs as $blog_id)
        {
            /** WPL is not activated for this blog **/
            if(!wpl_global::get_blog_option($blog_id, 'wpl_version', 0)) continue;
            
            switch_to_blog($blog_id);
            
            if($type == 'install') $fsblogs->install($blog_id, $addon);
            elseif($type == 'update') $fsblogs->update($blog_id, $addon);
        }

        switch_to_blog($current_blog_id);
        return true;
    }
    
    /**
     * It's for initializing table data when it was universally shared but we change it.
     * For example, it called on /views/backend/addon_franchise/wpl_ajax.php
     * @author Howard R <howard@realtyna.com>
     * @param string $table
     */
    public function table($table)
    {
        $current_blog_id = wpl_global::get_current_blog_id();
        $fsblogs = new wpl_addon_franchise_blogs();
        
        // Upgrade all blogs
        $blogs = $this->db->select("SELECT `blog_id` FROM `#__blogs`", 'loadColumn');
        foreach($blogs as $blog_id)
        {
            /** WPL is not activated for this blog **/
            if(!wpl_global::get_blog_option($blog_id, 'wpl_version', 0)) continue;
            
            switch_to_blog($blog_id);
            
            $fsblogs->table($blog_id, $table);
        }

        switch_to_blog($current_blog_id);
    }
}

/**
 * WPL SQL Parser library
 * @author Howard <howard@realtyna.com>
 * @package Franchise
 */
class wpl_sql_parser
{
    /**
     * SQL Parser Object
     * @author Howard <howard@realtyna.com>
     * @var object
     */
    private $parser;
    
    /**
     * For enabling/disabling Query parser Don't forgot to enable it again if you disabled it temporary
     * @var boolean
     */
    public $enabled = true;
    
    /**
     * Run Franchise cronjob function or not, It will set to tru if a "CREATE TABLE" query ran.
     * @var boolean
     */
    public $force_cronjob = false;
    
    /**
     * For enabling/disabling select criteria for wpl_properties table
     * @var boolean
     */
    public $criteria = true;

    public $parsed = array();
    public $db;
    public $database;
    public $prefix;
    public $base_prefix;
    public $fs;
    public $global;
    public $type;
    public $blog_id;
    public $table;
    public $alias;
    public $creator;
    public $fs_tables = array();
    public $fs_settings = array();

    /**
     * Call this method to get singleton object
     * @author Howard <howard@realtyna.com>
     * @return wpl_sql_parser
     */
    public static function getInstance()
    {
        static $instance = NULL;
        if($instance === NULL) $instance = new wpl_sql_parser();
        
        return $instance;
    }
    
    /**
     * @author Howard <howard@realtyna.com>
     */
	private function __construct()
	{
        $this->db = new wpl_db();
        
        $this->database = $this->db->get_DBO();
        $this->prefix = $this->database->prefix;
        $this->base_prefix = $this->database->base_prefix;
        
        $this->fs = new wpl_addon_franchise();
        $this->global = new wpl_global();
	}
    
    /**
     * @author Howard <howard@realtyna.com>
     */
    private function __clone()
    {
    }
    
    /**
     * @author Howard <howard@realtyna.com>
     */
    private function __wakeup()
    {
    }
    
    public function disable()
    {
        $this->enabled = false;
    }
    
    public function enable()
    {
        $this->enabled = true;
    }
    
    public function criteria($status = 'get')
    {
        $status = trim(strtolower($status));
        
        if($status == 'on') $this->criteria = true;
        elseif($status == 'off') $this->criteria = false;
        
        return $this->criteria;
    }
    
    public function parse($sql)
    {
        /** return query if parser is disabled **/
        if(!$this->enabled) return $sql;
        
        /** Run Franchise cronjob itelligently **/
        if($this->force_cronjob)
        {
            $this->disable();
            $this->fs->cronjob();
            $this->enable();
        }
        
        $this->parser = new PHPSQLParser($sql);
        $this->parsed = $this->parser->parsed;
        
        $this->type = $this->type();
        
        /** Add blog_id to the created table exactly after creation **/
        if($this->type == 'CREATE_TABLE')
        {
            $this->force_cronjob = true;
            return $sql;
        }
        
        /** Not Supported Query Type **/
        if($this->type == 'UNKNOWN') return $sql;
        
        $this->table = $this->table();
        
        /** Not Known Table **/
        if($this->table == 'UNKNOWN') return $sql;
        
        $this->alias = $this->alias();
        $this->blog_id = $this->global->get_current_blog_id();
        
        $this->disable();
        $this->fs_tables = $this->fs->tables();
        $this->fs_settings = $this->fs->fs_settings($this->blog_id);
        $this->enable();
        
        /** Table not Exists in Franchise tables **/
        if(!isset($this->fs_tables[$this->table])) return $sql;
        
        /** Table is not shared **/
        if($this->fs_tables[$this->table] < 0) return $sql;
        
        /** WordPress Shared table **/
        if($this->fs_tables[$this->table] == 0)
        {
            if($this->table == 'users') $this->tbl_users();
        }
        
        /** WPL Users Table **/
        if($this->fs_tables[$this->table] == 1 and $this->table == 'wpl_users')
        {
            $this->tbl_wpl_users();
        }
        /** WPL Properties Table **/
        elseif($this->fs_tables[$this->table] == 1 and $this->table == 'wpl_properties')
        {
            $this->tbl_wpl_properties();
        }
        /** WPL Properties Table **/
        elseif($this->fs_tables[$this->table] == 1 and $this->table == 'wpl_user_group_types')
        {
            $this->tbl_wpl_user_group_types();
        }
        /** WPL Shared Tables **/
        elseif($this->fs_tables[$this->table] == 1)
        {
            $this->set('blog_id', $this->blog_id);
            $this->where('blog_id', $this->blog_id, '=');
        }
        
        try
        {
            $this->creator = new PHPSQLCreator();
            return $this->creator->create($this->parsed);
        }
        catch(Exception $e)
        {
            return $sql;
        }
    }
    
    private function type()
    {
        if(isset($this->parsed['SELECT'])) return 'SELECT';
        elseif(isset($this->parsed['INSERT'])) return 'INSERT';
        elseif(isset($this->parsed['UPDATE'])) return 'UPDATE';
        elseif(isset($this->parsed['DELETE'])) return 'DELETE';
        elseif(isset($this->parsed['CREATE']) and strpos(trim($this->parsed['CREATE']['base_expr']), 'TABLE') === 0) return 'CREATE_TABLE';
        else return 'UNKNOWN';
    }
    
    private function table()
    {
        if($this->type == 'SELECT' and isset($this->parsed['FROM']))
        {
            if(isset($this->parsed['FROM'][0]['sub_tree']) and isset($this->parsed['FROM'][0]['expr_type']) and $this->parsed['FROM'][0]['expr_type'] == 'subquery') $table = $this->parsed['FROM'][0]['sub_tree']['FROM'][0]['no_quotes'];
            else $table = $this->parsed['FROM'][0]['no_quotes'];
        }
        elseif($this->type == 'INSERT' and isset($this->parsed['INSERT'])) $table = $this->parsed['INSERT'][0]['no_quotes'];
        elseif($this->type == 'UPDATE' and isset($this->parsed['UPDATE'])) $table = $this->parsed['UPDATE'][0]['no_quotes'];
        elseif($this->type == 'DELETE' and isset($this->parsed['FROM'])) $table = $this->parsed['FROM'][0]['no_quotes'];
        else $table = 'UNKNOWN';
        
        return str_replace('#__', '', $table);
    }
    
    private function alias()
    {
        $alias = '';
        
        if($this->type == 'SELECT' and isset($this->parsed['FROM'][0]['alias']) and is_array($this->parsed['FROM'][0]['alias'])) $alias = $this->parsed['FROM'][0]['alias']['no_quotes'];
        elseif($this->type == 'INSERT' and isset($this->parsed['INSERT'][0]['alias']) and is_array($this->parsed['INSERT'][0]['alias'])) $alias = $this->parsed['INSERT'][0]['alias']['no_quotes'];
        elseif($this->type == 'UPDATE' and isset($this->parsed['UPDATE'][0]['alias']) and is_array($this->parsed['UPDATE'][0]['alias'])) $alias = $this->parsed['UPDATE'][0]['alias']['no_quotes'];
        elseif($this->type == 'DELETE' and isset($this->parsed['FROM'][0]['alias']) and is_array($this->parsed['FROM'][0]['alias'])) $alias = $this->parsed['FROM'][0]['alias']['no_quotes'];
        
        return (trim($alias) != '' ? $alias.'.' : '');
    }
    
    private function set($key, $value)
    {
        if(!trim($key)) return false;
            
        if($this->type == 'INSERT')
        {
            for($i=0; $i<count($this->parsed['INSERT']); $i++) $this->parsed['INSERT'][$i]['columns'][] = array('expr_type'=>'colref', 'base_expr'=>$this->alias."`$key`", 'no_quotes'=>$this->alias.$key);
            for($i=0; $i<count($this->parsed['VALUES']); $i++) $this->parsed['VALUES'][$i]['data'][] = array('expr_type'=>'const', 'base_expr'=>"'$value'", 'sub_tree'=>'');
        }

        return true;
    }

    /**
     * @param string $key
     * @param string|array $values
     * @param string $operator
     * @return bool
     */
    private function where($key, $values, $operator = '=')
    {
        if(!trim($key)) return false;
        if(is_string($values) and trim($values) == '') return false;
        
        if(in_array($this->type, array('SELECT', 'UPDATE', 'DELETE')))
        {
            /** Set default WHERE condition **/
            if(!isset($this->parsed['WHERE']))
            {
                $this->parsed['WHERE'] = array();
                $this->parsed['WHERE'][] = array('expr_type'=>'const', 'base_expr'=>'1', 'sub_tree'=>'');
            }
            
            $this->parsed['WHERE'][] = array('expr_type'=>'operator', 'base_expr'=>'AND', 'sub_tree'=>'');
            
            if(in_array($operator, array('=', '>=', '<=')))
            {
                $this->parsed['WHERE'][] = array('expr_type'=>'colref', 'base_expr'=>$this->alias."`$key`", 'no_quotes'=>$this->alias.$key, 'sub_tree'=>'');
                $this->parsed['WHERE'][] = array('expr_type'=>'operator', 'base_expr'=>$operator, 'sub_tree'=>'');
                $this->parsed['WHERE'][] = array('expr_type'=>'const', 'base_expr'=>"'$values'", 'sub_tree'=>'');
            }
            elseif($operator == 'IN')
            {
                $sub_tree = array();
                foreach($values as $value) $sub_tree[] = array('expr_type'=>'const', 'base_expr'=>$value, 'sub_tree'=>'');
                
                $this->parsed['WHERE'][] = array('expr_type'=>'colref', 'base_expr'=>$this->alias."`$key`", 'no_quotes'=>$this->alias.$key, 'sub_tree'=>'');
                $this->parsed['WHERE'][] = array('expr_type'=>'operator', 'base_expr'=>'IN', 'sub_tree'=>'');
                $this->parsed['WHERE'][] = array('expr_type'=>'in-list', 'base_expr'=>"(".implode(',', $values).")", 'sub_tree'=>$sub_tree);
            }
            elseif($operator == 'NOTIN')
            {
                $sub_tree = array();
                foreach($values as $value) $sub_tree[] = array('expr_type'=>'const', 'base_expr'=>$value, 'sub_tree'=>'');
                
                $this->parsed['WHERE'][] = array('expr_type'=>'colref', 'base_expr'=>$this->alias."`$key`", 'no_quotes'=>$this->alias.$key, 'sub_tree'=>'');
                $this->parsed['WHERE'][] = array('expr_type'=>'operator', 'base_expr'=>'NOT', 'sub_tree'=>'');
                $this->parsed['WHERE'][] = array('expr_type'=>'operator', 'base_expr'=>'IN', 'sub_tree'=>'');
                $this->parsed['WHERE'][] = array('expr_type'=>'in-list', 'base_expr'=>"(".implode(',', $values).")", 'sub_tree'=>$sub_tree);
            }
            elseif($operator == 'LIKE')
            {
                $this->parsed['WHERE'][] = array('expr_type'=>'colref', 'base_expr'=>$this->alias."`$key`", 'no_quotes'=>$this->alias.$key, 'sub_tree'=>'');
                $this->parsed['WHERE'][] = array('expr_type'=>'operator', 'base_expr'=>'LIKE', 'sub_tree'=>'');
                $this->parsed['WHERE'][] = array('expr_type'=>'const', 'base_expr'=>"'%$values%'", 'sub_tree'=>'');
            }
            elseif($operator == 'TEXTSEARCH')
            {
                $sub_tree = array();
                
                $i = 0;
                foreach($values as $value)
                {
                    $i++;
                    $sub_tree[] = array('expr_type'=>'colref', 'base_expr'=>$this->alias."`$key`", 'no_quotes'=>$this->alias.$key, 'sub_tree'=>'');
                    $sub_tree[] = array('expr_type'=>'operator', 'base_expr'=>"LIKE", 'sub_tree'=>'');
                    $sub_tree[] = array('expr_type'=>'const', 'base_expr'=>"'%$value%'", 'sub_tree'=>'');
                    if($i != count($values)) $sub_tree[] = array('expr_type'=>'operator', 'base_expr'=>"AND", 'sub_tree'=>'');
                }
                
                $this->parsed['WHERE'][] = array('expr_type'=>'bracket_expression', 'base_expr'=>'', 'sub_tree'=>$sub_tree);
            }
            elseif($operator == 'SHARED')
            {
                $sub_tree = array();
                
                $sub_tree[] = array('expr_type'=>'colref', 'base_expr'=>$this->alias."`$key`", 'no_quotes'=>$this->alias.$key, 'sub_tree'=>'');
                $sub_tree[] = array('expr_type'=>'operator', 'base_expr'=>"=", 'sub_tree'=>'');
                $sub_tree[] = array('expr_type'=>'const', 'base_expr'=>"'1'", 'sub_tree'=>'');
                
                $sub_tree[] = array('expr_type'=>'operator', 'base_expr'=>"OR", 'sub_tree'=>'');
                $sub_tree[] = array('expr_type'=>'bracket_expression', 'base_expr'=>"", 'sub_tree'=>array(
                    array('expr_type'=>'colref', 'base_expr'=>$this->alias."`$key`", 'no_quotes'=>$this->alias.$key, 'sub_tree'=>''),
                    array('expr_type'=>'operator', 'base_expr'=>"=", 'sub_tree'=>''),
                    array('expr_type'=>'const', 'base_expr'=>"'2'", 'sub_tree'=>''),
                    array('expr_type'=>'operator', 'base_expr'=>"AND", 'sub_tree'=>''),
                    array('expr_type'=>'colref', 'base_expr'=>$this->alias."`share_options`", 'no_quotes'=>$this->alias.'share_options', 'sub_tree'=>''),
                    array('expr_type'=>'operator', 'base_expr'=>"LIKE", 'sub_tree'=>''),
                    array('expr_type'=>'const', 'base_expr'=>"'%,$values,%'", 'sub_tree'=>'')
                ));
                
                $sub_tree[] = array('expr_type'=>'operator', 'base_expr'=>"OR", 'sub_tree'=>'');
                $sub_tree[] = array('expr_type'=>'bracket_expression', 'base_expr'=>"", 'sub_tree'=>array(
                    array('expr_type'=>'colref', 'base_expr'=>$this->alias."`$key`", 'no_quotes'=>$this->alias.$key, 'sub_tree'=>''),
                    array('expr_type'=>'operator', 'base_expr'=>"=", 'sub_tree'=>''),
                    array('expr_type'=>'const', 'base_expr'=>"'3'", 'sub_tree'=>''),
                    array('expr_type'=>'operator', 'base_expr'=>"AND", 'sub_tree'=>''),
                    array('expr_type'=>'colref', 'base_expr'=>$this->alias."`share_options`", 'no_quotes'=>$this->alias.'share_options', 'sub_tree'=>''),
                    array('expr_type'=>'operator', 'base_expr'=>"NOT", 'sub_tree'=>''),
                    array('expr_type'=>'operator', 'base_expr'=>"LIKE", 'sub_tree'=>''),
                    array('expr_type'=>'const', 'base_expr'=>"'%,$values,%'", 'sub_tree'=>'')
                ));
                
                $this->parsed['WHERE'][] = array('expr_type'=>'bracket_expression', 'base_expr'=>"", 'sub_tree'=>$sub_tree);
            }
        }

        return true;
    }
    
    private function tbl_users()
    {
        /** No need to run this function in UPDATE, DELETE and INSERT queries **/
        if($this->type != 'SELECT') return;
        
        /** Set INNER JOIN **/
        $key = count($this->parsed['FROM']);
        $this->parsed['FROM'][$key] = array
        (
            'expr_type'=>'table', 'table'=>'`#__usermeta`', 'no_quotes'=>'#__usermeta', 'join_type'=>'JOIN', 'ref_type'=>'ON', 'sub_tree'=>NULL,
            'alias'=>array('as'=>1, 'name'=>'um', 'base_expr'=>'AS um', 'no_quotes'=>'um'),
            'ref_clause'=>array
            (
                array('expr_type'=>'colref', 'base_expr'=>$this->alias.'ID', 'no_quotes'=>$this->alias.'ID', 'sub_tree'=>NULL),
                array('expr_type'=>'operator', 'base_expr'=>'=', 'sub_tree'=>NULL),
                array('expr_type'=>'colref', 'base_expr'=>'um.user_id', 'no_quotes'=>'um.user_id', 'sub_tree'=>NULL)
            )
        );
        
        /** Set WHERE **/
        $key = isset($this->parsed['WHERE']) ? count($this->parsed['WHERE']) : 0;
        
        $this->parsed['WHERE'][$key++] = array('expr_type'=>'operator', 'base_expr'=>'AND', 'sub_tree'=>NULL);
        $this->parsed['WHERE'][$key++] = array('expr_type'=>'colref', 'base_expr'=>'um.meta_key', 'no_quotes'=>'um.meta_key', 'sub_tree'=>NULL);
        $this->parsed['WHERE'][$key++] = array('expr_type'=>'operator', 'base_expr'=>'=', 'sub_tree'=>NULL);
        $this->parsed['WHERE'][$key] = array('expr_type'=>'const', 'base_expr'=>"'#__capabilities'", 'sub_tree'=>NULL);
        
        if(isset($this->parsed['FROM'][1]) and $this->parsed['FROM'][1]['no_quotes'] == '#__wpl_users' and $this->fs_tables['wpl_users'] != '-1')
        {
            $alias = '';
            if(isset($this->parsed['FROM'][1]['alias']['no_quotes'])) $alias = $this->parsed['FROM'][1]['alias']['no_quotes'].'.';
            
            /** Set JOIN **/
            $key = count($this->parsed['FROM'][1]['ref_clause']);
            
            $this->parsed['FROM'][1]['ref_clause'][$key++] = array('expr_type'=>'operator', 'base_expr'=>'AND', 'sub_tree'=>NULL);
            $this->parsed['FROM'][1]['ref_clause'][$key++] = array('expr_type'=>'colref', 'base_expr'=>$alias.'blog_id', 'no_quotes'=>$alias.'blog_id', 'sub_tree'=>NULL);
            $this->parsed['FROM'][1]['ref_clause'][$key++] = array('expr_type'=>'operator', 'base_expr'=>'=', 'sub_tree'=>NULL);
            $this->parsed['FROM'][1]['ref_clause'][$key] = array('expr_type'=>'const', 'base_expr'=>$this->blog_id, 'sub_tree'=>NULL);
        }
    }
    
    private function tbl_wpl_users()
    {
        $criteria = true;
        
        // Turn criteria off for MIN and MAX functions
        if($this->type == 'SELECT' and isset($this->parsed['SELECT'][0]['alias']['no_quotes']) and in_array($this->parsed['SELECT'][0]['alias']['no_quotes'], array('min_id', 'max_id'))) $criteria = false;
        
        // Insert user to the target blog
        $this->set('blog_id', $this->blog_id);
        
        if($criteria)
        {
            $target_ids = $this->blog_id;
            $operator = '=';
            
            if(is_array($this->fs_settings['user']['select']['targets']) and count($this->fs_settings['user']['select']['targets']))
            {
                if(count($this->fs_settings['user']['select']['targets']) == 1 and $this->fs_settings['user']['select']['targets'][0] != '-1') $target_ids = $this->fs_settings['user']['select']['targets'][0];
                elseif(count($this->fs_settings['user']['select']['targets']) > 1)
                {
                    $targets = array();
                    foreach($this->fs_settings['user']['select']['targets'] as $target)
                    {
                        if($target == '-1') $target = $this->blog_id;
                        $targets[] = $target;
                    }
                    
                    $target_ids = $targets;
                    $operator = 'IN';
                }
            }
            
            // Select users from target blog(s)
            $this->where('blog_id', $target_ids, $operator);
        }
    }
    
    private function tbl_wpl_properties()
    {
        $criteria = true;
        
        // Turn criteria off for MIN and MAX functions
        if($this->type == 'SELECT' and isset($this->parsed['SELECT'][0]['alias']['no_quotes']) and in_array($this->parsed['SELECT'][0]['alias']['no_quotes'], array('min_id', 'max_id'))) $criteria = false;
        
        // Turn criteria off if it turned off manually
        if(!$this->criteria()) $criteria = false;
        
        // Insert property to the target blog
        $target_id = $this->blog_id;
        if($this->fs_settings['listing']['insert']['target'] != '-1') $target_id = $this->fs_settings['listing']['insert']['target'];
        
        $this->set('blog_id', $target_id);

        // Set source blog of property
        $this->set('source_blog_id', $this->blog_id);
        
        // Set Default property share options
        if(isset($this->fs_settings['listing']['share']))
        {
            // Set Shared Status
            $this->set('shared', $this->fs_settings['listing']['share']['shared']);
            
            // If it partially shared, insert the child websites
            if(!in_array($this->fs_settings['listing']['share']['shared'], array('0','1')) and isset($this->fs_settings['listing']['share']['childs']) and is_array($this->fs_settings['listing']['share']['childs'])) $this->set('share_options', ','.implode(',', $this->fs_settings['listing']['share']['childs']).',');
        }

        // Apply Source Blog ID in case of mass delete to avoid deleting whole properties
        if($this->type == 'DELETE' and (!isset($this->parsed['WHERE']) or (isset($this->parsed['WHERE']) and is_array($this->parsed['WHERE']) and (!count($this->parsed['WHERE']) or (count($this->parsed['WHERE']) == 1 and $this->parsed['WHERE'][0]['expr_type'] == 'const')))))
        {
            $this->where('source_blog_id', $this->blog_id);
        }
        
        // Turn Criteria Off when it's an update or delete query for certain properties
        if(in_array($this->type, array('UPDATE', 'DELETE')) and isset($this->parsed['WHERE']) and isset($this->parsed['WHERE'][0]) and $this->parsed['WHERE'][0]['no_quotes'] == 'id')
        {
            $criteria = false;
        }
        
        // Apply the criteria

        /* Flare Rush */
        // Disable listing criteria
        $this->fs_settings['listing']['select']['criteria'] = null;
        /* End */
        
        if($criteria)
        {
            $target_ids = $this->blog_id;
            $operator = '=';
            
            if(is_array($this->fs_settings['listing']['select']['targets']) and count($this->fs_settings['listing']['select']['targets']))
            {
                if(count($this->fs_settings['listing']['select']['targets']) == 1 and $this->fs_settings['listing']['select']['targets'][0] != '-1') $target_ids = $this->fs_settings['listing']['select']['targets'][0];
                elseif(count($this->fs_settings['listing']['select']['targets']) > 1)
                {
                    $targets = array();
                    foreach($this->fs_settings['listing']['select']['targets'] as $target)
                    {
                        if($target == '-1') $target = $this->blog_id;
                        $targets[] = $target;
                    }
                    
                    $target_ids = $targets;
                    $operator = 'IN';
                }
            }
            
            // Don't apply it when the user is super admin and he/she wants to update some listings
            if(!($this->type == 'UPDATE' and wpl_users::is_super_admin()))
            {
                // Select listings from target blog(s)
                $this->where('blog_id', $target_ids, $operator);
            
                // Select shared listings
                $this->where('shared', $this->blog_id, 'SHARED');
            }
            if(isset($this->fs_settings['listing']['select']['criteria']) and is_array($this->fs_settings['listing']['select']['criteria']) and count($this->fs_settings['listing']['select']['criteria']) and $this->fs->run_criteria())
            {
                foreach($this->fs_settings['listing']['select']['criteria'] as $column=>$query)
                {
                    $operator = $query['operator'];
                    
                    if($operator == 'GREATER') $operator = '>=';
                    elseif($operator == 'SMALLER') $operator = '<=';
                    elseif($operator == 'location-level-1')
                    {
                        $operator = '=';    
                        $column = 'location1_id'; 
                    }
                    elseif($operator == 'location-level-2')
                    {
                        $operator = '=';    
                        if(count(explode(',', $query['values'][0])) > 1) $operator = 'IN';
                        
                        $column = 'location2_id'; 
                    }
                    
                    $values = $query['values'];
                    if(is_array($values) and count($values) == 1 and !in_array($operator, array('IN', 'NOTIN'))) $values = $values[0];
                    
                    if($column == 'locations')
                    {
                        $column = 'textsearch';
                        $operator = 'TEXTSEARCH';
                        
                        /** Multilingual location text search **/
                        if(wpl_global::check_multilingual_status()) $column = wpl_addon_pro::get_column_lang_name($column, wpl_global::get_current_language(), false);
                        
                        $values = stripslashes($values);
                        $values_raw = array_reverse(explode(',', $values));
                        
                        $values = array();
    
                        $l = 0;
                        foreach($values_raw as $value_raw)
                        {
                            $l++;
                            if(trim($value_raw) == '') continue;

                            $value_raw = trim($value_raw);
                            if($l <= 2 and (strlen($value_raw) == 2 or strlen($value_raw) == 3)) $value_raw = wpl_locations::get_location_name_by_abbr(wpl_db::escape($value_raw), $l);

                            $ex_space = explode(' ', $value_raw);
                            foreach($ex_space as $ex_value_raw) array_push($values, 'LOC-'.$ex_value_raw);
                        }
                    }
                    
                    // Set the criteria
                    $this->where($column, $values, $operator);
                }
            }
        }
    }
    
    private function tbl_wpl_user_group_types()
    {
        $criteria = true;
        
        // Turn criteria off for MIN and MAX functions
        if($this->type == 'SELECT' and isset($this->parsed['SELECT'][0]['alias']['no_quotes']) and in_array($this->parsed['SELECT'][0]['alias']['no_quotes'], array('min_id', 'max_id'))) $criteria = false;
        
        // Insert group type to the target blog
        $this->set('blog_id', $this->blog_id);
        
        if($criteria)
        {
            $this->where('blog_id', $this->blog_id, '=');
        }
    }
}