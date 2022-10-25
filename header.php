<?php
/**
 * Header template.
 *
 * @package Avada
 * @subpackage Templates
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}
?>
<!DOCTYPE html>
<html class="<?php avada_the_html_class(); ?>" <?php language_attributes(); ?>>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<!--<script src="<?php //echo get_stylesheet_directory_uri(); ?>/js/custom.js" type="text/javascript"></script>-->
	
	<?php Avada()->head->the_viewport(); ?>

	<?php wp_head(); ?>

  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

	<?php
	/**
	 * The setting below is not sanitized.
	 * In order to be able to take advantage of this,
	 * a user would have to gain access to the database
	 * in which case this is the least of your worries.
	 */
	echo apply_filters( 'avada_space_head', Avada()->settings->get( 'space_head' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
	?>
</head>

<?php
$object_id      = get_queried_object_id();
$c_page_id      = Avada()->fusion_library->get_page_id();
$wrapper_class  = 'fusion-wrapper';
$wrapper_class .= ( is_page_template( 'blank.php' ) ) ? ' wrapper_blank' : '';
?>

<?php
global $current_user; wp_get_current_user();
if ( is_user_logged_in() ) {
  echo '<style>
        span.register-user-area {
            position: absolute;
            right: 21em;
            z-index: 11;
            top: 12px;
        }
        .my-account-btn-sec{
            width: auto;
            border: 1px solid #15161A;
            border-radius: 5px;
            background-color: #FFFFFF;
            padding: 10px 35px;
            color: #15161A;
            font-family: Inter;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: -0.14px;
            line-height: 20px;
            text-align: center;
        }
        
        // .my-account-btn-sec:hover {
        //     background: #141617 !important;
        //     color: #fff !important;
        // }
        
        .fusion-builder-row.fusion-builder-row-inner.fusion-row.fusion-flex-align-items-flex-start {
            display: none;
        }
        
        
        @media all and (max-width : 1800px){
          span.register-user-area {
                    position: absolute;
                    right: 18em !important;
                    z-index: 11;
                    top: 12px;
                }
        }
        @media all and (max-width : 1680px){
               span.register-user-area {
               right: 2em !important;
                }
        }

        @media all and (max-width : 1650px){
          span.register-user-area {
                    position: absolute;
                    right: 2em !important;
                    z-index: 11;
                    top: 10px;
                }
        }

        @media all and (max-width : 1512px){
          span.register-user-area {
          right: 8em !important;
        }
      }

      @media all and (max-width : 1440px){
          span.register-user-area {
          right: 3.5em !important;
          }
      }

      @media all and (max-width : 1366px){
          span.register-user-area {
          right: 2.5em !important;
          }
      }

        @media all and (max-width : 1360px){
          span.register-user-area {
          right: 2.5em !important;
          }
      }

        @media all and (max-width : 1312px){
          span.register-user-area {
          right: 3em !important;
        }
      }

      @media all and (max-width : 1168px){
          span.register-user-area {
          right: 2em !important;
        }
      }

        .dropbtn {
          background-color: #3498DB;
          color: white;
          padding: 16px;
          font-size: 16px;
          border: none;
          cursor: pointer;
        }
        
        .dropbtn:hover, .dropbtn:focus {
          background-color: #2980B9;
        }
        
        .dropdown {
          position: relative;
          display: inline-block;
        }
        
        .dropdown-content {
          display: none;
          position: absolute;
          background-color: #f1f1f1;
          min-width: 160px;
          overflow: auto;
          box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.5);
          z-index: 1;
        }
        
        .dropdown-content a {
          color: black;
          padding: 8px 16px;
          text-decoration: none;
          display: block;
          font-size: 14px;
        }
        
        .dropdown a:hover {background-color: #ddd;}
        
        .show {display: block;}
        
        span.user-arraw-icon {
            position: relative;
            top: 5px;
            left: 5px;
        }
    </style>';
} else {
  echo '<style>
        a.my-account-btn-sec{
            display: none;
        }
        a.fusion-button.menu-button-login, a.fusion-button.menu-button-register {
            font-size: 14px;
            border: 1px solid #15161a;
            color: #15161a;
            margin-top: 10px;
            padding-right: 22px;
            padding-left: 22px;
            margin-left: 3px;
            margin-right: 3px;
            display: block !important;
        }
    </style>';
}
?>
<script>
    function myFunction() {
  document.getElementById("myDropdown").classList.toggle("show");
}

// Close the dropdown if the user clicks outside of it
window.onclick = function(event) {
  if (!event.target.matches('.dropbtn')) {
    var dropdowns = document.getElementsByClassName("dropdown-content");
    var i;
    for (i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
}
</script>

<body <?php body_class(); ?> <?php fusion_element_attributes( 'body' ); ?>>
    <?php
    if ( is_user_logged_in() ) {
        
    }else{
        require 'partials/modal/register.php';
        require 'partials/modal/login.php';
        // require 'partials/modal/reset_password.php';
    } 
    ?>
	<?php do_action( 'avada_before_body_content' ); ?>
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'Avada' ); ?></a>

	<div id="boxed-wrapper">
		<div class="fusion-sides-frame"></div>
		<div id="wrapper" class="<?php echo esc_attr( $wrapper_class ); ?>">
			<div id="home" style="position:relative;top:-1px;">
		    <?php if ( is_user_logged_in() ) : ?>
		    <span class="register-user-area">
		        <div class="dropdown">
                  <button onclick="myFunction()" class="my-account-btn-sec"><img class="my-account-img" src="https://tosell.ca/wp-content/uploads/sites/49/2022/07/user.png" width="20">&nbsp;&nbsp; My Account
                  </button>
                  <div id="myDropdown" class="dropdown-content">
                    <a class="" target="_self" href="/tosell/properties-3/v/members/?wplmethod=profile">Profile</a>
    			    <a class="" target="_self" href="/tosell/properties-3/v/members/?wplmethod=logout">Logout</a>
                  </div>
                </div>
            </span>

			    <!--<span class="register-user-area">-->
    			<!--    <a class="my-account-btn-sec" target="_self" href="/tosell/properties-3/v/members/?wplmethod=profile">Profile</a>&nbsp;&nbsp;&nbsp;-->
    			<!--    <a class="my-account-btn-sec" target="_self" href="/tosell/properties-3/v/members/?wplmethod=logout">Logout</a>-->
			    <!--</span>-->
			<?php endif; ?>
		    </div>
			<?php if ( has_action( 'avada_render_header' ) ) : ?>
				<?php do_action( 'avada_render_header' ); ?>
			<?php else : ?>

				<?php avada_header_template( 'below', ( is_archive() || Avada_Helper::bbp_is_topic_tag() ) && ! ( class_exists( 'WooCommerce' ) && is_shop() ) ); ?>
				<?php if ( 'left' === fusion_get_option( 'header_position' ) || 'right' === fusion_get_option( 'header_position' ) ) : ?>
					<?php avada_side_header(); ?>
				<?php endif; ?>

				<?php avada_sliders_container(); ?>

				<?php avada_header_template( 'above', ( is_archive() || Avada_Helper::bbp_is_topic_tag() ) && ! ( class_exists( 'WooCommerce' ) && is_shop() ) ); ?>

			<?php endif; ?>

			<?php avada_current_page_title_bar( $c_page_id ); ?>

			<?php
			$row_css    = '';
			$main_class = '';

			if ( apply_filters( 'fusion_is_hundred_percent_template', false, $c_page_id ) ) {
				$row_css    = 'max-width:100%;';
				$main_class = 'width-100';
			}

			if ( fusion_get_option( 'content_bg_full' ) && 'no' !== fusion_get_option( 'content_bg_full' ) ) {
				$main_class .= ' full-bg';
			}
			do_action( 'avada_before_main_container' );
			?>
			<main id="main" class="clearfix <?php echo esc_attr( $main_class ); ?>">
				<div class="fusion-row" style="<?php echo esc_attr( $row_css ); ?>">