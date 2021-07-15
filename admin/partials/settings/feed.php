<?php

/**
 * KVS Plugin settings page view: Feed setting section
 *
 * @link       https://www.kernel-video-sharing.com/
 * @since      1.0.0
 *
 * @package    Kvs
 * @subpackage Kvs/admin/partials
 */
?>

<div class="wrap">
<?php
	settings_errors( 'kvs_messages' );
?>
<form method="post" action="options.php" id="kvs-settings-form">
    <?php settings_fields( 'kvs-settings-group' ); ?>
    <?php do_settings_sections( 'kvs-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row"><?php _e( 'KVS Feed URL', 'kvs' ); ?></th>
        <td>
            <?php if( !empty( get_option( 'kvs_feed_url' ) ) ): ?>
                <input type="text" name="kvs_feed_url_disabled" value="<?php echo esc_attr( get_option( 'kvs_feed_url' ) ); ?>" class="width-wide" disabled="disabled" />
                <input type="hidden" name="kvs_feed_url" value="<?php echo esc_attr( get_option( 'kvs_feed_url' ) ); ?>" id="kvs_feed_url">
                <a class="button button-secondary button-warning" onclick="if(confirm('<?php _e( 'Are you sure you want to disconnect current KVS feed?', 'kvs' );?>')){jQuery('#kvs_feed_url').val('');jQuery('#submit').click();}">
                    <i class="fas fa-unlink"></i> 
                    <?php _e( 'Disconnect feed', 'kvs' );?>
                </a>
            <?php else: ?>
                <input type="text" name="kvs_feed_url" value="" class="width-wide" />
            <?php endif; ?>
		</td>
        </tr>
        <?php if( !empty( get_option( 'kvs_feed_url' ) ) ): ?>
            <tr valign="top">
            <th scope="row"><?php _e( 'KVS libraries path', 'kvs' ); ?></th>
            <td>
                <input type="text" name="kvs_library_path" value="<?php echo esc_attr( get_option( 'kvs_library_path' ) ); ?>" class="width-wide" />
            </td>
            </tr>
        <?php endif; ?>
    </table>

<?php
	if( !empty( $kernel_video_sharing->reader->get_feed_url() ) ):
		$feed_meta = get_option( 'kvs_feed_meta' );
?>
    <h3>Adding and updating videos</h3>
    <table class="form-table">
        <tr valign="top">
        <th scope="row"><?php _e( 'Feed update period', 'kvs' ); ?></th>
        <td>
			<select name="kvs_update_period">
                <option value="manual"><?php _e( 'Manual synchronization', 'kvs' );?></option>
				<?php
				$selected = get_option('kvs_update_period');
				foreach( Kvs_Cron::kvs_cron_schedules( array() ) as $indx=>$val ) {
					echo '<option value="' . $indx . '"';
					echo ($indx === $selected) ? ' selected>' : '>';
					echo $val['display'];
                    echo '</option>';
				} ?>
			</select>
		</td>
        </tr>
        
        <tr valign="top" id="kvs_update_limit_row"<?php if( get_option( 'kvs_update_full' ) == 1 ){echo ' style="display:none;"';} ?>>
        <th scope="row"><?php _e( 'Limit videos per run', 'kvs' ); ?></th>
        <td>
            <?php $selected_limit = (int)get_option( 'kvs_update_limit' ); ?>
			<select name="kvs_update_limit">
				<option value="0"><?php _e( 'No limit', 'kvs' );?></option>
				<option value="10"<?php if( $selected_limit === 10 ){echo ' selected';} ?>><?php _e( '10 videos', 'kvs' );?></option>
				<option value="50"<?php if( $selected_limit === 50 ){echo ' selected';} ?>><?php _e( '50 videos', 'kvs' );?></option>
				<option value="100"<?php if( $selected_limit === 100 ){echo ' selected';} ?>><?php _e( '100 videos', 'kvs' );?></option>
			</select>
		</td>
        </tr>
        
        <tr valign="top">
        <th scope="row"><?php _e( 'Last updated video ID', 'kvs' ); ?></th>
        <td>
            <input type="text" name="kvs_feed_last_id" value="<?php echo get_option( 'kvs_feed_last_id' ); ?>" />
		</td>
        </tr>
    </table>
    
    
    <h3>Deleting outdated videos</h3>
    <table class="form-table">
        <tr valign="top">
        <th scope="row"><?php _e( 'Feed update period', 'kvs' ); ?></th>
        <td>
			<select name="kvs_delete_period">
                <option value="manual"><?php _e( 'Manual synchronization', 'kvs' );?></option>
				<?php
				$selected = get_option('kvs_delete_period');
				foreach( Kvs_Cron::kvs_cron_schedules( array() ) as $indx=>$val ) {
					echo '<option value="' . $indx . '"';
					echo ($indx === $selected) ? ' selected>' : '>';
					echo $val['display'];
                    echo '</option>';
				} ?>
			</select>
		</td>
        </tr>
        
        <tr valign="top">
        <th scope="row"><?php _e( 'Limit videos per run', 'kvs' ); ?></th>
        <td>
            <?php $selected_limit = (int)get_option( 'kvs_delete_limit' ); ?>
			<select name="kvs_delete_limit">
				<option value="0"><?php _e( 'No limit', 'kvs' );?></option>
				<option value="10"<?php if( $selected_limit === 10 ){echo ' selected';} ?>><?php _e( '10 videos', 'kvs' );?></option>
				<option value="50"<?php if( $selected_limit === 50 ){echo ' selected';} ?>><?php _e( '50 videos', 'kvs' );?></option>
				<option value="100"<?php if( $selected_limit === 100 ){echo ' selected';} ?>><?php _e( '100 videos', 'kvs' );?></option>
			</select>
		</td>
        </tr>
    </table>


    <h3>Full update</h3>
    <table class="form-table">
        <tr valign="top">
        <th scope="row"><?php _e( 'Full update period', 'kvs' ); ?></th>
        <td>
			<select name="kvs_full_period">
                <option value="manual"><?php _e( 'Run manually', 'kvs' );?></option>
				<?php
				$selected = get_option('kvs_full_period');
				foreach( Kvs_Cron::kvs_cron_schedules( array() ) as $indx=>$val ) {
                    if( $val['interval'] < 6*60*60 ) { // skip short schedules, less than 6h
                        continue;
                    } 
					echo '<option value="' . $indx . '"';
					echo ($indx === $selected) ? ' selected>' : '>';
					echo $val['display'];
                    echo '</option>';
				} ?>
			</select>
		</td>
        </tr>
    </table>
    
    
<?php
	endif; // Feed URL check
?>
    
<?php
	if( !empty( $kernel_video_sharing->reader->get_feed_url() ) ) {
		submit_button();
	} else {
		submit_button( __( 'Connect KVS feed', 'kvs' ) );
	}
?>

</form>
</div>