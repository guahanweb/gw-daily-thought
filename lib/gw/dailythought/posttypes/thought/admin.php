<?php
namespace GW\DailyThought\PostTypes\Thought;

use GW\DailyThought;
use GW\DailyThought\PostTypes\Thought;

class Admin {
    private static $config;
    private static $nonce;

    public static function init() {
        self::$config = DailyThought\Config::instance(GW_DAILYTHOUGHT_PLUGIN_NAME);
        self::$nonce = md5(GW_DAILYTHOUGHT_PLUGIN_NAME . self::$config->version);
    }

    public static function manageAdminMenu() {
        remove_menu_page('link-manager.php');
    }

    public static function setupTableHeadings($defaults) {
        $columns = array(
            'thought' => __('Thought', self::$config->domain),
            'scripture' => __('Scripture', self::$config->domain),
            'theme' => __('Theme', self::$config->domain),
            'author' => $defaults['author'],
            'date' => $defaults['date']
        );
        return $columns;
    }

    public static function manageCustomColumns($column_name, $post_id) {
        switch ($column_name) {
            case 'scripture':
                $ref = get_post_meta($post_id, 'gw_dailythought_reference', true);
                $verse = get_post_meta($post_id, 'gw_dailythought_verse', true);
                printf('<p><i>(%s)</i><br>%s</p>', $ref, $verse);
                break;

            case 'thought':
                $post = get_post($post_id);
                $content = get_post_meta($post_id, 'gw_dailythought_blurb', true);
                echo $content;
                break;

            case 'theme':
                $terms = wp_get_post_terms($post_id, 'theme', array('fields' => 'names'));
                echo implode(', ', $terms);
                break;

            default:
                echo '???';
        }
    }

    public static function renderAdvancedBoxes() {
        global $post, $wp_meta_boxes;
        do_meta_boxes(get_current_screen(), 'advanced', $post);
        unset($wp_meta_boxes[get_post_type($post)]['advanced']);
    }

    public static function registerMetaBoxes() {
        add_meta_box('gw_dailythought_thought_scripture', __('Verse', self::$config->domain), array('\GW\DailyThought\PostTypes\Thought\Admin', 'renderScriptureMetaBox'), 'thought', 'advanced', 'high');
        add_meta_box('gw_dailythought_thought_short', __('Thought', self::$config->domain), array('\GW\DailyThought\PostTypes\Thought\Admin', 'renderThoughtMetaBox'), 'thought', 'advanced', 'high');
    }

    public static function saveMetaData($post_id) {
        $post_type = get_post_type($post_id);
        if ('thought' !== $post_type) {
            return;
        }

        $is_autosave = wp_is_post_autosave($post_id);
        $is_revision = wp_is_post_revision($post_id);
        $is_valid_nonce = (isset($_POST[self::$nonce]) && wp_verify_nonce($_POST[self::$nonce], basename(__FILE__)));

        if ($is_autosave || $is_revision) {
            return;
        }

        if (isset($_POST['reference'])) {
            update_post_meta($post_id, 'gw_dailythought_reference', $_POST['reference']);
        }

        if (isset($_POST['verse'])) {
            update_post_meta($post_id, 'gw_dailythought_verse', $_POST['verse']);
        }

        if (isset($_POST['blurb'])) {
            update_post_meta($post_id, 'gw_dailythought_blurb', $_POST['blurb']);
        }
    }

    public static function renderThoughtMetaBox($post) {
        $id = $post->ID;
        $thought = get_post_meta($post->ID, 'gw_dailythought_blurb', true);

        echo <<<EOT
<div class="gw-dailythought-blurg" id="blurb-${id}">
    <div class="containter">
        <div class="blurb-entry">
EOT;

        wp_editor(esc_html($thought), 'gw_dailythought_thought', array(
            'teeny' => true,
            'textarea_name' => 'blurb',
            'textarea_rows' => 6,
            'media_buttons' => false,
            'tabindex' => 2,
            'quicktags' => false
        ));

        echo <<<EOT
        </div>
    </div>
</div>
EOT;
    }

    public static function renderScriptureMetaBox($post) {
        wp_nonce_field(basename(__FILE__), self::$nonce);

        $id = $post->ID;
        $refid = get_post_meta($post->ID, 'gw_dailythought_refid', true);
        $reference = get_post_meta($post->ID, 'gw_dailythought_reference', true);
        $passage = get_post_meta($post->ID, 'gw_dailythought_verse', true);

        echo "<div class=\"gw-dailythought-passage\" id=\"passage-${id}\">
                <div class=\"container\">
                    <div class=\"scripture-search\">
                        <input type=\"hidden\" name=\"refid\" id=\"refid\" value=\"" . esc_html($refid) . "\">
                        <label for=\"reference\">Reference</label>
                        <input type=\"text\" name=\"reference\" id=\"reference\" value=\"" . esc_html($reference) . "\" class=\"widefat\">
                    </div>
                    <div class=\"result\">
                        <div class=\"content\">
                            <label for=\"gw_dailythought_verse\">Text</label>";

        wp_editor(esc_html($passage), 'gw_dailythought_verse', array(
            'teeny' => true,
            'textarea_name' => 'verse',
            'textarea_rows' => 4,
            'media_buttons' => false,
            'tabindex' => 1,
            'quicktags' => false
        ));

        echo "</div>
                    </div>
                </div>
            </div>";
        return;

        // reference
        $reference = get_post_meta($post->ID, 'gw_dailythought_reference', true);
        echo '<div class="gw-field field-mini">';
        printf('<p class="gw-field-label"><label for="reference">%s</label></p>', __('Reference', self::$config->domain));
        printf('<p class="gw-field-input"><input type="text" name="reference" id="reference" value="%s" class="widefat"></p>', $reference);
        echo '</div>';

        // content
        $verse = get_post_meta($post->ID, 'gw_dailythought_verse', true);
        echo '<div class="gw-field field-mini">';
        printf('<p class="gw-field-label"><label for="verse">%s</label></p>', __('Text', self::$config->domain));
        printf('<p class="gw-field-input"><textarea name="verse" id="verse" class="widefat" rows="4">%s</textarea></p>', $verse);
        echo '</div>';
    }

    public static function updateAdminMenu() {
        // Remove unnecessary clutter from all except administrators
        global $menu;
        // possible removal list
        $for_removal = array('menu-posts', 'menu-links', 'menu-pages', 'menu-comments', 'menu-appearance', 'menu-plugins', 'menu-users', 'menu-tools', 'menu-settings');
    }

    public static function enqueueScripts($hook) {
        global $post;
        if ($hook == 'post-new.php' || $hook == 'post.php') {
            if ($post->post_type === 'thought') {
                wp_enqueue_script('gw-dailythought-admin.js', self::$config->plugin_uri . '/assets/js/admin.js');
                wp_enqueue_style('gw-dailythought-admin.css', self::$config->plugin_uri . '/assets/css/admin.css');
            }
        }
    }
}
