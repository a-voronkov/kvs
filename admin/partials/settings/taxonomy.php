<?php

/**
 * KVS Plugin settings page view: Taxonomies setting section
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
    
    $taxonomies = get_taxonomies( array( 'public'=>true ), 'objects');
    foreach( $taxonomies as $slug=>$obj ) {
        if( substr($slug, 0, 4) === 'kvs_' ) {
            unset( $taxonomies[$slug] );
        }
    }
?>
<form method="post" action="options.php" id="kvs-settings-form">
    <?php settings_fields( 'kvs-settings-group-taxonomy' ); ?>
    <?php do_settings_sections( 'kvs-settings-group-taxonomy' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row"><?php _e( 'Categories taxonomy', 'kvs' ); ?></th>
        <td>
            <select name="kvs_taxonomy_category" class="kvs_taxonomies_select">
                <option value="">&#9940; <?php _e( 'Do not import categories', 'kvs' ); ?></option>
				<?php
				$selected = get_option( 'kvs_taxonomy_category' );
				foreach( $taxonomies as $slug=>$obj ) {
					echo '<option value="' . $slug . '"';
					echo ( $slug === $selected ) ? ' selected>' : '>';
					echo $obj->label;
                    echo '</option>';
				} ?>
                <option value="kvs_category" class="kvs"<?php if($selected==='kvs_category') {echo ' selected';}?>>
                    &#11088; <?php _e( 'Create KVS categories taxonomy', 'kvs' ); ?>
                </option>
			</select>
		</td>
        </tr>
        
        <tr valign="top">
        <th scope="row"><?php _e( 'Tags taxonomy', 'kvs' ); ?></th>
        <td>
            <select name="kvs_taxonomy_tag" class="kvs_taxonomies_select">
                <option value="">&#9940; <?php _e( 'Do not import tags', 'kvs' ); ?></option>
				<?php
				$selected = get_option( 'kvs_taxonomy_tag' );
				foreach( $taxonomies as $slug=>$obj ) {
					echo '<option value="' . $slug . '"';
					echo ( $slug === $selected ) ? ' selected>' : '>';
					echo $obj->label;
                    echo '</option>';
				} ?>
                <option value="kvs_tag" class="kvs"<?php if($selected==='kvs_tag') {echo ' selected';}?>>
                    &#11088; <?php _e( 'Create KVS tags taxonomy', 'kvs' ); ?>
                </option>
			</select>
		</td>
        </tr>
        
        <tr valign="top">
        <th scope="row"><?php _e( 'Models taxonomy', 'kvs' ); ?></th>
        <td>
            <select name="kvs_taxonomy_model" class="kvs_taxonomies_select">
                <option value="">&#9940; <?php _e( 'Do not import models', 'kvs' ); ?></option>
				<?php
				$selected = get_option( 'kvs_taxonomy_model' );
				foreach( $taxonomies as $slug=>$obj ) {
					echo '<option value="' . $slug . '"';
					echo ( $slug === $selected ) ? ' selected>' : '>';
					echo $obj->label;
                    echo '</option>';
				} ?>
                <option value="kvs_model" class="kvs"<?php if($selected==='kvs_model') {echo ' selected';}?>>
                    &#11088; <?php _e( 'Create KVS models taxonomy', 'kvs' ); ?>
                </option>
			</select>
		</td>
        </tr>
        
        <tr valign="top">
        <th scope="row"><?php _e( 'Sources taxonomy', 'kvs' ); ?></th>
        <td>
            <select name="kvs_taxonomy_source" class="kvs_taxonomies_select">
                <option value="">&#9940; <?php _e( 'Do not import sources', 'kvs' ); ?></option>
				<?php
				$selected = get_option( 'kvs_taxonomy_source' );
				foreach( $taxonomies as $slug=>$obj ) {
					echo '<option value="' . $slug . '"';
					echo ( $slug === $selected ) ? ' selected>' : '>';
					echo $obj->label;
                    echo '</option>';
				} ?>
                <option value="kvs_source" class="kvs"<?php if($selected==='kvs_source') {echo ' selected';}?>>
                    &#11088; <?php _e( 'Create KVS sources taxonomy', 'kvs' ); ?>
                </option>
			</select>
		</td>
        </tr>
        
    </table>
    <?php submit_button( __( 'Save taxonomies settings', 'kvs' ) ); ?>
</form>
</div>