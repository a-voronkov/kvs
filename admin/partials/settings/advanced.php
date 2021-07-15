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
    <?php settings_fields( 'kvs-settings-group-advanced' ); ?>
    <?php do_settings_sections( 'kvs-settings-group-advanced' ); ?>

    <table class="form-table">
        <tr valign="top">
        <th scope="row"><?php _e( 'Write log file', 'kvs' ); ?></th>
        <td>
            <?php 
			$selected = get_option('kvs_log_level') ?: Kvs_Logger::get_log_level();
			$disabled = ( Kvs_Logger::get_log_level() !== $selected ) ? ' disabled="disabled"' : '';
            ?>
            <select name="kvs_log_level"<?php echo $disabled; ?>>
				<?php
				foreach(Kvs_Logger::LOG_LEVELS as $indx=>$val) {
					echo '<option value="' . $indx . '"';
					echo ( $indx === $selected ) ? ' selected>' : '>';
					echo $indx;
                    echo '</option>';
				} ?>
			</select>
		</td>
        </tr>
        
        <tr valign="top">
        <th scope="row"><?php _e( 'Current log file', 'kvs' ); ?></th>
        <td>
            <input type="text" value="<?php echo KVS_LOGFILE;?>" class="width-full margin-bottom-10" readonly="true" disabled="true" />
            <textarea id="kvs-log" class="width-full" style="white-space: pre; overflow: auto;" readonly="true"><?php echo Kvs_Logger::get_log_content();?></textarea>
            <a class="button secondary refresh-log" onclick="document.location=document.location;">
                <i class="fa fa-fw fa-refresh"></i> <?php _e( 'Refresh', 'kvs' );?>
            </a>
            <a class="button secondary clear-log" onclick="if(confirm('<?php _e( 'Are you sure you want to delete current log file?', 'kvs' );?>')){jQuery('#kvs-clear-log').submit();}">
                <?php _e( 'Clear log file', 'kvs' );?>
            </a>
		</td>
        </tr>
    </table>
    <?php submit_button(); ?>
</form>

<form method="post" id="kvs-import-form" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
    <input type="hidden" name="action" value="kvs_import_full">
    <h3>Full import</h3>
    <p>You can manually import all videos from the feed.</p>
    <p>Be careful, import starts immediately, and it can take a few minutes to process all the videos!</p>
<?php
    wp_nonce_field( 'kvs_import_full', 'kvs-nonce' );
    submit_button( __( 'Run full import now', 'kvs' ), 'secondary' );
?>
</form>

<form method="post" id="kvs-clear-log" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
    <input type="hidden" name="action" value="kvs_clear_log">
    <?php wp_nonce_field( 'kvs_import_full', 'kvs-nonce' );?>
</form>

</div>
