<?php
add_action('admin_head', 'insertMyChildPosts');
// post_type supportcenter
// post_title = 

function generate_list_from_csv(){
  $CSV_FILE = 'Init_Questions.csv';
  $rows   = array_map(
              function($v){return str_getcsv($v,";");}, 
              file( plugin_dir_path( __FILE__ ) .$CSV_FILE));
  $header = array_shift($rows);
  $csv    = array();
  foreach($rows as $row) {
    //ignore when there is a empty row
    if($row[0]!=''){
      $csv[] = array_combine($header, $row);
    } 
  }
  return $csv;
}

function insertMyChildPosts() {
  $QAs = generate_list_from_csv();
  $i = 1;
  foreach ($QAs as $QA) {
    $lipsum = simplexml_load_file('http://www.lipsum.com/feed/xml?amount=1&what=paras&start=0')->lipsum;
    $lipsum_text = ''.$lipsum ;
    wp_insert_post(array(
      'post_type' => 'supportcenter',
      'post_title' => $QA['title'],
      'post_status' => 'private',
      'post_content' => $lipsum_text, 
      'post_parent' => get_page_by_path('administration', OBJECT, 'supportcenter')->ID,
      'menu_order' => $i++,
    ));
  }
}

