<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class THEBRBRE_Bricks_Image_Accordion extends \Bricks\Element {

	public $category     = 'bricks fly';
	public $name         = 'aab-image-accordion';
	public $icon         = 'ti-layout-accordion-merged aab-element-marker';
	public $css_selector = '.aab-image-accordion';
	public $scripts      = [ 'thebrbreImageAccordion' ];

	public function get_label() {
		return esc_html__('Image Accordion', 'the-bricksfly');
	}

	public function get_keywords() {
		return [ 'image', 'accordion', 'gallery', 'hover' ];
	}

	public function enqueue_scripts() {
		wp_enqueue_style(
			'aab-image-accordion',
			THEBRBRE_URL . 'public/build/elements/image-accordion.css',
			[],
			'1.0.0'
		);

		wp_enqueue_script(
			'aab-image-accordion',
			THEBRBRE_URL . 'public/build/elements/image-accordion.js',
			[],
			'1.0.0',
			true
		);
	}

	public function set_control_groups() {
		$this->control_groups['accordion_items'] = [
			'title' => esc_html__('Accordion Items', 'the-bricksfly'),
			'tab'   => 'content',
		];

		$this->control_groups['settings'] = [
			'title' => esc_html__('Settings', 'the-bricksfly'),
			'tab'   => 'content',
		];

		$this->control_groups['button'] = [
			'title' => esc_html__('Button', 'the-bricksfly'),
			'tab'   => 'content',
		];

		$this->control_groups['content_style'] = [
			'title' => esc_html__('Content', 'the-bricksfly'),
			'tab'   => 'style',
		];

		$this->control_groups['title_style'] = [
			'title' => esc_html__('Title', 'the-bricksfly'),
			'tab'   => 'style',
		];

		$this->control_groups['subtitle_style'] = [
			'title' => esc_html__('Sub Title', 'the-bricksfly'),
			'tab'   => 'style',
		];

		$this->control_groups['desc_style'] = [
			'title' => esc_html__('Description', 'the-bricksfly'),
			'tab'   => 'style',
		];

		$this->control_groups['button_style'] = [
			'title' => esc_html__('Button', 'the-bricksfly'),
			'tab'   => 'style',
		];
	}

	public function set_controls() {

		// --- Accordion Items (Repeater) ---

		$this->controls['items'] = [
			'tab'           => 'content',
			'group'         => 'accordion_items',
			'label'         => esc_html__( 'Items', 'the-bricksfly' ),
			'type'          => 'repeater',
			'titleProperty' => 'title',
			'fields'        => [
				'image' => [
					'label' => esc_html__( 'Image', 'the-bricksfly' ),
					'type'  => 'image',
				],
				'title' => [
					'label'   => esc_html__( 'Title', 'the-bricksfly' ),
					'type'    => 'text',
					'default' => esc_html__( 'Siyantika Glory', 'the-bricksfly' ),
				],
				'subtitle' => [
					'label'   => esc_html__( 'Sub Title', 'the-bricksfly' ),
					'type'    => 'text',
					'default' => esc_html__( 'Modelling - 2012', 'the-bricksfly' ),
				],
				'description' => [
					'label'   => esc_html__( 'Description', 'the-bricksfly' ),
					'type'    => 'textarea',
					'default' => esc_html__( 'Hatha yoga built on a harmonious balance between body strength and softness', 'the-bricksfly' ),
				],
				'link' => [
					'label' => esc_html__( 'Link', 'the-bricksfly' ),
					'type'  => 'link',
				],
			],
			'default' => [
				[
					'title'       => esc_html__( 'Siyantika Glory', 'the-bricksfly' ),
					'subtitle'    => esc_html__( 'Modelling - 2012', 'the-bricksfly' ),
					'description' => esc_html__( 'Hatha yoga built on a harmonious balance between body strength and softness', 'the-bricksfly' ),
				],
				[
					'title'       => esc_html__( 'Siyantika Glory', 'the-bricksfly' ),
					'subtitle'    => esc_html__( 'Modelling - 2012', 'the-bricksfly' ),
					'description' => esc_html__( 'Hatha yoga built on a harmonious balance between body strength and softness', 'the-bricksfly' ),
				],
				[
					'title'       => esc_html__( 'Siyantika Glory', 'the-bricksfly' ),
					'subtitle'    => esc_html__( 'Modelling - 2012', 'the-bricksfly' ),
					'description' => esc_html__( 'Hatha yoga built on a harmonious balance between body strength and softness', 'the-bricksfly' ),
				],
				[
					'title'       => esc_html__( 'Siyantika Glory', 'the-bricksfly' ),
					'subtitle'    => esc_html__( 'Modelling - 2012', 'the-bricksfly' ),
					'description' => esc_html__( 'Hatha yoga built on a harmonious balance between body strength and softness', 'the-bricksfly' ),
				],
				[
					'title'       => esc_html__( 'Siyantika Glory', 'the-bricksfly' ),
					'subtitle'    => esc_html__( 'Modelling - 2012', 'the-bricksfly' ),
					'description' => esc_html__( 'Hatha yoga built on a harmonious balance between body strength and softness', 'the-bricksfly' ),
				],
			],
		];

		$this->controls['imageSize'] = [
			'tab'     => 'content',
			'group'   => 'accordion_items',
			'label'   => esc_html__( 'Image Size', 'the-bricksfly' ),
			'type'    => 'select',
			'options' => $this->get_image_sizes(),
			'default' => 'full',
		];

		// --- Settings ---

		$this->controls['titleTag'] = [
			'tab'     => 'content',
			'group'   => 'settings',
			'label'   => esc_html__( 'Title HTML Tag', 'the-bricksfly' ),
			'type'    => 'select',
			'options' => [
				'h1'   => 'H1',
				'h2'   => 'H2',
				'h3'   => 'H3',
				'h4'   => 'H4',
				'h5'   => 'H5',
				'h6'   => 'H6',
				'div'  => 'div',
				'span' => 'span',
				'p'    => 'p',
			],
			'default' => 'h4',
			'inline'  => true,
		];

		$this->controls['accordionLayout'] = [
			'tab'     => 'content',
			'group'   => 'settings',
			'label'   => esc_html__( 'Layout', 'the-bricksfly' ),
			'type'    => 'select',
			'options' => [
				'horizontal' => esc_html__( 'Horizontal', 'the-bricksfly' ),
				'vertical'   => esc_html__( 'Vertical', 'the-bricksfly' ),
			],
			'default' => 'horizontal',
			'inline'  => true,
		];

		$this->controls['mobileBreakpoint'] = [
			'tab'         => 'content',
			'group'       => 'settings',
			'label'       => esc_html__( 'Stack Below (px)', 'the-bricksfly' ),
			'type'        => 'number',
			'default'     => 768,
			'description' => esc_html__( 'Horizontal layout stacks vertically below this width. Set 0 to disable.', 'the-bricksfly' ),
			'required'    => [ 'accordionLayout', '=', 'horizontal' ],
		];

		$this->controls['expandStyle'] = [
			'tab'     => 'content',
			'group'   => 'settings',
			'label'   => esc_html__( 'Expand On', 'the-bricksfly' ),
			'type'    => 'select',
			'options' => [
				'hover' => esc_html__( 'Hover', 'the-bricksfly' ),
				'click' => esc_html__( 'Click', 'the-bricksfly' ),
			],
			'default' => 'hover',
			'inline'  => true,
		];

		$this->controls['defaultActiveItem'] = [
			'tab'         => 'content',
			'group'       => 'settings',
			'label'       => esc_html__( 'Default Open Item', 'the-bricksfly' ),
			'description' => esc_html__( '1-based index. Switch this in the builder to preview each item\'s expanded state while designing.', 'the-bricksfly' ),
			'type'        => 'number',
			'default'     => 1,
			'min'         => 1,
		];

		$this->controls['accordionHeight'] = [
			'tab'   => 'content',
			'group' => 'settings',
			'label' => esc_html__( 'Height', 'the-bricksfly' ),
			'type'  => 'number',
			'units' => [
				'px' => [ 'min' => 0, 'max' => 1000, 'step' => 5 ],
				'%'  => [ 'min' => 0, 'max' => 100 ],
				'vh' => [ 'min' => 0, 'max' => 100 ],
			],
			'css' => [
				[
					'property' => 'height',
					'selector' => '.aab-image-accordion',
				],
			],
		];

		$this->controls['contentAlign'] = [
			'tab'   => 'content',
			'group' => 'settings',
			'label' => esc_html__( 'Horizontal Align', 'the-bricksfly' ),
			'type'  => 'justify-content',
			'css'   => [
				[
					'property' => 'justify-content',
					'selector' => '.aab-image-accordion .accordion-item',
				],
			],
		];

		$this->controls['itemAlign'] = [
			'tab'   => 'content',
			'group' => 'settings',
			'label' => esc_html__( 'Vertical Align', 'the-bricksfly' ),
			'type'  => 'align-items',
			'css'   => [
				[
					'property' => 'align-items',
					'selector' => '.aab-image-accordion .accordion-item',
				],
			],
		];

		$this->controls['textAlign'] = [
			'tab'   => 'content',
			'group' => 'settings',
			'label' => esc_html__( 'Text Align', 'the-bricksfly' ),
			'type'  => 'text-align',
			'css'   => [
				[
					'property' => 'text-align',
					'selector' => '.aab-image-accordion .content',
				],
			],
		];

		// --- Button ---

		$this->controls['linkType'] = [
			'tab'     => 'content',
			'group'   => 'button',
			'label'   => esc_html__( 'Link Type', 'the-bricksfly' ),
			'type'    => 'select',
			'options' => [
				'none'   => esc_html__( 'None', 'the-bricksfly' ),
				'button' => esc_html__( 'Button', 'the-bricksfly' ),
			],
			'default' => 'button',
			'inline'  => true,
		];

		$this->controls['btnText'] = [
			'tab'      => 'content',
			'group'    => 'button',
			'label'    => esc_html__( 'Button Text', 'the-bricksfly' ),
			'type'     => 'text',
			'default'  => esc_html__( 'Read More', 'the-bricksfly' ),
			'required' => [ 'linkType', '=', 'button' ],
		];

		// --- Content Style ---

		$this->controls['overlayColor'] = [
			'tab'   => 'style',
			'group' => 'content_style',
			'label' => esc_html__( 'Overlay Color', 'the-bricksfly' ),
			'type'  => 'color',
			'css'   => [
				[
					'property' => 'background-color',
					'selector' => '.accordion-item::after',
				],
			],
		];

		$this->controls['contentPadding'] = [
			'tab'   => 'style',
			'group' => 'content_style',
			'label' => esc_html__( 'Padding', 'the-bricksfly' ),
			'type'  => 'dimensions',
			'css'   => [
				[
					'property' => 'padding',
					'selector' => '.content',
				],
			],
		];

		// --- Title Style ---

		$this->controls['titleSpacing'] = [
			'tab'   => 'style',
			'group' => 'title_style',
			'label' => esc_html__( 'Spacing', 'the-bricksfly' ),
			'type'  => 'number',
			'units' => [
				'px' => [ 'min' => -200, 'max' => 200 ],
			],
			'css' => [
				[
					'property' => 'margin-bottom',
					'selector' => '.title',
				],
			],
		];

		$this->controls['titleColor'] = [
			'tab'   => 'style',
			'group' => 'title_style',
			'label' => esc_html__( 'Color', 'the-bricksfly' ),
			'type'  => 'color',
			'css'   => [
				[
					'property' => 'color',
					'selector' => '.title',
				],
			],
		];

		$this->controls['titleTypography'] = [
			'tab'   => 'style',
			'group' => 'title_style',
			'label' => esc_html__( 'Typography', 'the-bricksfly' ),
			'type'  => 'typography',
			'css'   => [
				[
					'property' => 'font',
					'selector' => '.title',
				],
			],
		];

		// --- Subtitle Style ---

		$this->controls['subtitleSpacing'] = [
			'tab'   => 'style',
			'group' => 'subtitle_style',
			'label' => esc_html__( 'Spacing', 'the-bricksfly' ),
			'type'  => 'number',
			'units' => [
				'px' => [ 'min' => 0, 'max' => 200 ],
			],
			'css' => [
				[
					'property' => 'margin-bottom',
					'selector' => '.subtitle',
				],
			],
		];

		$this->controls['subtitleColor'] = [
			'tab'   => 'style',
			'group' => 'subtitle_style',
			'label' => esc_html__( 'Color', 'the-bricksfly' ),
			'type'  => 'color',
			'css'   => [
				[
					'property' => 'color',
					'selector' => '.subtitle',
				],
			],
		];

		$this->controls['subtitleTypography'] = [
			'tab'   => 'style',
			'group' => 'subtitle_style',
			'label' => esc_html__( 'Typography', 'the-bricksfly' ),
			'type'  => 'typography',
			'css'   => [
				[
					'property' => 'font',
					'selector' => '.subtitle',
				],
			],
		];

		// --- Description Style ---

		$this->controls['descSpacing'] = [
			'tab'   => 'style',
			'group' => 'desc_style',
			'label' => esc_html__( 'Spacing', 'the-bricksfly' ),
			'type'  => 'number',
			'units' => [
				'px' => [ 'min' => 0, 'max' => 200 ],
			],
			'css' => [
				[
					'property' => 'margin-bottom',
					'selector' => '.description',
				],
			],
		];

		$this->controls['descColor'] = [
			'tab'   => 'style',
			'group' => 'desc_style',
			'label' => esc_html__( 'Color', 'the-bricksfly' ),
			'type'  => 'color',
			'css'   => [
				[
					'property' => 'color',
					'selector' => '.description',
				],
			],
		];

		$this->controls['descTypography'] = [
			'tab'   => 'style',
			'group' => 'desc_style',
			'label' => esc_html__( 'Typography', 'the-bricksfly' ),
			'type'  => 'typography',
			'css'   => [
				[
					'property' => 'font',
					'selector' => '.description',
				],
			],
		];

		// --- Button Style ---

		$this->controls['btnTypography'] = [
			'tab'      => 'style',
			'group'    => 'button_style',
			'label'    => esc_html__( 'Typography', 'the-bricksfly' ),
			'type'     => 'typography',
			'css'      => [
				[
					'property' => 'font',
					'selector' => '.aab-btn',
				],
			],
			'required' => [ 'linkType', '=', 'button' ],
		];

		$this->controls['btnColor'] = [
			'tab'      => 'style',
			'group'    => 'button_style',
			'label'    => esc_html__( 'Color', 'the-bricksfly' ),
			'type'     => 'color',
			'css'      => [
				[
					'property' => 'color',
					'selector' => '.aab-btn',
				],
			],
			'required' => [ 'linkType', '=', 'button' ],
		];

		$this->controls['btnColorHover'] = [
			'tab'      => 'style',
			'group'    => 'button_style',
			'label'    => esc_html__( 'Color (Hover)', 'the-bricksfly' ),
			'type'     => 'color',
			'css'      => [
				[
					'property' => 'color',
					'selector' => '.aab-btn:hover',
				],
			],
			'required' => [ 'linkType', '=', 'button' ],
		];

		$this->controls['btnBackground'] = [
			'tab'      => 'style',
			'group'    => 'button_style',
			'label'    => esc_html__( 'Background', 'the-bricksfly' ),
			'type'     => 'background',
			'css'      => [
				[
					'property' => 'background',
					'selector' => '.aab-btn',
				],
			],
			'required' => [ 'linkType', '=', 'button' ],
		];

		$this->controls['btnBackgroundHover'] = [
			'tab'      => 'style',
			'group'    => 'button_style',
			'label'    => esc_html__( 'Background (Hover)', 'the-bricksfly' ),
			'type'     => 'background',
			'css'      => [
				[
					'property' => 'background',
					'selector' => '.aab-btn:hover',
				],
			],
			'required' => [ 'linkType', '=', 'button' ],
		];

		$this->controls['btnBorder'] = [
			'tab'      => 'style',
			'group'    => 'button_style',
			'label'    => esc_html__( 'Border', 'the-bricksfly' ),
			'type'     => 'border',
			'css'      => [
				[
					'property' => 'border',
					'selector' => '.aab-btn',
				],
			],
			'required' => [ 'linkType', '=', 'button' ],
		];

		$this->controls['btnPadding'] = [
			'tab'      => 'style',
			'group'    => 'button_style',
			'label'    => esc_html__( 'Padding', 'the-bricksfly' ),
			'type'     => 'dimensions',
			'css'      => [
				[
					'property' => 'padding',
					'selector' => '.aab-btn',
				],
			],
			'required' => [ 'linkType', '=', 'button' ],
		];
	}

	/**
	 * Get available image sizes
	 */
	private function get_image_sizes() {
		$sizes   = get_intermediate_image_sizes();
		$options = [];

		foreach ( $sizes as $size ) {
			$options[ $size ] = ucwords( str_replace( [ '-', '_' ], ' ', $size ) );
		}

		$options['full'] = esc_html__( 'Full', 'the-bricksfly' );

		return $options;
	}

	public function render() {
		$settings = $this->settings;
		$items    = ! empty( $settings['items'] ) ? $settings['items'] : [];
		$layout   = ! empty( $settings['accordionLayout'] ) ? $settings['accordionLayout'] : 'horizontal';
		$expand   = ! empty( $settings['expandStyle'] ) ? $settings['expandStyle'] : 'hover';
		$breakpoint = isset( $settings['mobileBreakpoint'] ) ? intval( $settings['mobileBreakpoint'] ) : 768;

		if ( empty( $items ) ) {
			return $this->render_element_placeholder( [
				'icon-class' => 'ti-layout-accordion-merged',
				'text'       => esc_html__( 'No accordion items added.', 'the-bricksfly' ),
			] );
		}

		$title_tag  = ! empty( $settings['titleTag'] ) ? $settings['titleTag'] : 'h4';
		$link_type  = ! empty( $settings['linkType'] ) ? $settings['linkType'] : 'none';
		$btn_text   = ! empty( $settings['btnText'] ) ? $settings['btnText'] : '';
		$image_size = ! empty( $settings['imageSize'] ) ? $settings['imageSize'] : 'full';

		// Validate title tag
		$allowed_tags = [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p' ];
		if ( ! in_array( $title_tag, $allowed_tags, true ) ) {
			$title_tag = 'h4';
		}

		$this->set_attribute( '_root', 'class', [ 'aab-image-accordion', 'accordion-layout-' . esc_attr( $layout ) ] );
		$this->set_attribute( '_root', 'data-expand', $expand );

		// 1-based default-open index, clamped to the item count so an
		// out-of-range value (e.g. user removed items after picking 5)
		// still resolves to a valid item instead of an invisible state.
		$default_active = isset( $settings['defaultActiveItem'] ) ? max( 1, intval( $settings['defaultActiveItem'] ) ) : 1;
		$default_active = min( $default_active, count( $items ) );
		$this->set_attribute( '_root', 'data-default-active', $default_active );

		// Breakpoint for responsive stacking
		if ( 'horizontal' === $layout && $breakpoint > 0 ) {
			$this->set_attribute( '_root', 'data-breakpoint', $breakpoint );
		}

		echo wp_kses_post('<div ' . $this->render_attributes( '_root' ) . '>');

		foreach ( $items as $index => $item ) {
			// Bricks image control: can be {id, url, size}, just an ID (int), or {url}
			$image_data = isset( $item['image'] ) ? $item['image'] : '';
			$image_url  = '';

			if ( is_array( $image_data ) ) {
				if ( ! empty( $image_data['url'] ) ) {
					$image_url = $image_data['url'];
				} elseif ( ! empty( $image_data['id'] ) ) {
					$src = wp_get_attachment_image_src( $image_data['id'], $image_size );
					$image_url = $src ? $src[0] : '';
				}
			} elseif ( is_numeric( $image_data ) && $image_data > 0 ) {
				$src = wp_get_attachment_image_src( (int) $image_data, $image_size );
				$image_url = $src ? $src[0] : '';
			}

			// Fallback: Bricks placeholder image
			if ( empty( $image_url ) ) {
				$image_url = BRICKS_URL_ASSETS . 'images/placeholder-image-800x600.jpg';
			}

			echo '<div class="accordion-item" style="background-image: url(' . esc_url( $image_url ) . ')">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '<div class="content">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			if ( ! empty( $item['subtitle'] ) ) {
				echo '<div class="subtitle">' . esc_html( $item['subtitle'] ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			echo '<' . tag_escape( $title_tag ) . ' class="title">' . esc_html( $item['title'] ) . '</' . tag_escape( $title_tag ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			if ( ! empty( $item['description'] ) ) {
				echo '<div class="description">' . esc_html( $item['description'] ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			// Button
			if ( 'button' === $link_type && ! empty( $btn_text ) ) {
				$link_key = 'btn-link-' . $index;

				if ( ! empty( $item['link'] ) ) {
					$this->set_link_attributes( $link_key, $item['link'] );
					$this->set_attribute( $link_key, 'class', 'aab-btn' );
					echo wp_kses_post('<a ' . $this->render_attributes( $link_key ) . '>' . esc_html( $btn_text ) . '</a>');
				} else {
					echo '<span class="aab-btn" role="button">' . esc_html( $btn_text ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
			}

			echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
