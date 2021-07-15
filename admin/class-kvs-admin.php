<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.kernel-video-sharing.com/
 * @since      1.0.0
 *
 * @package    Kvs
 * @subpackage Kvs/admin
 * @author     Kernel Video Sharing <sales@kernel-video-sharing.com>
 */
class Kvs_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		global $kernel_video_sharing;

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			'kvs-boot-css', 
			'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', 
			array(), '2.0.0', 'all' );
		wp_enqueue_style(
			$this->plugin_name, 
			plugin_dir_url( __FILE__ ) . 'css/kvs-admin.css', 
			array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 
			$this->plugin_name, 
			plugin_dir_url( __FILE__ ) . 'js/kvs-admin.js', 
			array( 'jquery' ), $this->version, true);
		wp_enqueue_script( 
			$this->plugin_name . '-fa', 
			plugin_dir_url( __FILE__ ) . 'js/fontawesome.js', 
			array(), $this->version, true);
	}


	/**
	 * KVS plugin activation notice
	 *
	 * @since 1.0.0
	 */
	public function kvs_admin_notice_activation() {
		if( get_transient( 'kvs-admin-notice' ) ) {
            $link = admin_url( 'edit.php?post_type=kvs_video&page=kvs-settings' );
			echo '<div class="updated notice is-dismissible">';
			echo '<p>'.__( 'Welcome to Kernel Video Sharing plugin. Start listing, syncing and managing your videos!', 'kvs' ).'</p>';
			echo '<p><a href="' . $link . '" class ="kvs_configuration_plugin_main">'.__( 'Connect to KVS', 'kvs' ).'</a></p>';
			echo '</div>';
			delete_transient( 'kvs-admin-notice' );
		}
	}


	/**
	 * KVS plugin status panel on all KVS pages
	 *
	 * @since 1.0.0
	 */
	public function kvs_top_info_panel() {
        global $pagenow, $post_type;
        
        $is_settings = false;
        if( !empty($_GET['page']) && $_GET['page'] === 'kvs-settings' ) {
            $is_settings = true;
        }
        if( $post_type !== 'kvs_video' && !$is_settings ) {
            return;
        }
        
        $cron_period = Kvs_Cron::get_cron_update_period();
        $cron_schedules = Kvs_Cron::kvs_cron_schedules();
        if( empty($cron_schedules[$cron_period]) ) {
            $cron_period = 'manual';
        }
        
        if( $cron_period !== 'manual' ) {
            $last_run = (int)get_option( 'kvs_feed_last_run', 0 );
            $next_run = wp_next_scheduled( 'kvs_cron_update_hook' );
            
            echo '<div class="kvs-info-panel ' . ( $is_settings ? ' no-options' : '' ) . '" id="kvs-info-panel">';
            
            echo '<i class="fa fa-clock fa-fw"></i> ';
            if( $last_run ) {
                $last_run_p = time() - $last_run;
                if( $last_run_p<=60 ) {
                    $last_run_p = __( 'less than a minute', 'kvs' );
                } elseif( $last_run_p < 60*60 ) {
                    $last_run_p = human_readable_duration( gmdate( 'i:s', $last_run_p ) );
                } else {
                    $last_run_p = human_readable_duration( gmdate( 'H:i:s', $last_run_p ) );
                }
                echo sprintf( __( 'Last import %s ago.', 'kvs' ), $last_run_p );
            } else {
                echo __( 'Automatic videos synchronization was not fired yet.', 'kvs' );
            }
            
            $updated = (int)get_option( 'kvs_feed_last_update', 0 );
            $inserted = (int)get_option( 'kvs_feed_last_insert', 0 );
            $deleted = (int)get_option( 'kvs_feed_last_delete', 0 );
            if( $updated + $inserted + $deleted > 0 ) {
                echo '<div class="kvs-last-run">';
                echo '<i class="fa fa-exclamation fa-fw"></i> ';
                $actions = array();
                if( $updated ) {
                    $actions[] = sprintf( __( '%d videos was updated', 'kvs' ), $updated );
                }
                if( $inserted ) {
                    $actions[] = sprintf( __( '%d new videos was added', 'kvs' ), $inserted );
                }
                if( $deleted ) {
                    $actions[] = sprintf( __( '%d videos was deleted', 'kvs' ), $deleted );
                }
                echo implode(', ', $actions);
                echo '.';
                echo '</div>';
            }
            
            $next_run_p = $next_run - time();
            if( $next_run_p<=60 ) {
                $next_run_r = __( 'less than a minute', 'kvs' );
            } elseif( $next_run_p < 60*60 ) {
                $next_run_r = human_readable_duration( gmdate( 'i:s', $next_run_p ) );
            } else {
                $next_run_r = human_readable_duration( gmdate( 'H:i:s', $next_run_p ) );
            }
            echo '<div class="kvs-next-run">';
            echo '<i class="fa fa-calendar fa-fw"></i> ';
            echo sprintf( __( 'Next run in %s.', 'kvs' ), $next_run_r );
            if( !empty( $cron_schedules[$cron_period]['display'] ) ) {
                echo ' (' . $cron_schedules[$cron_period]['display'] . ')';
            }
            echo '</div>';
            
            echo '</div>';
        }
	}


	/**
	 * Adding admin menus
	 *
	 * @since 1.0.0
	 */
	public function kvs_add_menus() {
        global $submenu;
        
		if( file_exists( KVS_DIRPATH . 'admin/partials/dashboard-view.php' ) ) {
    		add_submenu_page( 'edit.php?post_type=kvs_video', __( 'Dashboard', 'kvs'), __( 'Dashboard', 'kvs'), 'manage_options', 'kvs-dashboard', array( $this, 'kvs_dashboard_page' ), 0 );
        }
		if( file_exists( KVS_DIRPATH . 'admin/partials/settings-view.php' ) ) {
    		add_submenu_page( 'edit.php?post_type=kvs_video', __( 'Settings', 'kvs'), __( 'Settings', 'kvs'), 'manage_options', 'kvs-settings', array( $this, 'kvs_settings_page' ) );
        }
		if( file_exists( KVS_DIRPATH . 'admin/partials/help-view.php' ) ) {
    		add_submenu_page( 'edit.php?post_type=kvs_video', __( 'Help', 'kvs'), __( 'Help', 'kvs'), 'manage_options', 'kvs-help', array( $this, 'kvs_help_page' ) );
        }
        $submenu['edit.php?post_type=kvs_video'][] = array(__( 'Visit website', 'kvs') . ' <i class="fas fa-external-link-alt"></i>', 'manage_options', KVS_WEBSITE);
	}


	/**
	 * KVS Dashboard Widget adding
	 *
	 * @since 1.0.0
	 */
	public function kvs_add_dashboard_widget() {
        $title = '<span>';
        $title .= '<img src="' . KVS_DIRURL . 'admin/images/logo_wide.svg" height="30" style="margin: -10px 0;" alt=" ';
        $title .= __( 'Kernel Video Sharing', 'kvs' );
        $title .= '" /></span>';
        wp_add_dashboard_widget( 
            KVS_PREFIX . '_dashboard_widget', 
            $title,
            array( new Kvs_DB(), 'kvs_dashboard_widget' ),
            null, // control callback
            null, // callback args
            'side',
            'high'
        );
	}

	/**
	 * KVS Dashboard page
	 *
	 * @since 1.0.0
	 */
	public function kvs_dashboard_page() {
		global $kernel_video_sharing;

		if(!current_user_can('manage_options')) {
			wp_die('Unauthorized user');
		}

		include_once KVS_DIRPATH . 'admin/partials/dashboard-view.php';
	}

	/**
	 * KVS settings page
	 *
	 * @since 1.0.0
	 */
	public function kvs_settings_page() {
		global $kernel_video_sharing;

		if(!current_user_can('manage_options')) {
			wp_die('Unauthorized user');
		}

		include_once KVS_DIRPATH . 'admin/partials/settings-view.php';
	}


	/**
	 * KVS help page
	 *
	 * @since 1.0.0
	 */
	public function kvs_help_page() {
		global $kernel_video_sharing;

		if(!current_user_can('manage_options')) {
			wp_die('Unauthorized user');
		}

		include_once KVS_DIRPATH . 'admin/partials/help-view.php';
	}


	/**
	 * Manage admin KVS videos editor columns
	 *
	 * @since 1.0.0
	 * @param     array    Input columns
	 * @return    array    Filtered and extended columns
	 */
    public function kvs_editor_columns( $columns ) {
        // Insert Videos column anfter checkbox, before Title
        $keys = array_keys( $columns );
        $index = array_search( 'cb', $keys );
        $pos = false === $index ? count( $columns ) : $index + 1;

        $columns = array_merge(
            array_slice( $columns, 0, $pos ),
            arraY( 'video' => __( 'Video', 'kvs' ) ),
            array_slice( $columns, $pos )
        );
        
        $columns['last_modified'] = __( 'Last update', 'kvs' );
        $columns['shortcode'] = __( 'Shortcode', 'kvs' );
        
        return $columns;
    }
    
	/**
	 * Fill admin KVS videos editor custom columns
	 *
	 * @since 1.0.0
	 * @param     string    Input columns
	 * @param     int       Input columns
	 */
    public function kvs_editor_columns_data( $column, $post_id ) {
        if( $column == 'video' ) {
            echo '<a href="'.get_post_meta( $post_id, 'kvs-video-link', true ).'" target="_blank">';
            echo '<img src="'.get_post_meta( $post_id, 'kvs-video-screenshot', true ).'" height="80" style="max-width:100%;" />';
            echo '</a>';
        }
        if( $column == 'last_modified' ) {
            echo get_the_modified_date( get_option('date_format') . ' ' . get_option('time_format'), $post_id );
        }
        if( $column == 'shortcode' ) {
            $click2copy = __( 'Click to copy to clipboard', 'kvs' );
            echo '<span class="copy2cb" title="' . $click2copy . '">';
            echo Kvs_SC::kvs_player_shortcode_constructor( get_post_meta( $post_id, 'kvs-video-id', true ) );
            echo ' <i class="fas fa-fw fa-clone"></i>';
            echo '</span>';
        }
    }
    
    
	/**
	 * Add sorting to custom columns
	 *
	 * @since 1.0.0
	 * @param     string    Input columns
	 * @param     int       Input columns
	 */
    public function kvs_editor_columns_sort( $columns ) {
        $columns['last_modified'] = 'last_modified';
        
        return $columns;
    }

    
	/**
	 * Add "Manual Import" button to the KVS Videos list header
	 *
	 * @since 1.0.0
	 */
	public function kvs_add_import_videos_button() {
		global $post_type_object, $kernel_video_sharing;

		if( $post_type_object->name === 'kvs_video' &&
			 !empty( $kernel_video_sharing->reader->get_feed_url() ) ) {
?><script type="text/javascript">jQuery(document).ready( function($){
jQuery('.wrap h1').after('<button onclick="jQuery(\'#kvs-import-form\').submit();" class="page-title-action kvs-import"><i class="fas fa-fw fa-sync"></i> <?php echo __( 'Check for new videos', 'kvs' );?></button>');
});</script>
<form method="post" id="kvs-import-form" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
    <input type="hidden" name="action" value="kvs_import">
<?php
    wp_nonce_field( 'kvs_import', 'kvs-nonce' );
?>
</form>
    <?php
		}
	}


	/**
	 * Check KVS feed URL for validity
	 *
	 * @since    1.0.0
	 * @param     string    Input submitted
	 * @return    string    Input filtered
	 */
	public function kvs_feed_url_check( $val ) {
		global $kernel_video_sharing;

		if( !empty( $val && $val !== $kernel_video_sharing->reader->get_feed_url() ) ) {
			if( substr($val, -1, 1) != '/' ) {
				$val .= '/';
			}

			$meta = $kernel_video_sharing->reader->update_feed_meta( $val );
			if( empty($meta) ) {
                delete_option( 'kvs_feed_last_id' );
                delete_option( 'kvs_feed_meta' );
                delete_option( 'kvs_feed_meta_update_time' );
                wp_clear_scheduled_hook( 'kvs_cron_update_hook' );
                wp_clear_scheduled_hook( 'kvs_cron_delete_hook' );
                
				$val = '';
				add_settings_error(
					'kvs_messages',
					'kvs_feed_url_error',
					__( 'Feed URL is invalid', 'kvs' ),
					'error'
	        	);
			}
		}
		return $val;
	}
    
	/**
	 * Check if cron update period was changed
	 *
	 * @since    1.0.0
	 * @param     string    Input submitted
	 * @return    string    Input filtered
	 */
	public function kvs_cron_update_period_change( $val ) {
        global $kernel_video_sharing;
        
		if( $val !== Kvs_Cron::get_cron_update_period() ) {
            wp_clear_scheduled_hook( 'kvs_cron_update_hook' );
            $kernel_video_sharing->logger->log(
                'Scheduled feed update period was changed from "' .
                Kvs_Cron::get_schedule_title( Kvs_Cron::get_cron_update_period() ) .
                '" to "' . Kvs_Cron::get_schedule_title( $val ) . '"', 
                'DEBUG'
            );
		}
		return $val;
	}
    
    
	/**
	 * Check if cron delete period was changed
	 *
	 * @since    1.0.0
	 * @param     string    Input submitted
	 * @return    string    Input filtered
	 */
	public function kvs_cron_delete_period_change( $val ) {
        global $kernel_video_sharing;
        
		if( $val !== Kvs_Cron::get_cron_delete_period() ) {
            wp_clear_scheduled_hook( 'kvs_cron_delete_hook' );
            $kernel_video_sharing->logger->log(
                'Scheduled feed update period was changed to: ' . Kvs_Cron::get_schedule_title( $val ), 
                'DEBUG'
            );
		}
		return $val;
	}
    
    
	/**
	 * Check if cron FULL update period was changed
	 *
	 * @since    1.0.0
	 * @param     string    Input submitted
	 * @return    string    Input filtered
	 */
	public function kvs_cron_full_period_change( $val ) {
        global $kernel_video_sharing;
        
		if( $val !== Kvs_Cron::get_cron_full_period() ) {
            wp_clear_scheduled_hook( 'kvs_cron_full_hook' );
            $kernel_video_sharing->logger->log(
                'Scheduled feed FULL update period was changed from "' .
                Kvs_Cron::get_schedule_title( Kvs_Cron::get_cron_full_period() ) .
                '" to "' . Kvs_Cron::get_schedule_title( $val ) . '"', 
                'DEBUG'
            );
		}
		return $val;
	}
    
    
	/**
	 * Update KVS feed meta
	 *
	 * @since 1.0.0
	 */
    public function kvs_update_meta() {
        global $kernel_video_sharing;
        
        $meta = $kernel_video_sharing->reader->update_feed_meta();
        if( !empty($meta) ) {
            set_transient( 'kvs-meta-notice-success', true, 5 );
        }
        $link = admin_url( 'edit.php?post_type=kvs_video&page=kvs-settings&section=rules' );
        wp_redirect( $_SERVER["HTTP_REFERER"] ?? $link, 302, 'WordPress' );
        exit;
    }

	/**
	 * Do import action: add new videos and update old ones
	 *
	 * @since 1.0.0
	 * @param     bool    $silent         Show admin notice after and return headers
	 * @param     bool    $full_import    Ignore last imported Video ID and do full import
	 */
    public function kvs_do_import( $silent = false, $full_import = false ) {
		global $kernel_video_sharing, $wpdb;
        
        $debug = 'Videos ';
        if( $full_import ) {
            $debug .= 'FULL ';
        }
        $debug .= 'import/update started';
        if( $silent ) {
            $debug .= ' in silent mode';
        }
        $kernel_video_sharing->logger->log( $debug, 'DEBUG' );
        
        $taxonomy_category = get_option( 'kvs_taxonomy_category' );
        $taxonomy_tag      = get_option( 'kvs_taxonomy_tag' );
        $taxonomy_model    = get_option( 'kvs_taxonomy_model' );
        $taxonomy_source   = get_option( 'kvs_taxonomy_source' );
        
        $kernel_video_sharing->logger->log( 
            'Categories taxonomy: ' . ($taxonomy_category ?: '-') .
            '; Tags taxonomy: ' . ($taxonomy_tag ?: '-') .
            '; Models taxonomy: ' . ($taxonomy_model ?: '-') .
            '; Sources taxonomy: ' . ($taxonomy_source ?: '-'),
            'DEBUG' );

        $filter = null;
        $taxonomy_filter_by = get_option( 'kvs_video_filter_by' );
        switch ($taxonomy_filter_by) {
            case 'categories':
                $filter = get_option( 'kvs_video_filter_category' );
                break;
            case 'content_sources':
                $filter = get_option( 'kvs_video_filter_source' );
                break;
            default:
                $taxonomy_filter_by = '';
        }
        if( !empty( $taxonomy_filter_by ) ) {
            $kernel_video_sharing->logger->log(
                'Filter applied on ' . $taxonomy_filter_by . ': ' . implode(', ', $filter), 
                'DEBUG'
            );
        }
        
        $format = get_option( 'kvs_video_screenshot' );
        $locale = get_option( 'kvs_video_locale' );
        $kernel_video_sharing->logger->log( 'Feed locale set: ' . $locale, 'DEBUG' );
        
        $limit = 0;
        if( !$full_import ) {
            $limit = (int)get_option( 'kvs_update_limit' );
            $kernel_video_sharing->logger->log( 'Batch limit set: ' . $limit, 'DEBUG' );
        }
        
        $last_id = 0;
        if( !$full_import ) {
            $last_id = (int)get_option( 'kvs_feed_last_id' );
            $kernel_video_sharing->logger->log( 'Last video ID: ' . $last_id, 'DEBUG' );
        }
        
        $import_featured_image = !empty( get_option( 'kvs_post_import_featured_image' ) );
        
        $data = $kernel_video_sharing->reader->get_feed( $last_id+1, $limit, $format, $locale );
        if( !empty($data) ) {
            // Index videos in the feed
            $ids = array();
            foreach($data as $n=>$row) {
                $last_id = $row['id'];
                
                if( !empty( $taxonomy_filter_by ) ) {
                    if( $taxonomy_filter_by == 'content_source' ) {
                        if( !in_array( $row['content_source'], $filter ) ) {
                            unset( $data[$n] );
                            continue;
                        }
                    }
                    if( $taxonomy_filter_by == 'categories' ) {
                        if( !array_intersect( $row['categories'], $filter ) ) {
                            unset( $data[$n] );
                            continue;
                        }
                    }
                }
                
                $ids[ (int)$row['id'] ] = $n;
            }
            if( !empty( $taxonomy_filter_by ) ) {
                $kernel_video_sharing->logger->log( count( $ids ) . ' videos to process after filtering', 'DEBUG' );
            }
            
            if( !empty( $ids ) ) {
                // Find videos that are already in the WP DB
                // 
                // Warning: 
                // We do not check post status here to prevent post recreation 
                // in case of corresponding post was already trashed before
                $posts = $wpdb->get_results("SELECT post_id, meta_value FROM $wpdb->postmeta
                    WHERE meta_key = 'kvs-video-id' 
                    AND meta_value IN (" . implode( ',', array_keys($ids) ) . ")", ARRAY_N);
                $kernel_video_sharing->logger->log( count( $posts ) . ' corresponding posts found', 'DEBUG' );

                // Mark videos from the feed to UPDATE
                // Rest records will be created as a new posts
                foreach($posts as $post) {
                    $indx = $ids[(int)($post[1])];
                    $data[$indx]['wp_post_id'] = (int)($post[0]);
                }

                // Process videos (inser and update)
                foreach($data as $row) {
                    // Filling base post properties and custom fields
                    $video = array(
                        'post_type'     => get_option( 'kvs_post_type' ) ?: 'kvs_video', 
                        'post_title'    => $row['title'],
                        'post_content'  => $row['description'],
                        'post_date'     => $row['post_date'],
                        'post_date_gmt' => get_gmt_from_date( $row['post_date'] ),
                        'meta_input'    => array(
                            'kvs-video-id' => $row['id'],
                            'kvs-video-rating' => $row['rating'],
                            'kvs-video-rating-percent' => $row['rating_percent'],
                            'kvs-video-votes' => $row['votes'],
                            'kvs-video-popularity' => $row['popularity'],
                            'kvs-video-link' => $row['link'],
                            'kvs-video-file-url' => $row['file_url'], // ToDo: do not import in future
                            'kvs-video-duration' => $row['duration'],
                        ),
                    );
                    
                    $replacements = array(
                        '{%id%}'          => $row['id'],
                        '{%title%}'       => $row['title'],
                        '{%description%}' => $row['description'],
                        '{%date%}'        => $row['post_date'],
                        '{%popularity%}'  => $row['popularity'],
                        '{%rating%}'      => $row['rating'],
                        '{%votes%}'       => $row['votes'],
                        '{%duration%}'    => $row['duration'],
                        '{%link%}'        => $row['link'],
                    );
                    $contentTemplate = get_option( 'kvs_post_body_template' ) ?: '{%description%}';
                    $video['post_content'] = strtr( $contentTemplate, $replacements);
                    
                    if( !empty( $row['screenshot_main'] ) && 
                        !empty( $row['screenshots'][ $row['screenshot_main']-1 ] ) ) {
                        $video['meta_input']['kvs-video-screenshot'] = $row['screenshots'][ $row['screenshot_main']-1 ];
                    } else {
                        $video['meta_input']['kvs-video-screenshot'] = reset( $row['screenshots'] );
                    }

                    $post_id = null;
                    $featured_image_id = null;
                    // Checking if we need to update post or create a new one
                    if( !empty( $row['wp_post_id'] ) ) {
                        $video['ID'] = $row['wp_post_id'];
                        $post_id = wp_update_post( $video, $wp_error = true );
                        if( !is_wp_error( $post_id ) ) {
                            $featured_image_id = get_post_thumbnail_id( $post_id );
                            
                            $kernel_video_sharing->logger->log( 
                                'Updating post #' . $row['wp_post_id'] . 
                                ' for video #' . $row['id'],
                                'DEBUG'
                            );
                        } else {
                            $kernel_video_sharing->logger->log(
                                'Error updating post #' . $row['wp_post_id'] . 
                                ' for video #' . $row['id'] . ': ' . 
                                $post_id->get_error_message(),
                                'ERROR'
                            );
                        }
                    } else {
                        $video['post_status'] = 'publish'; // ToDo: Move that initial state setting to options
                        $video['post_author'] = 1;  // ToDo: Try to find corresponding WP user by $row['user']
                        $post_id = wp_insert_post( $video, $wp_error = true );
                        if( !is_wp_error( $post_id ) ) {
                            $kernel_video_sharing->logger->log( 
                                'New post #' . $post_id . 
                                ' added for video #' . $row['id'],
                                'DEBUG'
                            );
                        } else {
                            $kernel_video_sharing->logger->log(
                                'Error inserting new post for video #' . $row['id'] . ': ' . 
                                $post_id->get_error_message(),
                                'ERROR'
                            );
                        }
                    }

                    // Update taxonomies if post was successfully created or updated
                    if( !is_wp_error( $post_id ) && !empty( $post_id ) ) {
                        $debug = array();
                        
                        if( $import_featured_image ) {
                            $image = $this->kvs_generate_featured_image( 
                                $post_id,
                                $featured_image_id,
                                $video['post_title'],
                                $video['meta_input']['kvs-video-screenshot']
                            );
                            if( $image ) {
                                $debug[] = 'Featured image imported from ' . 
                                           $video['meta_input']['kvs-video-screenshot'];
                            } else {
                                $debug[] = 'Error importing featured image from ' . 
                                           $video['meta_input']['kvs-video-screenshot'];
                            }
                        }
                        
                        if( !empty( $taxonomy_category ) ) {
                            $row['categories'] = isSet( $row['categories'] ) ? $row['categories'] : null;
                            wp_set_object_terms( $post_id, $row['categories'], $taxonomy_category );
                            $debug[] = 'Catogories: ' . implode(', ', $row['categories']);
                        }
                        if( !empty( $taxonomy_tag ) ) {
                            $row['tags'] = isSet( $row['tags'] ) ? $row['tags'] : null;
                            wp_set_object_terms( $post_id, $row['tags'], $taxonomy_tag );
                            $debug[] = 'Tags: ' . implode(', ', $row['tags']);
                        }
                        if( !empty( $taxonomy_model ) ) {
                            $row['models'] = isSet( $row['models'] ) ? $row['models'] : null;
                            wp_set_object_terms( $post_id, $row['models'], $taxonomy_model );
                            $debug[] = 'Models: ' . implode(', ', $row['models']);
                        }
                        if( !empty( $taxonomy_source ) ) {
                            $row['content_source'] = isSet( $row['content_source'] ) ? $row['content_source'] : null;
                            wp_set_object_terms( $post_id, $row['content_source'], $taxonomy_source );
                            $debug[] = 'Source: ' . $row['content_source'];
                        }
                        if( !empty( $debug ) ) {
                            $kernel_video_sharing->logger->log(
                                'Taxonomies set for the post #' . $post_id . ': ' . 
                                implode( '; ', $debug ),
                                'DEBUG'
                            );
                        }
                    }

                }
                update_option( 'kvs_feed_last_update', count($posts) );
                update_option( 'kvs_feed_last_insert', count($data)-count($posts) );
            } else {
                if(!$silent) {
                    set_transient( 'kvs-import-notice-empty', true, 5 );
                }
                
                update_option( 'kvs_feed_last_update', 0 );
                update_option( 'kvs_feed_last_insert', 0 );
            }
            
            update_option( 'kvs_feed_last_id', $last_id );
            
            if(!$silent) {
                set_transient( 'kvs-import-notice-success', true, 5 );
            }
        } else {
            if(!$silent) {
                set_transient( 'kvs-import-notice-empty', true, 5 );
            }
            
            update_option( 'kvs_feed_last_update', 0 );
            update_option( 'kvs_feed_last_insert', 0 );
        }
        
        update_option( 'kvs_feed_last_run', time() );
        $kernel_video_sharing->logger->log( 'Feed processing finished', 'DEBUG' );
        
        if(!$silent) {
            $post_type_object = get_post_type_object( 'kvs_video' );
            $link = admin_url( $post_type_object->_edit_link );
            wp_redirect( $_SERVER["HTTP_REFERER"] ?? $link, 302, 'WordPress' );
            exit;
        }
    }
    
	/**
	 * Save screenshot image locally and attach to the post
	 *
	 * @since 1.0.3
	 * @param     int       $post_id       Post ID to attach featured image
	 * @param     int       $attach_id     Attachment ID for featured image (on post updating)
	 * @param     string    $title         featured image Title
	 * @param     string    $image_url     Screenshot image location
	 * @param     string    $file_name     File name for featured image
	 */
    public function kvs_generate_featured_image( $post_id, $attach_id, $title, $image_url, $file_name = null  ){
		global $kernel_video_sharing;
        
        if( empty( $image_url ) ) {
    		$kernel_video_sharing->logger->log( 'Empty screenshot URL provided', 'DEBUG' );
            return;
        }
        
        $image_data = wp_remote_retrieve_body( wp_remote_get( $image_url ) );
        if( empty( $image_data ) ) {
            $image_data = file_get_contents( $image_url );
        }
        if( empty( $image_data ) ) {
    		$kernel_video_sharing->logger->log( 'Empty screenshot file content', 'DEBUG' );
            return;
        }
        
        $upload_dir = wp_upload_dir();
        if( empty( $file_name ) ) {
            $file_name = $post_id . '-screenshot-' . basename( $image_url );
        }
        
        if( wp_mkdir_p( $upload_dir['path'] ) ) {
            $file = $upload_dir['path'] . '/' . $file_name;
        } else {
            $file = $upload_dir['basedir'] . '/' . $file_name;
        }
        $res = file_put_contents( $file, $image_data );
        if( empty( $res ) ) {
    		$kernel_video_sharing->logger->log(
                'Error writing featured image file contents to: ' . $file,
                'DEBUG'
            );
            return;
        }

        $wp_filetype = wp_check_filetype( $file_name, null );
        if( empty( $wp_filetype['type'] ) ) {
    		$kernel_video_sharing->logger->log(
                'Unsupported featured image file type',
                'DEBUG'
            );
            return;
        }
        
        if( empty( $title ) ) {
            $title = sanitize_file_name( $file_name );
        }

        if( empty( $attach_id ) ) {
            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title' => $title,
                'post_content' => '',
                'post_status' => 'inherit'
            );
            $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
        }
        
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
        wp_update_attachment_metadata( $attach_id, $attach_data );
        
        set_post_thumbnail( $post_id, $attach_id );
        
        return true;
    }

	/**
	 * Do import action: delete old videos
	 *
	 * @since 1.0.0
	 * @param     bool    $silent         Show admin notice after and return headers
	 * @param     bool    $full_import    Ignore last imported Video ID and do full import
	 */
    public function kvs_do_delete( $full_import = false ) {
		global $kernel_video_sharing, $wpdb;
        
        update_option( 'kvs_feed_last_delete', 0 );
        $days = $full_import ? 0 : 1;
        
        $deleted = $kernel_video_sharing->reader->get_deleted_ids( $days );
        if( !empty( $deleted ) ) {
            // Find videos in the WP DB by KVS IDs
            $posts = $wpdb->get_results("SELECT pm.post_id, pm.meta_value 
                FROM $wpdb->postmeta pm
                    INNER JOIN $wpdb->posts p ON pm.post_id = p.ID
                WHERE p.post_status = 'publish'
                AND pm.meta_key = 'kvs-video-id' 
                AND pm.meta_value IN (" . implode( ',', $deleted ) . ")", ARRAY_N);

            if( !empty($posts) ) {
                $del_log = array();
                foreach($posts as $post) {
                    $del_post = wp_delete_post( $post[0], $force_delete = false );
                    if( $del_post ) {
                        $del_log[] = $del_post->ID;
                    }
                }
                $kernel_video_sharing->logger->log(
                    'Deleted ' . count( $del_log ) . ' videos: ' . implode(', ', $del_log), 
                    'DEBUG'
                );
                update_option( 'kvs_feed_last_delete', count( $posts ) );
            }
        }
    }

	/**
	 * Full import action
	 *
	 * @since 1.0.0
	 */
    public function kvs_do_full_import() {
        global $kernel_video_sharing;
        
        $name = wp_get_current_user()->user_login;
		$kernel_video_sharing->logger->log( 'Manual FULL import started by ' . $name, 'DEBUG' );
        $this->kvs_do_delete( $full_import = true );
        $this->kvs_do_import( $silent = false, $full_import = true );
    }

	/**
	 * Clear log action
	 *
	 * @since 1.0.0
	 */
    public function kvs_do_clear_log() {
        global $kernel_video_sharing;
        
        $kernel_video_sharing->logger->clear_log_content();
        
        $link = admin_url( 'edit.php?post_type=kvs_video&page=kvs-settings&section=advanced' );
        wp_redirect( $_SERVER["HTTP_REFERER"] ?? $link, 302, 'WordPress' );
        exit;
    }
    

	/**
	 * Videos import notice
	 *
	 * @since 1.0.0
	 */
	public function kvs_admin_notice_import() {
		if( get_transient( 'kvs-import-notice-success' ) ) {
			echo '<div class="updated notice is-dismissible">';
            $updated = (int)get_option( 'kvs_feed_last_update', 0 );
            $inserted = (int)get_option( 'kvs_feed_last_insert', 0 );
            $deleted = (int)get_option( 'kvs_feed_last_delete', 0 );
            echo '<p>';
            if( $updated + $inserted + $deleted > 0 ) {
                $actions = array();
                if( $updated ) {
                    $actions[] = sprintf( __( '%d videos was updated', 'kvs' ), $updated );
                }
                if( $inserted ) {
                    $actions[] = sprintf( __( '%d new videos was added', 'kvs' ), $inserted );
                }
                if( $deleted ) {
                    $actions[] = sprintf( __( '%d videos was deleted', 'kvs' ), $deleted );
                }
                echo implode(', ', $actions);
                echo '!';
            } else {
                _e( 'Videos was successfuly updated!', 'kvs' );
            }
            echo '</p>';
			echo '<p><a href="' . admin_url( 'edit.php?post_type=kvs_video' ) . '">'.__( 'Manage videos', 'kvs' ) . '</a></p>';
			echo '</div>';
			delete_transient( 'kvs-import-notice-success' );
		}
		if( get_transient( 'kvs-import-notice-empty' ) ) {
            if( (int)get_option( 'kvs_feed_last_id' ) > 0 ) {
    			echo '<div class="notice is-dismissible">';
    			echo '<p>' . __( 'No new videos was found in the KVS feed!', 'kvs' ) . '</p>';
            	echo '</div>';
            } else {
    			echo '<div class="notice notice-warning is-dismissible">';
                echo '<p>' . __( 'No videos was found in the KVS feed!', 'kvs' ) . '</p>';
                $link = admin_url( 'edit.php?post_type=kvs_video&page=kvs-settings' );
        		echo '<p><a href="' . $link . '">' . __( 'Check your KVS feed settings', 'kvs' ) . '</a></p>';
            	echo '</div>';
            }
            delete_transient( 'kvs-import-notice-empty' );
		}
		if( get_transient( 'kvs-meta-notice-success' ) ) {
			echo '<div class="updated notice is-dismissible">';
			echo '<p>' . __( 'KVS feed metadata updated!', 'kvs' ) . '</p>';
			echo '</div>';
			delete_transient( 'kvs-meta-notice-success' );
		}
	}


}
