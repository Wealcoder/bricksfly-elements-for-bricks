<?php
/**
 * Seasonal upgrade banner. Included by Notices::admin_notices() with the
 * notice in $notice; its campaign entry is in $notice['campaign'].
 *
 * @package Bricks_Fly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bricksfly_campaign = $notice['campaign'];
?>
<div class="aab-promo">
	<a class="aab-promo__link" href="<?php echo esc_url( $bricksfly_campaign['url'] ); ?>" target="_blank" rel="noopener">
		<img src="<?php echo esc_url( BRICKSFLY_URL . 'assets/images/notices/' . $bricksfly_campaign['image'] ); ?>" width="1100" height="150" alt="<?php echo esc_attr( $bricksfly_campaign['alt'] ); ?>">
	</a>
	<button type="button" class="aab-promo__close aab-snooze-btn" data-snooze="true" data-snooze-time="<?php echo esc_attr( 3 * DAY_IN_SECONDS ); ?>">
		<span class="screen-reader-text"><?php esc_html_e( 'Close', 'bricksfly-elements-for-bricks' ); ?></span>
	</button>
</div>
<p class="aab-promo__actions">
	<button type="button" class="button-link notice-dismiss-btn">
		<?php esc_html_e( 'Don\'t show again', 'bricksfly-elements-for-bricks' ); ?>
	</button>
</p>
