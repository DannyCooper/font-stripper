<?php

/**
 * Plugin Name: Font Stripper
 * Plugin URI: https://wordpress.org/plugins/font-stripper/
 * Description: Removes inline font styles from existing content.
 * Version: 1.0.0
 * Author: Danny Cooper
 * Author URI: https://dannycooper.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Prevent direct access to this file
if (! defined('ABSPATH')) {
    exit;
}

class FontStripper
{
    public function __construct()
    {
        // Strip fonts when displaying content
        add_filter('the_content', array($this, 'strip_font_declarations'), 20);
    }

    /**
     * Strips font-family and font-size declarations from inline styles
     * 
     * @param string $content The post content
     * @return string Modified content without font declarations
     */
    public function strip_font_declarations($content)
    {
        if (empty($content) || is_admin()) {
            return $content;
        }

        $pattern = '/style=(["\'])(.*?)\1/i';
        $replacement = function($matches) {
            $quote = $matches[1];
            $styles = $matches[2];
            
            // Remove font-family, font-size, font-weight, line-height and color declarations
            $styles = preg_replace('/font-family\s*:\s*[^;]+;?/', '', $styles);
            $styles = preg_replace('/font-size\s*:\s*[^;]+;?/', '', $styles);
            $styles = preg_replace('/font-weight\s*:\s*[^;]+;?/', '', $styles);
            $styles = preg_replace('/line-height\s*:\s*[^;]+;?/', '', $styles);
            $styles = preg_replace('/color\s*:\s*[^;]+;?/', '', $styles);
            
            // Clean up any extra semicolons and whitespace
            $styles = trim($styles, "; ");
            
            // If there are no styles left, return empty string
            if (empty($styles)) {
                return '';
            }
            
            return sprintf('style=%s%s%s', $quote, $styles, $quote);
        };

        return preg_replace_callback($pattern, $replacement, $content);
    }
}

// Initialize the plugin
new FontStripper();