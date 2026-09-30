<?php

namespace wealcoder\bricksfly\Includes;

defined('ABSPATH') || exit;

/**
 * BricksFly needs the Bricks theme.
 *
 * When Bricks (or a Bricks child theme) is not the active theme, this shows
 * an admin notice linking to activate or get Bricks. It never stops the user
 * from switching themes.
 */
class BRICKSFLY_Bricks_Theme_Dependency
{
	const THEME_SLUG    = 'bricks';
	const PLUGIN_NAME   = 'BricksFly';
	const BRICKS_URL    = 'https://bricksbuilder.io/';

	public static function init(): void
	{
		add_action('admin_notices', [__CLASS__, 'render_notices']);
	}

	public static function is_bricks_active(): bool
	{
		return get_option('template') === self::THEME_SLUG;
	}

	public static function is_bricks_installed(): bool
	{
		return wp_get_theme(self::THEME_SLUG)->exists();
	}

	public static function render_notices(): void
	{
		if (self::is_bricks_active() || ! current_user_can('switch_themes')) {
			return;
		}

		self::render_missing_notice();
	}

	private static function render_missing_notice(): void
	{
		if (self::is_bricks_installed()) {
			$action_url = wp_nonce_url(
				admin_url('themes.php?action=activate&stylesheet=' . self::THEME_SLUG),
				'switch-theme_' . self::THEME_SLUG
			);
			$action_label = __('Activate Bricks', 'bricksfly-elements-for-bricks');
			$message      = sprintf(
				/* translators: %s: Plugin name */
				esc_html__('%s requires the Bricks theme to be active. Please activate it to use this plugin.', 'bricksfly-elements-for-bricks'),
				'<strong>' . esc_html(self::PLUGIN_NAME) . '</strong>'
			);
			$is_external = false;
		} else {
			$action_url   = self::BRICKS_URL;
			$action_label = __('Get Bricks', 'bricksfly-elements-for-bricks');
			$message      = sprintf(
				/* translators: %s: Plugin name */
				esc_html__('%s requires the Bricks theme. Please install and activate Bricks to use this plugin.', 'bricksfly-elements-for-bricks'),
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

BRICKSFLY_Bricks_Theme_Dependency::init();
