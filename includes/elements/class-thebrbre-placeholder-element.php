<?php

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Branded stand-in for a Bricksfly widget whose real PHP class isn't
 * currently loaded (toggled off in Bricksfly settings, or Pro/license
 * unavailable).
 *
 * Registered under the widget's real Bricks `$name` (see
 * thebrbre_register_widget_placeholder()) so `class_exists()` succeeds in
 * Bricks core's `Frontend::render_element()` and its default "PHP class
 * does not exist" fallback never fires for it. Carries no real widget
 * behavior — `render()` only ever prints a short admin-facing notice, and
 * only inside the Bricks builder itself, never on the live front-end.
 */
if (! class_exists('THEBRBRE_Placeholder_Element')) {
	class THEBRBRE_Placeholder_Element extends \Bricks\Element
	{
		public $category = 'bricks fly';
		public $icon     = 'ti-info-alt';

		public $placeholder_label = '';

		/**
		 * Per-subclass identity (real Bricks `$name` + human label), keyed by
		 * the generated subclass name. Populated once by
		 * thebrbre_register_widget_placeholder() instead of baking the values
		 * into eval()'d class source — Bricks always instantiates elements
		 * with `new $class_name()` (no constructor args it lets us supply),
		 * so this static map is how each subclass learns its own identity.
		 *
		 * @var array<string, array{name: string, label: string}>
		 */
		protected static $registry = array();

		public static function register($class_name, $bricks_name, $label)
		{
			self::$registry[$class_name] = array(
				'name'  => $bricks_name,
				'label' => (string) $label,
			);
		}

		public function __construct($element = null)
		{
			$data = self::$registry[static::class] ?? null;
			if ($data) {
				$this->name              = $data['name'];
				$this->placeholder_label = $data['label'];
			}

			parent::__construct($element);
		}

		public function get_label()
		{
			return $this->placeholder_label !== ''
				? $this->placeholder_label
				: esc_html__('Bricksfly widget', 'bricksfly-elements-for-bricks');
		}

		public function get_keywords()
		{
			return array('bricksfly', 'inactive');
		}

		public function set_control_groups()
		{
			// No controls: this element is never actually configured.
		}

		public function set_controls()
		{
			// No controls: this element is never actually configured.
		}

		public function render()
		{
			// Never shown on the live front-end — only inside the builder,
			// and only to users who could access the builder anyway.
			if (! function_exists('bricks_is_builder') || ! bricks_is_builder()) {
				return;
			}

			if (! class_exists('\Bricks\Capabilities') || ! \Bricks\Capabilities::current_user_can_use_builder()) {
				return;
			}

			$settings_url = admin_url('admin.php?page=thebrbre_addons_settings&tab=elements');

			$message = sprintf(
				/* translators: %s: widget label */
				esc_html__('%s is not active on this site.', 'bricksfly-elements-for-bricks'),
				esc_html($this->get_label())
			);

			// Inline styles, not a class: this can render inside any element's
			// own typography context (headings, buttons, …), so it can't rely
			// on inheriting a sane font-size from its surroundings. Colors
			// are the Bricksfly brand gold (#FFD53E, sampled from the
			// BricksFly wordmark logo — same value already used for the
			// "Pro" badge elsewhere in the dashboard UI), on a light tint
			// background with a darker gold for readable text/link contrast.
			$style = 'display:inline-block;font-size:12px;line-height:1.5;'
				. 'font-family:-apple-system,BlinkMacSystemFont,Segoe UI,sans-serif;'
				. 'font-weight:400;color:#7A5C00;background:#FFF6DC;'
				. 'border:1px solid #FFD53E;border-radius:4px;padding:6px 10px;'
				. 'white-space:normal;';

			$link = sprintf(
				'<a href="%s" target="_blank" rel="noopener" style="color:#7A5C00;text-decoration:underline;">%s</a>',
				esc_url($settings_url),
				esc_html__('Bricksfly → Elements', 'bricksfly-elements-for-bricks')
			);

			$instruction = sprintf(
				/* translators: %s: link to the Bricksfly Elements settings tab */
				esc_html__('Activate it from %s.', 'bricksfly-elements-for-bricks'),
				$link
			);

			echo '<div class="bricks-element-placeholder thebrbre-placeholder" style="' . esc_attr($style) . '">' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				. esc_html($message) . ' ' . wp_kses_post($instruction)
				. '</div>';
		}
	}
}
