<?php
/**
 * This is the template that will be used to render the shortcode for the
 * Daily Thought. You can override this template by copying this file to
 * your theme directory in the following path:
 *
 *  /wp-content/themes/[your_theme]/dailythought-plugin-templates/daily-thought.php
 *
 * If the requested thought is successfully found, the following variables
 * will be available for use in the template
 *
 *  $id             ID of the post
 *  $show_verse     Parameter specifying whether or not to show the verse
 *  $content        The content of the thought (post_content)
 *  $reference      The verse reference
 *  $verse          The verse content
 *  $author         The post author ID
 *  $category       An array of one or more category names
 */
?>
<article class="<?php post_class('gw-daily-thought'); ?>">
    <div class="thought">
        <?php if ($show_verse): ?>
        <div class="verse">
            <h3 class="reference"><?php echo $reference; ?></h3>
            <p class="text"><?php echo $verse; ?></p>
        </div>
        <?php endif; ?>
        <div class="content">
            <p><?php echo $content; ?></p>
        </div>
    </div>
</article>
