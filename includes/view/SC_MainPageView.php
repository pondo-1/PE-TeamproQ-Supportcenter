<?php

//Remove 'Private:' from private post title
add_filter( 'private_title_format', function ( $format ) {
  return '%s';
} );
//content customize
add_filter( 'the_content', 'supportcenter_mainpage_view' );

require_once plugin_dir_path(__FILE__) . '/ViewComponents.php';

function supportcenter_mainpage_view($content){
  global $post;
  $postId = $post->ID;
  // initialize 
  
  //only for supportcenter ctp and only for modul 
  if(is_page( 'supportcenter' )){
    $supportcenter_header = supportcenter_header();
    // breadcrumms
    $breadcrumms = supportcenter_breadcrumms(); 
    // module Überblick 
    $modul_ueberblick = supportcenter_module_ueberblick();
    $content = $supportcenter_header . $breadcrumms . $modul_ueberblick;
  } 
  return $content; 
}


