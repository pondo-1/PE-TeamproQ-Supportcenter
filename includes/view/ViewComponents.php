<?php
function get_scroll_button_html(){
  $up_arrow_icon = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 330 330" xml:space="preserve">
  <path id="XMLID_224_" d="M325.606,229.393l-150.004-150C172.79,76.58,168.974,75,164.996,75c-3.979,0-7.794,1.581-10.607,4.394
    l-149.996,150c-5.858,5.858-5.858,15.355,0,21.213c5.857,5.857,15.355,5.858,21.213,0l139.39-139.393l139.397,139.393
    C307.322,253.536,311.161,255,315,255c3.839,0,7.678-1.464,10.607-4.394C331.464,244.748,331.464,235.251,325.606,229.393z"></path>
  </svg>';
  $up_arrow_icon = '<div class="up_arrow_icon">'.$up_arrow_icon.'</div>';
  $scroll_button_html = '
    <div class="scroll-buttons">
      <button onclick="scrollToTop()" id="btnUP" title="Go to top">'.$up_arrow_icon.'</button>
    </div>
  ';
return $scroll_button_html;
}

function supportcenter_get_childern_contents($postId){

  $args = array(
    'post_type' => 'supportcenter',
    'post_parent' => $postId,
    'posts_per_page' => -1,
    'orderby' => 'menu_order',
    'order' => 'ASC',
    ); 
  $childern = get_children( $args);
  $title_list='';
  $content_list = '';
  
  if ( $childern ) {
    foreach ( $childern as $child ) {
      $title_list .= '<li><a href="#'. $child->post_name .'">'.$child->post_title.'</a></li>';
      $content_list .= '
      <div class="single" id ='.'>'.'
        <div class="question" id = "'. $child->post_name .'">'. $child->post_title . '</div>
        <div class="answer">'. $child->post_content .'</div>
      </div>' ;
    }
  }

  $title_list = '<div class="title-wrapper"><ul>'.$title_list.'</ul></div>';
  $content_list = '<div class="qanda-wrapper">'.$content_list.'</div>';
  
  $childern_contents = '<div class="q_and_a_contents">'.$title_list.$content_list.'</div>';
  return $childern_contents;
}


function supportcenter_modul_description($postId){            
  $icon = '<div class="icon-and-capture"><i class="tpq-icon-font '. get_post_meta($postId, 'icon_class', true) .'"></i></div>';
  $content = '<div class="modul_content">'.get_the_content($postId).'</div>';
  $modul_description_html = '<div class="modul-description">'.$icon . $content. '</div>';
  return $modul_description_html;
}


function supportcenter_header() {
  global $post;
  $postId = $post->ID;
  $header_html='';
  $supportcenter_description = '
    <h3 class="supportcenter_mainpage_content">
      '.get_the_content($postId).'
    </h3>
  ';
  $searchbox = '
    <div class="search-wrapper">
      <div class="searchbox">
        <input type="search" id="search-term" placeholder="Wie können wir Ihnen helfen?">
        <button><i class="et-pb-icon">&#x55;</i></button>
      </div>
      <div class="search-results" id="search-results">
      </div>
    </div>
  ';
  $background_image = '
    <div class="bg_wrap">
      <div class="background-pattern" 
        style="
          background-image: 
            url(&quot;'.plugin_dir_url( __FILE__ ).'asset/Sechsecke.svg&quot;);">
      </div>
    </div>
  ';
  $bottom_decoration = '<div class="bottom_inside_divider"></div>';

  if ( is_page('supportcenter')){
    $header_title = '
    <div class="supportcenter_title_container">
      <h1 class="entry-title">'.get_the_title($postId).'</h1>
    </div>
  ';
  }
  //for supportcenter ctp and only for modul
  else if( is_singular( 'supportcenter' ) && !get_post_parent($postId)){
    $header_title = '
    <div class="supportcenter_title_container">
      <h1 class="entry-title">Modul '.get_the_title($postId).'</h1>
    </div>
  ';
  }
  $header_html = $header_title . $searchbox;
  // add bottom decoration 
  $header_html = $background_image . $header_html . $bottom_decoration; 

return '<div class="supportcenter_header">' . $header_html . '</div>';
}


function supportcenter_breadcrumms(){
  // this is only for the suppportcenter 
  global $post;
  $breadcrumms = "";
  $icon = '<i class="et-pb-icon">&#x35;</i>';
  $bc_startseite = '<a href="/">Startseite</a>';
  $bc_supportcenter = '<a href="/supportcenter/">Supportcenter</a>';
  
  if(is_singular('supportcenter')){
    $bc_current  = '<div>'. $post->post_title. '</div>';
    $breadcrumms .= '<div>' . $bc_supportcenter .'</div><div>'.$icon . $bc_current. '</div>';
  }
  //wrap breadcrumms in wrapper 
  $breadcrumms = '<div class="breadcrumms">'. $breadcrumms.'</div>';
  return $breadcrumms;
}

function supportcenter_module_ueberblick(){
  $section_title = "<h2>Alle Module im Überblick</h2>";
  // Argments for the parents. 'post_parent' => 0 means no parents
  // here we find Modul
  $args = array(
    'post_type' => 'supportcenter',
    'post_parent' => 0,
    'posts_per_page' => -1,
    'orderby' => 'menu_order',
    'order' => 'ASC',
    ); 
  $module = get_children( $args , OBJECT);
  $modul_list ="";
  if ( $module ) {
    foreach ( $module as $modul) {
      $postId = $modul->ID;
      $modul_list .= '
        <div class="icon-text-wrapper">      
          <a href="'. get_the_permalink($postId) .'"> 
            <div class="icon-and-capture">
              <i class="tpq-icon-font '. get_post_meta($postId, 'icon_class', true) .'"></i>
            </div>
          </a>
          <p class="modul-icon-label">'. get_the_title($postId) .'</p>
        </div>
        ';
    }
  }
  $modul_list = '<div class="module modul-icons">'. $modul_list.'</div>';

  return '<div class="module-ueberblick">' . $section_title . $modul_list .'</div>';
}