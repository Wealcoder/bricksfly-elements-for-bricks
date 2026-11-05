<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BRICKSFLY_Draggable_Items extends \Bricks\Element {

	public $category     = 'bricks fly';
	public $name         = 'aab-draggable-items';
	public $icon         = 'ti-move aab-element-marker';
	public $css_selector = '.drag--item';
	public $scripts      = array( 'bricksflyDraggableItems' );

	public function get_label() {
		return esc_html__( 'Draggable Items', 'bricksfly-elements-for-bricks' );
	}

	public function get_keywords() {
		return array( 'draggable', 'drag', 'tags', 'interactive' );
	}

	public function enqueue_scripts() {
		wp_enqueue_script( 'jquery-ui-draggable' );

		wp_enqueue_style(
			'aab-draggable-items',
			BRICKSFLY_URL . 'public/build/elements/draggable-items.css',
			array(),
			'1.0.0'
		);

		wp_enqueue_script(
			'aab-draggable-items',
			BRICKSFLY_URL . 'public/build/elements/draggable-items.js',
			array( 'jquery', 'jquery-ui-draggable' ),
			'1.0.0',
			true
		);
	}

	public function set_control_groups() {
		$this->control_groups['items'] = array(
			'title' => esc_html__( 'Items', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'content',
		);
	}

	public function set_controls() {

		$this->controls['draggableItems'] = array(
			'tab'           => 'content',
			'group'         => 'items',
			'label'         => esc_html__( 'Items', 'bricksfly-elements-for-bricks' ),
			'type'          => 'repeater',
			'titleProperty' => 'title',
			'fields'        => array(

				'title'           => array(
					'label' => esc_html__( 'Title', 'bricksfly-elements-for-bricks' ),
					'type'  => 'text',
				),

				'link'            => array(
					'label' => esc_html__( 'Link', 'bricksfly-elements-for-bricks' ),
					'type'  => 'link',
				),

				'titleColor'      => array(
					'label' => esc_html__( 'Text Color', 'bricksfly-elements-for-bricks' ),
					'type'  => 'color',
				),

				'backgroundColor' => array(
					'label' => esc_html__( 'Background', 'bricksfly-elements-for-bricks' ),
					'type'  => 'color',
				),

				'rotate'          => array(
					'label' => esc_html__( 'Rotate (deg)', 'bricksfly-elements-for-bricks' ),
					'type'  => 'number',
					'min'   => -180,
					'max'   => 180,
					'step'  => 1,
				),
			),
			'default'       => array(
				array( 'title' => esc_html__( 'Technology', 'bricksfly-elements-for-bricks' ) ),
				array( 'title' => esc_html__( 'Manufacturing', 'bricksfly-elements-for-bricks' ) ),
				array( 'title' => esc_html__( 'Insurance', 'bricksfly-elements-for-bricks' ) ),
				array( 'title' => esc_html__( 'Transportation', 'bricksfly-elements-for-bricks' ) ),
				array( 'title' => esc_html__( 'Entertainment', 'bricksfly-elements-for-bricks' ) ),
			),
		);
	}

	private function extract_color( $value ) {
		if ( empty( $value ) ) {
			return '';
		}
		if ( is_array( $value ) ) {
			if ( ! empty( $value['rgb'] ) ) {
				return $value['rgb'];
			}
			if ( ! empty( $value['hex'] ) ) {
				return $value['hex'];
			}
			if ( ! empty( $value['raw'] ) ) {
				return $value['raw'];
			}
			return '';
		}
		return (string) $value;
	}

	public function render() {
		$settings = $this->settings;
		$items    = ! empty( $settings['draggableItems'] ) ? $settings['draggableItems'] : array();

		if ( empty( $items ) ) {
			return $this->render_element_placeholder(
				array(
					'icon-class' => 'ti-move',
					'text'       => esc_html__( 'No draggable items added.', 'bricksfly-elements-for-bricks' ),
				)
			);
		}

		$this->set_attribute( '_root', 'class', array( 'draggable--items' ) );

		echo wp_kses_post( '<div ' . $this->render_attributes( '_root' ) . '>' );

		foreach ( $items as $index => $item ) {
			$link_key = 'drag-item-' . $index;

			$styles = array();

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

			$this->set_attribute( $link_key, 'class', array( 'drag--item' ) );

			if ( ! empty( $styles ) ) {
				$this->set_attribute( $link_key, 'style', implode( ';', $styles ) );
			}

			if ( ! empty( $item['link'] ) ) {
				$this->set_link_attributes( $link_key, $item['link'] );
			}

			echo wp_kses_post( '<a ' . $this->render_attributes( $link_key ) . '>' );
			echo esc_html( ! empty( $item['title'] ) ? $item['title'] : '' );
			echo '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
