<?php

/**
 * Template für einzelne Supportcenter Posts
 * Dieses Template wird für Custom Post Type 'supportcenter' verwendet
 */

get_header(); ?>

<div id="primary" class="content-area">
  <main id="main" class="site-main">





    <div class="entry-content">
      <?php
      require_once PE_supportcenter_Plugin_Path . 'includes/view/ViewComponents.php';

      $supportcenter_header = supportcenter_header();
      // module Überblick 
      $modul_ueberblick = supportcenter_module_ueberblick();
      $content = $supportcenter_header . $modul_ueberblick;

      echo $content;
      ?>
    </div>



  </main>
</div>

<?php get_footer(); ?>