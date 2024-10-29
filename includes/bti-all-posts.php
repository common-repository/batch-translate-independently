<?php

class bti79_apply_all_posts
{

    public function post_types_args()
    {
        // Get all post types
        $args = array(
            'public' => true,
            'publicly_queriable' => true,
            'show_ui' => true,
            'show_in_nav_menus' => true,
            'exclude_from_search' => false);

        $post_types = get_post_types($args, 'names', 'or');
        // Remove the attachment post type. We don't want this in the query
        if (isset($post_types['attachment']))
        {
            unset($post_types['attachment']);
        }
        if (isset($post_types['shop_order']))
        {
            unset($post_types['shop_order']);
        }
        if (isset($post_types['shop_coupon']))
        {
            unset($post_types['shop_coupon']);
        }
        if (isset($post_types['shop_webhook']))
        {
            unset($post_types['shop_webhook']);
        }

        // Set the arguments of the query
        $args = array(
            'post_type' => $post_types,
            'post_status' => 'any',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'orderby' => 'title',
            'order' => 'ASC');

        return $args;
    }

    // Check the state of posts of the currently selected language
    public function check_all_posts()
    {
        $args = $this->post_types_args();
        $the_query = new WP_Query($args);
        if ($the_query->have_posts())
        {
            $i = 0;
            $j = 0;
            $sync_titles_array = array();
            $sync_titles_id = array();
            $unsync_titles_array = array();
            $unsync_titles_id = array();
            foreach ($the_query->posts as $id)
            {
                if (get_post_meta($id, '_icl_lang_duplicate_of', true) && get_post_meta($id,
                    '_icl_lang_duplicate_of', true) != 0)
                {
                    $sync_titles_array[] = get_the_title($id);
                    $sync_titles_id[] = $id;
                    $i++;
                } elseif (!get_post_meta($id, '_icl_lang_duplicate_of', true))
                {
                    $unsync_titles_array[] = get_the_title($id);
                    $unsync_titles_id[] = $id;
                    $j++;
                }
            }
            return array(
                $i,
                $j,
                $sync_titles_array,
                $sync_titles_id,
                $unsync_titles_array,
                $unsync_titles_id,
                $the_query->post_count);
        } else
        {
            return array(0, 0);
        }
        wp_reset_postdata();
    }

    // Process all posts of the currently selected language to translate independently
    public function process_all_posts()
    {
        $args = $this->post_types_args();
        $the_query = new WP_Query($args);
        if ($the_query->have_posts())
        {
            foreach ($the_query->posts as $id)
            {
                if (get_post_meta($id, '_icl_lang_duplicate_of', true))
                {
                    $ids_array[] = $id;
                    delete_post_meta($id, '_icl_lang_duplicate_of');
                }
            }
        }
        wp_reset_postdata();

        // Create a delimited string from the array and output result
        if (!empty($ids_array))
        {
            sort($ids_array);
            $number = count($ids_array);
            $the_ids = implode(', ', $ids_array);
            return array($number, $the_ids);
        } else
        {
            $the_ids = 'Nothing changed!';
            return array(0, $the_ids);
        }
    }

    // Revert all posts of the currently selected language to synchronised translations
    public function revert_all_posts()
    {
        global $sitepress;
        $args = $this->post_types_args();
        $the_query = new WP_Query($args);
        if ($the_query->have_posts())
        {
            foreach ($the_query->posts as $id)
            {
                if (!get_post_meta($id, '_icl_lang_duplicate_of', true) && $id !== icl_object_id
                    ($id, 'any', true, $sitepress->get_default_language()) && icl_object_id($id,
                    'any', false, $sitepress->get_default_language()))
                {
                    delete_post_meta(icl_object_id($id, 'any', true, $sitepress->
                        get_default_language()), '_icl_lang_duplicate_of');
                    $ids_array[] = $id;
                    add_post_meta($id, '_icl_lang_duplicate_of', icl_object_id($id, 'any', true, $sitepress->
                        get_default_language()), true);
                }
            }
        }
        wp_reset_postdata();

        // Create a delimited string from the array and output result
        if (!empty($ids_array))
        {
            sort($ids_array);
            $number = count($ids_array);
            $the_ids = implode(', ', $ids_array);
            return array($number, $the_ids);
        } else
        {
            $the_ids = 'Nothing changed!';
            return array(0, $the_ids);
        }
    }

}

?>