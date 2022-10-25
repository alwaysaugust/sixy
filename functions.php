<?php

$fullUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 
                "https" : "http") . "://" . $_SERVER['HTTP_HOST'] .  
                $_SERVER['REQUEST_URI'];

$fullPath = parse_url($fullUrl, PHP_URL_PATH);
$finalUrl = explode('/', $fullPath)[1];

//Hide icons menu on dashboard
/**
 * David.M
 * I disabled this line because it is causing an issue for ajax requests
 */
//if (isset($_GET['wplmethod']) && $_GET['wplmethod']){?
//    <style type="text/css">#block-icons-menu{display:none;}</style>
//?php }

function theme_enqueue_styles() {
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', [] );
    wp_enqueue_style( 'child-core', get_stylesheet_directory_uri() . '/core.css', [] );
    
    // wp_enqueue_style( 'responsive', get_stylesheet_directory_uri() . '/assets/css/responsive.css', [] );
    
    wp_enqueue_style( 'custom', get_stylesheet_directory_uri() . '/assets/css/custom-style.css', [] );
    
    // wp_enqueue_style( 'mobile-slider', get_stylesheet_directory_uri() . '/assets/css/mobile-slider.css', [] );
    
    // wp_enqueue_script( 'mobile-slider', get_stylesheet_directory_uri() . '/assets/js/mobile-slider.js', array ( 'jquery' ), 1.1, true);

      $heroStyle = get_field('hero_styles', 20023); //Tied to Homepage
      $gallStyle = get_field('gallery_styles', 18478); //Tied to Gallery
      $headStyle = get_field('header_styles','options'); //Tied to Sixy-Options Menu
    
    if( !empty($headStyle) ) {
        wp_enqueue_style( 'variations-header', get_stylesheet_directory_uri() . '/variations/'.$headStyle.'.css', [] ); //NAV        
    }    
    if( !empty($heroStyle) ) {
        wp_enqueue_style( 'variations-hero', get_stylesheet_directory_uri() . '/variations/'.$heroStyle.'.css', [] ); //HERO        
    }
    if( !empty($gallStyle) ) {
        wp_enqueue_style( 'variations-gallery', get_stylesheet_directory_uri() . '/variations/'.$gallStyle.'.css', [] ); //GALLERY
    }
    wp_enqueue_script( 'customjs', get_stylesheet_directory_uri() . '/assets/js/customjs.js', array ( 'jquery' ), 1.1, true);
}

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles', 20 );

//OPTIONS MENU SETUP
if( function_exists('acf_add_options_page') ) {
    acf_add_options_page(
        array(
            'page_title' => 'Sixy Options',
            'icon_url' => 'dashicons-admin-home'//'dashicons-admin-generic'
            )
    );
}

/* ACF ----
  ______  __  __  ______  ______  ______  ______  ______  _____   ______  ______    
 /\  ___\/\ \_\ \/\  __ \/\  == \/\__  _\/\  ___\/\  __ \/\  __-./\  ___\/\  ___\   
 \ \___  \ \  __ \ \ \/\ \ \  __<\/_/\ \/\ \ \___\ \ \/\ \ \ \/\ \ \  __\\ \___  \  
  \/\_____\ \_\ \_\ \_____\ \_\ \_\ \ \_\ \ \_____\ \_____\ \____-\ \_____\/\_____\ 
   \/_____/\/_/\/_/\/_____/\/_/ /_/  \/_/  \/_____/\/_____/\/____/ \/_____/\/_____/ 
*/                                                                                   

//--- CALCULATOR

function calculator_sc( $atts ){
    
    $calcBgcolor = get_sub_field('calc_section_background');
    
    ?>
    
    <div class="flx_calculator_wrapper" style="background-color:<?php echo $calcBgcolor; ?>">
        <?php include'calculator.php'; ?>
    </div>
    <?php
 }
add_shortcode( 'calculator-sc', 'calculator_sc' );

//--- HERO

function acf_hero( $atts ){?>
<div class="fusion-fullwidth fullwidth-box fusion-builder-row-2 fusion-flex-container fusion-parallax-none banner-homepage nonhundred-percent-fullwidth non-hundred-percent-height-scrolling" style="background-image: url(<?php echo the_field('hero_background_image', 20023);?>);">
    <div class="fusion-builder-row fusion-row fusion-flex-align-items-flex-start">
    <div class="fusion-layout-column fusion_builder_column fusion-builder-column-4 fusion_builder_column_1_1 1_1 fusion-flex-column fusion-flex-align-self-center banner-messaging">
        <div class="fusion-title title fusion-title-1 fusion-sep-none fusion-title-text fusion-title-size-one"><h1 class="title-heading-left fusion-responsive-typography-calculated"><?php echo the_field('hero_title', 20023);?></h1></div>
    </div>
    <div class="fusion-text fusion-text-3">
        <p class="banner-messaging-paragraph"><?php echo the_field('hero_text', 20023);?></p>
    </div>

 <?php
 $heroStyle = get_field('hero_styles', 20023);
 if($heroStyle == 'Hero1') {
    echo '<div class="banner-button"><a href="./mapview/" class="fusion-button button-flat fusion-button-default-size button-default button-all" target="_self"><span class="fusion-button-text">View Listings</span></a></div>'; }?>

    <?php /*if( empty($heroStyle) ) {
             
}*/ ?>
     <!--<div class="fusion-text fusion-text-5 block-hp-search"><?php //echo do_shortcode( '[wpl_widget_instance id="wpl_search_widget-7"]' );?></div>-->
   </div>
</div>

<?php }
add_shortcode( 'acf-hero', 'acf_hero' );

/*function acf_form_footer( $atts ){ ?>

<div><?php echo do_shortcode( '[fusion_form form_post_id="18587"]' );?></div>

<?php }
add_shortcode( 'acf-form-footer', 'acf_form_footer' );*/


//------------- //////////////// FLEXI /////////////////////////////////////////

function flexible_content( $atts ){

if( have_rows('flx_homepage') ):
    while( have_rows('flx_homepage') ) : the_row();

        // Get parent value.
        //$parent_title = get_sub_field('properties_columns');

//--- PROP
        if( have_rows('col_prop') ):
            while( have_rows('col_prop') ) : the_row();

            $propertiesTitle = get_sub_field('prop_title');
            $propertiesGrid = get_sub_field('prop_shortcode');
            $propertiesButton = get_sub_field('prop_button');
            $propertiesLink = get_sub_field('prop_button_link');
            $propertiesBgcolor = get_sub_field('prop_section_background');
           
            ?>
            
            <div class="prop_grid_wrapper" style="background-color:<?php echo $propertiesBgcolor; ?>"> 
                <div class="fusion-text fusion-text-5 block-hp-search"><?php echo do_shortcode( '[wpl_widget_instance id="wpl_search_widget-7"]' );?></div>
              <div class="prop_grid">
                <h3><?php echo $propertiesTitle; ?></h3>
                
                <div class="block-hp-featured"><?php echo $propertiesGrid ?>
                    <a href="<?php echo $propertiesLink; ?>" class="fusion-button button-flat fusion-button-default-size button-default button-all" target="_self"><span class="fusion-button-text"><?php echo $propertiesButton ?></span></a>
                </div>
                </div>
            </div>
      <?php 
      endwhile; 
      endif;
         
//--- TEXT
        if( have_rows('col_text') ):
             while( have_rows('col_text') ) : the_row();
             $general_text = get_sub_field('flx_text');
        ?>
             
            <div class="fusion-fullwidth fullwidth-box fusion-builder-row-2 fusion-flex-container flx_text">
                <P><?php echo $general_text; ?></P>
            </div>
     <?php 
      endwhile; 
      endif;

//--- IMAGES
        if( have_rows('col_img') ):
        while( have_rows('col_img') ) : the_row();
             $general_image = get_sub_field('flx_image');
             ?>
                <div class="fusion-fullwidth fullwidth-box fusion-builder-row-2 fusion-flex-container flx_text">
                    <img src="<?php echo $general_image; ?>" />
                </div>
     <?php 
      endwhile; 
      endif;
    
//--- CALL TO ACTION BANNER
        if( have_rows('col_cta') ):
        while( have_rows('col_cta') ) : the_row();
  
             $cta_title = get_sub_field('flx_cta_title');
             $cta_button = get_sub_field('flx_cta_button');
             $cta_link = get_sub_field('flx_cta_link');
             $cta_image = get_sub_field('flx_cta_image');
             $ctaBgcolor = get_sub_field('cta_section_background');
             
        ?>
     <div class="flx_cta_wrapper" style="background-color:<?php echo $ctaBgcolor; ?>">        
        <div class="fusion-layout-column fusion_builder_column fusion-builder-column-24 fusion_builder_column_1_1 1_1 fusion-flex-column flx_cta">
          <div class="fusion-title title fusion-title-3 fusion-sep-none fusion-title-text fusion-title-size-one flx_cta_inner" style="background-image: url('<?php echo $cta_image; ?>'); background-position:left top;background-repeat:no-repeat;-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover;background-color:#313a41; padding: 0px 0px 0px 0px;">
              <h3 class="white fusion-responsive-typography-calculated"><?php echo $cta_title; ?></h3>
                 <a href="<?php echo $cta_link; ?>" class="fusion-button button-flat fusion-button-default-size button-default button-all" target="_self"><span class="fusion-button-text"><?php echo $cta_button; ?></span></a>
                  </div>
                <div>
              </div>
            </div>
          </div>
                
     <?php 
      endwhile; 
      endif;

//--- NEIGHBOURHOODS
    if( have_rows('col_neighbourhoods') ):
        while( have_rows('col_neighbourhoods') ) : the_row();
             
             $neighbourhoodsBgcolor = get_sub_field('neighbourhoods_section_background');
           
    if( have_rows('neighbourhoods_tile1') ):
         while( have_rows('neighbourhoods_tile1') ) : the_row();
        
         $neighbourhoods_tile_title1 = get_sub_field('grp_neighbourhoods_tile_title');
         $neighbourhoods_tile_bg1 = get_sub_field('grp_neighbourhoods_tile_bg');
         
      endwhile; 
      endif;
      
    if( have_rows('neighbourhoods_tile2') ):
         while( have_rows('neighbourhoods_tile2') ) : the_row();
        
         $neighbourhoods_tile_title2 = get_sub_field('grp_neighbourhoods_tile_title');
         $neighbourhoods_tile_bg2 = get_sub_field('grp_neighbourhoods_tile_bg');
         
      endwhile; 
      endif;
      
        if( have_rows('neighbourhoods_tile3') ):
                while( have_rows('neighbourhoods_tile3') ) : the_row();
        
         $neighbourhoods_tile_title3 = get_sub_field('grp_neighbourhoods_tile_title');
         $neighbourhoods_tile_bg3 = get_sub_field('grp_neighbourhoods_tile_bg');
         
      endwhile; 
      endif;
      
        if( have_rows('neighbourhoods_tile4') ):
                while( have_rows('neighbourhoods_tile4') ) : the_row();
        
         $neighbourhoods_tile_title4 = get_sub_field('grp_neighbourhoods_tile_title');
         $neighbourhoods_tile_bg4 = get_sub_field('grp_neighbourhoods_tile_bg');
         
      endwhile; 
      endif;
      
        if( have_rows('neighbourhoods_tile5') ):
                while( have_rows('neighbourhoods_tile5') ) : the_row();
        
         $neighbourhoods_tile_title5 = get_sub_field('grp_neighbourhoods_tile_title');
         $neighbourhoods_tile_bg5 = get_sub_field('grp_neighbourhoods_tile_bg');
         
      endwhile; 
      endif;
      
        if( have_rows('neighbourhoods_tile6') ):
                while( have_rows('neighbourhoods_tile6') ) : the_row();
        
         $neighbourhoods_tile_title6 = get_sub_field('grp_neighbourhoods_tile_title');
         $neighbourhoods_tile_bg6 = get_sub_field('grp_neighbourhoods_tile_bg');
         
      endwhile; 
      endif;
             
    ?>
    <div class="flx_neighbourhoods_wrapper" style="background-color:<?php echo $neighbourhoodsBgcolor; ?>">
        <div class="fusion-layout-column fusion_builder_column fusion-builder-column-24 fusion_builder_column_1_1 1_1 fusion-flex-column flx_neighbourhoods">
             <div class="d1 fusion-layout-column fusion-flex-column tile-city" style="background-image: url('<?php echo $neighbourhoods_tile_bg1; ?>');">
                 <p><span class="city-tag"><?php echo $neighbourhoods_tile_title1; ?></span></p>
             </div>
             <div class="d2 fusion-layout-column fusion-flex-column tile-city" style="background-image: url('<?php echo $neighbourhoods_tile_bg2; ?>');">
                 <p><span class="city-tag"><?php echo $neighbourhoods_tile_title2; ?></span></p>
             </div>
             <div class="d3 fusion-layout-column fusion-flex-column tile-city" style="background-image: url('<?php echo $neighbourhoods_tile_bg3; ?>');">
                 <p><span class="city-tag"><?php echo $neighbourhoods_tile_title3; ?></span></p>
             </div>
             <div class="d4 fusion-layout-column fusion-flex-column tile-city" style="background-image: url('<?php echo $neighbourhoods_tile_bg4; ?>');">
                 <p><span class="city-tag"><?php echo $neighbourhoods_tile_title4; ?></span></p>
             </div>
             <div class="d5 fusion-layout-column fusion-flex-column tile-city" style="background-image: url('<?php echo $neighbourhoods_tile_bg5; ?>');">
                 <p><span class="city-tag"><?php echo $neighbourhoods_tile_title5; ?></span></p>
             </div>
             <div class="d6 fusion-layout-column fusion-flex-column tile-city" style="background-image: url('<?php echo $neighbourhoods_tile_bg6; ?>');">
                 <p><span class="city-tag"><?php echo $neighbourhoods_tile_title6; ?></span></p>
             </div>
        </div>
    </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
  $(document).ready(function() {
	$('.tile-city').css("cursor","pointer");
 	$('.tile-city').click(function() {
 	     var neighLocation = $(this).find("span").text();
    	 window.open("/tosell/neighbourhood/?"+neighLocation,"_blank");
	});
  });
</script>

     <?php 
       endwhile; 
       endif;
      
//--- SUBSCRIPTION FORM
        if( have_rows('col_subscription') ):
                while( have_rows('col_subscription') ) : the_row();
  
             $subscription_text = get_sub_field('flx_subscription_text');
             $subscriptionBgcolor = get_sub_field ('subscription_section_background');
             
             ?>
             <div class="flx_subscription_wrapper" style="background-color:<?php echo $subscriptionBgcolor; ?>">
             <div class="fusion-layout-column fusion_builder_column fusion-builder-column-24 fusion_builder_column_1_1 1_1 fusion-flex-column flx_subscription">
                   
                <div class="fusion-text fusion-text-5 flx_subscription_inner">
                        <h3><?php echo $subscription_text; ?></h3>
                </div>
                <div class="flx_subscription_form_wrapper">
                       <?php echo do_shortcode( '[fusion_form form_post_id="18536"]' );?>
                </div>
              </div>
            </div>
      <?php 
      endwhile; 
      endif;
      
//--- TESTIMONIALS
        if( have_rows('col_testimonials') ):
                while( have_rows('col_testimonials') ) : the_row();
  
             $testimonials_title = get_sub_field('flx_testimonials_title');
             $testimonials_image = get_sub_field('flx_testimonials_image');
             
            //Group
             if( have_rows('testimonials_data') ):
                while( have_rows('testimonials_data') ) : the_row();
                
                $testimonials_person_image = get_sub_field('grp_testimonials_person_image');
                $testimonials_person_name = get_sub_field('grp_testimonials_person_name');
                
             //echo 'test'.$testimonials_person_name;
             
             ?>
             <div class="fusion-layout-column fusion_builder_column fusion-builder-column-24 fusion_builder_column_1_1 1_1 fusion-flex-column flx_testimonials">
                   
                            <div class="fusion-text fusion-text-5 flx_testimonials_ui">
                                <h3><?php echo $testimonials_title; ?></h3>
                                <div class="ui-quotations"><img class="quote-left" src="/wp-content/uploads/sites/49/2022/02/icon_qt-down.png" /><img class="quote-right" src="/wp-content/uploads/sites/49/2022/02/icon_qt-up.png" /></div>
                            <div class="block-testimonials" style="background:none">
                        
  
<?php
echo do_shortcode('[fusion_testimonials design="classic" navigation="yes" speed="" backgroundcolor="" hue="" saturation="" lightness="" alpha="" textcolor="" random="" hide_on_mobile="small-visibility,medium-visibility,large-visibility" class="" id=""]

[fusion_testimonial name="'.$testimonials_person_name.'" avatar="image" image="" image_id="18602|full" image_border_radius="" company="Company Name" link="" target="_self"]
I found my perfect home with your platform thanks to easy to navigate user experience.[/fusion_testimonial]

[fusion_testimonial name="Adam A." avatar="image" image="" image_id="18602|full" image_border_radius="" company="Company Name" link="" target="_self"]
I found my perfect home with your platform thanks to easy to navigate user experience.[/fusion_testimonial]

[/fusion_testimonials]');?>

                    </div>
                      </div>
                        <div class="flx_testimonials_img">
                            <img src="<?php echo $testimonials_image; ?>" />
                        </div>
                    </div>
                    
      <?php 
      endwhile; 
      endif;
      endwhile; 
      endif;

//--- CONTACT FORM
        if( have_rows('col_contactForm') ):
                while( have_rows('col_contactForm') ) : the_row();
  
             $flx_contactForm_title = get_sub_field('flx_contactForm_title');
             $flx_contactForm_body = get_sub_field('flx_contactForm_body');
             $contactFormBgcolor = get_sub_field('contactForm_section_background');
        ?>
        <div class="flx_contact_wrapper" style="background-color:<?php echo $contactFormBgcolor; ?>">
            <div class="fusion-layout-column fusion_builder_column fusion-builder-column-24 fusion_builder_column_1_1 1_1 fusion-flex-column flx_contact">
               <h3><?php echo $flx_contactForm_title; ?></h3>
            <div><?php echo do_shortcode( $flx_contactForm_body );?></div>
         </div>
      </div>
    
    <?php 
      endwhile; 
      endif;

//--- BLOG
        if( have_rows('col_blog') ):
                while( have_rows('col_blog') ) : the_row();
  
             $blog_title = get_sub_field('flx_blog_title');
             $blog_button = get_sub_field('flx_blog_button');
             $blog_button_link = get_sub_field('flx_blog_button_link');
             $blogsBgcolor = get_sub_field('blog_section_background'); 
             
             ?>
        <div class="flx_blog_wrapper" style="background-color:<?php echo $blogsBgcolor; ?>">
        <div class="fusion-layout-column fusion_builder_column fusion-builder-column-24 fusion_builder_column_1_1 1_1 fusion-flex-column flx_blog">
                   
        <h3 style="text-align:left"><?php echo $blog_title; ?></h3>
          <div class="block-blog"><?php echo do_shortcode( '[fusion_recent_posts layout="default" picture_size="fixed" hover_type="none" columns="4" number_posts="4" post_status="" offset="0" pull_by="category" cat_slug="" exclude_cats="" tag_slug="" exclude_tags="" thumbnail="yes" title="yes" meta="yes" meta_author="no" meta_categories="no" meta_date="yes" meta_comments="no" meta_tags="no" content_alignment="left" excerpt="yes" excerpt_length="9" strip_html="yes" scrolling="no" hide_on_mobile="small-visibility,medium-visibility,large-visibility" class="" id="" animation_type="" animation_direction="left" animation_speed="0.3" animation_offset=""][/fusion_recent_posts]' );?></div>
            <a href="<?php echo $blog_button_link; ?>" class="fusion-button button-flat fusion-button-default-size button-default button-all" target="_self" style="width: 100%; margin: 0 auto; max-width: 200px; float: none; display: block;"><span class="fusion-button-text"><?php echo $blog_button ?></span></a>
        </div>
      </div>
      <?php 
      endwhile; 
      endif;

//--- TEAM
        if( have_rows('col_team') ):
             while( have_rows('col_team') ) : the_row();
  
             $about_bio_title = get_sub_field('flx_agent_bio_title');
             $agent_bio_text = get_sub_field('flx_agent_bio');
             $agent_name = get_sub_field('flx_agent_name');
             $agent_title = get_sub_field('flx_agent_title');
             $agent_image = get_sub_field('flx_agent_image');
             $agent_button = get_sub_field('flx_agent_contactButton');
             $agent_contact_link = get_sub_field('flx_agent_contact_link');
             $agent_recognition_image = get_sub_field('flx_agent_recognition');
             $teamBgcolor = get_sub_field('team_section_background');
             
             ?>
             
             <div class="flx_team_wrapper" style="background-color:<?php echo $teamBgcolor; ?>">
               <div class="fusion-layout-column fusion_builder_column fusion-builder-column-24 fusion_builder_column_1_1 1_1 fusion-flex-column flx_team">
                   
                            <div class="flx_team_inner">
                                <img src="<?php echo $agent_image; ?>" />
                                <div class="person-author-wrapper">
                                   <span class="person-name"><?php echo $agent_name; ?></span>
                                   <span class="person-title"><?php echo $agent_title; ?></span>
                               </div>
                            </div>
                            <div class="flx_agent_info">
                                <h3><?php echo $about_bio_title; ?></h3>
                                <p><?php echo $agent_bio_text; ?></p>
                                <a href="<?php echo $agent_contact_link ?>" class="fusion-button button-flat fusion-button-default-size button-default button-10 fusion-button-default-span fusion-button-default-type button-primary" target="_self"><span class="fusion-button-text"><?php echo $agent_button; ?></span></a>
                            
                            <div class="agent_recognition">
                            <?php if( $agent_recognition_image ): ?>
  
                            <?php foreach( $agent_recognition_image as $image_id ): ?>
                                 <img style="margin-right:20px" src="<?php echo $image_id['sizes']['thumbnail']; ?>" />
                            
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </div>
     <?php 
      endwhile; 
      endif;
         
//--- CALC
        if( have_rows('col_calc') ):
             while( have_rows('col_calc') ) : the_row();
             $calc = get_sub_field('flx_calc');
       
            endwhile;      
        endif;
    endwhile;
endif;
}

add_shortcode( 'flexible-content', 'flexible_content' );

//------------- //////////////// GLOBAL ACF ///////////////////////////////////////////

//LOGO
function logo_func( $atts ){
global $finalUrl;?>

    <a href="<?php echo '/'.$finalUrl; ?>" class="company-logo"><img src="<?php echo the_field('logo_image','options'); ?>"></a>

<?php }   

add_shortcode( 'logo-img', 'logo_func' );

//FOOTER LOGO
function logo_func_footer( $atts ){?>
    <div class="footer-logo"><img src="<?php echo the_field('logo_image_footer','options'); ?>"></div>
<?php }   

add_shortcode( 'logo-img-footer', 'logo_func_footer' );

//CONTACT & SOCIAL MEDIA
function header_func( $atts ){
    
    $email_f = get_field('email_address','options');
    $phone_f = get_field('phone_number','options');
    $address_f = get_field('address','options');
    
    $phoneFinal = sprintf("%s-%s-%s",
              substr($phone_f, 0, 3),
              substr($phone_f, 4, 3),
              substr($phone_f, 6, 4));
    
    $facebook_f = get_field('f_fb','options');
    $twitter_f = get_field('f_tw','options');
    $linkedin_f = get_field('f_ln','options');
    $instagram_f = get_field('f_im','options');
    $youtube_f = get_field('f_yt','options');
    
    if ($facebook_f != NULL)
        $fb_icon = '<img src="/wp-content/uploads/sites/49/2022/02/icon_social-facebook.png" />';
    if ($twitter_f != NULL)
        $tw_icon = '<img src="/wp-content/uploads/sites/49/2022/02/icon_social-twitter.png" />';
    if ($linkedin_f != NULL)
        $ln_icon = '<img src="/wp-content/uploads/sites/49/2022/02/icon_social-linkedin.png" />';
    if ($instagram_f != NULL)
        $im_icon = '<img src="/wp-content/uploads/sites/49/2022/02/icon_social-instagram.png" />';
    if ($youtube_f != NULL)
        $yt_icon = '<img src="/wp-content/uploads/sites/49/2022/02/icon_social-youtube.png" />';
?>

<div class="header-contact-wrapper">
<div class="header-social">
        <a href="<?php echo $facebook_f; ?>" target="blank"><?php echo $fb_icon; ?></a>
        <a href="<?php echo $twitter_f; ?>" target="blank"><?php echo $tw_icon; ?></a>
        <a href="<?php echo $linkedin_f; ?>" target="blank"><?php echo $ln_icon; ?></a>
        <a href="<?php echo $instagram_f; ?>" target="blank"><?php echo $im_icon; ?></a>
        <a href="<?php echo $youtube_f; ?>" target="blank"><?php echo $yt_icon; ?></a>
</div>

<div class="header-contact">
    <div class="header-email"><img src="/wp-content/uploads/sites/49/2022/02/icon_envelope.png" /><a href="mailto:<?php echo $email_f; ?>"><?php echo $email_f; ?></a></div>
    <div class="header-phone"><img src="/wp-content/uploads/sites/49/2022/02/icon_phone.png" /><a href="tel:<?php echo $phone_f; ?>"><?php echo $phoneFinal; ?></a></div>
    <div class="header-address"><img src="/wp-content/uploads/sites/49/2022/07/icon_address.png" /><a href=""><?php echo $address_f; ?></a></div>
</div>
</div>

 <?php }
  
add_shortcode( 'header-data', 'header_func' );


//PRIVACY & TERMS
function privacy_func( $atts  ){
    
global $finalUrl;

$entity_name = get_field('legal_entity_name');

$privacyIntro = '<p>This Privacy Policy describes how <u>'.$entity_name.'</u> (<b>“Real Estate Agent”, “Company”, “we”,</b> or <b>“our”</b>) collects, stores, uses and distributes 
information when you access and/or use the <u>'.$finalUrl.'</u> website, including when we provide any services or when you purchase, request, 
or obtain any services, including, but not limited to, from or on the <u>'.$finalUrl.'</u> website (each a <b>“Service”</b>) (the foregoing, collectively the <b>“Website”</b>). 
Real Estate Agent is a realtor providing real estate services.</p>';

return $privacyIntro;
}

add_shortcode( 'privacy-policy-intro', 'privacy_func' );


function terms_func( $atts ){

$entity_name = get_field('legal_entity_name');

$fullUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 
                "https" : "http") . "://" . $_SERVER['HTTP_HOST'] .  
                $_SERVER['REQUEST_URI'];

$fullPath = parse_url($fullUrl, PHP_URL_PATH);

$finalUrl = explode('/', $fullPath)[1];

$termsIntro = 'These Terms and Conditions of Use (“Terms of Use”) comprise a legal agreement between <u>'.$entity_name.'</u> (<b>“Real Estate Agent”, “Company”, “we”,</b> or <b>“our”</b>) 
and you, the person, who has legal capacity to enter contracts (“you”), accessing and/or using the <u>'.$finalUrl.'</u> website, including when we provide 
any services or when you request or obtain any services, including, but not limited to, from or on the <u>'.$finalUrl.'</u> website (each a “Service”) 
(the foregoing, collectively the “Website”).These Terms of Use set forth the legally binding terms and conditions for the use of the Website that is owned, operated 
and maintained, directly or indirectly, by Real Estate Agent, and all other sites owned and operated by Real Estate Agent that redirect to the Website, all subdomains 
provided through such other sites or the Website, and all downloadable applications, features, functionality, content or information that is made available or 
provided on this Website.';

return $termsIntro;
}

add_shortcode( 'terms-intro', 'terms_func' );

function avada_lang_setup() {
	$lang = get_stylesheet_directory() . '/languages';
	load_child_theme_textdomain( 'Avada', $lang );
}
add_action( 'after_setup_theme', 'avada_lang_setup' );

//Widgets PHP

function php_execute($html){
    if(strpos($html,"<"."?php")!==false){
    ob_start(); eval("?".">".$html); $html=ob_get_contents();
    ob_end_clean(); }
    return $html; 
}
add_filter('widget_text','php_execute',100);

function my_load_more_posts_name( $load_more_posts ) {
	$load_more_posts = "Load More";
	return $load_more_posts;
}
add_filter( 'avada_load_more_posts_name', 'my_load_more_posts_name' );

//overriding the default fusion builder shortcodes 
add_action( 'init', 'remove_my_shortcodes',20 );
function remove_my_shortcodes() {
    remove_shortcode( 'fusion_recent_posts' );
    require 'fusion-builder/shortcodes/fusion-overrides.php';
}

//SHORTCODE - neighbour trend
function neighbourhoodTrends_callback( $atts, $content = null  ) {
    
      $atts_data = shortcode_atts( array(
        'title' => '',
    ), $atts );
    // echo $atts_data['title'];
    
    ob_start();
    
    get_template_part(
    '/partials/neighbourhood-trends',
    'my-template',
    array(
        'title' => $atts_data['title'], // passing this array possible since WP 5.5
    )
);

    //get_template_part('/partials/neighbourhood-trends');
    return ob_get_clean();   
}
add_shortcode( 'neighbourhoodTrends', 'neighbourhoodTrends_callback' );


//SHORTCODE - neighbour trends
function neighbourhoodTrendsDynamic_callback( $atts, $content = null  ) {
    
    $neighbourhood_url = basename($_SERVER['REQUEST_URI']);
     $replace = str_replace("?", "", str_replace("%20", " ", str_replace("&wplpage=2", "",$neighbourhood_url)));
     
    //  echo $replace;die;
     
     // get url 
     $neighbourhood_url = get_permalink();
     $neighbourhood_basename = basename($neighbourhood_url);
     $test = ucfirst($replace);
    //  echo $test;
    // die;

  ob_start();
    
    get_template_part(
    '/partials/neighbourhood-trends-dynamic',
    'my-template',
    array(
        'title' => $test, // passing this array possible since WP 5.5
    )
);
    return ob_get_clean();   
}
add_shortcode( 'neighbourhoodTrendsDynamic', 'neighbourhoodTrendsDynamic_callback' );

//SHORTCODE - community trends
function communityTrendsDynamic_callback( $atts, $content = null  ) {
    
    $neighbourhood_url = basename($_SERVER['REQUEST_URI']);
     $replace = str_replace("?", "", str_replace("%20", " ", str_replace("&wplpage=2", "",$neighbourhood_url)));
     
    //  echo $replace;die;
     
     // get url 
     $neighbourhood_url = get_permalink();
     $neighbourhood_basename = basename($neighbourhood_url);
     $test = ucfirst($replace);
    //  echo $test;
    // die;

  ob_start();
    
    get_template_part(
    '/partials/community-trends-dynamic',
    'my-template',
    array(
        'title' => $test, // passing this array possible since WP 5.5
    )
);

    return ob_get_clean();   
}
add_shortcode( 'communityTrendsDynamic', 'communityTrendsDynamic_callback' );

//SHORTCODE - neighbour trend
function neighbourhoodListingDynamic_callback( $atts, $content = null  ) {
    
    $neighbourhood_url = basename($_SERVER['REQUEST_URI']);
     $replace = str_replace("?", "", str_replace("%20", " ", str_replace("&wplpage=2", "",$neighbourhood_url)));
     // get url 
     $neighbourhood_url = get_permalink();
     $neighbourhood_basename = basename($neighbourhood_url);
     $test = ucfirst($replace);
    //  echo $test;
    // die;

  ob_start();
    
    get_template_part(
    '/partials/neighbourhood-listing',
    'my-template',
    array(
        'title' => $test, // passing this array possible since WP 5.5
    )
);

    return ob_get_clean();   
}
add_shortcode( 'neighbourhoodListingDynamic', 'neighbourhoodListingDynamic_callback' );

//community listing shortcode
function communityListingDynamic_callback( $atts, $content = null  ) {
    
    $neighbourhood_url = basename($_SERVER['REQUEST_URI']);
     $replace = str_replace("?", "", str_replace("%20", " ", str_replace("&wplpage=2", "",$neighbourhood_url)));
     // get url 
     $neighbourhood_url = get_permalink();
     $neighbourhood_basename = basename($neighbourhood_url);
     $test = ucfirst($replace);
    //  echo $test;
    // die;

  ob_start();
    
    get_template_part(
    '/partials/community-listing',
    'my-template',
    array(
        'title' => $test, // passing this array possible since WP 5.5
    )
);

    return ob_get_clean();   
}
add_shortcode( 'communityListingDynamic', 'communityListingDynamic_callback' );

//neighbour trend banner shortcode
function neighbourhoodBannerDynamic_callback( $atts, $content = null  ) {
    
    $neighbourhood_url = basename($_SERVER['REQUEST_URI']);
     $replace = str_replace("?", "", str_replace("%20", " ", str_replace("&wplpage=2", "",$neighbourhood_url)));
     // get url 
     $neighbourhood_url = get_permalink();
     $neighbourhood_basename = basename($neighbourhood_url);
     $test = ucfirst($replace);
    //  echo $test;
    // die;

  ob_start();
    
    get_template_part(
    '/partials/neighbourhood-banner',
    'my-template',
    array(
        'title' => $test, // passing this array possible since WP 5.5
    )
);

    return ob_get_clean();   
}
add_shortcode( 'neighbourhoodBannerDynamic', 'neighbourhoodBannerDynamic_callback' );

//community trend banner shortcode
function communityBannerDynamic_callback( $atts, $content = null  ) {
    
    $neighbourhood_url = basename($_SERVER['REQUEST_URI']);
    $replace = str_replace("?", "", str_replace("%20", " ", str_replace("&wplpage=2", "",$neighbourhood_url)));
     
    $test = ucfirst($replace);
  ob_start();
    
    get_template_part(
    '/partials/community-banner',
    'my-template',
    array(
        'title' => $test, // passing this array possible since WP 5.5
    )
);

    return ob_get_clean();   
}
add_shortcode( 'communityBannerDynamic', 'communityBannerDynamic_callback' );

//cron data shortcode
function dataDynamic_callback( $atts, $content = null  ) {
    
  ob_start();
    
    get_template_part(
    '/partials/cron-files/data-neighbourhood');

    return ob_get_clean();   
}
add_shortcode( 'dataDynamic', 'dataDynamic_callback' );

function dataCommunityDynamic_callback( $atts, $content = null  ) {
    
  ob_start();
    
    get_template_part(
    '/partials/cron-files/data-community');

    return ob_get_clean();   
}
add_shortcode( 'dataCommunityDynamic', 'dataCommunityDynamic_callback' );

function neighbourNameDynamic_callback( $atts, $content = null  ) {
    
  ob_start();
    
    get_template_part(
    '/partials/cron-files/neighbourhood-name');

    return ob_get_clean();   
}
add_shortcode( 'neighbourNameDynamic', 'neighbourNameDynamic_callback' );

function communityNameDynamic_callback( $atts, $content = null  ) {
    
  ob_start();
    
    get_template_part(
    '/partials/cron-files/community-name');

    return ob_get_clean();   
}
add_shortcode( 'communityNameDynamic', 'communityNameDynamic_callback' );


function dateDiff($date1, $date2)
{
    $date1_ts = strtotime($date1);
    $date2_ts = strtotime($date2);
    $diff = $date2_ts - $date1_ts;
    return round($diff / 86400);
}

//neighbourhood
function get_community_table(){
  
    $title = '';
    ini_set('max_execution_time', 120);
    global $wpdb;
    $table_name = 'wp_wpl_properties';
    
    //Single Family
    $family_results = $wpdb->get_results(
        "SELECT * FROM $table_name where property_type = 3  And location5_name like '%".$title."%' OR location4_name like '%".$title."%' limit 1"
    );
    // //Condo
    $condo_results = $wpdb->get_results(
        "SELECT * FROM $table_name where property_type = 17  And location5_name like '%".$title."%' OR location4_name like '%".$title."%' limit 1"
    );
    //retail
    $retail_results = $wpdb->get_results(
        "SELECT * FROM $table_name where property_type = 6  And location5_name like '%".$title."%' OR location4_name like '%".$title."%' limit 1"
    );
    
    //Single Family
    $family_last_12_months = $wpdb->get_results(
        // "SELECT id,add_date,price FROM $table_name where location4_name like '%Toronto%' limit 1"
        "SELECT * FROM $table_name where where property_type = 3 and add_date> now() - INTERVAL 12 month OR location5_name like '%".$title."%' OR location4_name like '%".$title."%' limit 1"
    );
    //Condo
    $condo_last_12_months = $wpdb->get_results(
        // "SELECT id,add_date,price FROM $table_name where location4_name like '%Toronto%' limit 1"
        "SELECT * FROM $table_name where property_type = 17 and add_date> now() - INTERVAL 12 month OR location5_name like '%".$title."%' OR location4_name like '%".$title."%' limit 1"
    );
    //retail
    $retail_last_12_months = $wpdb->get_results(
        // "SELECT id,add_date,price FROM $table_name where location4_name like '%Toronto%' limit 1"
        "SELECT * FROM $table_name where property_type = 6 and add_date> now() - INTERVAL 12 month OR location5_name like '%".$title."%' OR location4_name like '%".$title."%' limit 1"
    );
    
    //family
    $family_for_rent = 0;
    $family_for_sale = 0;
    $family_sold_properties_prices = 0;
    $family_selling_price = 0;
    
    //condo
    $condo_for_rent = 0;
    $condo_for_sale = 0;
    $condo_sold_properties_prices = 0;
    $condo_selling_price = 0;
    
    //retail
    $retail_for_rent = 0;
    $retail_for_sale = 0;
    $retail_sold_properties_prices = 0;
    $retail_selling_price = 0;
    
    //last 12 months
    
    //family
    foreach($family_last_12_months as $family_last_value)
    {
         $rendered =  json_decode($family_last_value->rendered,true);
         $family_last_status = str_replace( ',', '', $family_last_value->f_3066_options );
        //  echo $rendered['rendered'][6]['raw'];
        if($rendered['materials']['listing']['value'] == 'For Rent'){
            $family_for_rent++;
        }
        if($family_last_status =='11'){
            $family_for_rent++;
        }
    }
    foreach($family_results as $family_results_value)
    {
         $family_dateDiff += dateDiff($family_results_value->add_date,date('Y-m-d H:i:s'));
         $family_status = str_replace( ',', '', $family_results_value->f_3066_options );
         if($family_status =='11'){
            $family_sold_properties_prices += $family_results_value->field_3069;
        }
        $family_selling_price += $family_results_value->price;
    }
    
    $family_list_vs_selling_price = (($family_sold_properties_prices - $family_selling_price)/$family_selling_price)*100;
    $family_total_count = count( $family_results );
    $family_days_on_the_market = round(($family_dateDiff)/$family_total_count);

    $family_list_vs_selling_price = round($family_list_vs_selling_price);
    $family_up_or_down  = '';
    if($family_list_vs_selling_price > 0){
          $family_up_or_down  = "Up";
        }
        elseif($family_list_vs_selling_price < 0){
          $family_up_or_down = "% Below";
        }
                            
    $family_list_vs_selling_price = $family_list_vs_selling_price.$family_up_or_down;
    
    //condo
    foreach($condo_last_12_months as $condo_last_value)
    {
         $rendered =  json_decode($condo_last_value->rendered,true);
         $condo_last_status = str_replace( ',', '', $condo_last_value->f_3066_options );
        //  echo $rendered['rendered'][6]['raw'];
        if($rendered['materials']['listing']['value'] == 'For Rent'){
            $condo_for_rent++;
        }
        if($condo_last_status =='11'){
            $condo_for_rent++;
        }
    }
    foreach($condo_results as $condo_results_value)
    {
         $condo_dateDiff += dateDiff($condo_results_value->add_date,date('Y-m-d H:i:s'));
         $condo_status = str_replace( ',', '', $condo_results_value->f_3066_options );
         if($condo_status =='11'){
            $condo_sold_properties_prices += $condo_results_value->field_3069;
        }
        $condo_selling_price += $condo_results_value->price;
    }
    
    $condo_list_vs_selling_price = (($condo_sold_properties_prices - $condo_selling_price)/$condo_selling_price)*100;
    $condo_total_count = count( $condo_results );
    $condo_days_on_the_market = round(($condo_dateDiff)/$condo_total_count);

    $condo_list_vs_selling_price = round($condo_list_vs_selling_price);
    $condo_up_or_down  = '';
    if($condo_list_vs_selling_price > 0){
          $condo_up_or_down  = "Up";
        }
        elseif($condo_list_vs_selling_price < 0){
          $condo_up_or_down = "% Below";
        }
                            
    $condo_list_vs_selling_price = $condo_list_vs_selling_price.$condo_up_or_down;
    
    //retail
    foreach($retail_last_12_months as $retail_last_value)
    {
         $rendered =  json_decode($retail_last_value->rendered,true);
         $retail_last_status = str_replace( ',', '', $retail_last_value->f_3066_options );
        //  echo $rendered['rendered'][6]['raw'];
        if($rendered['materials']['listing']['value'] == 'For Rent'){
            $retail_for_rent++;
        }
        if($retail_last_status =='11'){
            $retail_for_rent++;
        }
    }
    foreach($retail_results as $retail_results_value)
    {
         $retail_dateDiff += dateDiff($retail_results_value->add_date,date('Y-m-d H:i:s'));
         $retail_status = str_replace( ',', '', $retail_results_value->f_3066_options );
         if($retail_status =='11'){
            $retail_sold_properties_prices += $retail_results_value->field_3069;
        }
        $retail_selling_price += $retail_results_value->price;
    }
    
    $retail_list_vs_selling_price = (($retail_sold_properties_prices - $retail_selling_price)/$retail_selling_price)*100;
    $retail_total_count = count( $retail_results );
    $retail_days_on_the_market = round(($retail_dateDiff)/$retail_total_count);

    $retail_list_vs_selling_price = round($retail_list_vs_selling_price);
    $retail_up_or_down  = '';
    if($retail_list_vs_selling_price > 0){
          $retail_up_or_down  = "Up";
        }
        elseif($retail_list_vs_selling_price < 0){
          $retail_up_or_down = "% Below";
        }
                            
    $retail_list_vs_selling_price = $retail_list_vs_selling_price.$retail_up_or_down;
                                
    $neighbourhood = '<section class="neighboor">
        	<div class="inner-neighbor">
        		<div class="container-fluid p-5">
        			<div class="row">
        				<div class="col-12 col-md-4 nbh-counter-main pad-left">
        					<div class="neighborBoxTitle">
        						Single Family
        					</div>
        					<div class="neighborMainBox">
        					<div class="neighborBox">
        						<div class="nbhBox-top">
        							<div class="nbhBox-contet">
        								<span class="count-descripts">Days on Market</span>
        							</div>
        						</div>
        						<div class="nbhBox-bottom">
        							<span class="nbh-bold">'.$family_days_on_the_market.' Days</span>
        						</div>
        					</div>
        					<div class="neighborBox">
        						<div class="nbhBox-top">
        							<div class="nbhBox-contet">
        								<span class="count-descripts">List vs. Selling Price</span>
        							</div>
        						</div>
        						<div class="nbhBox-bottom">
        							<span class="nbh-bold">'.$family_list_vs_selling_price.'</span>
        						</div>
        					</div>
        					<div class="neighborBox">
        						<div class="nbhBox-top">
        							<div class="nbhBox-contet">
        								<span class="count-descripts">Sold <span class="inner-muted">(12mo)</span></span>
        							</div>
        						</div>
        						<div class="nbhBox-bottom">
        							<span class="nbh-bold">'.$family_for_sale.'</span>
        						</div>
        					</div>
        					<div class="neighborBox">
        						<div class="nbhBox-top">
        							<div class="nbhBox-contet">
        								<span class="count-descripts">Rented <span class="inner-muted">(12mo)</span></span>
        							</div>
        						</div>
        						<div class="nbhBox-bottom">
        							<span class="nbh-bold">'.$family_for_rent.'</span>
        						</div>
        					</div>
        				</div>
        				</div>
        				<div class="col-12 col-md-4 nbh-counter-main">
        					<div class="neighborBoxTitle">
        						Condo
        					</div>
        					<div class="neighborMainBox">
        					<div class="neighborBox">
        						<div class="nbhBox-top">
        							<div class="nbhBox-contet">
        								<span class="count-descripts">Days on Market</span>
        							</div>
        						</div>
        						<div class="nbhBox-bottom">
        							<span class="nbh-bold">'.$condo_days_on_the_market.' Days</span>
        						</div>
        					</div>
        					<div class="neighborBox">
        						<div class="nbhBox-top">
        							<div class="nbhBox-contet">
        								<span class="count-descripts">List vs. Selling Price</span>
        							</div>
        						</div>
        						<div class="nbhBox-bottom">
        							<span class="nbh-bold">'.$condo_list_vs_selling_price.'</span>
        						</div>
        					</div>
        					<div class="neighborBox">
        						<div class="nbhBox-top">
        							<div class="nbhBox-contet">
        								<span class="count-descripts">Sold <span class="inner-muted">(12mo)</span></span>
        							</div>
        						</div>
        						<div class="nbhBox-bottom">
        							<span class="nbh-bold">'.$condo_for_sale.'</span>
        						</div>
        					</div>
        					<div class="neighborBox">
        						<div class="nbhBox-top">
        							<div class="nbhBox-contet">
        								<span class="count-descripts">Rented <span class="inner-muted">(12mo)</span></span>
        							</div>
        						</div>
        						<div class="nbhBox-bottom">
        							<span class="nbh-bold">'.$condo_for_rent.'</span>
        						</div>
        					</div>
        				</div>
        				</div>
        				<div class="col-12 col-md-4 nbh-counter-main pad-right">
        					<div class="neighborBoxTitle">
        						Retail
        					</div>
        					<div class="neighborMainBox">
        					<div class="neighborBox">
        						<div class="nbhBox-top">
        							<div class="nbhBox-contet">
        								<span class="count-descripts">Days on Market</span>
        							</div>
        						</div>
        						<div class="nbhBox-bottom">
        							<span class="nbh-bold">'.$retail_days_on_the_market.' Days</span>
        						</div>
        					</div>
        					<div class="neighborBox">
        						<div class="nbhBox-top">
        							<div class="nbhBox-contet">
        								<span class="count-descripts">List vs. Selling Price</span>
        							</div>
        						</div>
        						<div class="nbhBox-bottom">
        							<span class="nbh-bold">'.$retail_list_vs_selling_price.'</span>
        						</div>
        					</div>
        					<div class="neighborBox">
        						<div class="nbhBox-top">
        							<div class="nbhBox-contet">
        								<span class="count-descripts">Sold <span class="inner-muted">(12mo)</span></span>
        							</div>
        						</div>
        						<div class="nbhBox-bottom">
        							<span class="nbh-bold">'.$retail_for_sale.'</span>
        						</div>
        					</div>
        					<div class="neighborBox">
        						<div class="nbhBox-top">
        							<div class="nbhBox-contet">
        								<span class="count-descripts">Rented <span class="inner-muted">(12mo)</span></span>
        							</div>
        						</div>
        						<div class="nbhBox-bottom">
        							<span class="nbh-bold">'.$retail_for_rent.'</span>
        						</div>
        					</div>
        				</div>
        				</div>
        			</div>
        		</div>
        	</div>
        </section>';

echo $neighbourhood;


   wp_die();// this is required to terminate immediately and return a proper response
}


add_action('wp_ajax_get_community_table', 'get_community_table'); // for logged in users only
add_action('wp_ajax_nopriv_get_community_table', 'get_community_table'); // for ALL users
add_action('wp_head', 'myplugin_community_ajaxurl');

function myplugin_community_ajaxurl() {

echo '<script type="text/javascript">
           var ajaxurl = "' . admin_url('admin-ajax.php') . '";
         </script>';
   ?>
   
   <script type="text/javascript" >

      var data = {
         'action'   : 'get_community_table', 
         };
       
      jQuery.post(ajaxurl, data, function(response) {
        //   jQuery(".wpl_category_6").remove();
          jQuery('#community-page').after(response).fadeIn();
         
      });
  
</script>
   
   <?php
}

function get_property_trend(){
  
    $title = '';
    $r = $_SERVER['HTTP_REFERER']; 
    $r = explode('/', $r);
    $lastparturl = explode('-', $r[5]);
    $id = $lastparturl[0];
    
       global $wpdb;
       $id = $lastparturl[0];
     if( empty($id) ) {
         die();
     }
        $table_name = 'wp_wpl_properties';
        $results = $wpdb->get_results(
            "SELECT location4_name FROM $table_name where id = $id"
        );
    $location = $results[0]->location4_name;
   
    $title = $location;
    
    $table_name = 'wp_wpl_properties';
    $results = $wpdb->get_results(
        // "SELECT id,add_date,price FROM $table_name where location4_name like '%Toronto%' limit 1"
        "SELECT * FROM $table_name where location4_name like '%".$title."%'"
    );
    
    $results_last_12_months = $wpdb->get_results(
        // "SELECT id,add_date,price FROM $table_name where location4_name like '%Toronto%' limit 1"
        "SELECT * FROM $table_name where location4_name like '%".$title."%' and add_date> now() - INTERVAL 12 month"
    );
    $for_rent = 0;
    $for_sale = 0;
    $sold_properties_prices = 0;
    $selling_price = 0;
    //last 12 months
    foreach($results_last_12_months as $value)
    {
         $rendered =  json_decode($value->rendered,true);
         $status = str_replace( ',', '', $value->f_3066_options );
        //  echo $rendered['rendered'][6]['raw'];
        if($rendered['materials']['listing']['value'] == 'For Rent'){
            $for_rent++;
        }
        if($status =='11'){
            $for_sale++;
        }
    }
    foreach($results as $value)
    {
         $dateDiff += dateDiff($value->add_date,date('Y-m-d H:i:s'));
         $status = str_replace( ',', '', $value->f_3066_options );
         if($status =='11'){
            $sold_properties_prices += $value->field_3069;
        }
        $selling_price += $value->price;
    }
    // echo "listing price ".$selling_price."<br>";
    // echo "sold price ".$sold_properties_prices;
    
    //$list_vs_selling_price = (($selling_price - $sold_properties_prices)/$selling_price)*100;
    $list_vs_selling_price = (($sold_properties_prices - $selling_price)/$selling_price)*100;
    $total_count = count( $results );
    $days_on_the_market = round(($dateDiff)/$total_count);

$list_vs_selling_price = round($list_vs_selling_price);
$up_or_down  = '';
if($list_vs_selling_price > 0){
                              $up_or_down  = "Up";
                            }
                            elseif($list_vs_selling_price < 0){
                              $up_or_down = "% Below";
                            }
                            
$list_vs_selling_price = $list_vs_selling_price.$up_or_down;
                            
$neighbourhood = '<div class="wpl_prp_show_detail_boxes wpl_category_6">
                            <div class="wpl_prp_show_detail_boxes_title"><span>Neighborhood Trends</span></div>
                            <div class="wpl-small-up-1 wpl-medium-up-1 wpl-large-up-2 wpl_prp_show_detail_boxes_cont"><section class="neighboor">
                            <div class="nb-hr">&nbsp;</div>
	<div class="inner-neighbor">
		<div class="container-fluid p-5">
			<div class="row">
				
				<div class="col-12 col-md-3 nbh-counter-main">
					<div class="neighborBox">
						<div class="nbhBox-top">
							<div class="nbhBox-contet">
								<span class="count-descripts">Days on Market</span>
							</div>
						</div>
						<div class="nbhBox-bottom">
							<span class="nbh-bold">'.$days_on_the_market.' Days</span>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-3 nbh-counter-main">
					<div class="neighborBox">
						<div class="nbhBox-top">
							<div class="nbhBox-contet">
								<span class="count-descripts">List vs. Selling Price</span>
							</div>
						</div>
						<div class="nbhBox-bottom">
							<span class="nbh-bold">'.$list_vs_selling_price.'</span>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-3 nbh-counter-main">
					<div class="neighborBox">
						<div class="nbhBox-top">
							<div class="nbhBox-contet">
								<span class="count-descripts">Offer Competition</span>
							</div>
						</div>
						<div class="nbhBox-bottom">
							<span class="nbh-bold">N/A</span>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-3 nbh-counter-main">
					<div class="neighborBox">
						<div class="nbhBox-top">
							<div class="nbhBox-contet">
								<span class="count-descripts">Turnover</span>
							</div>
						</div>
						<div class="nbhBox-bottom">
							<span class="nbh-bold">N/A</span>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-12 col-md-3 nbh-counter-main-sec">
					<div class="neighborBox">
						<div class="nbhBox-top">
							<div class="nbhBox-contet">
								<span class="count-descripts">Property Value </span><span class="count-descripts-span">(12mo)</span>
							</div>
						</div>
						<div class="nbhBox-bottom">
							<span class="nbh-bold">N/A<!--<img src="/wp-content/uploads/sites/49/2022/05/icons8-sort-right-30.png"/>-->
							</span>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-3 nbh-counter-main-sec">
					<div class="neighborBox">
						<div class="nbhBox-top">
							<div class="nbhBox-contet">
								<span class="count-descripts">Price Ranking</span>
							</div>
						</div>
						<div class="nbhBox-bottom">
							<span class="nbh-bold">N/A</span>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-3 nbh-counter-main-sec">
					<div class="neighborBox">
						<div class="nbhBox-top">
							<div class="nbhBox-contet">
								<span class="count-descripts">Sold </span><span class="count-descripts-span">(12mo)</span>
							</div>
						</div>
						<div class="nbhBox-bottom">
							<span class="nbh-bold">'.$for_sale.'</span>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-3 nbh-counter-main-sec">
					<div class="neighborBox">
						<div class="nbhBox-top">
							<div class="nbhBox-contet">
								<span class="count-descripts">Rented </span><span class="count-descripts-span">(12mo)</span>
							</div>
						</div>
						<div class="nbhBox-bottom">
							<span class="nbh-bold">'.$for_rent.'</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>	</div>
	</div>';

echo $neighbourhood;


   wp_die();// this is required to terminate immediately and return a proper response
}


add_action('wp_ajax_get_property_trend', 'get_property_trend'); // for logged in users only
add_action('wp_ajax_nopriv_get_property_trend', 'get_property_trend'); // for ALL users



add_action('wp_head', 'myplugin_ajaxurl');

function myplugin_ajaxurl() {

echo '<script type="text/javascript">
           var ajaxurl = "' . admin_url('admin-ajax.php') . '";
         </script>';
   ?>
   
   <script type="text/javascript" >

      var data = {
         'action'   : 'get_property_trend', 
         };
       
      jQuery.post(ajaxurl, data, function(response) {
          jQuery(".wpl_category_6").remove();
          jQuery('.wpl_category_0').after(response).fadeIn();
         
      });
  
</script>
   
   <?php
}
/**
 * David.M - Realtyna
 * this code is causing an issue on membership login
 *
//redirect users to homepage after logging out
add_action('wp_logout','redirect_to_homepage_after_logout');

function redirect_to_homepage_after_logout(){
  wp_safe_redirect( home_url() );
  exit;
}

//redirect users to homepage after login
add_action('wp_login','redirect_to_homepage_after_login');

function redirect_to_homepage_after_login(){
  wp_safe_redirect( home_url() );
  exit;
}
*/

// add_filter( 'query_vars', 'addnew_query_vars', 10, 1 );
// function addnew_query_vars($vars)
// {   
//     $vars[] = 'city';  
    
//     return $vars;
// }

// function custom_rewrite_basic() 
// {
    
//     add_rewrite_rule('^neighbourhood/([0-9]+)/?', 'neighbourhood?city=$1', 'top');
// }
// add_action('init', 'custom_rewrite_basic');



wp_enqueue_script('jquery');

function addUser() {

    global $wpdb;
    
    $username = $_POST['username'];
    $firstname = $_POST['first_name'];
    $lastname = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['[password]'];
    
    $user_id = wp_insert_user( array(
      'user_login' => $username,
      'user_pass' => $password,
      'user_email' => $email,
      'first_name' => $firstname,
      'last_name' => $lastname,
      'display_name' => $firstname .' '. $lastname,
      'role' => 'subscriber'
    ));

    if($user_id) {
        echo json_encode(array('success' => true));
    }
    else {
        wp_send_json_error(); // {"success":false}
        
    }
    die();
}
add_action('wp_ajax_addUser', 'addUser');
add_action('wp_ajax_nopriv_addUser', 'addUser');