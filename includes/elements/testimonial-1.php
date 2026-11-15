<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BRICKSFLY_Bricks_Testimonial extends \Bricks\Element {

	public $category     = 'bricks fly';
	public $name         = 'aae-testimonial';
	public $icon         = 'ti-comment-alt aab-element-marker';
	public $css_selector = '.aae-testimonial-wrapper';
	public $scripts      = array( 'aaeTestimonial' );

	public function get_label() {
		return esc_html__( 'Testimonial', 'bricksfly-elements-for-bricks' );
	}

	public function get_keywords() {
		return array( 'testimonial', 'review', 'slider', 'feedback', 'swiper' );
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

		$this->control_groups['slide_style'] = array(
			'title' => esc_html__( 'Slide', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);

		$this->control_groups['navigation_style'] = array(
			'title' => esc_html__( 'Navigation', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);

		$this->control_groups['image_style'] = array(
			'title' => esc_html__( 'Image', 'bricksfly-elements-for-bricks' ),
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
	}

	public function set_controls() {
		// === CONTENT TAB ===

		// Testimonial Style
		$this->controls['elementList'] = array(
			'group'   => 'content',
			'label'   => esc_html__( 'Testimonial Style', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'options' => array(
				''   => esc_html__( 'Default', 'bricksfly-elements-for-bricks' ),
				'1'  => esc_html__( 'One', 'bricksfly-elements-for-bricks' ),
				'2'  => esc_html__( 'Two', 'bricksfly-elements-for-bricks' ),
				'3'  => esc_html__( 'Three', 'bricksfly-elements-for-bricks' ),
				'4'  => esc_html__( 'Four', 'bricksfly-elements-for-bricks' ),
				'5'  => esc_html__( 'Five', 'bricksfly-elements-for-bricks' ),
				'6'  => esc_html__( 'Six', 'bricksfly-elements-for-bricks' ),
				'7'  => esc_html__( 'Seven', 'bricksfly-elements-for-bricks' ),
				'8'  => esc_html__( 'Eight', 'bricksfly-elements-for-bricks' ),
				'9'  => esc_html__( 'Nine', 'bricksfly-elements-for-bricks' ),
				'10' => esc_html__( 'Ten', 'bricksfly-elements-for-bricks' ),
			),
			'default' => '',
		);

		// Repeater: Testimonials
		$this->controls['testimonials'] = array(
			'group'   => 'content',
			'label'   => esc_html__( 'Testimonials', 'bricksfly-elements-for-bricks' ),
			'type'    => 'repeater',
			'fields'  => array(
				'testimonialContent' => array(
					'label'   => esc_html__( 'Content', 'bricksfly-elements-for-bricks' ),
					'type'    => 'textarea',
					'default' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'bricksfly-elements-for-bricks' ),
				),
				'testimonialImage'   => array(
					'label' => esc_html__( 'Image', 'bricksfly-elements-for-bricks' ),
					'type'  => 'image',
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
			'default' => array(
				array(
					'testimonialContent' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'bricksfly-elements-for-bricks' ),
					'testimonialName'    => esc_html__( 'John Doe', 'bricksfly-elements-for-bricks' ),
					'testimonialJob'     => esc_html__( 'Designer', 'bricksfly-elements-for-bricks' ),
				),
				array(
					'testimonialContent' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'bricksfly-elements-for-bricks' ),
					'testimonialName'    => esc_html__( 'Jane Smith', 'bricksfly-elements-for-bricks' ),
					'testimonialJob'     => esc_html__( 'Developer', 'bricksfly-elements-for-bricks' ),
				),
				array(
					'testimonialContent' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'bricksfly-elements-for-bricks' ),
					'testimonialName'    => esc_html__( 'Bob Wilson', 'bricksfly-elements-for-bricks' ),
					'testimonialJob'     => esc_html__( 'Manager', 'bricksfly-elements-for-bricks' ),
				),
			),
		);

		// Image Size
		$this->controls['imageSize'] = array(
			'group'   => 'content',
			'label'   => esc_html__( 'Image Size', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'options' => array(
				'thumbnail' => esc_html__( 'Thumbnail', 'bricksfly-elements-for-bricks' ),
				'medium'    => esc_html__( 'Medium', 'bricksfly-elements-for-bricks' ),
				'large'     => esc_html__( 'Large', 'bricksfly-elements-for-bricks' ),
				'full'      => esc_html__( 'Full', 'bricksfly-elements-for-bricks' ),
			),
			'default' => 'full',
		);

		// === SLIDER OPTIONS ===

		$this->controls['slidesToShow'] = array(
			'group'   => 'slider_options',
			'label'   => esc_html__( 'Slides to Show', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'options' => array(
				'auto' => esc_html__( 'Auto', 'bricksfly-elements-for-bricks' ),
				'1'    => '1',
				'2'    => '2',
				'3'    => '3',
				'4'    => '4',
				'5'    => '5',
				'6'    => '6',
				'7'    => '7',
				'8'    => '8',
				'9'    => '9',
				'10'   => '10',
			),
			'default' => '1',
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
			'required' => array( 'autoplay', '=', true ),
		);

		$this->controls['autoplayInteraction'] = array(
			'group'    => 'slider_options',
			'label'    => esc_html__( 'Pause on Interaction', 'bricksfly-elements-for-bricks' ),
			'type'     => 'checkbox',
			'default'  => true,
			'required' => array( 'autoplay', '=', true ),
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
			'default' => 500,
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
		);

		$this->controls['navigationPreviousIcon'] = array(
			'group'    => 'slider_options',
			'label'    => esc_html__( 'Previous Arrow Icon', 'bricksfly-elements-for-bricks' ),
			'type'     => 'icon',
			'required' => array( 'navigation', '=', array( 'both', 'arrows' ) ),
		);

		$this->controls['navigationNextIcon'] = array(
			'group'    => 'slider_options',
			'label'    => esc_html__( 'Next Arrow Icon', 'bricksfly-elements-for-bricks' ),
			'type'     => 'icon',
			'required' => array( 'navigation', '=', array( 'both', 'arrows' ) ),
		);

		$this->controls['direction'] = array(
			'group'   => 'slider_options',
			'label'   => esc_html__( 'Direction', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'options' => array(
				'ltr' => esc_html__( 'Left', 'bricksfly-elements-for-bricks' ),
				'rtl' => esc_html__( 'Right', 'bricksfly-elements-for-bricks' ),
			),
			'default' => 'ltr',
		);

		// === STYLE TAB: Slide ===

		$this->controls['slideBackground'] = array(
			'group' => 'slide_style',
			'label' => esc_html__( 'Background', 'bricksfly-elements-for-bricks' ),
			'type'  => 'background',
			'css'   => array(
				array(
					'property' => 'background',
					'selector' => '.slide',
				),
			),
		);

		$this->controls['slidePadding'] = array(
			'group' => 'slide_style',
			'label' => esc_html__( 'Padding', 'bricksfly-elements-for-bricks' ),
			'type'  => 'dimensions',
			'css'   => array(
				array(
					'property' => 'padding',
					'selector' => '.slide',
				),
			),
		);

		$this->controls['sectionBorder'] = array(
			'group' => 'slide_style',
			'label' => esc_html__( 'Border', 'bricksfly-elements-for-bricks' ),
			'type'  => 'border',
			'css'   => array(
				array(
					'property' => 'border',
					'selector' => '.slide',
				),
			),
		);

		$this->controls['slideWidth'] = array(
			'group'    => 'slide_style',
			'label'    => esc_html__( 'Width', 'bricksfly-elements-for-bricks' ),
			'type'     => 'number',
			'units'    => true,
			'css'      => array(
				array(
					'property' => 'max-width',
					'selector' => '.wcf__slider',
				),
			),
			'required' => array( 'elementList', '!=', '8' ),
		);

		// === STYLE TAB: Navigation ===

		$this->controls['navigationWidth'] = array(
			'group'    => 'navigation_style',
			'label'    => esc_html__( 'Navigation Width', 'bricksfly-elements-for-bricks' ),
			'type'     => 'number',
			'units'    => true,
			'css'      => array(
				array(
					'property' => 'width',
					'selector' => '.ts-navigation',
				),
			),
			'required' => array( 'navigation', '=', array( 'arrows', 'both' ) ),
		);

		$this->controls['arrowsSize'] = array(
			'group'    => 'navigation_style',
			'label'    => esc_html__( 'Arrows Size', 'bricksfly-elements-for-bricks' ),
			'type'     => 'number',
			'units'    => true,
			'css'      => array(
				array(
					'property' => 'font-size',
					'selector' => '.wcf-arrow',
				),
			),
			'required' => array( 'navigation', '=', array( 'arrows', 'both' ) ),
		);

		// Offsets are written as inline styles on .ts-navigation in render().
		// - arrowsOffset → flex `gap` between the two arrows (always positive).
		// - arrowsOffsetVertical → `top` on the relatively-positioned nav,
		// so siblings (pagination dots) stay put; defaults to % unit so
		// "-50%" is a natural way to center over the slider.
		// % only — keeps the offsets responsive across viewports.
		// Driven through Bricks's `'css'` array (NOT inline style on
		// .ts-navigation) so the builder patches the generated CSS live as the
		// slider is dragged. Inline style would pin the value to the last
		// server-rendered state — that's what made editor and frontend
		// disagree before.
		$this->controls['arrowsOffset'] = array(
			'group'       => 'navigation_style',
			'label'       => esc_html__( 'Arrows Offset', 'bricksfly-elements-for-bricks' ),
			'type'        => 'slider',
			'units'       => array(
				'%' => array(
					'min' => 0,
					'max' => 200,
				),
			),
			'default'     => '5%',
			'placeholder' => '5%',
			'description' => esc_html__( 'Space between the previous and next arrow.', 'bricksfly-elements-for-bricks' ),
			'css'         => array(
				array(
					'property' => 'gap',
					'selector' => '.ts-navigation',
				),
			),
			'required'    => array( 'navigation', '=', array( 'arrows', 'both' ) ),
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
			'default'     => '95%',
			'placeholder' => '95%',
			'description' => esc_html__( 'Negative pulls arrows up over the slider; positive pushes them down.', 'bricksfly-elements-for-bricks' ),
			'css'         => array(
				array(
					'property' => 'top',
					'selector' => '.ts-navigation',
				),
			),
			'required'    => array( 'navigation', '=', array( 'arrows', 'both' ) ),
		);

		$this->controls['arrowsBorder'] = array(
			'group'    => 'navigation_style',
			'label'    => esc_html__( 'Arrows Border', 'bricksfly-elements-for-bricks' ),
			'type'     => 'border',
			'css'      => array(
				array(
					'property' => 'border',
					'selector' => '.wcf-arrow',
				),
			),
			'required' => array( 'navigation', '=', array( 'arrows', 'both' ) ),
		);

		$this->controls['arrowsColor'] = array(
			'group'    => 'navigation_style',
			'label'    => esc_html__( 'Arrows Color', 'bricksfly-elements-for-bricks' ),
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
			'required' => array( 'navigation', '=', array( 'arrows', 'both' ) ),
		);

		$this->controls['arrowsBgColor'] = array(
			'group'    => 'navigation_style',
			'label'    => esc_html__( 'Arrows Background', 'bricksfly-elements-for-bricks' ),
			'type'     => 'background',
			'css'      => array(
				array(
					'property' => 'background',
					'selector' => '.wcf-arrow',
				),
			),
			'required' => array( 'navigation', '=', array( 'arrows', 'both' ) ),
		);

		$this->controls['arrowsHoverColor'] = array(
			'group'    => 'navigation_style',
			'label'    => esc_html__( 'Arrows Hover Color', 'bricksfly-elements-for-bricks' ),
			'type'     => 'color',
			'css'      => array(
				array(
					'property' => 'color',
					'selector' => '.wcf-arrow:hover',
				),
				array(
					'property' => 'fill',
					'selector' => '.wcf-arrow:hover svg',
				),
			),
			'required' => array( 'navigation', '=', array( 'arrows', 'both' ) ),
		);

		$this->controls['arrowsHoverBgColor'] = array(
			'group'    => 'navigation_style',
			'label'    => esc_html__( 'Arrows Hover Background', 'bricksfly-elements-for-bricks' ),
			'type'     => 'background',
			'css'      => array(
				array(
					'property' => 'background',
					'selector' => '.wcf-arrow:hover',
				),
			),
			'required' => array( 'navigation', '=', array( 'arrows', 'both' ) ),
		);

		$this->controls['arrowsHoverBorderColor'] = array(
			'group'    => 'navigation_style',
			'label'    => esc_html__( 'Arrows Hover Border Color', 'bricksfly-elements-for-bricks' ),
			'type'     => 'color',
			'css'      => array(
				array(
					'property' => 'border-color',
					'selector' => '.wcf-arrow:hover',
				),
			),
			'required' => array( 'navigation', '=', array( 'arrows', 'both' ) ),
		);

		$this->controls['dotsSize'] = array(
			'group'    => 'navigation_style',
			'label'    => esc_html__( 'Dots Size', 'bricksfly-elements-for-bricks' ),
			'type'     => 'number',
			'units'    => true,
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
			'required' => array( 'navigation', '=', array( 'dots', 'both' ) ),
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
			'required'    => array( 'navigation', '=', array( 'dots', 'both' ) ),
		);

		$this->controls['dotsInactiveColor'] = array(
			'group'    => 'navigation_style',
			'label'    => esc_html__( 'Dots Color', 'bricksfly-elements-for-bricks' ),
			'type'     => 'color',
			'css'      => array(
				array(
					'property' => 'background',
					'selector' => '.swiper-pagination-bullet:not(.swiper-pagination-bullet-active)',
				),
				array(
					'property' => 'color',
					'selector' => '.swiper-pagination-current, .swiper-pagination-total',
				),
			),
			'required' => array( 'navigation', '=', array( 'dots', 'both' ) ),
		);

		$this->controls['dotsActiveColor'] = array(
			'group'    => 'navigation_style',
			'label'    => esc_html__( 'Dots Active Color', 'bricksfly-elements-for-bricks' ),
			'type'     => 'color',
			'css'      => array(
				array(
					'property' => 'background',
					'selector' => '.swiper-pagination-bullet',
				),
				array(
					'property' => 'color',
					'selector' => '.swiper-pagination-current',
				),
			),
			'required' => array( 'navigation', '=', array( 'dots', 'both' ) ),
		);

		// === STYLE TAB: Image ===

		$this->controls['imgWidth'] = array(
			'group' => 'image_style',
			'label' => esc_html__( 'Width', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => true,
			'css'   => array(
				array(
					'property' => 'width',
					'selector' => '.image img',
				),
			),
		);

		$this->controls['imgHeight'] = array(
			'group' => 'image_style',
			'label' => esc_html__( 'Height', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => true,
			'css'   => array(
				array(
					'property' => 'height',
					'selector' => '.image img',
				),
			),
		);

		$this->controls['objectFit'] = array(
			'group'   => 'image_style',
			'label'   => esc_html__( 'Object Fit', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'options' => array(
				''        => esc_html__( 'Default', 'bricksfly-elements-for-bricks' ),
				'fill'    => esc_html__( 'Fill', 'bricksfly-elements-for-bricks' ),
				'cover'   => esc_html__( 'Cover', 'bricksfly-elements-for-bricks' ),
				'contain' => esc_html__( 'Contain', 'bricksfly-elements-for-bricks' ),
			),
			'css'     => array(
				array(
					'property' => 'object-fit',
					'selector' => '.image img',
				),
			),
		);

		$this->controls['objectPosition'] = array(
			'group'    => 'image_style',
			'label'    => esc_html__( 'Object Position', 'bricksfly-elements-for-bricks' ),
			'type'     => 'select',
			'options'  => array(
				'center center' => esc_html__( 'Center Center', 'bricksfly-elements-for-bricks' ),
				'center left'   => esc_html__( 'Center Left', 'bricksfly-elements-for-bricks' ),
				'center right'  => esc_html__( 'Center Right', 'bricksfly-elements-for-bricks' ),
				'top center'    => esc_html__( 'Top Center', 'bricksfly-elements-for-bricks' ),
				'top left'      => esc_html__( 'Top Left', 'bricksfly-elements-for-bricks' ),
				'top right'     => esc_html__( 'Top Right', 'bricksfly-elements-for-bricks' ),
				'bottom center' => esc_html__( 'Bottom Center', 'bricksfly-elements-for-bricks' ),
				'bottom left'   => esc_html__( 'Bottom Left', 'bricksfly-elements-for-bricks' ),
				'bottom right'  => esc_html__( 'Bottom Right', 'bricksfly-elements-for-bricks' ),
			),
			'default'  => 'center center',
			'css'      => array(
				array(
					'property' => 'object-position',
					'selector' => '.image img',
				),
			),
			'required' => array( 'objectFit', '=', 'cover' ),
		);

		$this->controls['imageBorder'] = array(
			'group' => 'image_style',
			'label' => esc_html__( 'Border', 'bricksfly-elements-for-bricks' ),
			'type'  => 'border',
			'css'   => array(
				array(
					'property' => 'border',
					'selector' => '.image img',
				),
			),
		);

		// === STYLE TAB: Content ===

		$this->controls['contentTypography'] = array(
			'group' => 'content_style',
			'label' => esc_html__( 'Typography', 'bricksfly-elements-for-bricks' ),
			'type'  => 'typography',
			'css'   => array(
				array(
					'property' => 'font',
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

		$this->controls['contentMargin'] = array(
			'group' => 'content_style',
			'label' => esc_html__( 'Margin', 'bricksfly-elements-for-bricks' ),
			'type'  => 'dimensions',
			'css'   => array(
				array(
					'property' => 'margin',
					'selector' => '.feedback',
				),
			),
		);

		// === STYLE TAB: Name ===

		$this->controls['nameTypography'] = array(
			'group' => 'name_style',
			'label' => esc_html__( 'Typography', 'bricksfly-elements-for-bricks' ),
			'type'  => 'typography',
			'css'   => array(
				array(
					'property' => 'font',
					'selector' => '.name',
				),
			),
		);

		$this->controls['nameMargin'] = array(
			'group' => 'name_style',
			'label' => esc_html__( 'Margin', 'bricksfly-elements-for-bricks' ),
			'type'  => 'dimensions',
			'css'   => array(
				array(
					'property' => 'margin',
					'selector' => '.name',
				),
			),
		);

		// === STYLE TAB: Designation ===

		$this->controls['designationTypography'] = array(
			'group' => 'designation_style',
			'label' => esc_html__( 'Typography', 'bricksfly-elements-for-bricks' ),
			'type'  => 'typography',
			'css'   => array(
				array(
					'property' => 'font',
					'selector' => '.designation',
				),
			),
		);

		$this->controls['designationMargin'] = array(
			'group' => 'designation_style',
			'label' => esc_html__( 'Margin', 'bricksfly-elements-for-bricks' ),
			'type'  => 'dimensions',
			'css'   => array(
				array(
					'property' => 'margin',
					'selector' => '.designation',
				),
			),
		);
	}

	public function enqueue_scripts() {

		wp_enqueue_style( 'bricks-font-awesome-6' );
		wp_enqueue_style( 'bricks-font-awesome-6-brands' );

		// Swiper CSS
		$posts_slider_css = BRICKSFLY_PATH . 'public/build/elements/posts-slider.css';
		wp_enqueue_style(
			'bricksfly-post-slider',
			BRICKSFLY_URL . 'public/build/elements/posts-slider.css',
			array(),
			file_exists( $posts_slider_css ) ? filemtime( $posts_slider_css ) : BRICKSFLY_VERSION
		);

		// Use the Swiper bundled with Bricks (registered by Bricks core as 'bricks-swiper').
		wp_enqueue_style( 'bricks-swiper' );
		wp_enqueue_script( 'bricks-swiper' );

		// Testimonial CSS — use filemtime() so cache busts on every edit
		// (hardcoded '1.0.0' caused stale browser cache to mask updates).
		$testimonial_css = BRICKSFLY_PATH . 'public/build/elements/testimonial.css';
		wp_enqueue_style(
			'aae-testimonial',
			BRICKSFLY_URL . 'public/build/elements/testimonial.css',
			array( 'bricks-swiper', 'bricksfly-post-slider' ),
			file_exists( $testimonial_css ) ? filemtime( $testimonial_css ) : BRICKSFLY_VERSION
		);

		// Testimonial JS — same cache-bust treatment.
		$testimonial_js = BRICKSFLY_PATH . 'public/build/elements/testimonial.js';
		wp_enqueue_script(
			'aae-testimonial',
			BRICKSFLY_URL . 'public/build/elements/testimonial.js',
			array( 'bricks-swiper' ),
			file_exists( $testimonial_js ) ? filemtime( $testimonial_js ) : BRICKSFLY_VERSION,
			true
		);
	}

	public function render() {
		$settings = $this->settings;

		if ( empty( $settings['testimonials'] ) ) {
			return $this->render_element_placeholder( array( 'title' => esc_html__( 'No testimonials added.', 'bricksfly-elements-for-bricks' ) ) );
		}

		$element_list = $settings['elementList'] ?? '';
		$nav_type     = $settings['navigation'] ?? 'both';
		$show_arrows  = in_array( $nav_type, array( 'arrows', 'both' ), true );
		$show_dots    = in_array( $nav_type, array( 'dots', 'both' ), true );
		$image_size   = $settings['imageSize'] ?? 'full';

		// Swiper requires slidesPerView to be a number or the string 'auto';
		// anything else (including a numeric string like "3") silently falls
		// back to 1 — which is what was making the frontend disagree with
		// the CSS-calc preview in the editor. Default is 1 across all
		// testimonial elements for consistency.
		$slides_to_show  = $settings['slidesToShow'] ?? '1';
		$slides_per_view = ( $slides_to_show === 'auto' ) ? 'auto' : (int) $slides_to_show;
		$space_between   = (int) ( $settings['spaceBetween'] ?? 20 );

		// Build Swiper settings as data attribute
		$slider_settings = array(
			'loop'           => ! empty( $settings['loop'] ),
			'speed'          => (int) ( $settings['speed'] ?? 500 ),
			'allowTouchMove' => ! empty( $settings['allowTouchMove'] ),
			'slidesPerView'  => $slides_per_view,
			'spaceBetween'   => $space_between,
		);

		if ( ! empty( $settings['autoplay'] ) ) {
			$slider_settings['autoplay'] = array(
				'delay'                => (int) ( $settings['autoplayDelay'] ?? 3000 ),
				'disableOnInteraction' => ! empty( $settings['autoplayInteraction'] ),
			);
		}

		if ( $show_arrows ) {
			$slider_settings['navigation'] = array(
				'nextEl' => '#brx-content .brxe-aae-testimonial[data-element-id="' . $this->id . '"] .wcf-arrow-next',
				'prevEl' => '#brx-content .brxe-aae-testimonial[data-element-id="' . $this->id . '"] .wcf-arrow-prev',
			);
		}

		if ( $show_dots ) {
			$slider_settings['pagination'] = array(
				'el'        => '#brx-content .brxe-aae-testimonial[data-element-id="' . $this->id . '"] .swiper-pagination',
				'clickable' => true,
			);

			if ( in_array( $element_list, array( '5', '6', '7', '8', '9' ), true ) ) {
				$slider_settings['pagination']['type'] = 'fraction';
			}
		}

		// Root classes
		$root_classes = array( 'aae-testimonial-wrapper', 'wcf__testimonial' );
		if ( $element_list !== '' ) {
			$root_classes[] = 'style-' . $element_list;
		}
		$this->set_attribute( '_root', 'class', $root_classes );
		$this->set_attribute( '_root', 'data-element-id', $this->id );

		// Pre-init slide-width preview vars on the root (consumed by
		// testimonial.css before Swiper initializes).
		$this->set_attribute(
			'_root',
			'style',
			sprintf( '--slides-to-show: %s; --space-between: %dpx;', esc_attr( (string) $slides_to_show ), $space_between )
		);

		// arrowsOffset / arrowsOffsetVertical / dotsOffset are now applied
		// via Bricks's `'css'` array on the controls themselves — no inline
		// style needed here.

		$direction = $settings['direction'] ?? 'ltr';

		echo wp_kses_post( '<div ' . $this->render_attributes( '_root' ) . '>' );

		echo '<div class="wcf__slider swiper" dir="' . esc_attr( $direction ) . '">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div class="swiper-wrapper" data-settings="' . esc_attr( json_encode( $slider_settings ) ) . '">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		foreach ( $settings['testimonials'] as $index => $item ) {
			$has_link   = ! empty( $item['link']['url'] );
			$link_attrs = '';
			if ( $has_link ) {
				$link_attrs = ' href="' . esc_url( $item['link']['url'] ) . '"';
				if ( ! empty( $item['link']['newTab'] ) ) {
					$link_attrs .= ' target="_blank"';
				}
				if ( ! empty( $item['link']['noFollow'] ) ) {
					$link_attrs .= ' rel="nofollow"';
				}
			}

			echo '<div class="swiper-slide">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '<div class="slide">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			// Image
			echo '<div class="image">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			$image_html = '';
			if ( ! empty( $item['testimonialImage']['id'] ) ) {
				$image_html = wp_get_attachment_image(
					$item['testimonialImage']['id'],
					$image_size,
					false,
					array(
						'class' => 'swiper-slide-image',
						'alt'   => esc_attr( $item['testimonialName'] ?? '' ),
					)
				);
			} elseif ( ! empty( $item['testimonialImage']['url'] ) ) {
				$image_html = '<img class="swiper-slide-image" src="' . esc_url( $item['testimonialImage']['url'] ) . '" alt="' . esc_attr( $item['testimonialName'] ?? '' ) . '">';
			}

			if ( $has_link && $image_html ) {
				echo wp_kses_post( '<a' . $link_attrs . '>' . $image_html . '</a>' );
			} else {
				echo wp_kses_post( $image_html );
			}
			echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			// Feedback
			echo '<div class="feedback">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo wp_kses_post( $item['testimonialContent'] ?? '' );
			echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			// Name
			if ( $has_link ) {
				echo wp_kses_post( '<a class="name"' . $link_attrs . '>' . esc_html( $item['testimonialName'] ?? '' ) . '</a>' );
			} else {
				echo '<div class="name">' . esc_html( $item['testimonialName'] ?? '' ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			// Designation
			echo '<div class="designation">' . esc_html( $item['testimonialJob'] ?? '' ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		// Navigation & Pagination (only if more than 1 slide)
		if ( count( $settings['testimonials'] ) > 1 ) {
			if ( $show_arrows ) {
				echo '<div class="ts-navigation">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo '<div class="wcf-arrow wcf-arrow-prev" role="button" tabindex="0">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$this->render_swiper_button( 'previous' );
				echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo '<div class="wcf-arrow wcf-arrow-next" role="button" tabindex="0">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$this->render_swiper_button( 'next' );
				echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			if ( $show_dots ) {
				echo '<div class="ts-pagination">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo '<div class="swiper-pagination"></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	private function render_swiper_button( $type ) {
		$settings  = $this->settings;
		$direction = $type === 'next' ? 'right' : 'left';
		$icon_key  = $type === 'next' ? 'navigationNextIcon' : 'navigationPreviousIcon';

		if ( ! empty( $settings[ $icon_key ] ) ) {
			echo wp_kses_post( self::render_icon( $settings[ $icon_key ], array( 'aria-hidden' => 'true' ) ) );
		} else {
			echo '<i class="fas fa-chevron-' . esc_attr( $direction ) . '" aria-hidden="true"></i>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}
