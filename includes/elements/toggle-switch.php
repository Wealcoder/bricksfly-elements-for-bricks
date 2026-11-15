<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BRICKSFLY_Bricks_Toggle_Switch extends \Bricks\Element {

	public $category     = 'bricks fly';
	public $name         = 'aae-toggle-switch';
	public $icon         = 'ti-control-shuffle aab-element-marker';
	public $css_selector = '.aae-toggle-switch-wrapper';
	public $scripts      = array( 'aaeToggleSwitch' );

	public function get_label() {
		return esc_html__( 'Toggle Switch', 'bricksfly-elements-for-bricks' );
	}

	public function get_keywords() {
		return array( 'toggle', 'switch', 'switcher', 'tab', 'pricing', 'monthly', 'yearly' );
	}

	public function set_control_groups() {
		$this->control_groups['toggle'] = array(
			'title' => esc_html__( 'Toggle Switch', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'content',
		);

		$this->control_groups['wrapper_style'] = array(
			'title' => esc_html__( 'Switcher Wrapper', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);

		$this->control_groups['switcher_style'] = array(
			'title'    => esc_html__( 'Switcher', 'bricksfly-elements-for-bricks' ),
			'tab'      => 'style',
			'required' => array( 'elementList', '=', '1' ),
		);

		$this->control_groups['title_style'] = array(
			'title' => esc_html__( 'Title', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);

		$this->control_groups['active_title_style'] = array(
			'title' => esc_html__( 'Active Title', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);
	}

	public function set_controls() {
		// === CONTENT: Toggle Switch ===

		$this->controls['elementList'] = array(
			'group'   => 'toggle',
			'label'   => esc_html__( 'Style', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'options' => array(
				'1' => esc_html__( 'One', 'bricksfly-elements-for-bricks' ),
				'2' => esc_html__( 'Two', 'bricksfly-elements-for-bricks' ),
			),
			'default' => '1',
			'inline'  => true,
		);

		$this->controls['toggleSwitcher'] = array(
			'group'         => 'toggle',
			'label'         => esc_html__( 'Toggle Switcher', 'bricksfly-elements-for-bricks' ),
			'type'          => 'repeater',
			'titleProperty' => 'switchTitle',
			'description'   => esc_html__( 'Only the first 2 items are used (before / after).', 'bricksfly-elements-for-bricks' ),
			'fields'        => array(
				'switchTitle'    => array(
					'label'       => esc_html__( 'Title', 'bricksfly-elements-for-bricks' ),
					'type'        => 'text',
					'placeholder' => esc_html__( 'Monthly', 'bricksfly-elements-for-bricks' ),
				),
				'contentType'    => array(
					'label'   => esc_html__( 'Content Type', 'bricksfly-elements-for-bricks' ),
					'type'    => 'select',
					'options' => array(
						'content'  => esc_html__( 'Content', 'bricksfly-elements-for-bricks' ),
						'template' => esc_html__( 'Saved Templates', 'bricksfly-elements-for-bricks' ),
					),
					'default' => 'content',
					'inline'  => true,
				),
				'switchTemplate' => array(
					'label'       => esc_html__( 'Saved Template', 'bricksfly-elements-for-bricks' ),
					'type'        => 'select',
					'options'     => bricks_is_builder() ? \Bricks\Templates::get_templates_list( array( 'section', 'content', 'popup' ), get_the_ID() ) : array(),
					'searchable'  => true,
					'placeholder' => esc_html__( 'Select template', 'bricksfly-elements-for-bricks' ),
					'required'    => array( 'contentType', '=', 'template' ),
				),
				'switchContent'  => array(
					'label'    => esc_html__( 'Content', 'bricksfly-elements-for-bricks' ),
					'type'     => 'editor',
					'default'  => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'bricksfly-elements-for-bricks' ),
					'required' => array( 'contentType', '=', 'content' ),
				),
			),
			'default'       => array(
				array(
					'switchTitle'   => 'Monthly',
					'contentType'   => 'content',
					'switchContent' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.',
				),
				array(
					'switchTitle'   => 'Yearly',
					'contentType'   => 'content',
					'switchContent' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.',
				),
			),
		);

		$this->controls['toggleGap'] = array(
			'group'   => 'toggle',
			'label'   => esc_html__( 'Gap', 'bricksfly-elements-for-bricks' ),
			'type'    => 'slider',
			'units'   => array(
				'px' => array(
					'min' => 0,
					'max' => 200,
				),
				// '%'  => ['min' => 0, 'max' => 100],
			),
			'default' => 10,
			// 'default' => [
			// 'value' => 10,
			// 'unit'  => 'px',
			// ],
			// 'css' => [
			// [
			// 'property' => 'gap',
			// 'selector' => '.slide-toggle-wrapper',
			// 'value'    => '%value%%unit%', // ← append unit placeholder
			// ],
			// ],
		);

		$this->controls['toggleBottomSpace'] = array(
			'group'   => 'toggle',
			'label'   => esc_html__( 'Bottom Space', 'bricksfly-elements-for-bricks' ),
			'type'    => 'slider',
			'units'   => array(
				'px' => array(
					'min' => 0,
					'max' => 200,
				),
				// '%' => ['min' => 0, 'max' => 100]
			),
			'default' => 20,
			// 'default' => [
			// 'value' => 20,
			// 'unit'  => 'px',
			// ],
			// 'css'   => [
			// ['property' => 'margin-bottom', 'selector' => '.slide-toggle-wrapper'],
			// ],
		);

		// === STYLE: Switcher Wrapper ===

		$this->controls['wrapBackground'] = array(
			'group' => 'wrapper_style',
			'label' => esc_html__( 'Background', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'background-color',
					'selector' => '.slide-toggle-wrapper',
				),
			),
		);

		$this->controls['wrapPadding'] = array(
			'group' => 'wrapper_style',
			'label' => esc_html__( 'Padding', 'bricksfly-elements-for-bricks' ),
			'type'  => 'dimensions',
			'css'   => array(
				array(
					'property' => 'padding',
					'selector' => '.slide-toggle-wrapper',
				),
			),
		);

		$this->controls['wrapBorder'] = array(
			'group' => 'wrapper_style',
			'label' => esc_html__( 'Border', 'bricksfly-elements-for-bricks' ),
			'type'  => 'border',
			'css'   => array(
				array(
					'property' => 'border',
					'selector' => '.slide-toggle-wrapper',
				),
			),
		);

		// === STYLE: Switcher (style 1 only) ===

		$this->controls['switcherWidth'] = array(
			'group'       => 'switcher_style',
			'label'       => esc_html__( 'Width', 'bricksfly-elements-for-bricks' ),
			'type'        => 'number',
			'units'       => array(
				'px' => array(
					'min' => 0,
					'max' => 200,
				),
			),
			'default'     => '40',
			'placeholder' => '40',
			'css'         => array(
				array(
					'property' => '--switcher-width',
					'selector' => '.slide-toggle-wrapper',
				),
				array(
					'property' => 'width',
					'selector' => '.switcher',
				),
			),
		);

		$this->controls['switcherHeight'] = array(
			'group'       => 'switcher_style',
			'label'       => esc_html__( 'Height', 'bricksfly-elements-for-bricks' ),
			'type'        => 'number',
			'units'       => array(
				'px' => array(
					'min' => 0,
					'max' => 200,
				),
			),
			'default'     => '20',
			'placeholder' => '20',
			'css'         => array(
				array(
					'property' => 'height',
					'selector' => '.switcher',
				),
			),
		);

		$this->controls['switcherBackground'] = array(
			'group' => 'switcher_style',
			'label' => esc_html__( 'Background', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'background-color',
					'selector' => '.switcher',
				),
				array(
					'property' => 'border-color',
					'selector' => '.switcher',
				),
			),
		);

		$this->controls['switcherRadius'] = array(
			'group' => 'switcher_style',
			'label' => esc_html__( 'Border', 'bricksfly-elements-for-bricks' ),
			'type'  => 'border',
			'css'   => array(
				array(
					'property' => 'border',
					'selector' => '.switcher',
				),
			),
		);

		$this->controls['switcherActiveBackground'] = array(
			'group' => 'switcher_style',
			'label' => esc_html__( 'Active Background', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'background-color',
					'selector' => 'input:checked + .switcher',
				),
				array(
					'property' => 'border-color',
					'selector' => 'input:checked + .switcher',
				),
			),
		);

		$this->controls['indicatorHeading'] = array(
			'group' => 'switcher_style',
			'label' => esc_html__( 'Indicator', 'bricksfly-elements-for-bricks' ),
			'type'  => 'separator',
		);

		$this->controls['indicatorWidth'] = array(
			'group'       => 'switcher_style',
			'label'       => esc_html__( 'Indicator Size', 'bricksfly-elements-for-bricks' ),
			'type'        => 'number',
			'units'       => array(
				'px' => array(
					'min' => 0,
					'max' => 200,
				),
			),
			'default'     => '16',
			'placeholder' => '16',
			'css'         => array(
				array(
					'property' => '--switcher-indicator-width',
					'selector' => '.slide-toggle-wrapper',
				),
			),
		);

		$this->controls['indicatorSpace'] = array(
			'group'       => 'switcher_style',
			'label'       => esc_html__( 'Indicator Space', 'bricksfly-elements-for-bricks' ),
			'type'        => 'number',
			'default'     => '2',
			'placeholder' => '2',
			'units'       => array(
				'px' => array(
					'min' => 0,
					'max' => 200,
				),
			),
			'css'         => array(
				array(
					'property' => '--switcher-border-width',
					'selector' => '.slide-toggle-wrapper',
				),
			),
		);

		$this->controls['indicatorBackground'] = array(
			'group' => 'switcher_style',
			'label' => esc_html__( 'Background', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'background-color',
					'selector' => '.switcher::before',
				),
				array(
					'property' => 'border-color',
					'selector' => '.switcher::before',
				),
			),
		);

		$this->controls['indicatorRadius'] = array(
			'group' => 'switcher_style',
			'label' => esc_html__( 'Border', 'bricksfly-elements-for-bricks' ),
			'type'  => 'border',
			'css'   => array(
				array(
					'property' => 'border',
					'selector' => '.switcher::before',
				),
			),
		);

		// === STYLE: Title ===

		$this->controls['titleTypography'] = array(
			'group' => 'title_style',
			'label' => esc_html__( 'Typography', 'bricksfly-elements-for-bricks' ),
			'type'  => 'typography',
			'css'   => array(
				array(
					'property' => 'typography',
					'selector' => '.before_label',
				),
				array(
					'property' => 'typography',
					'selector' => '.after_label',
				),
			),
		);

		$this->controls['titleBgColor'] = array(
			'group'    => 'title_style',
			'label'    => esc_html__( 'Title Background', 'bricksfly-elements-for-bricks' ),
			'type'     => 'color',
			'css'      => array(
				array(
					'property' => 'background-color',
					'selector' => '.before_label',
				),
				array(
					'property' => 'background-color',
					'selector' => '.after_label',
				),
			),
			'required' => array( 'elementList', '=', '2' ),
		);

			$this->controls['TitleBorder'] = array(
				'tab'   => 'style',
				'group' => 'title_style',
				'label' => esc_html__( 'Title Border', 'bricksfly-elements-for-bricks' ),
				'type'  => 'border',
				'css'   => array(
					array(
						'property' => 'border',
						'selector' => '.after_label, .before_label',
					),
				),
			);

			// $this->controls['titleColor'] = [
			// 'group' => 'title_style',
			// 'label' => esc_html__('Text Color', 'bricksfly-elements-for-bricks'),
			// 'type'  => 'color',
			// 'css'   => [
			// ['property' => 'color', 'selector' => '.before_label'],
			// ['property' => 'color', 'selector' => '.after_label'],
			// ],
			// ];

			// === STYLE: Active Title ===

			$this->controls['activeTitleColor'] = array(
				'group' => 'active_title_style',
				'label' => esc_html__( 'Text Color', 'bricksfly-elements-for-bricks' ),
				'type'  => 'color',
				'css'   => array(
					array(
						'property' => 'color',
						'selector' => '.before_label.active',
					),
					array(
						'property' => 'color',
						'selector' => '.after_label.active',
					),
				),
			);

			$this->controls['activeTitleBackground'] = array(
				'group'    => 'active_title_style',
				'label'    => esc_html__( 'Title Background', 'bricksfly-elements-for-bricks' ),
				'type'     => 'color',
				'css'      => array(
					array(
						'property' => 'background-color',
						'selector' => '.before_label::after',
					),
					array(
						'property' => 'background-color',
						'selector' => '.after_label::after',
					),
				),
				'required' => array( 'elementList', '=', '2' ),
			);

			$this->controls['activeTitlePadding'] = array(
				'group'    => 'active_title_style',
				'label'    => esc_html__( 'Padding', 'bricksfly-elements-for-bricks' ),
				'type'     => 'dimensions',
				'css'      => array(
					array(
						'property' => 'padding',
						'selector' => '.before_label',
					),
					array(
						'property' => 'padding',
						'selector' => '.after_label',
					),
				),
				'required' => array( 'elementList', '=', '2' ),
			);

			$this->controls['activeTitleBorder'] = array(
				'tab'   => 'style',
				'group' => 'active_title_style',
				'label' => esc_html__( 'Active Title Border', 'bricksfly-elements-for-bricks' ),
				'type'  => 'border',
				'css'   => array(
					array(
						'property' => 'border',
						'selector' => '.active.after_label, .active.before_label',
					),
				),
			);
	}

	public function enqueue_scripts() {
		wp_enqueue_style(
			'aae-toggle-switch',
			BRICKSFLY_URL . 'public/build/elements/toggle-switch.css',
			array(),
			'1.0.0'
		);

		wp_enqueue_script(
			'aae-toggle-switch',
			BRICKSFLY_URL . 'public/build/elements/toggle-switch.js',
			array(),
			'1.0.0',
			true
		);
	}

	public function render() {

		$settings = $this->settings;
		$items    = ! empty( $settings['toggleSwitcher'] ) ? $settings['toggleSwitcher'] : array();
		$style    = ! empty( $settings['elementList'] ) ? $settings['elementList'] : '1';

		$toggleGap_value         = '10px';
		$toggleBottomSpace_value = '20px';

		if ( ! empty( $settings['toggleGap'] ) ) {
			$value           = intval( $settings['toggleGap'] );
			$toggleGap_value = "{$value}px";
		}

		if ( ! empty( $settings['toggleBottomSpace'] ) ) {
			$value                   = intval( $settings['toggleBottomSpace'] );
			$toggleBottomSpace_value = "{$value}px";
		}

		// // toggleGap
		// if (isset($settings['toggleGap'])) {
		// $value = intval($settings['toggleGap']);
		// $this->set_attribute('_root', 'style', "--toggle-gap: {$value}px");
		// }

		// // toggleBottomSpace
		// if (isset($settings['toggleBottomSpace'])) {
		// $value = intval($settings['toggleBottomSpace']);
		// $this->set_attribute('_root', 'style', "--toggle-bt-space-gap: {$value}px");
		// }

		$this->set_attribute( '_root', 'style', "--toggle-gap: {$toggleGap_value}; --toggle-bt-space-gap: {$toggleBottomSpace_value};" );

		if ( count( $items ) < 2 ) {
			return $this->render_element_placeholder(
				array(
					'icon-class' => 'ti-control-shuffle',
					'text'       => esc_html__( 'Toggle Switch needs at least 2 items.', 'bricksfly-elements-for-bricks' ),
				)
			);
		}

		$this->set_attribute( '_root', 'class', array( 'aae-toggle-switch-wrapper', 'style-' . $style ) );

		$input_id = 'aae-toggle-' . $this->id;

		$before = $items[0];
		$after  = $items[1];
		?>
		<?php echo wp_kses_post( '<div ' . $this->render_attributes( '_root' ) . '>' ); ?>

			<div class="slide-toggle-wrapper">
				<label for="<?php echo esc_attr( $input_id ); ?>" class="before_label active">
					<?php echo esc_html( $before['switchTitle'] ?? '' ); ?>
				</label>
				<input type="checkbox" id="<?php echo esc_attr( $input_id ); ?>">
				<label for="<?php echo esc_attr( $input_id ); ?>" class="switcher"></label>
				<label for="<?php echo esc_attr( $input_id ); ?>" class="after_label">
					<?php echo esc_html( $after['switchTitle'] ?? '' ); ?>
				</label>
			</div>

			<div class="toggle-content">
				<?php
				foreach ( $items as $index => $item ) :
					$pane_class = 'toggle-pane' . ( 0 === $index ? ' show' : '' );
					$type       = $item['contentType'] ?? 'content';
					?>
					<div class="<?php echo esc_attr( $pane_class ); ?>">
						<?php
						if ( 'template' === $type ) {
							if ( ! empty( $item['switchTemplate'] ) ) {
								echo do_shortcode( '[bricks_template id="' . intval( $item['switchTemplate'] ) . '"]' );
							}
						} else {
							echo wp_kses_post( $item['switchContent'] ?? '' );
						}
						?>
					</div>
				<?php endforeach; ?>
			</div>

		</div>
		<?php
	}
}
