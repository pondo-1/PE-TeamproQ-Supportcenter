<?php
add_action('admin_head', 'insertMyPosts');
// post_type supportcenter
// post_title = 

function generate_list_from_csv(){
  $CSV_FILE = 'Init_Modul_List.csv';
  $rows   = array_map('str_getcsv', file( plugin_dir_path( __FILE__ ) .'/' .$CSV_FILE));
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

function insertMyPosts() {
  $modules = generate_list_from_csv();
  foreach ($modules as $module) {

    $lipsum = simplexml_load_file('http://www.lipsum.com/feed/xml?amount=20&what=words&start=0')->lipsum;
    $content = 'Mit dem Modul "'.$module['name'].'" haben Sie eine Möglichkeit an der Hand, ';
    wp_insert_post(array(
      'post_type' => 'supportcenter',
      'post_title' => $module['name'],
      'post_status' => 'publish',
      'post_content' => $content . $lipsum,  
      'meta_input' => array(
        'icon_class' => $module['icon-name'],
        )
    ));
  }
}

