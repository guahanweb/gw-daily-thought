<?php
namespace GW\DailyThought\PostTypes;

use GW\DailyThought;

class Thought {
    private static $config;
    private static $initialized = false;

    public static function init() {
        if (!self::$initialized) {
            self::$config = DailyThought\Config::instance(GW_DAILYTHOUGHT_PLUGIN_NAME);
            self::initHooks();
            self::registerPostTypes();
            self::registerTaxonomies();
            self::registerShortCodes();

            DailyThought\PostTypes\Thought\Admin::init();
        }
    }

    public static function initHooks() {
        self::$initialized = true;
        // Post type actions
        add_filter('the_content', array('\GW\DailyThought\PostTypes\Thought', 'filterContent'), 0);
        // add_filter('template_include', array('\GW\DailyThought\PostTypes\Thought', 'filterTemplateInclude'));

        // Admin actions
        add_action('admin_menu', array('\GW\DailyThought\PostTypes\Thought\Admin', 'manageAdminMenu'));
        add_filter('manage_thought_posts_columns', array('\GW\DailyThought\PostTypes\Thought\Admin', 'setupTableHeadings'));
        add_action('manage_thought_posts_custom_column', array('\GW\DailyThought\PostTypes\Thought\Admin', 'manageCustomColumns'), 10, 2);
        add_action('save_post', array('\GW\DailyThought\PostTypes\Thought\Admin', 'saveMetaData'));
        add_action('admin_menu', array('\GW\DailyThought\PostTypes\Thought\Admin', 'updateAdminMenu'));
        add_action('admin_enqueue_scripts', array('\GW\DailyThought\PostTypes\Thought\Admin', 'enqueueScripts'), 10, 1);
        add_action('edit_form_after_title', array('\GW\DailyThought\PostTypes\Thought\Admin', 'renderAdvancedBoxes'));
    }

    public static function registerPostTypes() {
        $slug = get_theme_mod('event_permalink');
        $slug = empty($slug) ? self::$config->slug : $slug;

        register_post_type(self::$config->post_type, array(
            'label' => __('thoughts', self::$config->domain),
            'description' => __('Thoughts of the day', self::$config->domain),
            'labels' => array(
                'name' => __('Daily Thoughts', self::$config->domain),
                'singular_name' => __('Daily Thought', self::$config->domain),
                'menu_name' => __('Daily Thoughts', self::$config->domain),
                'all_items' => __('All Thoughts', self::$config->domain),
                'view_items' => __('View Thoughts', self::$config->domain),
                'add_new_item' => __('Add Thought', self::$config->domain),
                'add_nem' => __('New', self::$config->domain),
                'edit_item' => __('Edit Thought', self::$config->domain),
                'update_item' => __('Update Thought', self::$config->domain),
                'search_items' => __('Search Daily Thoughts', self::$config->domain),
                'not_found' => __('Not Found', self::$config->domain),
                'not_found_in_trash' => __('Not Found in Trash', self::$config->domain)
            ),

            'supports' => array('editor', 'author', 'thumbnail'),
            'taxonomies' => array('theme'),
            'rewrite' => array('slug' => $slug),
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => false,
            'show_in_admin_bar' => true,
            'menu_position' => 4,
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'post',

            // Meta boxes
            'register_meta_box_cb' => array('\GW\DailyThought\PostTypes\Thought\Admin', 'registerMetaBoxes')
        ));
    }

    public static function registerTaxonomies() {
        register_taxonomy(
            'theme',
            ['thought'],
            array(
                'labels' => array(
                    'name' => __('Themes', self::$config->domain),
                    'signular_name' => __('Theme', self::$config->domain),
                    'add_new_item' => __('Add Thought Theme', self::$config->domain),
                    'new_item_name' => __('New Thought Theme', self::$config->domain)
                ),
                'show_ui' => true,
                'show_tagcloud' => false,
                'hierarchical' => true,
                'rewrite' => array('slug', 'theme')
            )
        );
    }

    public static function filterContent($content) {
        // Modify content however necessary
        return $content;
    }

    public static function filterTemplateInclude($template) {
        // Override templates here, if needed
        // http://jeroensormani.com/how-to-add-template-files-in-your-plugin/
        echo 'TEMPLATE: ' . $template;
        return $template;
    }

    public static function parseVerse($atts = [], $content = null, $tag) {
        $atts = shortcode_atts(array(
            'ref' => null,
            'content' => $content
        ), $atts, 'verse');

        // consider filters here
        $out = "<span class=\"gw-verse verse-quote\">${atts['content']}";
        if (!is_null($atts['ref'])) {
            $out .= "<span class=\"reference-holder\"><span class=\"reference\">${atts['ref']}</span></span>";
        }
        $out .= "</span>";
        return $out;
    }

    public static function registerShortCodes() {
        add_shortcode('verse', array('\GW\DailyThought\PostTypes\Thought', 'parseVerse'));
    }
}
