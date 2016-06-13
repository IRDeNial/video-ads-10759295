<?php
/**
 * Plugin Name: Video Advertisemenets
 * Version: 1.0
 * License: GPL2
 * Description: Shows ads only once on a video page 
 * Author: Mike Orozco
 * Author URI: https://www.freelancer.com/u/mikeorozco94.html
 * Tested up to: 4.5.2
 * Requires at least: 4.1
 */

defined('ABSPATH') or die();

require_once('updater.php');

add_action( 'init', 'github_plugin_updater_test_init' );

function github_plugin_updater_test_init() {
    if (is_admin()) { // note the use of is_admin() to double check that this is happening in the admin
        require 'plugin-update-checker/plugin-update-checker.php';
        $className = PucFactory::getLatestClassVersion('PucGitHubChecker');
        $myUpdateChecker = new $className(
            'https://github.com/IRDeNial/video-ads-10759295',
            __FILE__,
            'master'
        );
        $myUpdateChecker->setAccessToken('82dc1d82f96dc923c347e8ae025d967fc1fc5cf9 ');
    }
}

echo("Kool");

?>
