<?php
/**
 * Plugin Name: Video Advertisemenets
 * Version: 1.1
 * License: GPL3
 * Description: Shows ads only once on a video page 
 * Author: Mike Orozco
 * Author URI: https://www.freelancer.com/u/mikeorozco94.html
 * Tested up to: 4.5.2
 * Requires at least: 4.1
 */

defined('ABSPATH') or die();

if(!class_exists('WP_Video_Ads')) {
    class WP_Video_Ads {
        private $cookieName;
        private $showAd;

        public function __construct() {
            add_action('init', array(&$this,'github_updater'));
            add_action('template_redirect', array(&$this,'main_plugin_func'));
            add_action('wp_footer', array(&$this,'generateVideoAd'));
            add_action('admin_menu', array(&$this,'addSettingsMenu'));
            add_action('admin_init', array(&$this,'settingsRegister'));

            $this->cookieName = 'WP_Video_ads-PagesVisited';
            $this->showAd = false;
        }

        public function github_updater() {
            if (is_admin()) {
                require 'plugin-update-checker/plugin-update-checker.php';
                $className = PucFactory::getLatestClassVersion('PucGitHubChecker');
                $myUpdateChecker = new $className(
                    'https://github.com/IRDeNial/video-ads-10759295',
                    __FILE__,
                    'master'
                );
                $myUpdateChecker->setAccessToken('82dc1d82f96dc923c347e8ae025d967fc1fc5cf9');
            }
        }

        public function main_plugin_func() {
            global $wp_session;
            if(!isset($_COOKIE[$this->cookieName])) {
                setcookie($this->cookieName,base64_encode(''),time()+3600);
            }

            $permaLink = $_SERVER['HTTP_HOST'] .$_SERVER['REQUEST_URI'];

            $pagesVisited = explode(',',base64_decode($_COOKIE[$this->cookieName]));

            if(!in_array($permaLink,$pagesVisited)) {
                array_push($pagesVisited,$permaLink);
                $this->showAd = true;
                setcookie($this->cookieName,base64_encode(implode(',',$pagesVisited)),time()+3600);
            }
        }

        public function addSettingsMenu() {
            //add_menu_page("Video Ads", "Video Ads", "manage_options", "video-ads-settings", array(&$this,'settingsPage'), null, 99);
            add_menu_page('Video Ads Settings', 'Video Ads', 'administrator', 'video-ads-settings', array(&$this,'settingsPage') , null);
        }

        public function settingsRegister() {
            register_setting('videoads-settings-group', 'videoads-url');
        }

        public function settingsPage() {
            ?>
                <div class="wrap">
                    <h1>Video Ads Settings</h1>
                    <form method="post" action="options.php">
                        <?php settings_fields('videoads-settings-group'); ?>
                        <?php do_settings_sections('videoads-settings-group'); ?>
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row">Video Ad URL</th>
                                <td><input type="text" name="videoads-url" value="<?php echo(esc_attr(get_option('videoads-url'))); ?>"></td>
                            </tr>
                        </table>
                        <?php submit_button(); ?>          
                    </form>
                </div>
            <?php
        }

        public function generateVideoAd() {
            if($this->showAd) {
                ?>
                <a style="" href="<?php echo(get_option('videoads-url')); ?>" target="_BLANK" id="video-clicker">&nbsp;</a>
                <script>
                    jQuery(document).ready(function(){
                        jQuery('video').on('playing', function() {
                            jQuery(this).off('playing');
                            jQuery(this)[0].pause();
                            jQuery('#video-clicker')[0].click();
                        });
                    });
                </script>
                <?php 
            }
        }
    }

    $WP_Video_Ads = new WP_Video_Ads();
}

?>
