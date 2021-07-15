<?php

/**
 * KVS Plugin settings page view: Import rules section
 *
 * @link       https://www.kernel-video-sharing.com/
 * @since      1.0.0
 *
 * @package    Kvs
 * @subpackage Kvs/admin/partials
 */

if( empty( $kernel_video_sharing->reader->get_feed_url() ) ) {
	exit;
}
?>

<div class="wrap">
<?php
	settings_errors( 'kvs_messages' );
?>
<form method="post" action="options.php" id="kvs-settings-form">
    <?php settings_fields( 'kvs-settings-group-rules' ); ?>
    <?php do_settings_sections( 'kvs-settings-group-rules' ); ?>
<?php
	$feed_meta = get_option( 'kvs_feed_meta' );
?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row"><?php _e( 'Feed locale', 'kvs' ); ?></th>
        <td>
			<select name="kvs_video_locale">
                <option value="all"><?php _e( 'Default', 'kvs' ); ?></option>
				<?php
				$selected = get_option( 'kvs_video_locale' );
				foreach($feed_meta['locales'] as $loc=>$title) {
					echo '<option value="' . $loc . '"';
                    echo ( $loc === $selected ) ? ' selected>' : '>';
					echo $title;
                    echo '</option>';
				} ?>
			</select>
		</td>
        </tr>
        
        <tr valign="top">
        <th scope="row"><?php _e( 'Screenshot size', 'kvs' ); ?></th>
        <td>
			<select name="kvs_video_screenshot">
				<?php
				$selected = get_option( 'kvs_video_screenshot' ) ?: 'source';
				foreach($feed_meta['screenshots'] as $screen) {
					echo '<option value="' . $screen . '"';
					echo ($screen === $selected) ? ' selected>' : '>';
					echo $screen;
                    echo '</option>';
				} ?>
			</select>
		</td>
        </tr>
        
        <tr valign="top">
        <th scope="row"><?php _e( 'Filter videos by', 'kvs' ); ?></th>
        <td>
            <select name="kvs_video_filter_by" id="kvs_video_filter_by" onchange="changeFilterList(this.value);">
                <option value=""><?php _e( 'Do not filter','kvs' ); ?></option>
				<?php
                $options = [
                    'categories'      => __( 'Categories', 'kvs' ),
                    'content_sources' => __( 'Content providers', 'kvs' ),
                ];
				$filter_by = get_option( 'kvs_video_filter_by' );
				foreach($options as $opt=>$title) {
					echo '<option value="' . $opt . '"';
					echo ( $opt === $filter_by ) ? ' selected' : '';
                    echo ( empty( $feed_meta[$opt] ) ) ? ' disabled' : '';
					echo '>';
                    echo $title;
                    echo '</option>';
				} ?>
			</select>
		</td>
        </tr>
        
        <tr valign="top" class="kvs-filter-list" id="kvs-filter-categories"<?php if( $filter_by !== 'categories' ) {echo ' style="display:none;"';} ?>>
        <th scope="row"><?php _e( 'Select categories to import', 'kvs' ); ?></th>
        <td><div class="kvs-filters-grid">
            <?php
            $selected = get_option( 'kvs_video_filter_category' ) ?: [];
            if( !empty($selected) && !is_array($selected) ) {
                $selected = [ $selected ];
            }
            if( !empty( $feed_meta['categories'] ) ) 
            foreach( $feed_meta['categories'] as $item ) {
                echo '<label>';
                echo '<input type="checkbox" name="kvs_video_filter_category[]" ';
                echo 'value="' . $item . '"';
                echo ( in_array( $item, $selected ) ) ? ' checked="checked"' : '';
                echo ' /> ';
                echo $item;
                echo '</label>';
            } ?>
		</div></td>
        </tr>

        <tr valign="top" class="kvs-filter-list" id="kvs-filter-content_sources"<?php if( $filter_by !== 'content_sources' ) {echo ' style="display:none;"';} ?>>
        <th scope="row"><?php _e( 'Select sources to import', 'kvs' ); ?></th>
        <td><div class="kvs-filters-grid">
            <?php
            $selected = get_option( 'kvs_video_filter_source' ) ?: [];
            if( !empty($selected) && !is_array($selected) ) {
                $selected = [ $selected ];
            }
            if( !empty( $feed_meta['content_sources'] ) ) 
            foreach( $feed_meta['content_sources'] as $item ) {
                echo '<label>';
                echo '<input type="checkbox" name="kvs_video_filter_source[]" ';
                echo 'value="' . $item . '"';
                echo ( in_array( $item, $selected ) ) ? ' checked="checked"' : '';
                echo ' /> ';
                echo $item;
                echo '</label>';
            } ?>
		</div></td>
        </tr>
    </table>
    <?php submit_button( __( 'Save rules', 'kvs' ), 'primary', 'submit', false ); ?>
    <a class="button button-secondary renew-meta" 
       onclick="jQuery('#kvs-meta-update-form').submit();" 
       title="<?php _e( 'Renew KVS feed meta data and import lates rules available', 'kvs' );?>">
        <i class="fas fa-fw fa-sync"></i> 
        <?php _e( 'Import latest rules from the feed', 'kvs' ); ?>
    </a>
</form>

<form method="post" id="kvs-meta-update-form" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
    <input type="hidden" name="action" value="kvs_update_meta">
    <?php wp_nonce_field( 'kvs_import_full', 'kvs-nonce' ); ?>
</form>
    
<script>
    function changeFilterList( selected) {
        jQuery( '.kvs-filter-list' ).css( 'display', 'none' );
        if( selected ) {
            jQuery( '#kvs-filter-' + selected ).css( 'display', 'table-row' );
        }
    }
</script>

</div>