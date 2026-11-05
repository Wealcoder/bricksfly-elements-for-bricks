<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BRICKSFLY_Bricks_Testimonial3 extends \Bricks\Element {

	public $category     = 'bricks fly';
	public $name         = 'aae-testimonial-3';
	public $icon         = 'ti-comment-alt aab-element-marker';
	public $css_selector = '.aae-testimonial-3-wrapper';
	public $scripts      = array( 'aaeTestimonial3' );

	public function get_label() {
		return esc_html__( 'Modern Testimonial', 'bricksfly-elements-for-bricks' );
	}

	public function get_keywords() {
		return array( 'testimonial', 'modern', 'review', 'slider', 'feedback', 'swiper' );
	}

	public function set_control_groups() {
		$this->control_groups['content'] = array(
			'title' => esc_html__( 'Testimonial', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'content',
		);

		$this->control_groups['slider_options'] = array(
			'title' => esc_html__( 'Slider Options', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'content',
		);

		$this->control_groups['border_style'] = array(
			'title' => esc_html__( 'Section Border', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);

		$this->control_groups['navigation_style'] = array(
			'title' => esc_html__( 'Navigation', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);

		$this->control_groups['section_title_style'] = array(
			'title' => esc_html__( 'Section Title', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);

		$this->control_groups['content_style'] = array(
			'title' => esc_html__( 'Content', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);

		$this->control_groups['name_style'] = array(
			'title' => esc_html__( 'Name', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);

		$this->control_groups['designation_style'] = array(
			'title' => esc_html__( 'Designation', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);

		$this->control_groups['quote_style'] = array(
			'title' => esc_html__( 'Quote', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);
	}

	public function set_controls() {
		// === CONTENT TAB ===

		// Section Title
		$this->controls['sectionTitle'] = array(
			'group'   => 'content',
			'label'   => esc_html__( 'Section Title', 'bricksfly-elements-for-bricks' ),
			'type'    => 'textarea',
			'default' => esc_html__( 'MY CLIENTS SAY ME BEST ONE', 'bricksfly-elements-for-bricks' ),
		);

		$this->controls['headerTag'] = array(
			'group'   => 'content',
			'label'   => esc_html__( 'HTML Tag', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'options' => array(
				'h1'   => 'H1',
				'h2'   => 'H2',
				'h3'   => 'H3',
				'h4'   => 'H4',
				'h5'   => 'H5',
				'h6'   => 'H6',
				'div'  => 'div',
				'span' => 'span',
				'p'    => 'p',
			),
			'default' => 'h2',
			'inline'  => true,
		);

		// Repeater: Testimonials
		$this->controls['testimonials'] = array(
			'group'         => 'content',
			'label'         => esc_html__( 'Testimonials', 'bricksfly-elements-for-bricks' ),
			'type'          => 'repeater',
			'titleProperty' => 'testimonialName',
			'fields'        => array(
				'testimonialContent' => array(
					'label'   => esc_html__( 'Content', 'bricksfly-elements-for-bricks' ),
					'type'    => 'textarea',
					'default' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'bricksfly-elements-for-bricks' ),
				),
				'testimonialName'    => array(
					'label'   => esc_html__( 'Name', 'bricksfly-elements-for-bricks' ),
					'type'    => 'text',
					'default' => esc_html__( 'John Doe', 'bricksfly-elements-for-bricks' ),
				),
				'testimonialJob'     => array(
					'label'   => esc_html__( 'Designation', 'bricksfly-elements-for-bricks' ),
					'type'    => 'text',
					'default' => esc_html__( 'Designer', 'bricksfly-elements-for-bricks' ),
				),
				'link'               => array(
					'label' => esc_html__( 'Link', 'bricksfly-elements-for-bricks' ),
					'type'  => 'link',
				),
			),
			'default'       => array(
				array(
					'testimonialContent' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'bricksfly-elements-for-bricks' ),
					'testimonialName'    => esc_html__( 'John Doe', 'bricksfly-elements-for-bricks' ),
					'testimonialJob'     => esc_html__( 'Designer', 'bricksfly-elements-for-bricks' ),
				),
				array(
					'testimonialContent' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'bricksfly-elements-for-bricks' ),
					'testimonialName'    => esc_html__( 'Jane Smith', 'bricksfly-elements-for-bricks' ),
					'testimonialJob'     => esc_html__( 'Developer', 'bricksfly-elements-for-bricks' ),
				),
				array(
					'testimonialContent' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'bricksfly-elements-for-bricks' ),
					'testimonialName'    => esc_html__( 'Bob Wilson', 'bricksfly-elements-for-bricks' ),
					'testimonialJob'     => esc_html__( 'Manager', 'bricksfly-elements-for-bricks' ),
				),
				array(
					'testimonialContent' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'bricksfly-elements-for-bricks' ),
					'testimonialName'    => esc_html__( 'Alice Brown', 'bricksfly-elements-for-bricks' ),
					'testimonialJob'     => esc_html__( 'Director', 'bricksfly-elements-for-bricks' ),
				),
				array(
					'testimonialContent' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'bricksfly-elements-for-bricks' ),
					'testimonialName'    => esc_html__( 'Tom Davis', 'bricksfly-elements-for-bricks' ),
					'testimonialJob'     => esc_html__( 'Engineer', 'bricksfly-elements-for-bricks' ),
				),
			),
		);

		// Quote Icon
		$this->controls['quoteIcon'] = array(
			'group'   => 'content',
			'label'   => esc_html__( 'Quote Icon', 'bricksfly-elements-for-bricks' ),
			'type'    => 'icon',
			'default' => array(
				'library' => 'fontawesome',
				'icon'    => 'fas fa-quote-left',
			),
		);

		// === SLIDER OPTIONS ===

		$this->controls['slidesToShow'] = array(
			'group'   => 'slider_options',
			'label'   => esc_html__( 'Slides to Show', 'bricksfly-elements-for-bricks' ),
			'type'    => 'number',
			'min'     => 1,
			'max'     => 10,
			'default' => 1,
		);

		$this->controls['autoplay'] = array(
			'group'   => 'slider_options',
			'label'   => esc_html__( 'Autoplay', 'bricksfly-elements-for-bricks' ),
			'type'    => 'checkbox',
			'default' => true,
		);

		$this->controls['autoplayDelay'] = array(
			'group'    => 'slider_options',
			'label'    => esc_html__( 'Autoplay Delay (ms)', 'bricksfly-elements-for-bricks' ),
			'type'     => 'number',
			'default'  => 3000,
			'required' => array( 'autoplay', '!=', '' ),
		);

		$this->controls['autoplayInteraction'] = array(
			'group'    => 'slider_options',
			'label'    => esc_html__( 'Pause on Interaction', 'bricksfly-elements-for-bricks' ),
			'type'     => 'checkbox',
			'default'  => true,
			'required' => array( 'autoplay', '!=', '' ),
		);

		$this->controls['allowTouchMove'] = array(
			'group'   => 'slider_options',
			'label'   => esc_html__( 'Allow Touch Move', 'bricksfly-elements-for-bricks' ),
			'type'    => 'checkbox',
			'default' => false,
		);

		$this->controls['loop'] = array(
			'group'   => 'slider_options',
			'label'   => esc_html__( 'Loop', 'bricksfly-elements-for-bricks' ),
			'type'    => 'checkbox',
			'default' => true,
		);

		$this->controls['speed'] = array(
			'group'   => 'slider_options',
			'label'   => esc_html__( 'Animation Speed (ms)', 'bricksfly-elements-for-bricks' ),
			'type'    => 'number',
			'default' => 2500,
		);

		$this->controls['spaceBetween'] = array(
			'group'   => 'slider_options',
			'label'   => esc_html__( 'Space Between (px)', 'bricksfly-elements-for-bricks' ),
			'type'    => 'number',
			'default' => 20,
		);

		$this->controls['navigation'] = array(
			'group'   => 'slider_options',
			'label'   => esc_html__( 'Navigation', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'options' => array(
				'both'   => esc_html__( 'Arrows and Dots', 'bricksfly-elements-for-bricks' ),
				'arrows' => esc_html__( 'Arrows', 'bricksfly-elements-for-bricks' ),
				'dots'   => esc_html__( 'Dots', 'bricksfly-elements-for-bricks' ),
				'none'   => esc_html__( 'None', 'bricksfly-elements-for-bricks' ),
			),
			'default' => 'arrows',
			'inline'  => true,
		);

		$this->controls['prevIcon'] = array(
			'group'    => 'slider_options',
			'label'    => esc_html__( 'Previous Arrow Icon', 'bricksfly-elements-for-bricks' ),
			'type'     => 'icon',
			'default'  => array(
				'library' => 'fontawesome',
				'icon'    => 'fas fa-chevron-left',
			),
			'required' => array( 'navigation', '=', array( 'both', 'arrows' ) ),
		);

		$this->controls['nextIcon'] = array(
			'group'    => 'slider_options',
			'label'    => esc_html__( 'Next Arrow Icon', 'bricksfly-elements-for-bricks' ),
			'type'     => 'icon',
			'default'  => array(
				'library' => 'fontawesome',
				'icon'    => 'fas fa-chevron-right',
			),
			'required' => array( 'navigation', '=', array( 'both', 'arrows' ) ),
		);

		$this->controls['sliderDirection'] = array(
			'group'   => 'slider_options',
			'label'   => esc_html__( 'Direction', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'options' => array(
				'ltr' => esc_html__( 'Left to Right', 'bricksfly-elements-for-bricks' ),
				'rtl' => esc_html__( 'Right to Left', 'bricksfly-elements-for-bricks' ),
			),
			'default' => 'ltr',
			'inline'  => true,
		);

		// === STYLE TAB ===

		// Section Border
		$this->controls['sectionBorderColor'] = array(
			'group' => 'border_style',
			'label' => esc_html__( 'Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'border-inline-end-color',
					'selector' => '.aae-testimonial-3-wrapper.style-1 .quote',
				),
				array(
					'property' => 'background-color',
					'selector' => '.aae-testimonial-3-wrapper.style-1 .quote::before',
				),
				array(
					'property' => 'background-color',
					'selector' => '.aae-testimonial-3-wrapper.style-1 .quote::after',
				),
				array(
					'property' => 'border-color',
					'selector' => '.aae-testimonial-3-wrapper.style-1',
				),
				array(
					'property' => 'border-top-color',
					'selector' => '.aae-testimonial-3-wrapper.style-1 .wcf-arrow-next',
				),
				array(
					'property' => 'background-color',
					'selector' => '.aae-testimonial-3-wrapper.style-1 .wcf-arrow-next::before',
				),
				array(
					'property' => 'border-inline-end-color',
					'selector' => '.aae-testimonial-3-wrapper.style-1 .main-title',
				),
				array(
					'property' => 'background-color',
					'selector' => '.aae-testimonial-3-wrapper.style-1 .main-title::before',
				),
				array(
					'property' => 'background-color',
					'selector' => '.aae-testimonial-3-wrapper.style-1 .main-title::after',
				),
				array(
					'property' => 'border-inline-end-color',
					'selector' => '.aae-testimonial-3-wrapper.style-1 .wcf__slider',
				),
				array(
					'property' => 'background-color',
					'selector' => '.aae-testimonial-3-wrapper.style-1 .ts-navigation::after',
				),
				array(
					'property' => 'background-color',
					'selector' => '.aae-testimonial-3-wrapper.style-1 .ts-navigation::before',
				),
			),
		);

		// Navigation Style
		$this->controls['arrowsSize'] = array(
			'group'    => 'navigation_style',
			'label'    => esc_html__( 'Arrow Size', 'bricksfly-elements-for-bricks' ),
			'type'     => 'slider',
			'units'    => array(
				'px' => array(
					'min'  => 10,
					'max'  => 60,
					'step' => 1,
				),
			),
			'css'      => array(
				array(
					'property' => 'font-size',
					'selector' => '.wcf-arrow',
				),
			),
			'required' => array( 'navigation', '=', array( 'both', 'arrows' ) ),
		);

		$this->controls['arrowsColor'] = array(
			'group'    => 'navigation_style',
			'label'    => esc_html__( 'Arrow Color', 'bricksfly-elements-for-bricks' ),
			'type'     => 'color',
			'css'      => array(
				array(
					'property' => 'color',
					'selector' => '.wcf-arrow',
				),
				array(
					'property' => 'fill',
					'selector' => '.wcf-arrow svg',
				),
			),
			'required' => array( 'navigation', '=', array( 'both', 'arrows' ) ),
		);

		// Driven through Bricks's `'css'` array so the builder patches the
		// generated CSS live as the slider is dragged.
		$this->controls['arrowsOffset'] = array(
			'group'       => 'navigation_style',
			'label'       => esc_html__( 'Arrows Offset', 'bricksfly-elements-for-bricks' ),
			'type'        => 'slider',
			'units'       => array(
				'%' => array(
					'min' => 0,
					'max' => 100,
				),
			),
			'default'     => '0%',
			'placeholder' => '0%',
			'description' => esc_html__( 'Space between the previous and next arrow.', 'bricksfly-elements-for-bricks' ),
			'css'         => array(
				array(
					'property' => 'gap',
					'selector' => '.ts-navigation',
				),
			),
			'required'    => array( 'navigation', '=', array( 'both', 'arrows' ) ),
		);

		$this->controls['arrowsOffsetVertical'] = array(
			'group'       => 'navigation_style',
			'label'       => esc_html__( 'Arrows Offset (Vertical)', 'bricksfly-elements-for-bricks' ),
			'type'        => 'slider',
			'units'       => array(
				'%' => array(
					'min' => -100,
					'max' => 100,
				),
			),
			'default'     => '0%',
			'placeholder' => '0%',
			'description' => esc_html__( 'Negative pulls arrows up over the slider; positive pushes them down.', 'bricksfly-elements-for-bricks' ),
			'css'         => array(
				array(
					'property' => 'top',
					'selector' => '.ts-navigation',
				),
			),
			'required'    => array( 'navigation', '=', array( 'both', 'arrows' ) ),
		);

		$this->controls['dotsSize'] = array(
			'group'    => 'navigation_style',
			'label'    => esc_html__( 'Dot Size', 'bricksfly-elements-for-bricks' ),
			'type'     => 'slider',
			'units'    => array(
				'px' => array(
					'min'  => 5,
					'max'  => 20,
					'step' => 1,
				),
			),
			'css'      => array(
				array(
					'property' => 'width',
					'selector' => '.swiper-pagination-bullet',
				),
				array(
					'property' => 'height',
					'selector' => '.swiper-pagination-bullet',
				),
			),
			'required' => array( 'navigation', '=', array( 'both', 'dots' ) ),
		);

		$this->controls['dotsColor'] = array(
			'group'    => 'navigation_style',
			'label'    => esc_html__( 'Dot Color', 'bricksfly-elements-for-bricks' ),
			'type'     => 'color',
			'css'      => array(
				array(
					'property' => 'background',
					'selector' => '.swiper-pagination-bullet:not(.swiper-pagination-bullet-active)',
				),
			),
			'required' => array( 'navigation', '=', array( 'both', 'dots' ) ),
		);

		$this->controls['dotsActiveColor'] = array(
			'group'    => 'navigation_style',
			'label'    => esc_html__( 'Dot Active Color', 'bricksfly-elements-for-bricks' ),
			'type'     => 'color',
			'css'      => array(
				array(
					'property' => 'background',
					'selector' => '.swiper-pagination-bullet',
				),
			),
			'required' => array( 'navigation', '=', array( 'both', 'dots' ) ),
		);

		// .ts-pagination is absolutely positioned (position: absolute;
		// width: 100%; flex centered — see base CSS). This control writes
		// `bottom: <value>` on it. px is the natural default unit.
		$this->controls['dotsOffset'] = array(
			'group'       => 'navigation_style',
			'label'       => esc_html__( 'Dots Offset', 'bricksfly-elements-for-bricks' ),
			'type'        => 'number',
			'units'       => true,
			'placeholder' => '0px',
			'description' => esc_html__( 'Distance of the dots row from the bottom of the slider.', 'bricksfly-elements-for-bricks' ),
			'css'         => array(
				array(
					'property' => 'bottom',
					'selector' => '.ts-pagination',
				),
			),
			'required'    => array( 'navigation', '=', array( 'both', 'dots' ) ),
		);

		// Section Title Style
		$this->controls['secTitleTypography'] = array(
			'group' => 'section_title_style',
			'label' => esc_html__( 'Typography', 'bricksfly-elements-for-bricks' ),
			'type'  => 'typography',
			'css'   => array(
				array(
					'property' => 'typography',
					'selector' => '.title',
				),
			),
		);

		$this->controls['secTitleTextShadow'] = array(
			'group' => 'section_title_style',
			'label' => esc_html__( 'Text Shadow', 'bricksfly-elements-for-bricks' ),
			'type'  => 'text-shadow',
			'css'   => array(
				array(
					'property' => 'text-shadow',
					'selector' => '.title',
				),
			),
		);

		$this->controls['secTitlePadding'] = array(
			'group' => 'section_title_style',
			'label' => esc_html__( 'Section Padding', 'bricksfly-elements-for-bricks' ),
			'type'  => 'dimensions',
			'css'   => array(
				array(
					'property' => 'padding',
					'selector' => '.main-title',
				),
			),
		);

		// Content Style
		$this->controls['slideWrapperPadding'] = array(
			'group' => 'content_style',
			'label' => esc_html__( 'Wrapper Padding', 'bricksfly-elements-for-bricks' ),
			'type'  => 'dimensions',
			'css'   => array(
				array(
					'property' => 'padding',
					'selector' => '.slide',
				),
			),
		);

		$this->controls['contentTypography'] = array(
			'group' => 'content_style',
			'label' => esc_html__( 'Typography', 'bricksfly-elements-for-bricks' ),
			'type'  => 'typography',
			'css'   => array(
				array(
					'property' => 'typography',
					'selector' => '.feedback',
				),
			),
		);

		$this->controls['contentTextShadow'] = array(
			'group' => 'content_style',
			'label' => esc_html__( 'Text Shadow', 'bricksfly-elements-for-bricks' ),
			'type'  => 'text-shadow',
			'css'   => array(
				array(
					'property' => 'text-shadow',
					'selector' => '.feedback',
				),
			),
		);

		$this->controls['contentPadding'] = array(
			'group' => 'content_style',
			'label' => esc_html__( 'Padding', 'bricksfly-elements-for-bricks' ),
			'type'  => 'dimensions',
			'css'   => array(
				array(
					'property' => 'padding',
					'selector' => '.feedback',
				),
			),
		);

		// Name Style
		$this->controls['nameTypography'] = array(
			'group' => 'name_style',
			'label' => esc_html__( 'Typography', 'bricksfly-elements-for-bricks' ),
			'type'  => 'typography',
			'css'   => array(
				array(
					'property' => 'typography',
					'selector' => '.name',
				),
			),
		);

		$this->controls['nameTextShadow'] = array(
			'group' => 'name_style',
			'label' => esc_html__( 'Text Shadow', 'bricksfly-elements-for-bricks' ),
			'type'  => 'text-shadow',
			'css'   => array(
				array(
					'property' => 'text-shadow',
					'selector' => '.name',
				),
			),
		);

		$this->controls['namePadding'] = array(
			'group' => 'name_style',
			'label' => esc_html__( 'Padding', 'bricksfly-elements-for-bricks' ),
			'type'  => 'dimensions',
			'css'   => array(
				array(
					'property' => 'padding',
					'selector' => '.name',
				),
			),
		);

		// Designation Style
		$this->controls['designationTypography'] = array(
			'group' => 'designation_style',
			'label' => esc_html__( 'Typography', 'bricksfly-elements-for-bricks' ),
			'type'  => 'typography',
			'css'   => array(
				array(
					'property' => 'typography',
					'selector' => '.designation',
				),
			),
		);

		$this->controls['designationTextShadow'] = array(
			'group' => 'designation_style',
			'label' => esc_html__( 'Text Shadow', 'bricksfly-elements-for-bricks' ),
			'type'  => 'text-shadow',
			'css'   => array(
				array(
					'property' => 'text-shadow',
					'selector' => '.designation',
				),
			),
		);

		$this->controls['designationPadding'] = array(
			'group' => 'designation_style',
			'label' => esc_html__( 'Padding', 'bricksfly-elements-for-bricks' ),
			'type'  => 'dimensions',
			'css'   => array(
				array(
					'property' => 'padding',
					'selector' => '.designation',
				),
			),
		);

		// Quote Style
		$this->controls['quoteColor'] = array(
			'group' => 'quote_style',
			'label' => esc_html__( 'Quote Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'color',
					'selector' => '.quote-icon i',
				),
				array(
					'property' => 'fill',
					'selector' => '.quote-icon svg',
				),
			),
		);

		// number + units: true so Bricks emits CSS values with the unit
		// appended (e.g. "50px") — slider with `units: ['px' => [...]]`
		// sometimes drops the unit, producing invalid `font-size: 50`.
		// Target the actual icon elements (i for Font Awesome, svg for the
		// SVG library) so the size applies regardless of icon type.
		$this->controls['quoteSize'] = array(
			'group' => 'quote_style',
			'label' => esc_html__( 'Quote Size', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => true,
			'css'   => array(
				array(
					'property' => 'font-size',
					'selector' => '.quote-icon',
				),
				array(
					'property' => 'font-size',
					'selector' => '.quote-icon i',
				),
				array(
					'property' => 'width',
					'selector' => '.quote-icon svg',
				),
				array(
					'property' => 'height',
					'selector' => '.quote-icon svg',
				),
			),
		);

		$this->controls['quotePadding'] = array(
			'group' => 'quote_style',
			'label' => esc_html__( 'Padding', 'bricksfly-elements-for-bricks' ),
			'type'  => 'dimensions',
			'css'   => array(
				array(
					'property' => 'padding',
					'selector' => '.quote-icon',
				),
			),
		);
	}

	public function enqueue_scripts() {
		// Use the Swiper bundled with Bricks (registered by Bricks core as 'bricks-swiper').
		wp_enqueue_style( 'bricks-swiper' );
		wp_enqueue_script( 'bricks-swiper' );

		wp_enqueue_style( 'bricks-font-awesome-6' );
		wp_enqueue_style( 'bricks-font-awesome-6-brands' );

		// filemtime() so the URL changes whenever the file does — without
		// this the browser caches the old file forever (hardcoded '1.0.0').
		$css_file = BRICKSFLY_PATH . 'public/build/elements/testimonial-3.css';
		wp_enqueue_style(
			'aae-testimonial-3',
			BRICKSFLY_URL . 'public/build/elements/testimonial-3.css',
			array( 'bricks-swiper' ),
			file_exists( $css_file ) ? filemtime( $css_file ) : BRICKSFLY_VERSION
		);

		$js_file = BRICKSFLY_PATH . 'public/build/elements/testimonial-3.js';
		wp_enqueue_script(
			'aae-testimonial-3',
			BRICKSFLY_URL . 'public/build/elements/testimonial-3.js',
			array( 'bricks-swiper' ),
			file_exists( $js_file ) ? filemtime( $js_file ) : BRICKSFLY_VERSION,
			true
		);
	}

	public function render() {
		$settings = $this->settings;

		if ( empty( $settings['testimonials'] ) ) {
			return $this->render_element_placeholder(
				array(
					'icon-class' => 'ti-comment-alt',
					'text'       => esc_html__( 'No testimonials added.', 'bricksfly-elements-for-bricks' ),
				)
			);
		}

		$show_arrows = in_array( $settings['navigation'] ?? 'both', array( 'arrows', 'both' ) );
		$show_dots   = in_array( $settings['navigation'] ?? 'both', array( 'dots', 'both' ) );
		$header_tag  = $settings['headerTag'] ?? 'h2';
		$direction   = $settings['sliderDirection'] ?? 'ltr';

		// Validate header tag
		$allowed_tags = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p' );
		if ( ! in_array( $header_tag, $allowed_tags ) ) {
			$header_tag = 'h2';
		}

		// Build Swiper options
		$slider_options = array(
			'loop'           => ! empty( $settings['loop'] ),
			'speed'          => (int) ( $settings['speed'] ?? 2500 ),
			'allowTouchMove' => ! empty( $settings['allowTouchMove'] ),
			'slidesPerView'  => (int) ( $settings['slidesToShow'] ?? 1 ),
			'spaceBetween'   => (int) ( $settings['spaceBetween'] ?? 20 ),
		);

		if ( ! empty( $settings['autoplay'] ) ) {
			$slider_options['autoplay'] = array(
				'delay'                => (int) ( $settings['autoplayDelay'] ?? 3000 ),
				'disableOnInteraction' => ! empty( $settings['autoplayInteraction'] ),
			);
		}

		if ( $show_arrows ) {
			$slider_options['navigation'] = array(
				'nextEl' => '#aae-ts3-' . $this->id . ' .wcf-arrow-next',
				'prevEl' => '#aae-ts3-' . $this->id . ' .wcf-arrow-prev',
			);
		}

		if ( $show_dots ) {
			$slider_options['pagination'] = array(
				'el'        => '#aae-ts3-' . $this->id . ' .swiper-pagination',
				'clickable' => true,
			);
		}

		// Do NOT override the root id — Bricks scopes its generated per-element
		// CSS to `#brxe-XXX`. Overriding the id breaks every style control on
		// this element. The JS resolves navigation/pagination via DOM queries,
		// so the custom id isn't needed.
		$this->set_attribute( '_root', 'class', array( 'aae-testimonial-3-wrapper', 'style-1' ) );
		$this->set_attribute( '_root', 'data-swiper-options', wp_json_encode( $slider_options ) );

		// arrowsOffset / arrowsOffsetVertical / dotsOffset are now applied
		// via Bricks's `'css'` array on the controls themselves — no inline
		// style needed here.
		?>
		<?php echo wp_kses_post( '<div ' . $this->render_attributes( '_root' ) . '>' ); ?>

			<div class="quote quote-icon">
				<?php
				if ( ! empty( $settings['quoteIcon'] ) ) {
					echo wp_kses_post( self::render_icon( $settings['quoteIcon'], array( 'aria-hidden' => 'true' ) ) );
				}
				?>
			</div>

			<div class="main-title">
				<<?php echo esc_html( $header_tag ); ?> class="title"><?php echo esc_html( $settings['sectionTitle'] ?? '' ); ?></<?php echo esc_html( $header_tag ); ?>>
			</div>

			<div class="wcf__slider swiper" dir="<?php echo esc_attr( $direction ); ?>">
				<div class="swiper-wrapper">
					<?php foreach ( $settings['testimonials'] as $index => $item ) : ?>
						<?php
						$has_link = ! empty( $item['link']['url'] );
						if ( $has_link ) {
							$this->set_link_attributes( "ts3-link-{$index}", $item['link'] );
						}
						?>
						<div class="swiper-slide">
							<div class="slide">
								<div class="feedback">
									<?php echo wp_kses_post( $item['testimonialContent'] ?? '' ); ?>
								</div>
								<?php if ( $has_link ) : ?>
									<?php echo wp_kses_post( '<a class="name" ' . $this->render_attributes( "ts3-link-{$index}" ) . '>' ); ?><?php echo esc_html( $item['testimonialName'] ?? '' ); ?></a>
								<?php else : ?>
									<div class="name"><?php echo esc_html( $item['testimonialName'] ?? '' ); ?></div>
								<?php endif; ?>
								<div class="designation"><?php echo esc_html( $item['testimonialJob'] ?? '' ); ?></div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<?php if ( count( $settings['testimonials'] ) > 1 ) : ?>
				<?php if ( $show_arrows ) : ?>
					<div class="ts-navigation">
						<div class="wcf-arrow wcf-arrow-prev" role="button" tabindex="0">
							<?php $this->render_arrow_icon( 'prev' ); ?>
						</div>
						<div class="wcf-arrow wcf-arrow-next" role="button" tabindex="0">
							<?php $this->render_arrow_icon( 'next' ); ?>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( $show_dots ) : ?>
					<div class="ts-pagination">
						<div class="swiper-pagination"></div>
					</div>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php
	}

	private function render_arrow_icon( $type ) {
		$settings = $this->settings;
		$key      = $type === 'prev' ? 'prevIcon' : 'nextIcon';

		if ( ! empty( $settings[ $key ] ) ) {
			echo wp_kses_post( self::render_icon( $settings[ $key ], array( 'aria-hidden' => 'true' ) ) );
		} else {
			$direction = $type === 'prev' ? 'left' : 'right';
			echo '<i class="fas fa-chevron-' . esc_attr( $direction ) . '"></i>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}
