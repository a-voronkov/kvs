<?php

/**
 * KVS Plugin settings page view: Header & Menu
 *
 * @link       https://www.kernel-video-sharing.com/
 * @since      1.0.0
 *
 * @package    Kvs
 * @subpackage Kvs/admin/partials
 */

$base = admin_url( 'edit.php?post_type=kvs_video&page=kvs-help' );
$sections = array(
    'basic' => __( 'Basic help', 'kvs' ),
);
?>

<div class="kvs-setting-header">
    <h1><img src="https://www.kernel-video-sharing.com/images/logo.svg" height="60" alt="<?php _e( 'Kernel Video Sharing plugin help', 'kvs' ); ?>" /></h1>
	<nav class="kvs-setting-tabs-wrapper hide-if-no-js" aria-label="Secondary menu">
    <?php foreach( $sections as $sec=>$title ): ?>
        <a href="<?php echo $base . '&section=' . $sec; ?>" class="kvs-setting-tab<?php if( $sec === $section ) {echo ' active';} ?>" aria-current="true"><?php echo $title; ?></a>
    <?php endforeach; ?>
	</nav>
</div>
