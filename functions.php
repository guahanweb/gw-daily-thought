<?php
function gwdt_locate_template($template_name, $template_path = '', $default_path = '') {
    if (!$template_path) {
        $template_path = 'dailythought-plugin-templates/';
    }

    if (!$default_path) {
        $default_path = plugin_dir_path(__FILE__) . 'templates/';
    }

    $template = locate_template(array(
        $template_path . $template_name,
        $template_name
    ));

    if (!$template) {
        $template = $default_path . $template_name;
    }

    return apply_filters('gwdt_locate_template', $template, $template_name, $template_path, $default_path);
}

function gwdt_get_template($template_name, $args = array(), $template_path = '', $default_path = '') {
    if (is_array($args) && isset($args)) {
        extract($args);
    }

    $template_file = gwdt_locate_template($template_name, $template_path, $default_path);
    if (!file_exists($template_file)) {
        _doing_it_wrong(__FUNCTION__, sprintf('<code>%s</code> does not exist.', $template_file), '1.0.0');
    }

    ob_start();
    include $template_file;
    return ob_get_clean();
}

function gwdt_daily_thought_shortcode($atts = [], $content = null, $tag = '') {
    $atts = shortcode_atts(array(
        'id' => null,
        'show_verse' => true,
        'content' => $content
    ), $atts, 'daily_thought');

    if ($atts['id'] === null) {
        // No ID provided, so get a random one
        $thoughts = get_posts(array('post_type' => 'thought', 'orderby' => 'rand', 'numberposts' => 1));
        $post = count($thoughts) === 1 ? $thoughts[0] : null;
    } else {
        $post = get_post($atts['id']);
    }

    if ($post === null) {
        // No post retrieved, so short circuit
        return;
    }

    $atts['id'] = $post->ID;
    $atts['reference'] = get_post_meta($post->ID, 'gw_dailythought_reference', true);
    $atts['verse'] = get_post_meta($post->ID, 'gw_dailythought_verse', true);
    $atts['content'] = $post->post_content;
    $atts['author'] = $post->post_author;
    $atts['category'] = wp_get_post_terms($post->ID, 'thought_theme', array('fields' => 'names'));

    return gwdt_get_template('daily-thought.php', $atts);
}
add_shortcode('daily_thought', 'gwdt_daily_thought_shortcode');

function gwdt_get_template_part($slug, $name = '') {
    echo "<p>Template: {$slug}, {$name}</p>";
    return;
}
add_action('get_template_part_thought', 'gwdt_get_template_part', 2, 2);
