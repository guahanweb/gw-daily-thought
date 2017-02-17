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

    return gwdt_get_template('daily-thought.php', $atts);
}
add_shortcode('daily_thought', 'gwdt_daily_thought_shortcode');

function gwdt_get_template_part($slug, $name = '') {
    echo "<p>Template: {$slug}, {$name}</p>";
    return;
}
add_action('get_template_part_thought', 'gwdt_get_template_part', 2, 2);
