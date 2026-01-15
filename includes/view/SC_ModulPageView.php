<?php

//Remove 'Private:' from private post title
add_filter( 'private_title_format', function ( $format ) {
  return '%s';
} );
//content customize
add_filter( 'the_content', 'supportcenter_modul_view' );

require_once plugin_dir_path(__FILE__) . '/ViewComponents.php';

function supportcenter_modul_view($content){
  global $post;
  $postId = $post->ID;
  // initialize 
  
  //only for supportcenter ctp && only for modul(no parent) 
  if(is_singular( 'supportcenter' ) && !get_post_parent($postId)){
    $supportcenter_header = supportcenter_header();
    // breadcrumms
    $breadcrumms = supportcenter_breadcrumms();
    // modul description 
    $modul_description = supportcenter_modul_description($postId);
    // Child Posts
    $childern_contents = supportcenter_get_childern_contents($postId);
    //scroll button
    $scroll_button = get_scroll_button_html();
    // module Überblick 
    $modul_ueberblick = supportcenter_module_ueberblick();
    //modify the incoming content 
    $content = $supportcenter_header . $breadcrumms . $modul_description. $childern_contents. $modul_ueberblick . $scroll_button;
  } 
  return $content; 
}
