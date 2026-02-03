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

// Template loader for custom post type
class PE_Template_Loader {
  public function __construct() {
    add_filter('single_template', array($this, 'load_supportcenter_template'));
  }
  
  public function load_supportcenter_template($template) {
    global $post;
    
    // Überprüfe ob es ein supportcenter Custom Post Type ist
    if ($post->post_type == PE_SC_CTP_name) {
      // Plugin Template-Datei verwenden
      $plugin_template = plugin_dir_path(__FILE__) . 'templates/single-supportcenter.php';
      
      // Überprüfe ob die Plugin Template-Datei existiert
      if (file_exists($plugin_template)) {
        return $plugin_template;
      }
    }
    
    return $template;
  }
}
$pe_template_loader = new PE_Template_Loader();

class PE_style_and_js{
  public function __construct(){
    add_action( 'wp_enqueue_scripts', array($this, 'style_and_script'));
  }

  public function style_and_script(){
    // for all Supportcenter Pages
    if(is_page( PE_SC_Main_Page_slug ) || is_singular( PE_SC_CTP_name )){
      // Enqueue compiled CSS from build directory
      wp_enqueue_style('supportcenter-overall-css',  plugin_dir_url( __FILE__ ) .'build/index.css','','',false);
      wp_enqueue_script('modules-js',  plugin_dir_url( __FILE__ ) .'build/index.js','jQuery','',true);
      
      wp_localize_script('modules-js', 'scData' , array(
        'root_url' => get_site_url(),
        'currentModul' => get_post_field( 'post_name', get_post() ),
      ));

      // CSS wird jetzt über SCSS kompiliert und in build/index.css bereitgestellt
      // Separate Dateien für Main und Module Page sind nicht mehr nötig
    }
    if(is_singular( PE_SC_CTP_name )){
      wp_enqueue_script('supportcenter-js',  plugin_dir_url( __FILE__ ) .'includes/js_functions.js','','',true);
    }

  }
}
$pe_style_and_js = new PE_style_and_js();
