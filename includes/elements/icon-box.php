<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BRICKSFLY_Bricks_Icon_Box extends \Bricks\Element {


	public $category     = 'bricks fly';
	public $name         = 'aab-icon-box';
	public $icon         = 'ti-layout-media-center-alt aab-element-marker';
	public $css_selector = '.aab-icon-box';
	public $scripts      = array( 'bricksflyIconBox' );

	public function get_label() {
		return esc_html__( 'Icon Box', 'bricksfly-elements-for-bricks' );
	}

	public function get_keywords() {
		return array( 'icon', 'box', 'feature', 'service', 'info' );
	}

	public function enqueue_scripts() {
		wp_enqueue_style(
			'aab-icon-box',
			BRICKSFLY_URL . 'public/build/elements/icon-box.css',
			array(),
			'1.0.0'
		);

		wp_enqueue_script(
			'aab-icon-box',
			BRICKSFLY_URL . 'public/build/elements/icon-box.js',
			array(),
			'1.0.0',
			true
		);
	}

	public function set_control_groups() {
		$this->control_groups['content'] = array(
			'title' => esc_html__( 'Content', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'content',
		);

		$this->control_groups['layout'] = array(
			'title' => esc_html__( 'Layout', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'content',
		);

		$this->control_groups['button_content'] = array(
			'title' => esc_html__( 'Button', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'content',
		);

		$this->control_groups['box_style'] = array(
			'title' => esc_html__( 'Box', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);

		$this->control_groups['icon_style'] = array(
			'title' => esc_html__( 'Icon', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);

		$this->control_groups['title_style'] = array(
			'title' => esc_html__( 'Title', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);

		$this->control_groups['desc_style'] = array(
			'title' => esc_html__( 'Description', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);

		$this->control_groups['btn_style'] = array(
			'title' => esc_html__( 'Button Style', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);

		$this->control_groups['btn_hover_style'] = array(
			'title' => esc_html__( 'Button Hover', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);

		$this->control_groups['flip_back_style'] = array(
			'title'    => esc_html__( 'Flip Back', 'bricksfly-elements-for-bricks' ),
			'tab'      => 'style',
			'required' => array( array( 'layout_preset', '=', 'flip-card' ) ),
		);
	}

	public function set_controls() {

		/*
		=========================
			CONTENT
		========================= */

		$this->controls['icon'] = array(
			'group'   => 'content',
			'label'   => esc_html__( 'Icon', 'bricksfly-elements-for-bricks' ),
			'type'    => 'icon',
			'default' => array(
				'library' => 'themify',
				'icon'    => 'ti-star',
			),
		);

		$this->controls['title'] = array(
			'group'   => 'content',
			'label'   => esc_html__( 'Title', 'bricksfly-elements-for-bricks' ),
			'type'    => 'text',
			'default' => esc_html__( 'Icon Box Title', 'bricksfly-elements-for-bricks' ),
		);

		$this->controls['title_tag'] = array(
			'group'   => 'content',
			'label'   => esc_html__( 'Title Tag', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'options' => array(
				'h1'   => 'H1',
				'h2'   => 'H2',
				'h3'   => 'H3',
				'h4'   => 'H4',
				'h5'   => 'H5',
				'h6'   => 'H6',
				'p'    => 'p',
				'span' => 'span',
				'div'  => 'div',
			),
			'default' => 'h3',
			'inline'  => true,
		);

		$this->controls['description'] = array(
			'group'   => 'content',
			'label'   => esc_html__( 'Description', 'bricksfly-elements-for-bricks' ),
			'type'    => 'textarea',
			'default' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis.', 'bricksfly-elements-for-bricks' ),
		);

		$this->controls['box_link'] = array(
			'group' => 'content',
			'label' => esc_html__( 'Box Link', 'bricksfly-elements-for-bricks' ),
			'type'  => 'link',
		);

		/*
		=========================
			LAYOUT
		========================= */

		$this->controls['layout_preset'] = array(
			'group'   => 'layout',
			'label'   => esc_html__( 'Layout', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'options' => array(
				'default'        => esc_html__( 'Default', 'bricksfly-elements-for-bricks' ),
				'stacked'        => esc_html__( 'Stacked Card', 'bricksfly-elements-for-bricks' ),
				'bordered'       => esc_html__( 'Bordered', 'bricksfly-elements-for-bricks' ),
				'minimal'        => esc_html__( 'Minimal', 'bricksfly-elements-for-bricks' ),
				'floating'       => esc_html__( 'Floating Icon', 'bricksfly-elements-for-bricks' ),
				'gradient-bar'   => esc_html__( 'Gradient Bar', 'bricksfly-elements-for-bricks' ),
				'icon-left-line' => esc_html__( 'Icon + Left Line', 'bricksfly-elements-for-bricks' ),
				'zoom-hover'     => esc_html__( 'Zoom on Hover', 'bricksfly-elements-for-bricks' ),
				'flip-card'      => esc_html__( 'Flip Card', 'bricksfly-elements-for-bricks' ),
				'slide-up'       => esc_html__( 'Slide Up Reveal', 'bricksfly-elements-for-bricks' ),
			),
			'default' => 'default',
		);

		$this->controls['direction'] = array(
			'group'   => 'layout',
			'label'   => esc_html__( 'Direction', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'options' => array(
				'vertical'           => esc_html__( 'Vertical', 'bricksfly-elements-for-bricks' ),
				'horizontal'         => esc_html__( 'Horizontal', 'bricksfly-elements-for-bricks' ),
				'horizontal-reverse' => esc_html__( 'Horizontal Reverse', 'bricksfly-elements-for-bricks' ),
				'vertical-reverse'   => esc_html__( 'Vertical Reverse', 'bricksfly-elements-for-bricks' ),
			),
			'default' => 'vertical',
		);

		$this->controls['align'] = array(
			'group'   => 'layout',
			'label'   => esc_html__( 'Alignment', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'options' => array(
				'left'   => esc_html__( 'Left', 'bricksfly-elements-for-bricks' ),
				'center' => esc_html__( 'Center', 'bricksfly-elements-for-bricks' ),
				'right'  => esc_html__( 'Right', 'bricksfly-elements-for-bricks' ),
			),
			'default' => 'center',
		);

		$this->controls['icon_position'] = array(
			'group'    => 'layout',
			'label'    => esc_html__( 'Icon Vertical Align', 'bricksfly-elements-for-bricks' ),
			'type'     => 'select',
			'options'  => array(
				'top'    => esc_html__( 'Top', 'bricksfly-elements-for-bricks' ),
				'center' => esc_html__( 'Center', 'bricksfly-elements-for-bricks' ),
				'bottom' => esc_html__( 'Bottom', 'bricksfly-elements-for-bricks' ),
			),
			'default'  => 'top',
			'required' => array( array( 'direction', '=', array( 'horizontal', 'horizontal-reverse' ) ) ),
		);

		$this->controls['content_gap'] = array(
			'group' => 'layout',
			'label' => esc_html__( 'Content Gap', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => true,
			'css'   => array(
				array(
					'property' => 'gap',
					'selector' => '.aab-icon-box',
				),
			),
		);

		// Flip card back content
		$this->controls['flip_back_text'] = array(
			'group'    => 'layout',
			'label'    => esc_html__( 'Back Text', 'bricksfly-elements-for-bricks' ),
			'type'     => 'textarea',
			'default'  => esc_html__( 'Hover to see this side! Add any content for the card back.', 'bricksfly-elements-for-bricks' ),
			'required' => array( array( 'layout_preset', '=', 'flip-card' ) ),
		);

		$this->controls['flip_direction'] = array(
			'group'    => 'layout',
			'label'    => esc_html__( 'Flip Direction', 'bricksfly-elements-for-bricks' ),
			'type'     => 'select',
			'options'  => array(
				'horizontal' => esc_html__( 'Horizontal', 'bricksfly-elements-for-bricks' ),
				'vertical'   => esc_html__( 'Vertical', 'bricksfly-elements-for-bricks' ),
			),
			'default'  => 'horizontal',
			'required' => array( array( 'layout_preset', '=', 'flip-card' ) ),
		);

		// Gradient bar color
		$this->controls['gradient_bar_color'] = array(
			'group'    => 'layout',
			'label'    => esc_html__( 'Bar Gradient', 'bricksfly-elements-for-bricks' ),
			'type'     => 'background',
			'required' => array( array( 'layout_preset', '=', 'gradient-bar' ) ),
			'css'      => array(
				array(
					'property' => 'background',
					'selector' => '.aab-icon-box::before',
				),
			),
		);

		// Line color
		$this->controls['line_color'] = array(
			'group'    => 'layout',
			'label'    => esc_html__( 'Line Color', 'bricksfly-elements-for-bricks' ),
			'type'     => 'color',
			'required' => array( array( 'layout_preset', '=', 'icon-left-line' ) ),
			'css'      => array(
				array(
					'property' => 'background-color',
					'selector' => '.aab-icon-box__line',
				),
			),
		);

		/*
		=========================
			BUTTON CONTENT
		========================= */

		$this->controls['show_button'] = array(
			'group'   => 'button_content',
			'label'   => esc_html__( 'Show Button', 'bricksfly-elements-for-bricks' ),
			'type'    => 'checkbox',
			'default' => false,
		);

		$this->controls['btn_text'] = array(
			'group'    => 'button_content',
			'label'    => esc_html__( 'Button Text', 'bricksfly-elements-for-bricks' ),
			'type'     => 'text',
			'default'  => esc_html__( 'Read More', 'bricksfly-elements-for-bricks' ),
			'required' => array( array( 'show_button', '!=', '' ) ),
		);

		$this->controls['btn_link'] = array(
			'group'    => 'button_content',
			'label'    => esc_html__( 'Button Link', 'bricksfly-elements-for-bricks' ),
			'type'     => 'link',
			'required' => array( array( 'show_button', '!=', '' ) ),
		);

		$this->controls['btn_icon'] = array(
			'group'    => 'button_content',
			'label'    => esc_html__( 'Button Icon', 'bricksfly-elements-for-bricks' ),
			'type'     => 'icon',
			'required' => array( array( 'show_button', '!=', '' ) ),
		);

		$this->controls['btn_icon_position'] = array(
			'group'    => 'button_content',
			'label'    => esc_html__( 'Icon Position', 'bricksfly-elements-for-bricks' ),
			'type'     => 'select',
			'options'  => array(
				'before' => esc_html__( 'Before', 'bricksfly-elements-for-bricks' ),
				'after'  => esc_html__( 'After', 'bricksfly-elements-for-bricks' ),
			),
			'default'  => 'after',
			'required' => array( array( 'show_button', '!=', '' ) ),
		);

		$this->controls['btn_style_type'] = array(
			'group'    => 'button_content',
			'label'    => esc_html__( 'Style Type', 'bricksfly-elements-for-bricks' ),
			'type'     => 'select',
			'options'  => array(
				'solid'     => esc_html__( 'Solid', 'bricksfly-elements-for-bricks' ),
				'outline'   => esc_html__( 'Outline', 'bricksfly-elements-for-bricks' ),
				'ghost'     => esc_html__( 'Ghost (Text)', 'bricksfly-elements-for-bricks' ),
				'underline' => esc_html__( 'Underline', 'bricksfly-elements-for-bricks' ),
			),
			'default'  => 'solid',
			'required' => array( array( 'show_button', '!=', '' ) ),
		);

		$this->controls['btn_hover_type'] = array(
			'group'    => 'button_content',
			'label'    => esc_html__( 'Hover Effect', 'bricksfly-elements-for-bricks' ),
			'type'     => 'select',
			'options'  => array(
				'none'        => esc_html__( 'None', 'bricksfly-elements-for-bricks' ),
				'fill-left'   => esc_html__( 'Fill Left', 'bricksfly-elements-for-bricks' ),
				'fill-right'  => esc_html__( 'Fill Right', 'bricksfly-elements-for-bricks' ),
				'fill-top'    => esc_html__( 'Fill Top', 'bricksfly-elements-for-bricks' ),
				'fill-bottom' => esc_html__( 'Fill Bottom', 'bricksfly-elements-for-bricks' ),
				'shrink'      => esc_html__( 'Shrink', 'bricksfly-elements-for-bricks' ),
				'grow'        => esc_html__( 'Grow', 'bricksfly-elements-for-bricks' ),
				'icon-slide'  => esc_html__( 'Icon Slide', 'bricksfly-elements-for-bricks' ),
			),
			'default'  => 'none',
			'required' => array( array( 'show_button', '!=', '' ) ),
		);

		$this->controls['btn_size'] = array(
			'group'    => 'button_content',
			'label'    => esc_html__( 'Size', 'bricksfly-elements-for-bricks' ),
			'type'     => 'select',
			'options'  => array(
				'xs' => 'XS',
				'sm' => 'SM',
				'md' => 'MD',
				'lg' => 'LG',
			),
			'default'  => 'sm',
			'required' => array( array( 'show_button', '!=', '' ) ),
		);

		$this->controls['btn_full_width'] = array(
			'group'    => 'button_content',
			'label'    => esc_html__( 'Full Width', 'bricksfly-elements-for-bricks' ),
			'type'     => 'checkbox',
			'required' => array( array( 'show_button', '!=', '' ) ),
		);

		/*
		=========================
			BOX STYLE
		========================= */

		$this->controls['box_bg'] = array(
			'group' => 'box_style',
			'label' => esc_html__( 'Background', 'bricksfly-elements-for-bricks' ),
			'type'  => 'background',
			'css'   => array(
				array(
					'property' => 'background',
					'selector' => '.aab-icon-box',
				),
			),
		);

		$this->controls['box_padding'] = array(
			'group' => 'box_style',
			'label' => esc_html__( 'Padding', 'bricksfly-elements-for-bricks' ),
			'type'  => 'dimensions',
			'css'   => array(
				array(
					'property' => 'padding',
					'selector' => '.aab-icon-box',
				),
			),
		);

		$this->controls['box_border'] = array(
			'group' => 'box_style',
			'label' => esc_html__( 'Border', 'bricksfly-elements-for-bricks' ),
			'type'  => 'border',
			'css'   => array(
				array(
					'property' => 'border',
					'selector' => '.aab-icon-box:not(.aab-icon-box--flip-card) ',
				),
				array(
					'property' => 'border-radius',
					'selector' => '.aab-icon-box--flip-card .aab-icon-box__flip-front, .aab-icon-box--flip-card .aab-icon-box__flip-back',
				),
			),
		);

		// $this->controls['box_border'] = [
		// 'group' => 'box_style',
		// 'label' => esc_html__('Border', 'bricksfly-elements-for-bricks'),
		// 'type'  => 'border',
		// 'css'   => [
		// [
		// 'property' => 'border',
		// 'selector' => '.aab-icon-box',
		// ],
		// [
		// 'property' => 'border-radius',
		// 'selector' => '.aab-icon-box--flip-card .aab-icon-box__flip-front, .aab-icon-box--flip-card .aab-icon-box__flip-back',
		// ],
		// ],
		// ];

		$this->controls['box_shadow'] = array(
			'group' => 'box_style',
			'label' => esc_html__( 'Shadow', 'bricksfly-elements-for-bricks' ),
			'type'  => 'box-shadow',
			'css'   => array(
				array(
					'property' => 'box-shadow',
					'selector' => '.aab-icon-box',
				),
			),
		);

		$this->controls['box_h_bg'] = array(
			'group' => 'box_style',
			'label' => esc_html__( 'Hover Background', 'bricksfly-elements-for-bricks' ),
			'type'  => 'background',
			'css'   => array(
				array(
					'property' => 'background',
					'selector' => '.aab-icon-box:hover',
				),
			),
		);

		$this->controls['box_h_border_color'] = array(
			'group' => 'box_style',
			'label' => esc_html__( 'Hover Border Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'border-color',
					'selector' => '.aab-icon-box:hover',
				),
			),
		);

		$this->controls['box_h_shadow'] = array(
			'group' => 'box_style',
			'label' => esc_html__( 'Hover Shadow', 'bricksfly-elements-for-bricks' ),
			'type'  => 'box-shadow',
			'css'   => array(
				array(
					'property' => 'box-shadow',
					'selector' => '.aab-icon-box:hover',
				),
			),
		);

		$this->controls['box_transition'] = array(
			'group'   => 'box_style',
			'label'   => esc_html__( 'Transition (ms)', 'bricksfly-elements-for-bricks' ),
			'type'    => 'number',
			'default' => 300,
			'css'     => array(
				array(
					'property' => 'transition-duration',
					'selector' => '.aab-icon-box',
					'value'    => '%sms',
				),
			),
		);

		/*
		=========================
			ICON STYLE
		========================= */

		$this->controls['icon_size'] = array(
			'group' => 'icon_style',
			'label' => esc_html__( 'Size', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => true,
			'css'   => array(
				array(
					'property' => 'font-size',
					'selector' => '.aab-icon-box__icon',
				),
			),
		);

		$this->controls['icon_color'] = array(
			'group' => 'icon_style',
			'label' => esc_html__( 'Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'color',
					'selector' => '.aab-icon-box__icon',
				),
			),
		);

		$this->controls['icon_bg'] = array(
			'group' => 'icon_style',
			'label' => esc_html__( 'Background', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'background-color',
					'selector' => '.aab-icon-box__icon',
				),
			),
		);

		$this->controls['icon_width'] = array(
			'group' => 'icon_style',
			'label' => esc_html__( 'Width / Height', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => true,
			'css'   => array(
				array(
					'property' => 'width',
					'selector' => '.aab-icon-box__icon',
				),
				array(
					'property' => 'height',
					'selector' => '.aab-icon-box__icon',
				),
			),
		);

		$this->controls['icon_border'] = array(
			'group' => 'icon_style',
			'label' => esc_html__( 'Border', 'bricksfly-elements-for-bricks' ),
			'type'  => 'border',
			'css'   => array(
				array(
					'property' => 'border',
					'selector' => '.aab-icon-box__icon',
				),
			),
		);

		$this->controls['icon_padding'] = array(
			'group' => 'icon_style',
			'label' => esc_html__( 'Padding', 'bricksfly-elements-for-bricks' ),
			'type'  => 'dimensions',
			'css'   => array(
				array(
					'property' => 'padding',
					'selector' => '.aab-icon-box__icon',
				),
			),
		);

		$this->controls['icon_shadow'] = array(
			'group' => 'icon_style',
			'label' => esc_html__( 'Shadow', 'bricksfly-elements-for-bricks' ),
			'type'  => 'box-shadow',
			'css'   => array(
				array(
					'property' => 'box-shadow',
					'selector' => '.aab-icon-box__icon',
				),
			),
		);

		$this->controls['icon_h_color'] = array(
			'group' => 'icon_style',
			'label' => esc_html__( 'Hover Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'color',
					'selector' => '.aab-icon-box:hover .aab-icon-box__icon',
				),
			),
		);

		$this->controls['icon_h_bg'] = array(
			'group' => 'icon_style',
			'label' => esc_html__( 'Hover Background', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'background-color',
					'selector' => '.aab-icon-box:hover .aab-icon-box__icon',
				),
			),
		);

		$this->controls['icon_spacing'] = array(
			'group' => 'icon_style',
			'label' => esc_html__( 'Bottom Spacing', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => true,
			'css'   => array(
				array(
					'property' => 'margin-bottom',
					'selector' => '.aab-icon-box__icon',
				),
			),
		);

		/*
		=========================
			TITLE STYLE
		========================= */

		$this->controls['title_typo'] = array(
			'group' => 'title_style',
			'label' => esc_html__( 'Typography', 'bricksfly-elements-for-bricks' ),
			'type'  => 'typography',
			'css'   => array(
				array(
					'property' => 'typography',
					'selector' => '.aab-icon-box__title',
				),
			),
		);

		$this->controls['title_color'] = array(
			'group' => 'title_style',
			'label' => esc_html__( 'Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'color',
					'selector' => '.aab-icon-box__title',
				),
			),
		);

		$this->controls['title_h_color'] = array(
			'group' => 'title_style',
			'label' => esc_html__( 'Hover Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'color',
					'selector' => '.aab-icon-box:hover .aab-icon-box__title',
				),
			),
		);

		$this->controls['title_margin'] = array(
			'group' => 'title_style',
			'label' => esc_html__( 'Margin', 'bricksfly-elements-for-bricks' ),
			'type'  => 'dimensions',
			'css'   => array(
				array(
					'property' => 'margin',
					'selector' => '.aab-icon-box__title',
				),
			),
		);

		/*
		=========================
			DESCRIPTION STYLE
		========================= */

		$this->controls['desc_typo'] = array(
			'group' => 'desc_style',
			'label' => esc_html__( 'Typography', 'bricksfly-elements-for-bricks' ),
			'type'  => 'typography',
			'css'   => array(
				array(
					'property' => 'typography',
					'selector' => '.aab-icon-box__desc',
				),
			),
		);

		$this->controls['desc_color'] = array(
			'group' => 'desc_style',
			'label' => esc_html__( 'Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'color',
					'selector' => '.aab-icon-box__desc',
				),
			),
		);

		$this->controls['desc_h_color'] = array(
			'group' => 'desc_style',
			'label' => esc_html__( 'Hover Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'color',
					'selector' => '.aab-icon-box:hover .aab-icon-box__desc',
				),
			),
		);

		$this->controls['desc_margin'] = array(
			'group' => 'desc_style',
			'label' => esc_html__( 'Margin', 'bricksfly-elements-for-bricks' ),
			'type'  => 'dimensions',
			'css'   => array(
				array(
					'property' => 'margin',
					'selector' => '.aab-icon-box__desc',
				),
			),
		);

		/*
		=========================
			BUTTON STYLE
		========================= */

		$this->controls['btn_typo'] = array(
			'group' => 'btn_style',
			'label' => esc_html__( 'Typography', 'bricksfly-elements-for-bricks' ),
			'type'  => 'typography',
			'css'   => array(
				array(
					'property' => 'typography',
					'selector' => '.aab-icon-box__btn',
				),
			),
		);

		$this->controls['btn_color'] = array(
			'group' => 'btn_style',
			'label' => esc_html__( 'Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'color',
					'selector' => '.aab-icon-box__btn',
				),
			),
		);

		$this->controls['btn_bg_color'] = array(
			'group' => 'btn_style',
			'label' => esc_html__( 'Background', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'background-color',
					'selector' => '.aab-icon-box__btn',
				),
			),
		);

		$this->controls['btn_border'] = array(
			'group' => 'btn_style',
			'label' => esc_html__( 'Border', 'bricksfly-elements-for-bricks' ),
			'type'  => 'border',
			'css'   => array(
				array(
					'property' => 'border',
					'selector' => '.aab-icon-box__btn',
				),
			),
		);

		$this->controls['btn_padding'] = array(
			'group' => 'btn_style',
			'label' => esc_html__( 'Padding', 'bricksfly-elements-for-bricks' ),
			'type'  => 'dimensions',
			'css'   => array(
				array(
					'property' => 'padding',
					'selector' => '.aab-icon-box__btn',
				),
			),
		);

		$this->controls['btn_shadow'] = array(
			'group' => 'btn_style',
			'label' => esc_html__( 'Shadow', 'bricksfly-elements-for-bricks' ),
			'type'  => 'box-shadow',
			'css'   => array(
				array(
					'property' => 'box-shadow',
					'selector' => '.aab-icon-box__btn',
				),
			),
		);

		$this->controls['btn_icon_size'] = array(
			'group' => 'btn_style',
			'label' => esc_html__( 'Icon Size', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => true,
			'css'   => array(
				array(
					'property' => 'font-size',
					'selector' => '.aab-icon-box__btn-icon',
				),
			),
		);

		$this->controls['btn_icon_gap'] = array(
			'group' => 'btn_style',
			'label' => esc_html__( 'Icon Gap', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => true,
			'css'   => array(
				array(
					'property' => 'gap',
					'selector' => '.aab-icon-box__btn',
				),
			),
		);

		/*
		=========================
			BUTTON HOVER STYLE
		========================= */

		$this->controls['btn_h_color'] = array(
			'group' => 'btn_hover_style',
			'label' => esc_html__( 'Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'color',
					'selector' => '.aab-icon-box__btn:hover',
				),
			),
		);

		$this->controls['btn_h_bg_color'] = array(
			'group' => 'btn_hover_style',
			'label' => esc_html__( 'Background', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'background-color',
					'selector' => '.aab-icon-box__btn:hover',
				),
			),
		);

		$this->controls['btn_h_border_color'] = array(
			'group' => 'btn_hover_style',
			'label' => esc_html__( 'Border Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'border-color',
					'selector' => '.aab-icon-box__btn:hover',
				),
			),
		);

		$this->controls['btn_h_shadow'] = array(
			'group' => 'btn_hover_style',
			'label' => esc_html__( 'Shadow', 'bricksfly-elements-for-bricks' ),
			'type'  => 'box-shadow',
			'css'   => array(
				array(
					'property' => 'box-shadow',
					'selector' => '.aab-icon-box__btn:hover',
				),
			),
		);

		$this->controls['btn_hover_fill_color'] = array(
			'group'    => 'btn_hover_style',
			'label'    => esc_html__( 'Fill Color', 'bricksfly-elements-for-bricks' ),
			'type'     => 'color',
			'required' => array( array( 'btn_hover_type', '=', array( 'fill-left', 'fill-right', 'fill-top', 'fill-bottom' ) ) ),
			'css'      => array(
				array(
					'property' => 'background-color',
					'selector' => '.aab-icon-box__btn::before',
				),
			),
		);

		$this->controls['btn_transition'] = array(
			'group'   => 'btn_hover_style',
			'label'   => esc_html__( 'Transition (ms)', 'bricksfly-elements-for-bricks' ),
			'type'    => 'number',
			'default' => 300,
			'css'     => array(
				array(
					'property' => 'transition-duration',
					'selector' => '.aab-icon-box__btn',
					'value'    => '%sms',
				),
			),
		);

		/*
		=========================
			FLIP BACK STYLE
		========================= */

		$this->controls['flip_back_bg'] = array(
			'group'    => 'flip_back_style',
			'label'    => esc_html__( 'Background', 'bricksfly-elements-for-bricks' ),
			'type'     => 'background',
			'required' => array( array( 'layout_preset', '=', 'flip-card' ) ),
			'css'      => array(
				array(
					'property' => 'background',
					'selector' => '.aab-icon-box__flip-back',
				),
			),
		);

		$this->controls['flip_back_padding'] = array(
			'group'    => 'flip_back_style',
			'label'    => esc_html__( 'Padding', 'bricksfly-elements-for-bricks' ),
			'type'     => 'dimensions',
			'required' => array( array( 'layout_preset', '=', 'flip-card' ) ),
			'css'      => array(
				array(
					'property' => 'padding',
					'selector' => '.aab-icon-box__flip-back',
				),
			),
		);

		// $this->controls['flip_back_border'] = [
		// 'group'    => 'flip_back_style',
		// 'label'    => esc_html__('Border', 'bricksfly-elements-for-bricks'),
		// 'type'     => 'border',
		// 'required' => [['layout_preset', '=', 'flip-card']],
		// 'css'      => [[
		// 'property' => 'border',
		// 'selector' => '.aab-icon-box__flip-back',
		// ]],
		// ];

		$this->controls['flip_back_border_color'] = array(
			'group'    => 'flip_back_style',
			'label'    => esc_html__( 'Border Color', 'bricksfly-elements-for-bricks' ),
			'type'     => 'color',
			'required' => array( array( 'layout_preset', '=', 'flip-card' ) ),
			'css'      => array(
				array(
					'property' => 'border-color',
					'selector' => '.aab-icon-box--flip-card .aab-icon-box__flip-back',
				),
			),
		);

		$this->controls['flip_back_shadow'] = array(
			'group'    => 'flip_back_style',
			'label'    => esc_html__( 'Shadow', 'bricksfly-elements-for-bricks' ),
			'type'     => 'box-shadow',
			'required' => array( array( 'layout_preset', '=', 'flip-card' ) ),
			'css'      => array(
				array(
					'property' => 'box-shadow',
					'selector' => '.aab-icon-box__flip-back',
				),
			),
		);

		$this->controls['flip_back_typo'] = array(
			'group'    => 'flip_back_style',
			'label'    => esc_html__( 'Typography', 'bricksfly-elements-for-bricks' ),
			'type'     => 'typography',
			'required' => array( array( 'layout_preset', '=', 'flip-card' ) ),
			'css'      => array(
				array(
					'property' => 'typography',
					'selector' => '.aab-icon-box__flip-back .aab-icon-box__desc',
				),
			),
		);

		// $this->controls['flip_back_color'] = [
		// 'group'    => 'flip_back_style',
		// 'label'    => esc_html__('Text Color', 'bricksfly-elements-for-bricks'),
		// 'type'     => 'color',
		// 'required' => [['layout_preset', '=', 'flip-card']],
		// 'css'      => [[
		// 'property' => 'color',
		// 'selector' => '.aab-icon-box__flip-back .aab-icon-box__desc',
		// ]],
		// ];

		$this->controls['flip_back_text_margin'] = array(
			'group'    => 'flip_back_style',
			'label'    => esc_html__( 'Text Margin', 'bricksfly-elements-for-bricks' ),
			'type'     => 'dimensions',
			'required' => array( array( 'layout_preset', '=', 'flip-card' ) ),
			'css'      => array(
				array(
					'property' => 'margin',
					'selector' => '.aab-icon-box__flip-back .aab-icon-box__desc',
				),
			),
		);
	}

	public function render() {
		$s = $this->settings;

		$layout    = $s['layout_preset'] ?? 'default';
		$direction = $s['direction'] ?? 'vertical';
		$align     = $s['align'] ?? 'center';
		$icon_pos  = $s['icon_position'] ?? 'top';

		// Flip card has its own render
		if ( $layout === 'flip-card' ) {
			$this->render_flip_card( $s );
			return;
		}

		// Box classes
		$box_classes = array(
			'aab-icon-box',
			'aab-icon-box--' . $layout,
			'aab-icon-box--' . $direction,
			'aab-icon-box--align-' . $align,
		);

		if ( in_array( $direction, array( 'horizontal', 'horizontal-reverse' ), true ) ) {
			$box_classes[] = 'aab-icon-box--icon-' . $icon_pos;
		}

		$this->set_attribute( 'box', 'class', $box_classes );

		// Box link
		$has_box_link = ! empty( $s['box_link']['url'] );

		echo wp_kses_post( '<div ' . $this->render_attributes( '_root' ) . '>' );
		echo wp_kses_post( '<div ' . $this->render_attributes( 'box' ) . '>' );

		// Icon
		if ( ! empty( $s['icon'] ) ) {
			echo '<div class="aab-icon-box__icon">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo wp_kses_post( self::render_icon( $s['icon'] ) );
			echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		// Line element for icon-left-line layout
		if ( $layout === 'icon-left-line' ) {
			echo '<div class="aab-icon-box__line"></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		// Content wrapper
		echo '<div class="aab-icon-box__content">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		// Title
		$tag = $s['title_tag'] ?? 'h3';
		$tag = in_array( $tag, array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'span', 'div' ), true ) ? $tag : 'h3';
		if ( ! empty( $s['title'] ) ) {
			echo '<' . tag_escape( $tag ) . ' class="aab-icon-box__title">' . esc_html( $s['title'] ) . '</' . tag_escape( $tag ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		// Description
		if ( ! empty( $s['description'] ) ) {
			echo '<p class="aab-icon-box__desc">' . esc_html( $s['description'] ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		// Button
		if ( ! empty( $s['show_button'] ) ) {
			$this->render_button( $s );
		}

		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		// Slide-up overlay
		if ( $layout === 'slide-up' ) {
			echo '<div class="aab-icon-box__slide-overlay">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			if ( ! empty( $s['description'] ) ) {
				echo '<p class="aab-icon-box__desc">' . esc_html( $s['description'] ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			if ( ! empty( $s['show_button'] ) ) {
				$this->render_button( $s );
			}
			echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		// Box link overlay
		if ( $has_box_link ) {
			$this->set_attribute( 'box-link', 'class', 'aab-icon-box__link-overlay' );
			$this->set_attribute( 'box-link', 'href', esc_url( $s['box_link']['url'] ) );
			if ( ! empty( $s['box_link']['newTab'] ) ) {
				$this->set_attribute( 'box-link', 'target', '_blank' );
				$this->set_attribute( 'box-link', 'rel', 'noopener noreferrer' );
			}
			echo wp_kses_post( '<a ' . $this->render_attributes( 'box-link' ) . ' aria-label="' . esc_attr( $s['title'] ?? '' ) . '"></a>' );
		}

		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	private function render_flip_card( $s ) {
		$flip_dir  = $s['flip_direction'] ?? 'horizontal';
		$back_text = $s['flip_back_text'] ?? '';

		$box_classes = array(
			'aab-icon-box',
			'aab-icon-box--flip-card',
			'aab-icon-box--flip-' . $flip_dir,
			'aab-icon-box--align-' . ( $s['align'] ?? 'center' ),
		);

		$this->set_attribute( 'box', 'class', $box_classes );

		$tag = $s['title_tag'] ?? 'h3';
		$tag = in_array( $tag, array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'span', 'div' ), true ) ? $tag : 'h3';

		echo wp_kses_post( '<div ' . $this->render_attributes( '_root' ) . '>' );
		echo wp_kses_post( '<div ' . $this->render_attributes( 'box' ) . '>' );

		// Front
		echo '<div class="aab-icon-box__flip-front">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		if ( ! empty( $s['icon'] ) ) {
			echo wp_kses_post( '<div class="aab-icon-box__icon">' . self::render_icon( $s['icon'] ) . '</div>' );
		}
		if ( ! empty( $s['title'] ) ) {
			echo '<' . tag_escape( $tag ) . ' class="aab-icon-box__title">' . esc_html( $s['title'] ) . '</' . tag_escape( $tag ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( ! empty( $s['description'] ) ) {
			echo '<p class="aab-icon-box__desc">' . esc_html( $s['description'] ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		// Back
		echo '<div class="aab-icon-box__flip-back">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		if ( ! empty( $back_text ) ) {
			echo '<p class="aab-icon-box__desc">' . esc_html( $back_text ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( ! empty( $s['show_button'] ) ) {
			$this->render_button( $s );
		}
		// Box link on back
		if ( ! empty( $s['box_link']['url'] ) ) {
			echo '<a class="aab-icon-box__link-overlay" href="' . esc_url( $s['box_link']['url'] ) . '"'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			if ( ! empty( $s['box_link']['newTab'] ) ) {
				echo ' target="_blank" rel="noopener noreferrer"'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			echo ' aria-label="' . esc_attr( $s['title'] ?? '' ) . '"></a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	private function render_button( $s ) {
		$btn_style = $s['btn_style_type'] ?? 'solid';
		$btn_hover = $s['btn_hover_type'] ?? 'none';
		$btn_size  = $s['btn_size'] ?? 'sm';
		$icon_pos  = $s['btn_icon_position'] ?? 'after';

		$btn_classes = array(
			'aab-icon-box__btn',
			'aab-icon-box__btn--' . $btn_style,
			'aab-icon-box__btn--' . $btn_size,
		);

		if ( $btn_hover !== 'none' ) {
			$btn_classes[] = 'aab-icon-box__btn--hover-' . $btn_hover;
		}

		if ( ! empty( $s['btn_full_width'] ) ) {
			$btn_classes[] = 'aab-icon-box__btn--full';
		}

		$btn_classes[] = 'aab-icon-box__btn--icon-' . $icon_pos;

		$this->set_attribute( 'btn', 'class', $btn_classes );

		if ( ! empty( $s['btn_link']['url'] ) ) {
			$this->set_attribute( 'btn', 'href', esc_url( $s['btn_link']['url'] ) );
			if ( ! empty( $s['btn_link']['newTab'] ) ) {
				$this->set_attribute( 'btn', 'target', '_blank' );
				$this->set_attribute( 'btn', 'rel', 'noopener noreferrer' );
			}
		}

		echo wp_kses_post( '<a ' . $this->render_attributes( 'btn' ) . '>' );

		if ( ! empty( $s['btn_icon'] ) && $icon_pos === 'before' ) {
			echo wp_kses_post( '<span class="aab-icon-box__btn-icon">' . self::render_icon( $s['btn_icon'] ) . '</span>' );
		}

		echo '<span class="aab-icon-box__btn-text">' . esc_html( $s['btn_text'] ?? esc_html__( 'Read More', 'bricksfly-elements-for-bricks' ) ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( ! empty( $s['btn_icon'] ) && $icon_pos === 'after' ) {
			echo wp_kses_post( '<span class="aab-icon-box__btn-icon">' . self::render_icon( $s['btn_icon'] ) . '</span>' );
		}

		echo '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
