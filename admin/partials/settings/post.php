<?php

/**
 * KVS Plugin settings page view: Posts creation setting section
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
    
    $post_types = get_post_types( array(
        'public' => true,
        'capability_type' => 'post'
        ), 'objects');
    foreach( $post_types as $slug=>$obj ) {
        if( substr($slug, 0, 4) === 'kvs_' ) {
            unset( $post_types[$slug] );
        }
    }
?>
<form method="post" action="options.php" id="kvs-settings-form">
    <?php settings_fields( 'kvs-settings-group-post' ); ?>
    <?php do_settings_sections( 'kvs-settings-group-post' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row"><?php _e( 'Create posts of type', 'kvs' ); ?></th>
        <td>
            <select name="kvs_post_type">
                <option value=""><?php _e( 'Default KVS post type', 'kvs' ); ?></option>
				<?php
				$selected = get_option( 'kvs_post_type' );
				foreach( $post_types as $slug=>$obj ) {
					echo '<option value="' . $slug . '"';
					echo ( $slug === $selected ) ? ' selected>' : '>';
					echo $obj->label;
                    echo '</option>';
				} ?>
			</select>
            <label>
				<?php
				$checked = get_option( 'kvs_post_import_featured_image' );
                ?>
                <input type="checkbox" name="kvs_post_import_featured_image" value="import"<?php echo $checked ? ' checked="checked"' : ''; ?>>
                <?php _e('Import screenshots as featured images', 'kvs'); ?>
            </label>
		</td>
        </tr>
        
        <tr valign="top">
        <th scope="row"><?php _e( 'Post body template', 'kvs' ); ?></th>
        <td>
            <textarea name="kvs_post_body_template" id="kvs_post_body_template" class="width-wide" rows="10"><?php echo get_option( 'kvs_post_body_template' ); ?></textarea>
            <div class="hint">
                <h3>Template elements available:</h3>
                <ul>
                    <li><code>{%id%}</code> - KVS video ID</li>
                    <li><code>{%title%}</code> - video title</li>
                    <li><code>{%description%}</code> - full video description</li>
                    <li><code>{%date%}</code> - video publication date</li>
                    <li><code>{%popularity%}</code> - video views amount</li>
                    <li><code>{%rating%}</code> - video rating</li>
                    <li><code>{%votes%}</code> - votes amount</li>
                    <li><code>{%duration%}</code> - video duration <i>h:m:s</i></li>
                    <li><code>{%link%}</code> - video URL</li>
                </ul>
            </div>
            <div class="hint">
                <h3>Post template sample:</h3>
                <code class="sample">{%description%}<br/>
Rating: {%rating%}<br/>
[kvs_player id={%id%}]</code>
            </div>
		</td>
        </tr>
        
    </table>
    <?php submit_button( __( 'Save posts settings', 'kvs' ) ); ?>
</form>
</div>
<script>
    jQuery(document).ready(function() {
        jQuery('code').on('click', function() {
            var templatetext = jQuery("#kvs_post_body_template");
            var position = templatetext.getCursorPosition();
            templatetext.val(
                templatetext.val().substr(0, position) + 
                this.innerText + 
                templatetext.val().substr(position)
            );
        });
    });
    (function ($, undefined) {
        $.fn.getCursorPosition = function () {
            var el = $(this).get(0);
            var pos = 0;
            if ('selectionStart' in el) {
                pos = el.selectionStart;
            } else if ('selection' in document) {
                el.focus();
                var Sel = document.selection.createRange();
                var SelLength = document.selection.createRange().text.length;
                Sel.moveStart('character', -el.value.length);
                pos = Sel.text.length - SelLength;
            }
            return pos;
        }
    })(jQuery);
</script>