<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class THEBRBRE_Draggable_Items extends \Bricks\Element {

	public $category     = 'bricks fly';
	public $name         = 'aab-draggable-items';
	public $icon         = 'ti-move aab-element-marker';
	public $css_selector = '.drag--item';
	public $scripts      = [ 'thebrbreDraggableItems' ];

	public function get_label() {
		return esc_html__('Draggable Items', 'the-bricksfly');
	}

	public function get_keywords() {
		return [ 'draggable', 'drag', 'tags', 'interactive' ];
	}

	public function enqueue_scripts() {
		wp_enqueue_script( 'jquery-ui-draggable' );

		wp_enqueue_style(
			'aab-draggable-items',
			THEBRBRE_URL . 'public/build/elements/draggable-items.css',
			[],
			'1.0.0'
		);

		wp_enqueue_script(
			'aab-draggable-items',
			THEBRBRE_URL . 'public/build/elements/draggable-items.js',
			[ 'jquery', 'jquery-ui-draggable' ],
			'1.0.0',
			true
		);
	}

	public function set_control_groups() {
		$this->control_groups['items'] = [
			'title' => esc_html__('Items', 'the-bricksfly'),
			'tab'   => 'content',
		];
	}

	public function set_controls() {

		$this->controls['draggableItems'] = [
			'tab'           => 'content',
			'group'         => 'items',
			'label'         => esc_html__( 'Items', 'the-bricksfly' ),
			'type'          => 'repeater',
			'titleProperty' => 'title',
			'fields'        => [

				'title' => [
					'label' => esc_html__( 'Title', 'the-bricksfly' ),
					'type'  => 'text',
				],

				'link' => [
					'label' => esc_html__( 'Link', 'the-bricksfly' ),
					'type'  => 'link',
				],

				'titleColor' => [
					'label' => esc_html__( 'Text Color', 'the-bricksfly' ),
					'type'  => 'color',
				],

				'backgroundColor' => [
					'label' => esc_html__( 'Background', 'the-bricksfly' ),
					'type'  => 'color',
				],

				'rotate' => [
					'label' => esc_html__( 'Rotate (deg)', 'the-bricksfly' ),
					'type'  => 'number',
					'min'   => -180,
					'max'   => 180,
					'step'  => 1,
				],
			],
			'default' => [
				[ 'title' => esc_html__( 'Technology', 'the-bricksfly' ) ],
				[ 'title' => esc_html__( 'Manufacturing', 'the-bricksfly' ) ],
				[ 'title' => esc_html__( 'Insurance', 'the-bricksfly' ) ],
				[ 'title' => esc_html__( 'Transportation', 'the-bricksfly' ) ],
				[ 'title' => esc_html__( 'Entertainment', 'the-bricksfly' ) ],
			],
		];
	}

	private function extract_color( $value ) {
		if ( empty( $value ) ) {
			return '';
		}
		if ( is_array( $value ) ) {
			if ( ! empty( $value['rgb'] ) )  return $value['rgb'];
			if ( ! empty( $value['hex'] ) )  return $value['hex'];
			if ( ! empty( $value['raw'] ) )  return $value['raw'];
			return '';
		}
		return (string) $value;
	}

	public function render() {
		$settings = $this->settings;
		$items    = ! empty( $settings['draggableItems'] ) ? $settings['draggableItems'] : [];

		if ( empty( $items ) ) {
			return $this->render_element_placeholder( [
				'icon-class' => 'ti-move',
				'text'       => esc_html__( 'No draggable items added.', 'the-bricksfly' ),
			] );
		}

		$this->set_attribute( '_root', 'class', [ 'draggable--items' ] );

		echo wp_kses_post('<div ' . $this->render_attributes( '_root' ) . '>');

		foreach ( $items as $index => $item ) {
			$link_key = 'drag-item-' . $index;

			$styles = [];

			$text_color = $this->extract_color( $item['titleColor'] ?? '' );
			if ( $text_color ) {
				$styles[] = 'color:' . esc_attr( $text_color );
			}

			$bg_color = $this->extract_color( $item['backgroundColor'] ?? '' );
			if ( $bg_color ) {
				$styles[] = 'background:' . esc_attr( $bg_color );
			}

			$rotate_deg = ( isset( $item['rotate'] ) && $item['rotate'] !== '' ) ? intval( $item['rotate'] ) : 0;
			$styles[]   = '--drag-rotate:' . $rotate_deg . 'deg';

			$this->set_attribute( $link_key, 'class', [ 'drag--item' ] );

			if ( ! empty( $styles ) ) {
				$this->set_attribute( $link_key, 'style', implode( ';', $styles ) );
			}

			if ( ! empty( $item['link'] ) ) {
				$this->set_link_attributes( $link_key, $item['link'] );
			}

			echo wp_kses_post('<a ' . $this->render_attributes( $link_key ) . '>');
			echo esc_html( ! empty( $item['title'] ) ? $item['title'] : '' );
			echo '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
