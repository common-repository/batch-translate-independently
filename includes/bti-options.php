<?php

class bti79Options
{
    // Constructor
    public function __construct()
    {
        add_action('admin_menu', array($this, 'bti79_page'));
        add_action('admin_init', array($this, 'bti79_admin_init'));
        add_filter('set-screen-option', array(
            $this,
            'bti79_set_option',
            10,
            3));
    }

    // Register the style and JS script for the options page
    public function bti79_admin_init()
    {
        wp_register_script('bti-js', plugins_url('bti-js.js', __file__));
        wp_register_style('bti-style', plugins_url('bti-style.css', __file__));
    }

    // Enqueue the style and JS script for the options page
    public function bti79_admin_scripts()
    {
        wp_enqueue_script('bti-js');
        wp_enqueue_style('bti-style');
    }

    // Set the options page and output its styles and scripts
    public function bti79_page()
    {
        $hook = add_options_page('Batch Translate Independently', // Page Title
            'Batch Translate Independently', // Menu Title
            'manage_options', // Capability Required
            'batch-translate-independently', // Menu Slug
            array($this, 'bti79_create_admin_page') // Function Name
            );
        add_action("load-{$hook}", array($this, 'bti79_screen_options'));
        add_action("admin_print_scripts-{$hook}", array($this, 'bti79_admin_scripts'));
    }

    // Create the options page
    public function bti79_create_admin_page()
    {

?>
<div class="wrap">        
<h2>Batch Translate Independently for WPML</h2>
<?php

        $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'process_all_tab';

?>
<h2 class="nav-tab-wrapper">
<a href="?page=batch-translate-independently&tab=process_all_tab"
class="bti79_tab nav-tab <?php

        echo $active_tab == 'process_all_tab' ? 'nav-tab-active' : '';

?>"><?php _e('All Posts', 'bti-79'); ?></a>
<a href="?page=batch-translate-independently&tab=process_selected_tab"
class="bti79_tab nav-tab <?php

        echo $active_tab == 'process_selected_tab' ? 'nav-tab-active' : '';

?>"><?php _e('Selected Posts &rarr; Translate Independently', 'bti-79'); ?></a>
<a href="?page=batch-translate-independently&tab=revert_selected_tab"
class="bti79_tab nav-tab <?php

        echo $active_tab == 'revert_selected_tab' ? 'nav-tab-active' : '';

?>"><?php _e('Selected Posts &rarr; Synchronise', 'bti-79'); ?></a>                 
</h2>
<?php

        if ($active_tab == 'process_all_tab')
        {
            global $sitepress;
            $bti79AllPosts = new bti79_apply_all_posts();

?>
<form class="bti79_form" method="post" action="">				
<?php

            if ($sitepress->get_default_language() == ICL_LANGUAGE_CODE)
            {

?>
<div class="bti79_info_div"><?php _e('You are currently working for', 'bti-79'); ?> <b><?php

                echo ICL_LANGUAGE_NAME_EN;

?></b> <?php _e('posts/pages', 'bti-79'); ?>.<br /><span class = "bti79_red"><b><?php _e('Since this is the default language, reverting to synchronised translation has been disabled', 'bti-79'); ?>!</b></span></div>
<?php

            } elseif ('all' == ICL_LANGUAGE_CODE)
            {

?>
<div class="bti79_info_div"><?php _e('You are currently working for posts/pages of', 'bti-79'); ?> <b><?php

                echo ICL_LANGUAGE_CODE;

?></b> languages.<br /><span class = "bti79_red"><b><?php _e('Since the default language is included, reverting to synchronised translation has been disabled', 'bti-79'); ?>!</b></span></div>
<?php

            } else
            {

?>
<div class="bti79_info_div"><?php _e('You are currently working for', 'bti-79'); ?> <b><?php

                echo ICL_LANGUAGE_NAME_EN;

?></b> <?php _e('posts/pages', 'bti-79'); ?>.</div>
<?php

            }
            if (isset($_POST['all_posts_trans_ind']))
            {
                $processed = $bti79AllPosts->process_all_posts();
            } elseif (isset($_POST['all_posts_revert']))
            {
                $reverted = $bti79AllPosts->revert_all_posts();
            }
            $stats = $bti79AllPosts->check_all_posts();

?>        	              	
<div class="bti79_bordered_div">
<div class="bti79_stats_div"><?php _e('There are', 'bti-79');
			
			echo '&nbsp;';
            echo $stats[0];
            echo ' out of ';
            echo $stats[6];
            echo '&nbsp;';
			if ('all' !== ICL_LANGUAGE_CODE)
            {
            	echo ICL_LANGUAGE_NAME_EN;			
				echo '&nbsp;';
			}

_e('posts that are synchronised', 'bti-79'); ?>.</div><button type="button" class="button button-small bti79_btn" onclick="bti79_process_post_list()"><?php _e('Show List', 'bti-79'); ?></button><br />
<div class="bti79_flex">
<input style="width: 100px;" type="submit" value="Apply" class="button button-primary" id="all_posts_trans_ind" name="all_posts_trans_ind"/>
<span class="bti79_span"><?php _e('Clicking this button will', 'bti-79'); ?> <b><?php _e('set the', 'bti-79');

			echo '&nbsp;';
            echo $stats[0];
            echo ' ';
            echo ICL_LANGUAGE_NAME_EN;

?></b> <?php _e('posts and pages synchronised', 'bti-79'); ?> <b> <?php _e('to translate independently', 'bti-79'); ?></b>.</span></div>
<div id="bti_list_process" style="display: none;">                    
<?php

            if ($stats[2])
            {
                echo '<br/>';
                foreach ($stats[2] as $key => $post_title)
                {
                    echo $key + 1 . '. ' . $post_title . ', ID:' . $stats[3][$key] . '<br/>';
                }
            } else
            {
                echo '<br/>'; echo '<br/>' . _e('Thats empty!', 'bti-79');
            }

?>                    
</div>
<?php

            if (isset($_POST['all_posts_trans_ind']))
            {

?>
<div style="margin-top: 20px;">
<span><?php _e('Displaying the IDs of the', 'bti-79');

				echo '&nbsp;';
                echo $processed[0];
				echo '&nbsp;';

_e('posts that have been processed', 'bti-79'); ?>.</span> <br/>
<textarea class="bti79_textarea"><?php

                echo $processed[1];

?></textarea>
</div>
<?php

            }

?>
</div>
<?php

            if ($sitepress->get_default_language() !== ICL_LANGUAGE_CODE && 'all' !==
                ICL_LANGUAGE_CODE)
            {

?>                    
<div class="bti79_bordered_div">
<div class="bti79_stats_div"><?php _e('There are', 'bti-79');

				echo '&nbsp;';
                echo $stats[1];
                echo ' out of ';
                echo $stats[6];
                echo ' ';
                echo ICL_LANGUAGE_NAME_EN;
				echo '&nbsp;';

_e('posts that are set to translate independently', 'bti-79'); ?>.<?php

                if ('all' == ICL_LANGUAGE_CODE)
                {
				
				echo '&nbsp;';
?> <b style = "color: #ff0000;"> <?php _e('NOTE: Posts of the default language are excluded', 'bti-79'); ?>!</b> <?php
				echo '&nbsp;';
				
                }

?></div><button type="button" class="button button-small bti79_btn" onclick="bti79_revert_post_list()"><?php _e('Show List', 'bti-79'); ?></button><br />
<div class="bti79_flex">
<input style="width: 100px;" type="submit" value="Revert" class="button button-primary" id="all_posts_revert" name="all_posts_revert"/>
<span class="bti79_span"><?php _e('Clicking this button will', 'bti-79'); ?> <b><?php _e('revert the', 'bti-79');

				echo '&nbsp;';
                echo $stats[1];
                echo ' ';
                echo ICL_LANGUAGE_NAME_EN;

?></b> <?php _e('posts and pages', 'bti-79'); ?> <b><?php _e('to synchronised translation', 'bti-79'); ?></b>.</span></div>
<div id="bti_list_revert" style="display: none;">                    
<?php

                if ($stats[4])
                {
                    echo '<br/>';
                    foreach ($stats[4] as $key => $post_title)
                    {
                        echo $key + 1 . '. ' . $post_title . ', ID:' . $stats[5][$key] . '<br/>';
                    }
                } else
                {
                    echo '<br/>'; echo '<br/>' . _e('Thats empty!', 'bti-79');
                }

?>                   
</div>
<?php

                if (isset($_POST['all_posts_revert']))
                {

?>
<div style="margin-top: 20px;">
<span><?php _e('Displaying the IDs of the', 'bti-79');

					echo '&nbsp;';
                    echo $reverted[0];
					echo '&nbsp;';

_e('posts that have been reverted', 'bti-79'); ?>.</span><br/>
<textarea class="bti79_textarea"><?php

                    echo $reverted[1];

?></textarea>
</div>
<?php

                }

?>
</div>
<?php

            }

?>
</form>                
<?php

        } elseif ($active_tab == 'process_selected_tab')
        {

            $bti79PostListTable = new bti79_post_list_table();
			if (isset($_POST['selected_posts_trans_ind']))
			{
				$processed = $bti79PostListTable->process_selected_posts();
			}
            global $sitepress;

?>
<form class="bti79_form" method="post" action="">
<div class="bti79_info_div"><?php _e('You are currently working for', 'bti-79'); ?> <b><?php

            echo ICL_LANGUAGE_NAME_EN;

?></b> <?php _e('posts/pages', 'bti-79');

            if ('all' == ICL_LANGUAGE_CODE)
            {
                echo ' <b> of all languages</b>';
            }

?>.</div>                     
<?php

            $bti79PostListTable->prepare_process_items();
            $bti79PostListTable->display();

?>                  
<div class="bti79_bordered_div"> 
<div class="bti79_flex">
<input style="width: 100px;" type="submit" value="Apply" class="button button-primary" id="selected_posts_trans_ind" name="selected_posts_trans_ind"/>
<span class="bti79_span"><?php _e('Clicking this button will set the synchronised posts and pages selected from the list above', 'bti-79'); ?> <b><?php _e('to translate independently', 'bti-79'); ?></b>.</span></div> 
<?php

            if (isset($_POST['selected_posts_trans_ind']))
            {                
                if (!empty($processed))
                {
?>
<div style="margin-top: 20px;">
<span><?php _e('Displaying the IDs of the', 'bti-79');
					
					echo '&nbsp;';
                    echo $processed[0];
					echo '&nbsp;';

_e('posts that have been processed', 'bti-79'); ?>.</span><br/>
<textarea class="bti79_textarea"><?php

                    echo $processed[1];

?></textarea>
</div>
<?php

                } else
                {

?>
				<div style="margin-top: 20px;">
                <textarea class="bti79_textarea"><?php _e('Nothing changed', 'bti-79'); ?>!</textarea>
                </div>
<?php

                }
            }

?>
</div>
</form>
<?php

        } elseif ($active_tab == 'revert_selected_tab')
        {
            $bti79PostListTable = new bti79_post_list_table();
			if (isset($_POST['selected_posts_revert']))
			{
				$processed = $bti79PostListTable->revert_selected_posts();
			}
            global $sitepress;

?>
<form class="bti79_form" method="post" action="">
<?php

            if ($sitepress->get_default_language() == ICL_LANGUAGE_CODE)
            {

?>
<div class="bti79_info_div"><?php _e('You are currently working for', 'bti-79'); ?> <b><?php

                echo ICL_LANGUAGE_NAME_EN;

?></b> <?php _e('posts/pages', 'bti-79'); ?>.<br /><span class = "bti79_red"><b><?php _e('Since this is the default language, reverting to synchronised translation has been disabled', 'bti-79'); ?>!</b></span></div>
<?php

            } elseif ('all' == ICL_LANGUAGE_CODE)
            {

?>
<div class="bti79_info_div"><?php _e('You are currently working for posts/pages of', 'bti-79'); ?> <b><?php

                echo ICL_LANGUAGE_CODE;

?></b> <?php _e('languages', 'bti-79'); ?>.<br /><span class = "bti79_red"><b><?php _e('Since the default language is included, reverting to synchronised translation has been disabled', 'bti-79'); ?>!</b></span></div>
<?php

            } else
            {

?>
<div class="bti79_info_div"><?php _e('You are currently working for', 'bti-79'); ?> <b><?php

                echo ICL_LANGUAGE_NAME_EN;

?></b> <?php _e('posts/pages', 'bti-79'); ?>.</div>                     
<?php

                if ($sitepress->get_default_language() !== ICL_LANGUAGE_CODE && 'all' !==
                    ICL_LANGUAGE_CODE)
                {

                }
                $bti79PostListTable->prepare_revert_items();
                $bti79PostListTable->display();

?>                  
<div class="bti79_bordered_div">
<div class="bti79_flex">
<input style="width: 100px;" type="submit" value="Revert" class="button button-primary" id="selected_posts_revert" name="selected_posts_revert"/>
<span class="bti79_span"><?php _e('Clicking this button will revert the posts and pages selected from the list above', 'bti-79'); ?> <b><?php _e('to synchronised translation', 'bti-79'); ?></b>.</span></div>
<?php

                if (isset($_POST['selected_posts_revert']))
                {                    
                    if (!empty($processed))
                    {

?>
<div style="margin-top: 20px;">
<span><?php _e('Displaying the IDs of the', 'bti-79');
						
						echo '&nbsp;';
                        echo $processed[0];
						echo '&nbsp;';

_e('posts that have been processed', 'bti-79');?>.</span><br/>
<textarea class="bti79_textarea"><?php

                        echo $processed[1];

?></textarea>
</div>
<?php

                    } else
                    {

?>
				<div style="margin-top: 20px;">
                <textarea class="bti79_textarea"><?php _e('Nothing changed', 'bti-79'); ?>!</textarea>
                </div>
<?php

                    }
                }

?>
</div>
</form>		

<?php

            }

?>
	</div>
	<?php

        }
    }

    // Adds the screen options
    public function bti79_screen_options()
    {
        $args = array(
            'label' => 'Items per Page',
            'default' => 10,
            'option' => 'items_per_page');
        add_screen_option('per_page', $args);
    }

    // Returns the screen option values set by the user
    public function bti79_set_option($status, $option, $value)
    {
        if ('items_per_page' == $option)
            return $value;
        return $status;
    }
}

$bti79 = new bti79Options();

?>