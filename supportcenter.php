<?php
/*
Plugin Name: Customized Supportcenter - TeamProQ by Page-Effect 
Plugin URI: 
Description: This plugin for customized supportcenter pages
Version: 0.0.0
Author URI: page-effect.com
*/

if (!defined('ABSPATH')) die('No direct access allowed');

if ( ! defined( 'PE_supportcenter_Plugin_Path' ) ) {
	define( 'PE_supportcenter_Plugin_Path', plugin_dir_path( __FILE__ ) );
}
// Supportcenter main page slug 
define( 'PE_SC_Main_Page_slug', 'supportcenter');
define( 'PE_SC_CTP_name', 'supportcenter');

//Generate new Custom type Post: supportcenter, new column in admin, filter for modul  /////////////////// 
if (!(isset($initialize_ctp) && is_a($initialize_ctp, 'PE_Initializ_CTP'))) {
  require_once  PE_supportcenter_Plugin_Path . 'includes/Init_CPT_supportcenter.php';
  $initialize_ctp = new PE_Initializ_CTP();
}

// Supportcenter main page 
// require_once plugin_dir_path(__FILE__) . 'includes/Init_CreateSCmainPage.php';
// module posts 
// require_once plugin_dir_path(__FILE__) . 'includes/Init_CreateModulPosts.php';
// childern posts
// require_once plugin_dir_path(__FILE__) . 'includes/Init_CreateModulChildern.php';

// delet all the posts with CTP:supportcenter
// $wpdb->query(
// 	$wpdb->prepare(
// 		"
//     DELETE a,b,c
//     FROM wp_posts a
//     LEFT JOIN wp_term_relationships b
//         ON (a.ID = b.object_id)
//     LEFT JOIN wp_postmeta c
//         ON (a.ID = c.post_id)
//     WHERE a.post_type = 'supportcenter';
//     ")
// );


require_once plugin_dir_path(__FILE__) . 'includes/view/SC_MainPageView.php';
require_once plugin_dir_path(__FILE__) . 'includes/view/SC_ModulPageView.php';


class PE_style_and_js{
  public function __construct(){
    add_action( 'wp_enqueue_scripts', array($this, 'style_and_script'));
  }

  public function style_and_script(){
    // for all Supportcenter Pages
    if(is_page( PE_SC_Main_Page_slug ) || is_singular( PE_SC_CTP_name )){
      wp_enqueue_style('supportcenter-overall-css',  plugin_dir_url( __FILE__ ) .'includes/view/SC_All.css','','',false);
      wp_enqueue_script('modules-js',  plugin_dir_url( __FILE__ ) .'build/index.js','jQuery','',true);
      
      wp_localize_script('modules-js', 'scData' , array(
        'root_url' => get_site_url(),
        'currentModul' => get_post_field( 'post_name', get_post() ),
      ));

      // only Suppportcenter Main 
      if(is_page( PE_SC_Main_Page_slug )){
        wp_enqueue_style('supportcenter-mainpage-css',  plugin_dir_url( __FILE__ ) .'includes/view/SC_MainPage.css','','',false);
      }
      // only Suppportcenter Module
      else if(is_singular( PE_SC_CTP_name )){
        wp_enqueue_style('supportcenter-mainpage-css',  plugin_dir_url( __FILE__ ) .'includes/view/SC_ModulePage.css','','',false);
      }
    }
    if(is_singular( PE_SC_CTP_name )){
      wp_enqueue_script('supportcenter-js',  plugin_dir_url( __FILE__ ) .'includes/js_functions.js','','',true);
    }

  }
}
$pe_style_and_js = new PE_style_and_js();
