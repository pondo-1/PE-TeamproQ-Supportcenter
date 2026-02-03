<?php
/**
 * Template für einzelne Supportcenter Posts
 * Dieses Template wird für Custom Post Type 'supportcenter' verwendet
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        
        <?php while (have_posts()) : the_post(); ?>
            
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                
                <header class="entry-header">
                    <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
                </header>

                <div class="entry-content">
                    <?php
                    // Der Content wird automatisch durch den Filter in SC_ModulPageView.php modifiziert
                    the_content();
                    ?>
                </div>

            </article>
            
        <?php endwhile; ?>
        
    </main>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>