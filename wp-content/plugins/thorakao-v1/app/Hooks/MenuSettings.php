<?php
/**
 * Created by PhpStorm.
 * User: vietanh
 * Date: 23-Sep-16
 * Time: 4:54 PM
 */

namespace TVA\Hooks;

class MenuSettings
{
    private static $instance;


    public static function init()
    {
        if (!isset(self::$instance)) {
            self::$instance = new MenuSettings();
        }

        return self::$instance;
    }


    function __construct()
    {
        add_action('admin_menu', [$this, 'initPages']);
    }


    public function initPages()
    {
        add_menu_page('Thorakao Settings', 'Thorakao Settings', 'manage_options', 'trk-settings', [$this, 'trkSettings'], '', 50);
    }

    // Register Navigation Menus
    public function trkSettings()
    { ?>

        <form action='options.php' method='post'>

            <h2>Thorakao Settings</h2>

            <?php
            settings_fields('pluginPage');
            do_settings_sections('pluginPage');
            submit_button();
            ?>

        </form>

    <?php }

}