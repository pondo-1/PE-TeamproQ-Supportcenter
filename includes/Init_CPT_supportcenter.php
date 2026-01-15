<?php
if (!defined('ABSPATH')) die('No direct access allowed');

class PE_Initializ_CTP{


  public function __construct() {
    // create custom post type unternehmen
    add_action( 'init', array($this, 'create_posttype'));

    // add Modul Filter
    add_filter( 'parse_query',array($this, 'cbt_admin_posts_filter'));
    add_action( 'restrict_manage_posts',array($this, 'admin_page_filter_parentpages'));
    
    // add a column to the post type's admin
    // basically registers the column and sets it's title
    $MY_POST_TYPE = 'supportcenter';
    add_filter('manage_' . $MY_POST_TYPE . '_posts_columns', function ($columns) {
      $columns['menu_order'] = "Order";
      return $columns;
    });
    // display the column value
    add_action( 'manage_' . $MY_POST_TYPE . '_posts_custom_column', function ($column_name, $post_id){
      if ($column_name == 'menu_order') {
     echo get_post($post_id)->menu_order;
      }
    }, 10, 2); // priority, number of args - MANDATORY HERE!
    // make it sortable
    $menu_order_sortable_on_screen = 'edit-' . $MY_POST_TYPE; // screen name of LIST page of posts
    add_filter('manage_' . $menu_order_sortable_on_screen . '_sortable_columns', function ($columns){
      // column key => Query variable
      // menu_order is in Query by default so we can just set it
      $columns['menu_order'] = 'menu_order';
      return $columns;
    }); 

    add_action( 'rest_api_init', array($this,'private_json_api'));
    //create a custom taxonomy 
    //hook into the init action and call create_xy_taxonomies when it fires
    // add_action( 'init', array($this, 'create_custom_taxonomy_theme'));
  }

  function cbt_admin_posts_filter( $query ) {
    global $pagenow;
    if ( is_admin() && $pagenow == 'edit.php' && !empty($_GET['my_parent_pages'])) {
      $query->query_vars['post_parent'] = $_GET['my_parent_pages'];
    }
  }
  
  function admin_page_filter_parentpages() { 
    global $wpdb;   
    if (isset($_GET['post_type']) && $_GET['post_type'] == 'supportcenter') {
      $sql = "SELECT ID, post_title FROM ".$wpdb->posts." WHERE post_type = 'supportcenter' AND post_parent = 0 AND post_status = 'publish' ORDER BY post_title";
      $parent_pages = $wpdb->get_results($sql, OBJECT_K);
      $select = '
          <select name="my_parent_pages">
              <option value="">Modul</option>';
              $current = isset($_GET['my_parent_pages']) ? $_GET['my_parent_pages'] : '';
              foreach ($parent_pages as $page) {
                $select .= sprintf('
                  <option value="%s"%s>%s</option>', $page->ID, $page->ID == $current ? ' selected="selected"' : '', $page->post_title);
              }
      $select .= '
          </select>';
      echo $select;
    } else {
      return;
    }
  }

  function create_posttype() {

    /**
    * Register a custom post type called "Suppportcenter".
    *
    * @see get_post_type_labels() for label keys.
    */

    $labels = array(
      'name'                  => __( 'Supportcenter' ),
      'singular_name'         => __( 'supportcenter' ),
    );

    $args = array(
      'labels'             => $labels,
      'description'        => 'This is for die entries of Supportcenter Q and A.',
      'public'             => true,
      // 'publicly_queryable' => true,
      'show_ui'            => true,
      'show_in_menu'       => true,
      // Default: true – set to $post_type, let it default, ‘string’ – /?{query_var_string}={single_post_slug} will work as intended.
      // 'query_var'          => false,
      // alllow rewrite, Default: true and use $post_type as slug, let it defualt
      // 'rewrite'            => array( 'slug' => ''),
      'capability_type'    => 'post',
      'has_archive'        => false,
      'hierarchical'       => true,
      'menu_position'      => 50, //50 – below page
      'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'page-attributes'),
      // 'taxonomies'         => array( 'theme' ),
      'show_in_rest'       => true // it is for gutenberg, but let it 
    );
  
    register_post_type( 'supportcenter', $args );
  }

  function add_new_header_text_column($header_text_columns) {
    $header_text_columns['menu_order'] = "Order";
    return $header_text_columns;
  }
  function show_order_column($name){
    global $post;
  
    switch ($name) {
      case 'menu_order':
        $order = $post->menu_order;
        echo $order;
        break;
     default:
        break;
     }
  }



function private_json_api() {
  register_rest_route( 'PE_supportcenter', '/posts/', array(
  'methods' => WP_REST_SERVER::READABLE,
  'callback' => array($this,'supportcenter_json_generator')
  ));
}

function supportcenter_json_generator($data) {
  $unternehmen = new WP_Query(array(
  'post_type' => 'supportcenter',
  'post_status' => 'private',
  's' => sanitize_text_field($data['term']),
  'posts_per_page' => 5,
  ));

  $unternehmen_geojson = array();

  while ($unternehmen->have_posts()) {
    $unternehmen->the_post();
    array_push($unternehmen_geojson, array(
      'id' => get_the_ID(),
      'title' => get_the_title(),
      'content' => get_the_content(),
      'slug' => get_post_field( 'post_name', get_the_ID() ),
      'modul' => array(
        'name' => get_post_parent()->post_title,
        'slug' => basename(get_permalink(get_post_parent()->ID)), 
      ), 
    ));
  }
  return $unternehmen_geojson;
  }

}
