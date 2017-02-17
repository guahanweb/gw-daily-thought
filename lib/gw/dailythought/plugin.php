<?php
namespace GW\DailyThought;

use GW\DailyThought\PostTypes;

if (!class_exists('\GW\DailyThought\Plugin')):

define('GW_DAILYTHOUGHT_PLUGIN_NAME', '\GW\DailyThought\Plugin');

class Plugin {
    static public function instance($base = null) {
        static $instance;
        if (null === $instance) {
            $instance = new Plugin();
            $instance->configure($base);
            $instance->listen();
            $instance->modules();
        }
        return $instance;
    }

    public function configure($base) {
        global $wpdb;

        if (null === $base) {
            $base = __FILE__;
        }

        $config = Config::instance(GW_DAILYTHOUGHT_PLUGIN_NAME);
        $config->add('domain', 'gw');
        $config->add('min_version', '4.1');

        $config->add('settings_opt', 'gw_dailythought_plugin_settings');

        $config->add('basename', \plugin_basename(\plugin_dir_path($base) . 'gw-daily-thought.php'));
        $config->add('plugin_file', $base);
        $config->add('plugin_uri', \plugin_dir_url($base));
        $config->add('plugin_path', \plugin_dir_path($base));

        $config->add('dbprefix', $wpdb->prefix . 'gw_');

        $config->add('post_type', 'thought');
        $config->add('slug', 'thought');

        $this->config = $config;
    }

    public function install() {
        // Create new DB Tables

        \flush_rewrite_rules();
    }

    public function uninstall() {
        flush_rewrite_rules();
    }

    public function listen() {
        \register_activation_hook($this->config->plugin_file, array($this, 'install'));
        \register_deactivation_hook($this->config->plugin_file, array($this, 'uninstall'));

        // Initialize new post types
        \add_action('init', array('\GW\DailyThought\PostTypes\Thought', 'init'));
    }

    public function modules() {

    }
}

endif;
