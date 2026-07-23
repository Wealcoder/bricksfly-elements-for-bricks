<?php

namespace AABAddons\Includes;

defined('ABSPATH') || exit;

/**
 * Enforces the Bricks theme as a hard dependency for this plugin.
 *
 * 1. Shows an admin notice when Bricks is not the active theme, linking to
 *    install or activate it.
 * 2. Blocks the user from switching away from Bricks while this plugin is
 *    active (child themes of Bricks are allowed).
 */
class Bricks_Theme_Dependency
{
	const THEME_SLUG    = 'bricks';
	const BLOCK_FLAG    = 'aab_addons_theme_switch_blocked';
	const PLUGIN_NAME   = 'The BricksFly';
	const BRICKS_URL    = 'https://bricksbuilder.io/';

	public static function init(): void
	{
		add_action('admin_notices', [__CLASS__, 'render_notices']);
		add_filter('pre_update_option_template',   [__CLASS__, 'block_theme_switch'], 10, 2);
		add_filter('pre_update_option_stylesheet', [__CLASS__, 'block_theme_switch'], 10, 2);
	}

	public static function is_bricks_active(): bool
	{
		return get_option('template') === self::THEME_SLUG;
	}

	public static function is_bricks_installed(): bool
	{
		return wp_get_theme(self::THEME_SLUG)->exists();
	}

	/**
	 * Cancel any option update that would move away from Bricks (either as
	 * template or stylesheet) while this plugin is loaded.
	 */
	public static function block_theme_switch($new_value, $old_value)
	{
		if (get_option('template') !== self::THEME_SLUG) {
			return $new_value;
		}

		if (current_filter() === 'pre_update_option_template') {
			if ($new_value === self::THEME_SLUG) {
				return $new_value;
			}
		} else {
			$new_theme = wp_get_theme($new_value);
			if ($new_theme->exists() && $new_theme->get_template() === self::THEME_SLUG) {
				return $new_value;
			}
		}

		set_transient(self::BLOCK_FLAG, 1, 30);
		return $old_value;
	}

	public static function render_notices(): void
	{
		if (get_transient(self::BLOCK_FLAG)) {
			delete_transient(self::BLOCK_FLAG);
			self::render_block_notice();
		}

		if (self::is_bricks_active()) {
			return;
		}

		self::render_missing_notice();
	}

	private static function render_block_notice(): void
	{
		$message = sprintf(
			/* translators: %s: Plugin name */
			esc_html__('Theme switch was cancelled. %s requires the Bricks theme to remain active. Please deactivate the plugin before switching to another theme.', 'the-bricksfly'),
			'<strong>' . esc_html(self::PLUGIN_NAME) . '</strong>'
		);
?>
		<div class="notice notice-warning is-dismissible">
			<p><?php echo wp_kses_post($message); ?></p>
		</div>
<?php
	}

	private static function render_missing_notice(): void
	{
		if (self::is_bricks_installed()) {
			$action_url = wp_nonce_url(
				admin_url('themes.php?action=activate&stylesheet=' . self::THEME_SLUG),
				'switch-theme_' . self::THEME_SLUG
			);
			$action_label = __('Activate Bricks', 'the-bricksfly');
			$message      = sprintf(
				/* translators: %s: Plugin name */
				esc_html__('%s requires the Bricks theme to be active. Please activate it to use this plugin.', 'the-bricksfly'),
				'<strong>' . esc_html(self::PLUGIN_NAME) . '</strong>'
			);
			$is_external = false;
		} else {
			$action_url   = self::BRICKS_URL;
			$action_label = __('Get Bricks', 'the-bricksfly');
			$message      = sprintf(
				/* translators: %s: Plugin name */
				esc_html__('%s requires the Bricks theme. Please install and activate Bricks to use this plugin.', 'the-bricksfly'),
				'<strong>' . esc_html(self::PLUGIN_NAME) . '</strong>'
			);
			$is_external = true;
		}
?>
		<div class="notice notice-error">
			<p><?php echo wp_kses_post($message); ?></p>
			<p>
				<a href="<?php echo esc_url($action_url); ?>" class="button button-primary"<?php if ($is_external) { echo ' target="_blank" rel="noopener"'; } ?>>
					<?php echo esc_html($action_label); ?>
				</a>
			</p>
		</div>
<?php
	}
}

Bricks_Theme_Dependency::init();
