<?php
namespace TVA\Hooks;

class Rewrite
{
    private static $instance;

    public static function init()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Rewrite();
        }

        return self::$instance;
    }

    function __construct()
    {
        add_action('init', [$this, 'rewrite'], 8, 0);
    }

    public function rewrite()
    {
        // add_rewrite_rule(
        //     'thanh-phan/page/([^/]+)(?:/([0-9]+))?/?$',
        //     'index.php?pagename=recipe&paged=$matches[1]',
        //     'top'
        // );
        //
        // add_rewrite_rule(
        //     'thanh-phan/?$',
        //     'index.php?pagename=recipe',
        //     'top'
        // );
    }

}