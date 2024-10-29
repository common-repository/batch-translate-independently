<?php

class bti79_post_list_table extends WP_List_Table
{
    public function __construct()
    {
        global $status, $page;

        parent::__construct(array(
            'singular' => 'bti79_item',
            'plural' => 'bti79_items',
            'ajax' => false));

    }

    public function prepare_process_items()
    {
        $user = get_current_user_id();
        $screen = get_current_screen();
        $option = $screen->get_option('per_page', 'option');
        $perPage = get_user_meta($user, $option, true);
        if (empty($perPage) || $perPage < 1)
        {
            $perPage = $screen->get_option('per_page', 'default');
        }
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        $data = $this->process_table_data();
        usort($data, array(&$this, 'sort_data'));
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);
        $this->set_pagination_args(array('total_items' => $totalItems, 'per_page' => $perPage));
        $data = array_slice($data, (($currentPage - 1) * $perPage), $perPage);
        $this->_column_headers = array(
            $columns,
            $hidden,
            $sortable);
        $this->items = $data;
    }

    public function prepare_revert_items()
    {
        $user = get_current_user_id();
        $screen = get_current_screen();
        $option = $screen->get_option('per_page', 'option');
        $perPage = get_user_meta($user, $option, true);
        if (empty($perPage) || $perPage < 1)
        {
            $perPage = $screen->get_option('per_page', 'default');
        }
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        $data = $this->revert_table_data();
        usort($data, array(&$this, 'sort_data'));
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);
        $this->set_pagination_args(array('total_items' => $totalItems, 'per_page' => $perPage));
        $data = array_slice($data, (($currentPage - 1) * $perPage), $perPage);
        $this->_column_headers = array(
            $columns,
            $hidden,
            $sortable);
        $this->items = $data;
    }

    public function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'id' => 'ID',
            'name' => 'Name',
            'slug' => 'Slug',
            'date' => 'Date',
            'post_type' => 'Post Type');
        return $columns;
    }

    public function get_hidden_columns()
    {
        $screen = get_current_screen();
        if (is_string($screen))
            $user = get_current_user_id();
        $screen = get_current_screen();
        return (array )get_user_option('manage' . $screen->id . 'columnshidden');

    }

    public function get_sortable_columns()
    {
        return array(
            'id' => array('id', false),
            'name' => array('name', false),
            'slug' => array('slug', false),
            'date' => array('date', false),
            'post_type' => array('post_type', false));
    }

    public function column_cb($item)
    {
        return sprintf('<input id = "cb-select-%s" type="checkbox" name="%s[]" value="%s" />',
            esc_attr($item['id']), $this->_args['singular'], esc_attr($item['id']));
    }

    private function process_table_data()
    {
        $args = array(
            'public' => true,
            'publicly_queriable' => true,
            'show_ui' => true,
            'show_in_nav_menus' => true,
            'exclude_from_search' => false);

        $post_types = get_post_types($args, 'names', 'or');
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

        $args = array(
            'post_type' => $post_types,
            'post_status' => 'any',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC');

        $the_query = new WP_Query($args);
        $data = array();
        if ($the_query->have_posts())
        {
            while ($the_query->have_posts())
            {
                $the_query->the_post();
                if (get_post_meta($the_query->post->ID, '_icl_lang_duplicate_of', true) &&
                    get_post_meta($the_query->post->ID, '_icl_lang_duplicate_of', true) != 0)
                {
                    $data[] = array(
                        'id' => esc_attr($the_query->post->ID),
                        'name' => esc_attr($the_query->post->post_title),
                        'slug' => esc_attr($the_query->post->post_name),
                        'date' => esc_attr($the_query->post->post_date),
                        'post_type' => esc_attr($the_query->post->post_type));
                }
            }
        }
        return $data;
    }

    private function revert_table_data()
    {
        $args = array(
            'public' => true,
            'publicly_queriable' => true,
            'show_ui' => true,
            'show_in_nav_menus' => true,
            'exclude_from_search' => false);

        $post_types = get_post_types($args, 'names', 'or');
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

        $args = array(
            'post_type' => $post_types,
            'post_status' => 'any',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC');

        $the_query = new WP_Query($args);
        $data = array();
        if ($the_query->have_posts())
        {
            while ($the_query->have_posts())
            {
                $the_query->the_post();
                if (!get_post_meta($the_query->post->ID, '_icl_lang_duplicate_of', true))
                {
                    $data[] = array(
                        'id' => esc_attr($the_query->post->ID),
                        'name' => esc_attr($the_query->post->post_title),
                        'slug' => esc_attr($the_query->post->post_name),
                        'date' => esc_attr($the_query->post->post_date),
                        'post_type' => esc_attr($the_query->post->post_type));
                }
            }
        }
        return $data;
    }

    public function column_default($item, $column_name)
    {
        switch ($column_name)
        {
            case 'id':
            case 'name':
            case 'slug':
            case 'date':
            case 'post_type':
                return $item[$column_name];
            default:
                return print_r($item, true);
        }
    }

    private function sort_data($a, $b)
    {
        $orderby = 'name';
        $order = 'asc';

        if (!empty($_GET['orderby']))
        {
            $orderby = $_GET['orderby'];
        }

        if (!empty($_GET['order']))
        {
            $order = $_GET['order'];
        }

        $result = strnatcmp($a[$orderby], $b[$orderby]);

        if ($order === 'asc')
        {
            return $result;
        }

        return - $result;
    }

    public function process_selected_posts()
    {
        if (!empty($_POST['bti79_item']))
        {
            $checked = $_POST['bti79_item'];
            $selected_ids = array();
            foreach ($checked as $id)
            {
                $selected_ids[] = $id;
                delete_post_meta($id, '_icl_lang_duplicate_of');
            }
            $number = count($selected_ids);
            $the_ids = implode(', ', $selected_ids);
            return array($number, $the_ids);
        }
    }

    public function revert_selected_posts()
    {
        if (!empty($_POST['bti79_item']))
        {
            global $sitepress;
            $checked = $_POST['bti79_item'];
            $selected_ids = array();
            foreach ($checked as $id)
            {
                $selected_ids[] = $id;
                delete_post_meta(icl_object_id($id,'any',true, $sitepress->get_default_language()), '_icl_lang_duplicate_of');
                add_post_meta($id, '_icl_lang_duplicate_of', icl_object_id($id,'any',true, $sitepress->get_default_language()), true);
            }
			$number = count($selected_ids);
			$the_ids = implode(', ', $selected_ids);
			return array($number, $the_ids);            
        }
    }
}
