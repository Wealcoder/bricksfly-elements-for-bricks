<?php

/**
 * Compatibility with older BricksFly Pro versions.
 *
 * BricksFly Pro is a separate plugin. Its builds released before this version
 * of the free plugin read two identifier constants that the free plugin used
 * to define; without them those Pro builds stop with a fatal error. Current Pro
 * versions define everything they need themselves.
 *
 * When such an older Pro build is active, this file only:
 *  - defines those two identifiers, so the site keeps working, and
 *  - asks the administrator to update BricksFly Pro.
 *
 * Nothing here checks, stores or changes any license, and nothing runs when
 * Pro is absent or current. It can be removed once older Pro builds are gone.
 */

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Whether an older BricksFly Pro build is active.
 *
 * Current Pro builds load their own helpers (`bricksfly_is_license_valid()`)
 * before this runs; older builds relied on the free plugin for them.
 *
 * @return bool
 */
function bricksfly_is_outdated_pro()
{
	return defined('THEBRBRE_PRO_VERSION') && ! function_exists('bricksfly_is_license_valid');
}

add_action(
	'plugins_loaded',
	static function () {
		if (! bricksfly_is_outdated_pro()) {
			return;
		}

		// Identifiers older Pro builds read on `init`.
		if (! defined('BRICKSFLY_PRO_ITEM_ID')) {
			define('BRICKSFLY_PRO_ITEM_ID', 39996);
		}
		if (! defined('BRICKSFLY_PRO_ITEM_NAME')) {
			define('BRICKSFLY_PRO_ITEM_NAME', 'TheBricksFly');
		}

		add_action('admin_notices', 'bricksfly_outdated_pro_notice');
	},
	1
);

/**
 * Ask the administrator to update BricksFly Pro.
 *
 * @return void
 */
function bricksfly_outdated_pro_notice()
{
	if (! current_user_can('update_plugins')) {
		return;
	}

	$version = defined('THEBRBRE_PRO_VERSION') ? (string) THEBRBRE_PRO_VERSION : '';
?>
	<div class="notice notice-warning">
		<p>
			<strong><?php esc_html_e('Please update BricksFly Pro.', 'bricksfly-elements-for-bricks'); ?></strong>
			<?php
			printf(
				/* translators: %s: installed BricksFly Pro version. */
				esc_html__('BricksFly Pro %s was made for an older version of BricksFly. Update it to the latest version so all its features keep working.', 'bricksfly-elements-for-bricks'),
				esc_html($version)
			);
			?>
			<a href="<?php echo esc_url(self_admin_url('plugins.php?plugin_status=upgrade')); ?>"><?php esc_html_e('Go to Plugins', 'bricksfly-elements-for-bricks'); ?></a>
		</p>
	</div>
<?php
}
