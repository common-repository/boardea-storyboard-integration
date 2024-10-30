<?php

/*
  Plugin Name: Boardea integration
  Plugin URI: https://www.boardea.com/developers
  Description: Attach a link to video storyboard after the YouTube URL in the post
  Author: Boardea
  Version: 1.7
  Author URI: https://www.boardea.com
 */

if (!defined('ABSPATH')) {
    exit;
}

define('BOARDEA_VERSION', '1.7');

/**
 * Main function processing texts
 */
function boardea_core_filter($content) {

    return preg_replace_callback('#(?:https?:\/\/)?(?:m\.|www\.)?(?:youtu\.be\/|youtube\-nocookie\.com\/embed\/|youtube\.com\/(?:embed\/|v\/|e\/|\?v=|shared\?ci=|watch\?v=|watch\?.+&v=))([-_A-Za-z0-9]{10}[AEIMQUYcgkosw048])\S*#s', function($match) {
        return sprintf('%s <a href="https://www.boardea.com/embed/%s" class="iframe-lightbox-link" data-src="https://www.boardea.com/embed/%s">|β|</a>', $match[0], $match[1], $match[1]);
    }, $content);
}

add_filter('the_content', 'boardea_core_filter', -1); // for main content
add_filter('comment_text', 'boardea_core_filter', -1); // fot comments
add_filter('widget_text', 'boardea_core_filter', -1); // for widget texts

/**
 * Enqueues iframe-lightbox JavaScript to footer
 */
function boardea_js() {
    wp_enqueue_script('boardea', plugin_dir_url(__FILE__) . 'boardea.min.js', null, BOARDEA_VERSION, true);
}

add_action('wp_enqueue_scripts', 'boardea_js', 999);

/**
 * Enqueues iframe-lightbox Css to footer
 */
function boardea_css() {
    wp_enqueue_style('boardea', plugin_dir_url(__FILE__) . 'boardea.min.css', array(), BOARDEA_VERSION);
}

add_action('get_footer', 'boardea_css');

/**
 * Initialization of the lightbox in footer
 */
function boardea_wp_footer() {
    ?>
    <script type="text/javascript">
        [].forEach.call(document.getElementsByClassName("iframe-lightbox-link"), function (el) {
            el.lightbox = new IframeLightbox(el, {
                scrolling: true,
                touch: true
            });
        });
        // Boardea.com WP plugin end
    </script>
    <?php

}

add_action('wp_footer', 'boardea_wp_footer', 999);
