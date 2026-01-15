<?php
add_action('admin_head', 'insertSCmainPage');

function insertSCmainPage() {
  $content = 'Willkommen im TeamProQ Supportcenter, lorem ipsum dolor sit amet, consectetur adipisici elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua.';
    wp_insert_post(array(
      'post_type' => 'page',
      'post_title' => 'TeamProQ Supportcenter',
      'post_name'  => 'supportcenter',
      'post_status' => 'publish',
      'post_content' => $content,
  ));
}

