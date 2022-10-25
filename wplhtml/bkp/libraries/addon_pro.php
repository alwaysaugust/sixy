<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/**
 * WPL PRO library. This library includes some of WPL PRO features such as auto add user, favorites, compare etc
 * @author Howard <howard@realtyna.com>
 * @package WPL PRO
 */
class wpl_addon_pro
{
    /**
    * Default sitemap pagination offset
    * @static
    */
    public static $sitemap_pagination_offset = 2000;

    /**
     * Constructor Method
     * @author Howard <howard@realtyna.com>
     */
    public function __construct()
    {
    }
    
    /**
     * Adds the new user to WPL. This function runs by WordPress
     * @author Howard <howard@realtyna.com>
     * @param integer $user_id
     * @return boolean
     */
    public function add_user($user_id = 0)
    {
        /** first validation **/
        if(!$user_id) return false;
        
        return wpl_users::add_user_to_wpl($user_id);
    }

    /**
     * Add/Remove a property from favorites
     * @author Howard <howard@realtyna.com>
     * @param $property_id
     * @param string $mode
     * @param int $user_id
     * @return bool
     */
    public static function favorite_add_remove($property_id, $mode = 'add', $user_id = 0)
    {
        /** first validation **/
        if(!$property_id) return false;
        
        $pids = $user_id ? self::favorite_get_pids(false, $user_id) : self::favorite_get_pids();
                
        if($mode == 'add')
        {
            $key = array_search($property_id, $pids);
            if($key === false)
            {
                array_push($pids, $property_id);
                
                /** Logged in user **/
                $current_user_id = false;
                
                if($user_id == 0) $current_user_id = wpl_users::get_cur_user_id();
                elseif(wpl_users::get_user_by('id', $user_id)) $current_user_id = $user_id;
                
                if($current_user_id) wpl_db::q("INSERT INTO `#__wpl_addon_pro_favorites` (`user_id`,`property_id`) VALUES ('$current_user_id','$property_id')");
            }
        }
        elseif($mode == 'remove')
        {
            $key = array_search($property_id, $pids);
            if($key !== false)
            {
                unset($pids[$key]);
                
                /** Logged in user **/
                $current_user_id = false;
                
                if($user_id == 0) $current_user_id = wpl_users::get_cur_user_id();
                elseif(wpl_users::get_user_by('id', $user_id)) $current_user_id = $user_id;
                
                if($current_user_id) wpl_db::q("DELETE FROM `#__wpl_addon_pro_favorites` WHERE `user_id`='$current_user_id' AND `property_id`='$property_id'");
            }
        }
        
        $pids = array_unique($pids);
        self::favorite_set_cookie($pids);
        
        return true;
    }
    
    /**
     * Gets favorite pids
     * @author Howard <Howard@realtyna.com>
     * @static
     * @param boolean $from_cookie
     * @param integer $user_id
     * @return array $pids
     */
    public static function favorite_get_pids($from_cookie = false, $user_id = 0)
    {
        $cookie = wpl_request::get('COOKIE');
        $cookie_favorites = isset($cookie['favorite_listings']) ? $cookie['favorite_listings'] : '';
        
        $favorite_pids = trim($cookie_favorites) == '' ? array() : explode(',', $cookie_favorites);
        if($from_cookie) return $favorite_pids;

        $current_user_id = $user_id ? $user_id : wpl_users::get_cur_user_id();
        if(!$current_user_id) return $favorite_pids;

        // Cache Key
        $cache_key = 'wpl_favorites_'.$current_user_id;

        // Return From Cache
        $cached = wp_cache_get($cache_key);
        if($cached) return $cached;

        $db_favorites = wpl_db::select("SELECT `property_id` FROM `#__wpl_addon_pro_favorites` WHERE `user_id` = '{$current_user_id}'", 'loadColumn');
        if(!$db_favorites or !is_array($db_favorites) or !sizeof($db_favorites)) return $favorite_pids;

        foreach($db_favorites as $entry)
        {
            if(intval($entry)) array_push($favorite_pids, strval($entry));
        }

        $favorite_pids = array_unique($favorite_pids);

        // Set to Cache
        wp_cache_set($cache_key, $favorite_pids, '', 100);

        return $favorite_pids;
    }
    
    /**
     * Sets property IDs into the cookie
     * @author Howard <howard@realtyna.com>
     * @static
     * @param array $pids
     * @return boolean
     */
    public static function favorite_set_cookie($pids = array())
    {
        /** validation **/
        if(!is_array($pids)) $pids = array();
        
        $favorites = implode(',', $pids);
        setcookie('favorite_listings', $favorites, time()+(86400*30), '/');
        wpl_request::setVar('favorite_listings', $favorites, 'COOKIE');
        
        return true;
    }
    
    /**
     * Get favorites details object
     * @author Howard <howard@realtyna.com>
     * @static
     * @param mixed $pids
     * @param int $image_width
     * @param int $image_height
     * @param int $target_id
     * @return array
     */
    public static function favorites_load($pids = '', $image_width = 32, $image_height = 32, $target_id = NULL)
    {
        if(trim($pids) == '') $pids = self::favorite_get_pids();
        else $pids = explode(',', $pids);
        
        $results = array();
        
        $params = array();
        $params['wpltarget'] = $target_id;
        $plisting_fields = wpl_property::get_plisting_fields();
        
        $c = 0;
        foreach($pids as $pid)
        {
            $property = wpl_property::full_render($pid, $plisting_fields, NULL, $params);
            
            /** Property is not exists **/
            if(!isset($property['raw']))
            {
                self::favorite_add_remove($pid, 'remove');
                continue;
            }
            
            $main_image = (isset($property['items']) and isset($property['items']['gallery'][0])) ? $property['items']['gallery'][0] : NULL;
            $image_url = NULL;
            
            if($main_image and $main_image->item_cat != 'external')
            {
                $image_params = array();
                $image_params['image_name'] = $main_image->item_name;
                $image_params['image_parentid'] = $main_image->parent_id;
                $image_params['image_parentkind'] = $main_image->parent_kind;
                $image_params['image_source'] = wpl_global::get_upload_base_path(wpl_property::get_blog_id($pid)).$image_params['image_parentid'].DS.$image_params['image_name'];

                $image_url = wpl_images::create_gallery_image($image_width, $image_height, $image_params);
            }
            elseif($main_image) $image_url = $main_image->item_extra3;
            
            if(isset($property['property_title']) and trim($property['property_title'])) $title = $property['property_title'];
		    else $title = wpl_property::update_property_title($property['raw']);
            
            $results['listings'][$pid]['id'] = $pid;
            $results['listings'][$pid]['image'] = $image_url;
            $results['listings'][$pid]['title'] = $title;
            $results['listings'][$pid]['link'] = $property['property_link'];

            $c++;
        }

        $results['count'] = $c;
        
        return $results;
    }
    
    /**
     * For triggering send_favorites event
     * @author Howard <howard@realtyna.com>
     * @static
     * @param array $parameters
     * @return boolean
     */
    public static function favorites_send($parameters)
    {
        wpl_events::trigger('favorites_send', $parameters);
        return true;
    }
    
    /**
     * Sync favorite listings of user with database after user login or register
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int|string $user_id
     * @return boolean
     */
    public static function favorites_sync($user_id)
    {
        // It's a login event so we will receive the user login instead of user ID from WordPress
        if(!is_numeric($user_id)) $user_id = wpl_users::get_id_by_username($user_id);
        
        // User does not exists
        if(!wpl_db::exists($user_id, 'users', 'id')) return false;
        
        $pids = wpl_addon_pro::favorite_get_pids(true);
        
        // User doesn't have any favorite listings
        if(!count($pids)) return false;
        
        // Insert favorite listings into the database
        foreach($pids as $pid) wpl_db::q("INSERT IGNORE `#__wpl_addon_pro_favorites` (`user_id`,`property_id`) VALUES ('$user_id','$pid')");
        
        return true;
    }
    
    public static function favorites_user_delete($user_id)
    {
        wpl_db::q("DELETE FROM `#__wpl_addon_pro_favorites` WHERE `user_id`='$user_id'");
    }
    
    /**
     * For triggering report_abuse_send event
     * @author Howard <howard@realtyna.com>
     * @param array $parameters
     * @return boolean
     */
    public function report_abuse_send($parameters)
    {
        wpl_events::trigger('report_abuse_send', $parameters);
        return true;
    }
    
    /**
     * Add sitemaps to sitemap plugin
     * @author Howard <howard@realtyna.com>
     * @static
     * @param object|array $sm_object
     * @return string|array|boolean
     */
    public static function add_sitemaps($sm_object = NULL)
    {
        $wpl_sitemap = wpl_global::get_setting('wpl_sitemap');
        $timestamp = strtotime(wpl_global::get_lastmodification_date());

        $offset = self::get_sitemap_pagination_offset();

$wpl_property = new wpl_property;
$total = $wpl_property->get_properties_count();

        $pages = ceil($total/$offset);
        
        if($wpl_sitemap == 'google-sitemap-generator' and class_exists('GoogleSitemapGenerator'))
        {
            for($i=1; $i<=$pages; $i++) $sm_object->addSitemap('wpl-'.$i, NULL, $timestamp);
            return true;
        }
        elseif($wpl_sitemap == 'yoast-seo')
        {
            $sitemaps = '';

            for($i = 1; $i <= $pages; $i++) $sitemaps .= '<sitemap>' . '<loc>' . wpl_global::get_wp_site_url() . "wpl-{$i}-sitemap.xml</loc>" . '<lastmod>' . date('c', $timestamp) . '</lastmod>' . '</sitemap>';

            return $sitemaps;
        }
        elseif($wpl_sitemap == 'all-in-one-seo')
        {
            $aio_options = get_option('aioseop_options');
            $aio_sitemap_options = $aio_options['modules']['aiosp_sitemap_options'];
            $aio_sitemap_filename = $aio_sitemap_options['aiosp_sitemap_filename'];
            
            $current_url = wpl_global::get_full_url();

            /** defining outside the "end" method to regard PHP Strict Standards **/
            $current_url_parts = explode('/', $current_url);
            $current_sitemap = end($current_url_parts);

            /** is index page **/
            if($current_sitemap == $aio_sitemap_filename . '.xml')
            {
                for($i = 1; $i <= $pages; $i++) $sm_object[] = array('loc'=>wpl_global::get_wp_site_url() . $aio_sitemap_filename . '_wpl-' . $i . '.xml', 'lastmod'=>date('c', $timestamp));
            }

            return $sm_object;
        }

        return true;
    }
    
    /**
     * Run WPL sitemap function (Integrated with other plugins)
     * @author Howard <howard@realtyna.com>
     * @static
     * @param $sm_data
     * @return boolean|object|array
     */
    public static function add_sitemaps_links($sm_data = NULL)
    {
        $sitemap_url = wpl_global::get_full_url();
        $sitemap_url_parts = explode('/', $sitemap_url);
        $wpl_sitemap = wpl_global::get_setting('wpl_sitemap');

        $priority = 0.6;
        $change_frequency = 'weekly';
        $offset = self::get_sitemap_pagination_offset();

        $exclude_properties = wpl_global::get_setting('wpl_sitemap_ex_listings', '');
        $exclude_profiles = wpl_global::get_setting('wpl_sitemap_ex_users', '');
        $exclude = array('profiles'=>(trim($exclude_profiles) != '' ? explode(',', $exclude_profiles) : array()), 'properties'=>(trim($exclude_properties) != '' ? explode(',', $exclude_properties) : array()));
            
        if($wpl_sitemap == 'google-sitemap-generator' and class_exists('GoogleSitemapGenerator'))
        {
            if(strpos($sitemap_url, 'wpl-') === false) return false;
            
            $sitemap = &GoogleSitemapGenerator::GetInstance();
            
            /** check sitemap object **/
            if($sitemap == NULL) return false;
            
            $file = end($sitemap_url_parts);
            $pos1 = strpos($file, 'wpl-')+4;
            $pos2 = strpos($file, '.');
            
            $start = (substr($file, $pos1)-1)*$offset;
            
            /** WPL item links **/
            $wpl_items = wpl_global::get_wpl_item_links($exclude, $start, $offset);
        
            foreach($wpl_items as $wpl_item) $sitemap->AddUrl($wpl_item['link'], $wpl_item['time'], $change_frequency, $priority);
            return true;
        }
        elseif($wpl_sitemap == 'yoast-seo')
        {
            global $wpseo_sitemaps;
            $content = '';
            
            if(!$wpseo_sitemaps) return false;

            if(strpos($sitemap_url, 'wpl-') === false) return false;

            $file = (isset($_GET['sitemap']) ? $_GET['sitemap'].'-sitemap' : end($sitemap_url_parts));
            $pos1 = strpos($file, 'wpl-') + 4;
            $pos2 = strpos($file, '-', $pos1);

            $current_index = intval(substr($file, $pos1, ($pos2 - $pos1)));
            $start = (max($current_index, 1) - 1) * $offset;
                
            /** WPL item links **/
            $wpl_items = wpl_global::get_wpl_item_links($exclude, $start, $offset);

            if(method_exists($wpseo_sitemaps, 'sitemap_url'))
            {
                foreach($wpl_items as $item)
                {
                    $url = array('loc'=>$item['link'], 'pri'=>$priority, 'chf'=>$change_frequency, 'mod'=>date('c', $item['time']));
                    $content .= $wpseo_sitemaps->sitemap_url($url);
                }

                $sitemap = '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" ';
                $sitemap .= 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" ';
                $sitemap .= 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
                $sitemap .= $content . '</urlset>';
            }
            else
            {
                $urls = array();
                foreach($wpl_items as $item)
                {
                    $urls[] = array('loc'=>$item['link'], 'pri'=>$priority, 'chf'=>$change_frequency, 'mod'=>date('c', $item['time']));
                }

                $renderer = new WPSEO_Sitemaps_Renderer();
                $sitemap = $renderer->get_sitemap($urls, 'wpl-'.$current_index, $current_index);
            }

            $wpseo_sitemaps->set_sitemap($sitemap);
            return true;
        }
        elseif($wpl_sitemap == 'all-in-one-seo')
        {
            $aioseop_options = get_option('aioseop_options');

            if(!$aioseop_options or !is_array($sm_data)) return false;

            /** indexed sitemaps or single page sitemap **/
            if(trim($aioseop_options['modules']['aiosp_sitemap_options']['aiosp_sitemap_indexes']) == 'on')
            {
                if(strpos($sitemap_url, 'wpl-') === false) return $sm_data;

                $file = end($sitemap_url_parts);
                $pos1 = strpos($file, 'wpl-') + 4;
                $pos2 = strpos($file, '.', $pos1);

                $current_index = intval(substr($file, $pos1, ($pos2 - $pos1)));
                $start = ($current_index - 1) * $offset;

                $wpl_items = wpl_global::get_wpl_item_links($exclude, $start, $offset);
            }
            else
            {
                $wpl_items = wpl_global::get_wpl_item_links($exclude, 0, -1);
            }

            foreach($wpl_items as $wpl_item)
            {
                $sm_data[] = array('loc'=>$wpl_item['link'], 'priority'=>$priority, 'changefreq'=>$change_frequency, 'lastmod'=>date('c', $wpl_item['time']));
            }
            
            return $sm_data;
        }
        
        return false;
    }

    /**
    * Register Sitemap for Yoast SEO
    * @author Edward <edward@realtyna.com>
    * @static
    * @return void
    */
    public static function register_sitemaps_yoast()
    {
        global $wpseo_sitemaps;

        $offset = self::get_sitemap_pagination_offset();

        if(!$wpseo_sitemaps) return;


$wpl_property = new wpl_property;
$total = $wpl_property->get_properties_count();

        $pages = ceil($total / $offset);

        for($i = 1; $i <= $pages; $i++) $wpseo_sitemaps->register_sitemap('wpl-' . $i, array('wpl_addon_pro', 'add_sitemaps_links'));
    }

    /**
    * Get sitemap pagination offset
    * @author Edward <edward@realtyna.com>
    * @static
    * @return integer
    */
    public static function get_sitemap_pagination_offset()
    {
        $default_offset = self::$sitemap_pagination_offset;
        $wpl_sitemap = wpl_global::get_setting('wpl_sitemap');

        if($wpl_sitemap == 'google-sitemap-generator')
        {
            // Currently not supported in google sitemap generator
        }
        elseif($wpl_sitemap == 'yoast-seo')
        {
            $yoast_options = get_option('wpseo_xml');
            if($yoast_options && ($offset = intval($yoast_options['entries-per-page']))) return $offset;
        }
        elseif($wpl_sitemap == 'all-in-one-seo')
        {
            $aio_options = get_option('aioseop_options');
            $aio_sitemap_options = $aio_options['modules']['aiosp_sitemap_options'];
            if(trim($aio_sitemap_options['aiosp_sitemap_paginate']) == 'on' && ($offset = intval($aio_sitemap_options['aiosp_sitemap_max_posts']))) return $offset;
        }

        return $default_offset;
    }

    /**
     * Returns all installed languages for WPL multilingual
     * @author Howard <howard@realtyna.com>
     * @static
     * @return array wpl installed languages
     */
    public static function get_wpl_languages()
    {
        $langs = wpl_global::get_setting('languages');
        if(is_string($langs) and !trim($langs)) $langs = '{}';
        
        return json_decode($langs, true);
    }
    
    /**
     * Returns all installed language options
     * @author Howard <howard@realtyna.com>
     * @static
     * @return array
     */
    public static function get_wpl_language_options()
    {
        $lang_options = wpl_global::get_setting('lang_options');
        if(is_string($lang_options) and !trim($lang_options)) $lang_options = '{}';
        
        $result_array = json_decode($lang_options, true);

        return is_array($result_array) ? $result_array : array();
    }
    
    /**
     * Returns WPL main page of a language
     * @author Howard <howard@realtyna.com>
     * @static
     * @return string
     */
    public static function get_lang_main_page()
    {
        $current_language = wpl_global::get_current_language();
        $lang_options = wpl_addon_pro::get_wpl_language_options();
        
        $lang_main_page = NULL;
        foreach($lang_options as $lang_option)
        {
            if(strtolower($current_language) != strtolower($lang_option['full_code'])) continue;
            
            $lang_main_page = isset($lang_option['main_page']) ? $lang_option['main_page'] : NULL;
            break;
        }
        
        return $lang_main_page;
    }
    
    /**
     * Returns default language of WPL (First language if default)
     * @author Howard <howard@realtyna.com>
     * @static
     * @return string
     */
    public static function get_default_language()
    {
        $langs = wpl_addon_pro::get_wpl_languages();
        
        /** First Validation **/
        if(!is_array($langs)) return NULL;
        
        reset($langs);
        $first_key = key($langs);
        
        return (isset($langs[$first_key]) ? $langs[$first_key] : NULL);
    }
    
    /**
     * Returns column name based on WPL language
     * @author Howard <howard@realtyna.com>
     * @static
     * @param string $column
     * @param string $language
     * @param boolean $check_default
     * @return string
     */
    public static function get_column_lang_name($column, $language = NULL, $check_default = true)
    {
        $default_language = strtolower(self::get_default_language());
        $language = str_replace('-', '_', strtolower(trim($language)));
        
        /** if language is not valid **/
        if(!$language) return $column;
        
        if($default_language == $language and $check_default) return $column;
        else return $column.'_'.$language;
    }
    
    /**
     * Returns field multilingual status by column name
     * @author Howard <howard@realtyna.com>
     * @static
     * @param string $column
     * @param int $kind
     * @return boolean
     */
    public static function get_multiligual_status_by_column($column, $kind = 0)
    {
        $field = wpl_flex::get_field(wpl_flex::get_dbst_id($column, $kind));
        return ((isset($field->multilingual) and $field->multilingual) ? true : false);
    }
    
    /**
     * Store languages and add/remove fields based on languages
     * @author Howard <howard@realtyna.com>
     * @static
     * @param array $newlangs
     * @return boolean
     */
    public static function save_languages($newlangs = array())
    {
        $removed_langs = array();
        $kinds = wpl_flex::get_kinds(NULL);

        $current_langs = wpl_addon_pro::get_wpl_languages();
        foreach($current_langs as $current_lang)
        {
            if(!in_array($current_lang, $newlangs)) $removed_langs[] = $current_lang;
        }
        
        // remove columns of removed languages
        if(count($removed_langs))
        {
            // Remove Other Multilingual Fields
            self::multilingual_other_fields($removed_langs, 'remove');

            foreach($kinds as $kind)
            {
                $table = $kind['table'];
                
                // Remove system fields for removed language
                self::multilingual_system_fields($removed_langs, $table, 'remove');

                $columns = wpl_db::columns($table);
                
                $multilingual_fields = wpl_addon_pro::get_multilingual_fields($kind['id']);
                foreach($multilingual_fields as $multilingual_field)
                {
                    foreach($removed_langs as $removed_lang)
                    {
                        $column = self::get_column_lang_name($multilingual_field->table_column, $removed_lang, false);
                        
                        if(!in_array($column, $columns)) continue;
                        
                        $query = "ALTER TABLE `#__".$table."` DROP `$column`;";
                        wpl_db::q($query);
                    }
                }
            }
        }
        
        // Add columns of added languages
        if(count($newlangs))
        {
            // Add Other Multilingual Fields
            self::multilingual_other_fields($newlangs, 'add');

            foreach($kinds as $kind)
            {
                $table = $kind['table'];
                
                // Create new system fields for new language
                self::multilingual_system_fields($newlangs, $table, 'add');
            
                $columns = wpl_db::columns($table);
                
                $multilingual_fields = wpl_addon_pro::get_multilingual_fields($kind['id']);
                foreach($multilingual_fields as $multilingual_field)
                {
                    foreach($newlangs as $newlang)
                    {
                        $column_name = $multilingual_field->table_column;
                        $column = self::get_column_lang_name($column_name, $newlang, false);
                        
                        if(in_array($column, $columns)) continue;

                        $query = '';
                        $query2 = '';

                        if(in_array($multilingual_field->type, array('textarea', 'meta_key', 'meta_desc')))
                        {
                            $query = "ALTER TABLE `#__".$table."` ADD `$column` TEXT;";
                            $query2 = "UPDATE `#__".$table."` SET `$column`=`$column_name`;";
                        }
                        elseif($multilingual_field->type == 'text')
                        {
                            $query = "ALTER TABLE `#__".$table."` ADD `$column` VARCHAR(255) NULL;";
                            $query2 = "UPDATE `#__".$table."` SET `$column`=`$column_name`;";
                        }

                        if(trim($query)) wpl_db::q($query);
                        if(trim($query2)) wpl_db::q($query2);
                    }
                }
            }
        }
        
        return wpl_settings::save_setting('languages', json_encode($newlangs));
    }
    
    /**
     * Make system fields multilingual
     * @param array $languages
     * @param string $table
     * @param string $mode
     */
    public static function multilingual_system_fields($languages, $table = 'wpl_properties', $mode = 'add')
    {
        $system_columns = array('rendered', 'textsearch');
        $columns = wpl_db::columns($table);
        
        foreach($languages as $language)
        {
            if($mode == 'add')
            {
                foreach($system_columns as $column_name)
                {
                    $column = self::get_column_lang_name($column_name, $language, false);
                    
                    /** Continue to next field if system field is not available in table or multilingual column already created **/
                    if(!in_array($column_name, $columns) or in_array($column, $columns)) continue;
                    
                    $query = "ALTER TABLE `#__".$table."` ADD `$column` TEXT;";
                    $query2 = "UPDATE `#__".$table."` SET `$column`=`$column_name`;";
                    
                    wpl_db::q($query);
                    wpl_db::q($query2);
                    
                    // Add Full Text index to the text search column
                    if($column_name == 'textsearch')
                    {
                        $query3 = "ALTER TABLE `#__".$table."` ADD FULLTEXT `$column` (`$column`);";
                        wpl_db::q($query3);
                    }
                }
            }
            elseif($mode == 'remove')
            {
                foreach($system_columns as $column_name)
                {
                    $column = self::get_column_lang_name($column_name, $language, false);
                    
                    /** Continue to next field if multilingual column already removed **/
                    if(!in_array($column, $columns)) continue;
                    
                    $query = "ALTER TABLE `#__".$table."` DROP `$column`;";
                    wpl_db::q($query);
                }
            }
        }
    }

    /**
     * Make other fields multilingual
     * @param array $languages
     * @param string $mode
     */
    public static function multilingual_other_fields($languages, $mode = 'add')
    {
        $multilingual_columns = array('wpl_notifications'=>array('subject'));

        foreach($languages as $language)
        {
            if($mode == 'add')
            {
                foreach($multilingual_columns as $table=>$columns)
                {
                    $table_columns = wpl_db::columns($table);

                    foreach($columns as $column_name)
                    {
                        $column = self::get_column_lang_name($column_name, $language, false);

                        // Continue to next field if system field is not available in table or multilingual column already created
                        if(!in_array($column_name, $table_columns) or in_array($column, $table_columns)) continue;

                        $query = "ALTER TABLE `#__".$table."` ADD `$column` TEXT;";
                        $query2 = "UPDATE `#__".$table."` SET `$column`=`$column_name`;";

                        wpl_db::q($query);
                        wpl_db::q($query2);
                    }
                }
            }
            elseif($mode == 'remove')
            {
                foreach($multilingual_columns as $table=>$columns)
                {
                    $table_columns = wpl_db::columns($table);

                    foreach($columns as $column_name)
                    {
                        $column = self::get_column_lang_name($column_name, $language, false);

                        // Continue to next field if multilingual column already removed
                        if(!in_array($column, $table_columns)) continue;

                        $query = "ALTER TABLE `#__".$table."` DROP `$column`;";
                        wpl_db::q($query);
                    }
                }
            }
        }
    }
    
    /**
     * Care about multilingual field
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $dbst_id
     * @return boolean
     */
    public static function multilingual($dbst_id)
    {
        $wpllangs = wpl_addon_pro::get_wpl_languages();
        
        /** No language added **/
        if(!is_array($wpllangs) or (is_array($wpllangs) and !count($wpllangs))) return false;
        
        $field = wpl_flex::get_field($dbst_id);
		$column_name = $field->table_column;
        
        $table = $field->table_name;
        $columns = wpl_db::columns($table);
        
        $lang_columns = array();
        
		foreach($wpllangs as $lang) $lang_columns[] = self::get_column_lang_name($column_name, $lang, false);
        
        if(isset($field->multilingual) and $field->multilingual)
        {
            foreach($lang_columns as $column)
            {
                if(in_array($column, $columns)) continue;

                $query = '';
                $query2 = '';

                if(in_array($field->type, array('textarea', 'meta_key', 'meta_desc')))
                {
                    $query = "ALTER TABLE `#__".$table."` ADD `$column` TEXT;";
                    $query2 = "UPDATE `#__".$table."` SET `$column`=`$column_name`;";
                }
                elseif($field->type == 'text')
                {
                    $query = "ALTER TABLE `#__".$table."` ADD `$column` VARCHAR(100) NULL;";
                    $query2 = "UPDATE `#__".$table."` SET `$column`=`$column_name`;";
                }

                if(trim($query)) wpl_db::q($query);
                if(trim($query2)) wpl_db::q($query2);
            }
        }
        else
        {
            foreach($lang_columns as $column)
            {
                if(!in_array($column, $columns)) continue;
                
                $query = "ALTER TABLE `#__".$table."` DROP `$column`;";
                wpl_db::q($query);
            }
        }
        
        return true;
    }
    
    /**
     * Returns multilingual fields
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $kind
     * @return array
     */
    public static function get_multilingual_fields($kind = NULL)
    {
        return wpl_flex::get_fields(NULL, 0, $kind, 'multilingual', '1');
    }
    
    /**
     * Remove multilingual columns of a field
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $dbst_id
     * @return boolean
     */
    public static function remove_multilingual($dbst_id)
    {
        $field = wpl_flex::get_field($dbst_id);
        
        if(!isset($field->multilingual) or (isset($field->multilingual) and !$field->multilingual)) return false;
        
        $column_name = $field->table_column;
        $table = $field->table_name;
        
        $wpllangs = wpl_addon_pro::get_wpl_languages();
        $columns = wpl_db::columns($table);

        $lang_columns = array();
        foreach($wpllangs as $lang) $lang_columns[] = self::get_column_lang_name($column_name, $lang, false);
        
        foreach($lang_columns as $column)
        {
            if(!in_array($column, $columns)) continue;

            $query = "ALTER TABLE `#__".$table."` DROP `$column`;";
            wpl_db::q($query);
        }
        
        return true;
    }
    
    /**
     * Apply WPL SEF multilingual links
     * @author Howard <howard@realtyna.com>
     * @static
     * @param array $w_active_languages WPML languages
     * @return array
     */
    public static function wpml_language_switcher($w_active_languages)
    {
        $wplview = wpl_request::getVar('wplview', NULL);
        if(!in_array($wplview, array('property_show', 'profile_show', 'addon_membership'))) return $w_active_languages;
        
        $current_language = wpl_global::get_current_language();
        
        $filtered_languages = array();
        foreach($w_active_languages as $key=>$w_active_language)
        {
            if(!wpl_global::switch_language($w_active_language['default_locale'])) wpl_request::setVar('wpllang', $w_active_language['default_locale']);

            if($wplview == 'property_show')
            {
                $pid = wpl_request::getVar('pid', NULL);
                if($pid)
                {
                    $url = $w_active_language['url'];
                    if(strpos($url, '?') !== false) $url = substr($url, 0, strpos($url, '?'));
                    
                    // Make sure we have / at the end
                    $url = rtrim($url, '/').'/';
                    
                    $w_active_language['url'] = $url.$pid.'-'.wpl_property::update_alias(NULL, $pid, '-', false, true).'/';
                }
            }
            elseif($wplview == 'profile_show')
            {
                $uid = wpl_request::getVar('uid', NULL);
                if($uid)
                {
                    $user_data = wpl_users::get_user($uid);
                    
                    $url = $w_active_language['url'];
                    if(strpos($url, '?') !== false) $url = substr($url, 0, strpos($url, '?'));
                    
                    // Make sure we have / at the end
                    $url = rtrim($url, '/').'/';
                    
                    $w_active_language['url'] = $url.urlencode($user_data->data->user_login).'/';
                }
            }
            elseif($wplview == 'addon_membership')
            {
                $url = $w_active_language['url'];
                $full_url = wpl_global::get_full_url();
                
                // Membership pages set to a specific page and don't follow main WPL permalink URLs
                if(strpos($full_url, '/v/members'))
                {
                    if(strpos($url, '?') !== false) $url = substr($url, 0, strpos($url, '?'));

                    // Make sure we have / at the end
                    $url = rtrim($url, '/').'/';
                    $membership_url = substr($full_url, strpos($full_url, '/v/members'));

                    $w_active_language['url'] = $url.ltrim($membership_url, '/ ');
                }
            }

            $filtered_languages[$key] = $w_active_language;
        }
        
        /** Switch to current language again **/
        wpl_global::switch_language($current_language);
		
        wpl_request::setVar('wpllang', NULL);
        
        return $filtered_languages;
    }
    
    /**
     * Returns Property Manager link
     * @author Howard <howard@realtyna.com>
     * @static
     * @param string $method
     * @return string
     */
    public static function get_manager_link($method = NULL)
    {
        $url = wpl_sef::get_wpl_permalink(true);
        $nosef = wpl_sef::is_permalink_default();

        $wpl_main_page_id = wpl_sef::get_wpl_main_page_id();
        $home_type = wpl_global::get_wp_option('show_on_front', 'posts');
        $home_id = wpl_global::get_wp_option('page_on_front', 0);

        if(!$nosef)
        {
            if($home_type == 'page' and $home_id == $wpl_main_page_id) $url = wpl_global::add_qs_var('wplview', 'property_manager', $url);
            else $url = trim($url, '/').'/v/manager/';
        }
        else $url = wpl_global::add_qs_var('wplview', 'property_manager', $url);

        if(!is_null($method)) $url = wpl_global::add_qs_var('wplmethod', $method, $url);
        
        return $url;
    }
    
    /**
     * Generates Dynamic Keywords
     * @author Howard <howard@realtyna.com>
     * @static
     * @return void
     */
    public static function generate_dynamic_keywords()
    {
        $keyword_str = '<?php'.PHP_EOL;
        
        $activities = wpl_db::select("SELECT * FROM `#__wpl_activities`");
        foreach($activities as $activity)
        {
            if(trim($activity->title) == '') continue;
            $keyword_str .= "__('".wpl_db::escape($activity->title)."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;
        }
        
        $kinds = wpl_db::select("SELECT * FROM `#__wpl_kinds`");
        foreach($kinds as $kind)
        {
            $keyword_str .= "__('".wpl_db::escape($kind->name)."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;
        }
        
        $listing_types = wpl_db::select("SELECT * FROM `#__wpl_listing_types`");
        foreach($listing_types as $listing_type)
        {
            $keyword_str .= "__('".wpl_db::escape($listing_type->name)."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;
        }
        
        $property_types = wpl_db::select("SELECT * FROM `#__wpl_property_types`");
        foreach($property_types as $property_type)
        {
            $keyword_str .= "__('".wpl_db::escape($property_type->name)."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;
        }
        
        $dbcats = wpl_db::select("SELECT * FROM `#__wpl_dbcat`");
        foreach($dbcats as $dbcat)
        {
            $keyword_str .= "__('".wpl_db::escape($dbcat->name)."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;
        }

        $dbsts = wpl_db::select("SELECT * FROM `#__wpl_dbst`");
        foreach($dbsts as $dbst)
        {
            $keyword_str .= "__('".wpl_db::escape($dbst->name)."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;
            $options = json_decode($dbst->options, true);

            if(isset($options['params']) and is_array($options['params']))
            {
                foreach($options['params'] as $param)
                {
                    if(!isset($param['value']) or is_numeric($param['value']) or strlen($param['value']) <= 1) continue;
                    $keyword_str .= "__('".wpl_db::escape($param['value'])."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;
                }
            }

            if(isset($options['type']) and in_array($options['type'], array('single', 'multiple')))
            {
                foreach($options['values'] as $param)
                {
                    if(!isset($param['value']) or is_numeric($param['value']) or strlen($param['value']) <= 1) continue;
                    $keyword_str .= "__('".wpl_db::escape($param['value'])."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;
                }
            }
            
            if(isset($options['call_text'])) $keyword_str .= "__('".wpl_db::escape($options['call_text'])."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;
            if(isset($dbst->accesses_message) and trim($dbst->accesses_message)) $keyword_str .= "__('".wpl_db::escape($dbst->accesses_message)."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;
        }

        $types = wpl_db::select("SELECT * FROM `#__wpl_dbst_types`");
        foreach($types as $type)
        {
            $keyword_str .= "__('".wpl_db::escape($type->type)."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;
        }

        $item_categories = wpl_db::select("SELECT * FROM `#__wpl_item_categories`");
        foreach($item_categories as $item_category)
        {
            $keyword_str .= "__('".wpl_db::escape($item_category->category_name)."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;
        }

        $wpl_menus = wpl_db::select("SELECT * FROM `#__wpl_menus`");
        foreach($wpl_menus as $wpl_menu)
        {
            $keyword_str .= "__('".wpl_db::escape($wpl_menu->menu_title)."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;
            $keyword_str .= "__('".wpl_db::escape($wpl_menu->page_title)."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;
        }

        $room_types = wpl_db::select("SELECT * FROM `#__wpl_room_types`");
        foreach($room_types as $room_type)
        {
            $keyword_str .= "__('".wpl_db::escape($room_type->name)."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;
        }

        $wpl_settings = wpl_db::select("SELECT * FROM `#__wpl_settings`");
        foreach($wpl_settings as $wpl_setting)
        {
            if(trim($wpl_setting->title) != '') $keyword_str .= "__('".wpl_db::escape($wpl_setting->title)."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;

            $params = json_decode($wpl_setting->params, true);
            if(isset($params['tooltip'])) $keyword_str .= "__('".wpl_db::escape($params['tooltip'])."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;

            $options = json_decode($wpl_setting->options, true);
            if($wpl_setting->type == 'select' and isset($options['values']) and $wpl_setting->id != 27)
            {
                foreach($options['values'] as $arr)
                {
                    if(!isset($arr['value']) or is_numeric($arr['value'])) continue;
                    $keyword_str .= "__('".wpl_db::escape($arr['value'])."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;
                }
            }

            if(in_array($wpl_setting->id, array(13,14,15,16,17,18,19,20)))
            {
                if(trim($wpl_setting->setting_value) != '') $keyword_str .= "__('".wpl_db::escape($wpl_setting->setting_value)."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;
            }
        }

        $setting_categories = wpl_db::select("SELECT * FROM `#__wpl_setting_categories`");
        foreach($setting_categories as $setting_category)
        {
            $keyword_str .= "__('".wpl_db::escape($setting_category->name)."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;
        }

        $unit_types = wpl_db::select("SELECT * FROM `#__wpl_unit_types`");
        foreach($unit_types as $unit_type)
        {
            $keyword_str .= "__('".wpl_db::escape($unit_type->name)."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;
        }

        $groups = wpl_db::select("SELECT * FROM `#__wpl_users` WHERE id < 0");
        foreach($groups as $group)
        {
            $keyword_str .= "__('".wpl_db::escape($group->membership_name)."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;
            
            if(isset($group->maccess_long_description) and trim($group->maccess_long_description) != '') $keyword_str .= "__('".wpl_db::escape($group->maccess_long_description)."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;
            if(isset($group->maccess_short_description) and trim($group->maccess_short_description) != '') $keyword_str .= "__('".wpl_db::escape($group->maccess_short_description)."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;
        }
        
        $group_types = wpl_db::select("SELECT * FROM `#__wpl_user_group_types`");
        foreach($group_types as $group_type)
        {
            $keyword_str .= "__('".wpl_db::escape($group_type->name)."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;
            if(trim($group_type->description)) $keyword_str .= "__('".wpl_db::escape($group_type->description)."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;
        }
        
        $location_levels = array('1','2','3','4','5','6','7');
        foreach($location_levels as $location_level)
        {
            $locations = wpl_db::select("SELECT `name` FROM `#__wpl_location".$location_level."`");
            
            foreach($locations as $location)
            {
                $keyword_str .= "__('".wpl_db::escape($location->name)."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;
                if(isset($location->abbr)) $keyword_str .= "__('".wpl_db::escape($location->abbr)."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;
            }
        }
        
        $properties = wpl_db::select("SELECT `location1_name`, `location2_name`, `location3_name`, `location4_name`, `location5_name`, `location6_name`, `location7_name`, `field_42`, `street_no` FROM `#__wpl_properties`");
        foreach($properties as $property)
        {
            if(trim($property->location1_name)) $keyword_str .= "__('".wpl_db::escape($property->location1_name)."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;
            if(trim($property->location2_name)) $keyword_str .= "__('".wpl_db::escape($property->location2_name)."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;
            if(trim($property->location3_name)) $keyword_str .= "__('".wpl_db::escape($property->location3_name)."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;
            if(trim($property->location4_name)) $keyword_str .= "__('".wpl_db::escape($property->location4_name)."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;
            if(trim($property->location5_name)) $keyword_str .= "__('".wpl_db::escape($property->location5_name)."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;
            if(trim($property->location6_name)) $keyword_str .= "__('".wpl_db::escape($property->location6_name)."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;
            if(trim($property->location7_name)) $keyword_str .= "__('".wpl_db::escape($property->location7_name)."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;
            if(trim($property->field_42) and !is_numeric($property->field_42)) $keyword_str .= "__('".wpl_db::escape($property->field_42)."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;
            if(trim($property->street_no) and !is_numeric($property->street_no)) $keyword_str .= "__('".wpl_db::escape($property->street_no)."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;
        }

        // Calendar Addon
        if(wpl_global::check_addon('calendar'))
        {
        	$seasons = wpl_db::select("SELECT `name` FROM `#__wpl_addon_calendar_seasons`");
		    foreach($seasons as $season) $keyword_str .= "__('".wpl_db::escape($season->name)."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;

		    $time_periods = wpl_db::select("SELECT `name` FROM `#__wpl_addon_calendar_time_periods`");
		    foreach($time_periods as $time_period) $keyword_str .= "__('".wpl_db::escape($time_period->name)."', 'real-estate-listing-realtyna-wpl');".PHP_EOL;
        }
        
        $keyword_str .= "?>";
        
        $destination = WPL_ABSPATH.'languages'.DS.'keywords.php';
        wpl_file::write($destination, $keyword_str);
    }

    /**
     * Displays Compare page
     * @author Edward <edward@realtyna.com>
     * @static
     * @since WPL 3.0
     * @return void
     */
    public static function compare_display()
    {
        $html = wpl_html::getInstance();
        $html->set_title(__('Compare properties', 'real-estate-listing-realtyna-wpl'));

        _wpl_import('views.frontend.addon_pro.wpl_compare');

        $controller = new wpl_compare_controller();
        $controller->display();
    }

    /**
     * Get properties to compare from user favorites
     * @author Edward <edward@realtyna.com>
     * @static
     * @since WPL 3.0
     * @param array $pids
     * @param integer $kind
     * @return array
     */
    public static function compare_get_properties($pids = null, $kind = 0)
    {
        $result = array();
        if(!$pids) $pids = self::favorite_get_pids();

        if(!is_array($pids) or !sizeof($pids)) return array();
        $fields = self::compare_get_fields(false, false, $kind);

        foreach($pids as $pid)
        {
            $pid = (int) $pid;
            if($pid <= 0) continue;

            $is_active = (int) wpl_property::select_active_properties(" AND `id`='{$pid}'", "COUNT(`id`)", 'loadResult');
            if(!$is_active) continue;

            $result[$pid] = wpl_property::full_render($pid, $fields, null, array(), true);
        }

        return $result;
    }

    /**
     * Get compare table fields, or give fields and get categorized ones.
     * @author Edward <edward@realtyna.com>
     * @static
     * @since WPL 3.0
     * @param boolean $categorized
     * @param array|boolean $fields
     * @param integer $kind
     * @return array
     */
    public static function compare_get_fields($categorized = false, $fields = false, $kind = 0)
    {
        if(!is_array($fields) or !sizeof($fields)) $fields = wpl_flex::get_fields('', 1, $kind, 'compare_visible', 1);

        if(!$categorized) return $fields;

        $categories = wpl_flex::get_categories(1, $kind);

        $results = array();
        foreach($fields as $field)
        {
            if(!array_key_exists($field->category, $categories)) continue;

            $results[$categories[$field->category]->name][] = $field;
        }

        return $results;
    }

    /**
     * Filter fields to ones that exists at least in one of given properties
     * @author Edward <edward@realtyna.com>
     * @static
     * @since WPL 3.0
     * @param array $properties
     * @param array $fields
     * @param integer $kind
     * @return mixed
     */
    public static function compare_get_minimal_fields($properties, $fields = array(), $kind = 0)
    {
        if(!is_array($fields) or !sizeof($fields)) $fields = self::compare_get_fields(false, false, $kind);
        if(!is_array($properties) or !sizeof($properties)) return array();

        $minimal_fields = array();

        foreach ($fields as $field)
        {
            foreach($properties as $property)
            {
                $materials = $property['materials'];

                if(array_key_exists($field->table_column, $materials))
                {
                    /** With such structure we're fetching $minimal_fields[$i]->id into a vector **/
                    $minimal_fields_mapped = array_map(array('self', '_map_field_id'), $minimal_fields);

                    if(!in_array($field->id, $minimal_fields_mapped)) $minimal_fields[] = $field;
                }
            }
        }

        return $minimal_fields;
    }

    /**
     * Mapps fields id to an array
     * @TODO: make anonymous
     * @static
     * @author Edward
     * @since WPL 3.0.2
     * @param $element
     * @return int
     */
    private static function _map_field_id($element)
    {
        return is_object($element) ? $element->id : $element['id'];
    }

    /**
     * Gets compare page Url
     * @author Edward <edward@realtyna.com>
     * @static
     * @since WPL 3.0
     * @param string $pids
     * @return string
     */
    public static function compare_get_url($pids = '')
    {
        $pids = trim($pids);

        $nosef = wpl_sef::is_permalink_default();
        
        $home_type = wpl_global::get_wp_option('show_on_front', 'posts');
        $home_id = wpl_global::get_wp_option('page_on_front', 0);
        $wpl_main_page_id = wpl_sef::get_wpl_main_page_id();
        
        if($nosef or ($home_type == 'page' and $home_id == $wpl_main_page_id))
        {
            $url = wpl_sef::get_wpl_permalink(true);
            $url = wpl_global::add_qs_var('wplview', 'compare', $url);
            if($pids) $url = wpl_global::add_qs_var('pids', $pids, $url);
        }
        else
        {
            $url = rtrim(wpl_sef::get_wpl_permalink(true), '/') . '/v/compare';
            if($pids) $url .= '?pids=' . $pids;
        }
        
        return $url;
    }
    
    /**
     * Renders compare values also compare row values
     * @author Edward <edward@realtyna.com>
     * @static
     * @since WPL 3.0
     * @param array $properties
     * @param array $fields
     * @return mixed
     */
    public static function compare_render($properties, $fields)
    {
        $result = (object) array('values'=>array(), 'compare_row'=>array());

        if(!is_array($properties) or !is_array($fields) or !sizeof($properties) or !sizeof($fields)) return $result;

        $values = array();
        $compare_row_data = array();

        foreach($properties as $pid => $property)
        {
            $materials = $property['materials'];
            
            foreach ($fields as $field)
            {
                $value = '-';

                if($field->table_column == 'locations' and sizeof($materials['locations']['locations']))
                {
                    $value = implode(', ', array_reverse($materials['locations']['locations']));
                }
                elseif(array_key_exists($field->table_column, $materials))
                {
                    $current_entry = $materials[$field->table_column];

                    if(array_key_exists('value', $current_entry)) $value = $current_entry['value'];
                    if(array_key_exists('values', $current_entry)) $value = implode(', ', $current_entry['values']);
                }

                if(trim($value) == '') $value = '&nbsp;';

                /** Setting general values**/
                $values[$pid][$field->table_column] = $value;

                /** Row compare **/
                if(isset($field->compare_row) and intval($field->compare_row) > 0)
                {
                    if(!isset($compare_row_data[$field->table_column]['base'])) $compare_row_data[$field->table_column]['base'] = intval($field->compare_row);
                    $compare_row_data[$field->table_column]['values'][$pid] = floatval(preg_replace('/[^0-9\.]/', '', $value));
                }
            }
        }

        $compare_row = array();

        foreach($compare_row_data as $field => $data)
        {
            $min = min($data['values']);
            $max = max($data['values']);

            /** Prevent PHP to generate that crazy notices about Strict rules **/
            $array_keys_min = array_keys($data['values'], $min);
            $array_keys_max = array_keys($data['values'], $max);

            $min_pid = reset($array_keys_min);
            $max_pid = reset($array_keys_max);

            unset($data['values'][$min_pid], $data['values'][$max_pid]);

            /** Duplicate on min or max doesn't accepted **/
            if(in_array($min, $data['values'])) unset($min_pid);
            if(in_array($max, $data['values'])) unset($max_pid);

            if($data['base'] == 1)
            {
                if(isset($min_pid)) $compare_row[$field]['worst'] = $min_pid;
                if(isset($max_pid)) $compare_row[$field]['best'] = $max_pid;
            }
            /** Lower is better **/
            else
            {
                if(isset($min_pid)) $compare_row[$field]['best'] = $min_pid;
                if(isset($max_pid)) $compare_row[$field]['worst'] = $max_pid;
            }
        }

        $result->values = $values;
        $result->compare_row = $compare_row;

        return $result;
    }

    public function set_expired_properties()
    {
        // Settings
        $settings = wpl_global::get_settings();

        // Check Status
        if(!isset($settings['lisexpr_status']) or (isset($settings['lisexpr_status']) and !$settings['lisexpr_status'])) return false;

        $validity = (isset($settings['lisexpr_validity_days']) and is_numeric($settings['lisexpr_validity_days']) and $settings['lisexpr_validity_days'] > 0) ? $settings['lisexpr_validity_days'] : 30;
        $expiry_date = date('Y-m-d H:i:s', strtotime('-'.$validity.' days'));

        $query = "SELECT `id` FROM `#__wpl_properties` WHERE `expired`='0' AND `finalized`='1' AND `last_finalize_date`!='' AND `last_finalize_date`!='0000-00-00 00:00:00' AND `last_finalize_date`<'$expiry_date'";
        $listings = wpl_db::select($query, 'loadAssocList');

        // Set Expired Listings
        $affected = 0;
        foreach($listings as $listing)
        {
            $pid = $listing['id'];
            if(!$pid) continue;

            $affected += wpl_db::q("UPDATE `#__wpl_properties` SET `expired`='1' WHERE `id`='$pid'", 'UPDATE');
            wpl_events::trigger('listing_expired', array('id'=>$pid));
        }

        return $affected;
    }

    public function send_expiration_notification()
    {
        // Settings
        $settings = wpl_global::get_settings();

        // Check Status
        if(!isset($settings['lisexpr_status']) or (isset($settings['lisexpr_status']) and !$settings['lisexpr_status'])) return false;

        $notice_days_str = isset($settings['lisexpr_notification_days']) ? $settings['lisexpr_notification_days'] : '';

        // Before Expiry Days is not set
        if(trim($notice_days_str) == '') return false;

        $ex = explode(',', $notice_days_str);
        $notice_days = is_array($ex) ? $ex : array(1);

        $validity = (isset($settings['lisexpr_validity_days']) and is_numeric($settings['lisexpr_validity_days']) and $settings['lisexpr_validity_days'] > 0) ? $settings['lisexpr_validity_days'] : 30;

        foreach($notice_days as $notice_day)
        {
            if(!trim($notice_day)) continue;

            $modified_date = date('Y-m-d H:i:s', strtotime('-'.($validity - $notice_day).' days'));

            $query = "SELECT `id` FROM `#__wpl_properties` WHERE `expired`='0' AND `finalized`='1' AND `last_finalize_date`!='' AND `last_finalize_date`!='0000-00-00 00:00:00' AND DATE_FORMAT(`last_finalize_date`, '%Y %m %d')=DATE_FORMAT('$modified_date', '%Y %m %d')";
            $listings = wpl_db::select($query, 'loadAssocList');

            foreach($listings as $listing) wpl_events::trigger('listing_about_expire', array('id'=>$listing['id'], 'days'=>$notice_day));
        }

        return true;
    }

    public function show_compare($instance = array())
    {
        $listings_str = isset($instance['listings']) ? $instance['listings'] : NULL;
        if(!trim($listings_str)) return __("You should add some listings to compare!", 'real-estate-listing-realtyna-wpl');

        $listings = explode(',', $listings_str);

        // Convert Listing IDs to Property IDs
        $pids = '';
        foreach($listings as $listing) $pids .= wpl_property::pid(trim($listing)).',';

        // Set Property IDs
        $instance['pids'] = trim($pids, ', ');

        // Import Controller
        _wpl_import('views.frontend.addon_pro.wpl_compare');

        // Call the Function
        $controller = new wpl_compare_controller();
        return $controller->display($instance);
    }
}