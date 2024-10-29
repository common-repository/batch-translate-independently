<?php

/**
 * Plugin Name: Batch Translate Independently
 * Plugin URI:
 * Description: Batch modify the translate independently settings of WPML for all or selected posts of a given language.
 * Version: 1.0
 * Author: Harry Mandilas
 * Author URI:
 * License: GPL2
 *
 * Copyright 2016  Harry Mandilas (email : harman79 at gmail.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110, USA
 */

if (!defined('ABSPATH'))
{
    die('Get outta here!');
}
if (is_admin())
{

    class bti79
    {

        // Constructor
        public function __construct()
        {

            // check if WPML is activated
            if (!function_exists('is_plugin_active_for_network')):
                require_once (ABSPATH . '/wp-admin/includes/plugin.php');
            endif;

            if (!in_array('sitepress-multilingual-cms/sitepress.php', apply_filters('active_plugins',
                get_option('active_plugins')))):
                if (!is_plugin_active_for_network('sitepress-multilingual-cms/sitepress.php')):
                    add_action('admin_notices', array($this, 'bti79_message'));
                    return;
                endif;
            endif;

            // include the plugin files
			if (!class_exists('WP_List_Table')) {
				require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
			}
            if (!class_exists('bti79Options', false))
            {
                require_once (dirname(__file__) . '/includes/bti-options.php');
            }
            if (!class_exists('bti79_apply_all_posts', false))
            {
                require_once (dirname(__file__) . '/includes/bti-all-posts.php');
            }
            if (!class_exists('bti79_post_list_table', false))
            {
                require_once (dirname(__file__) . '/includes/bti-selected-posts.php');
            }

            // add actions and filters of the plugin
            add_filter('plugin_action_links_' . plugin_basename(__file__), array($this,
                    'bti79_action_links'));

        }

        // Add link to the plugin settings in plugins main list
        function bti79_action_links($links)
        {
            $mylinks = array('<a href="' . admin_url('options-general.php?page=batch-translate-independently') .
                    '">Use</a>', );
            return array_merge($links, $mylinks);
        }

        // Display a warning when WPML is not activated
        public function bti79_message()
        {

            // Enable message dismissal
            if (isset($_GET['dismiss_bti79_message']) && 1 == $_GET['dismiss_bti79_message']):
                update_option('ignore_bti79_message', 'yes');
            endif;

            if ('yes' != get_option('ignore_bti79_message')):

?><div class='updated'>
				<p><?php

                _e('WPML is not activated, please activate it before using Batch Translate Independently.',
                    'bti-79');

?><a class='alignright installer-dismiss-nag' href='<?php

                echo esc_url(add_query_arg('dismiss_bti79_message', true));

?>' data-repository='wpml'><?php

                _e('Dismiss', 'bti-79');

?></a>
				</p>
			</div><?php

            endif;

        }

    }
    $wpbti79 = new bti79();
}

?>