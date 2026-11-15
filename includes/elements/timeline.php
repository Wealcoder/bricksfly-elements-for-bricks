<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BRICKSFLY_Bricks_Timeline extends \Bricks\Element {

	public $category     = 'bricks fly';
	public $name         = 'aae-timeline';
	public $icon         = 'ti-time aab-element-marker';
	public $css_selector = '.aae-timeline-wrapper';

	public function get_label() {
		return esc_html__( 'Timeline', 'bricksfly-elements-for-bricks' );
	}

	public function get_keywords() {
		return array( 'timeline', 'step', 'history', 'journey' );
	}

	public function set_control_groups() {
		$this->control_groups['layout']         = array(
			'title' => esc_html__( 'Layout', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'content',
		);
		$this->control_groups['timeline']       = array(
			'title' => esc_html__( 'Timeline', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'content',
		);
		$this->control_groups['timeline_style'] = array(
			'title' => esc_html__( 'Timeline', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);
		$this->control_groups['content_style']  = array(
			'title' => esc_html__( 'Content', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);
		$this->control_groups['step_style']     = array(
			'title' => esc_html__( 'Step', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);
	}

	public function set_controls() {
		// === LAYOUT ===
		$this->controls['elementList'] = array(
			'group'   => 'layout',
			'label'   => esc_html__( 'Style', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'options' => array(
				'1' => esc_html__( 'One', 'bricksfly-elements-for-bricks' ),
				'2' => esc_html__( 'Two', 'bricksfly-elements-for-bricks' ),
			),
			'default' => '1',
			'inline'  => true,
		);

		$this->controls['titleTag'] = array(
			'group'   => 'layout',
			'label'   => esc_html__( 'Title HTML Tag', 'bricksfly-elements-for-bricks' ),
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
			'default' => 'h3',
			'inline'  => true,
		);

		$this->controls['dateIcon'] = array(
			'group'   => 'layout',
			'label'   => esc_html__( 'Date Icon', 'bricksfly-elements-for-bricks' ),
			'type'    => 'icon',
			'default' => array(
				'library' => 'fontawesome',
				'icon'    => 'fas fa-calendar-alt',
			),
		);

		$this->controls['imageSize'] = array(
			'group'   => 'layout',
			'label'   => esc_html__( 'Image Size', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'options' => $this->control_options['imageSizes'] ?? array(
				'thumbnail' => esc_html__( 'Thumbnail', 'bricksfly-elements-for-bricks' ),
				'medium'    => esc_html__( 'Medium', 'bricksfly-elements-for-bricks' ),
				'large'     => esc_html__( 'Large', 'bricksfly-elements-for-bricks' ),
				'full'      => esc_html__( 'Full', 'bricksfly-elements-for-bricks' ),
			),
			'default' => 'full',
		);

		$this->controls['imagePosition'] = array(
			'group'   => 'layout',
			'label'   => esc_html__( 'Image Position', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'options' => array(
				'column' => esc_html__( 'Top', 'bricksfly-elements-for-bricks' ),
				'row'    => esc_html__( 'Aside', 'bricksfly-elements-for-bricks' ),
			),
			'default' => 'column',
			'inline'  => true,
			'css'     => array(
				array(
					'property' => 'flex-direction',
					'selector' => '.content-wrap',
				),
			),
		);

		$this->controls['align'] = array(
			'group' => 'layout',
			'label' => esc_html__( 'Alignment', 'bricksfly-elements-for-bricks' ),
			'type'  => 'text-align',
			'css'   => array(
				array(
					'property' => 'text-align',
					'selector' => '',
				),
			),
		);

		$this->controls['showIndicator'] = array(
			'group'   => 'layout',
			'label'   => esc_html__( 'Indicator', 'bricksfly-elements-for-bricks' ),
			'type'    => 'checkbox',
			'default' => true,
		);

		// === TIMELINE (repeater) ===
		$this->controls['timelineItems'] = array(
			'group'         => 'timeline',
			'label'         => esc_html__( 'Timelines', 'bricksfly-elements-for-bricks' ),
			'type'          => 'repeater',
			'titleProperty' => 'timelineTitle',
			'fields'        => array(
				'stepType'      => array(
					'label'   => esc_html__( 'Step Type', 'bricksfly-elements-for-bricks' ),
					'type'    => 'select',
					'options' => array(
						'icon' => esc_html__( 'Icon', 'bricksfly-elements-for-bricks' ),
						'text' => esc_html__( 'Text', 'bricksfly-elements-for-bricks' ),
					),
					'default' => 'icon',
					'inline'  => true,
				),
				'stepIcon'      => array(
					'label'    => esc_html__( 'Step Icon', 'bricksfly-elements-for-bricks' ),
					'type'     => 'icon',
					'default'  => array(
						'library' => 'fontawesome',
						'icon'    => 'fas fa-dot-circle',
					),
					'required' => array( 'stepType', '=', 'icon' ),
				),
				'stepText'      => array(
					'label'    => esc_html__( 'Step Text', 'bricksfly-elements-for-bricks' ),
					'type'     => 'text',
					'default'  => '01',
					'required' => array( 'stepType', '=', 'text' ),
				),
				'timelineImage' => array(
					'label' => esc_html__( 'Choose Image', 'bricksfly-elements-for-bricks' ),
					'type'  => 'image',
				),
				'timelineDate'  => array(
					'label'   => esc_html__( 'Date', 'bricksfly-elements-for-bricks' ),
					'type'    => 'text',
					'default' => esc_html__( 'Jan 01, 2021', 'bricksfly-elements-for-bricks' ),
				),
				'timelineTitle' => array(
					'label'   => esc_html__( 'Title', 'bricksfly-elements-for-bricks' ),
					'type'    => 'text',
					'default' => esc_html__( 'Journey Started at New York', 'bricksfly-elements-for-bricks' ),
				),
				'timelineSub'   => array(
					'label'   => esc_html__( 'Sub Title', 'bricksfly-elements-for-bricks' ),
					'type'    => 'text',
					'default' => esc_html__( 'Designer', 'bricksfly-elements-for-bricks' ),
				),
				'timelineDesc'  => array(
					'label'   => esc_html__( 'Content', 'bricksfly-elements-for-bricks' ),
					'type'    => 'textarea',
					'default' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'bricksfly-elements-for-bricks' ),
				),
				'link'          => array(
					'label' => esc_html__( 'Link', 'bricksfly-elements-for-bricks' ),
					'type'  => 'link',
				),
			),
			'default'       => array(
				array(
					'timelineTitle' => esc_html__( 'Journey Started at New York', 'bricksfly-elements-for-bricks' ),
					'timelineSub'   => esc_html__( 'Designer', 'bricksfly-elements-for-bricks' ),
					'timelineDate'  => esc_html__( 'Jan 01, 2021', 'bricksfly-elements-for-bricks' ),
					'stepType'      => 'icon',
				),
				array(
					'timelineTitle' => esc_html__( 'Moved to San Francisco', 'bricksfly-elements-for-bricks' ),
					'timelineSub'   => esc_html__( 'Developer', 'bricksfly-elements-for-bricks' ),
					'timelineDate'  => esc_html__( 'Mar 15, 2022', 'bricksfly-elements-for-bricks' ),
					'stepType'      => 'icon',
				),
				array(
					'timelineTitle' => esc_html__( 'Launched First Product', 'bricksfly-elements-for-bricks' ),
					'timelineSub'   => esc_html__( 'Founder', 'bricksfly-elements-for-bricks' ),
					'timelineDate'  => esc_html__( 'Jun 20, 2023', 'bricksfly-elements-for-bricks' ),
					'stepType'      => 'icon',
				),
			),
		);

		// === STYLE: TIMELINE ===
		$this->controls['contentGap'] = array(
			'group'       => 'timeline_style',
			'label'       => esc_html__( 'Space', 'bricksfly-elements-for-bricks' ),
			'type'        => 'number',
			'default'     => 60,
			'placeholder' => '60',
			'units'       => array(
				'px' => array(
					'min' => 1,
					'max' => 300,
				),
			),
			'css'         => array(
				array(
					'property' => '--content-gap',
					'selector' => '&.aae-timeline',
				),
			),
		);

		$this->controls['contentBottomSpace'] = array(
			'group'       => 'timeline_style',
			'label'       => esc_html__( 'Content bottom space', 'bricksfly-elements-for-bricks' ),
			'type'        => 'number',
			'default'     => '10px',
			'placeholder' => '10',
			'units'       => array(
				'px' => array(
					'min' => 1,
					'max' => 300,
				),
			),
			'css'         => array(
				array(
					'property' => 'margin-bottom',
					'selector' => '.timeline-item:not(:last-child) .content-wrap',
				),
			),
		);

		// === STYLE: CONTENT ===
		$this->controls['contentBg'] = array(
			'group' => 'content_style',
			'label' => esc_html__( 'Background', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'background-color',
					'selector' => '.content-wrap',
				),
			),
		);

		$this->controls['contentWrapperPadding'] = array(
			'group' => 'content_style',
			'label' => esc_html__( 'Wrapper padding', 'bricksfly-elements-for-bricks' ),
			'type'  => 'dimensions',
			'css'   => array(
				array(
					'property' => 'padding',
					'selector' => '.content-wrap',
				),
			),
		);

		$this->controls['contentPadding'] = array(
			'group' => 'content_style',
			'label' => esc_html__( 'Content padding', 'bricksfly-elements-for-bricks' ),
			'type'  => 'dimensions',
			'css'   => array(
				array(
					'property' => 'padding',
					'selector' => '.content-wrap .content',
				),
			),
		);

		// $this->controls['contentBorderRadius'] = [
		// 'group' => 'content_style',
		// 'label' => esc_html__('Border Radius', 'bricksfly-elements-for-bricks'),
		// 'type'  => 'dimensions',
		// 'css'   => [['property' => 'border-radius', 'selector' => '.content-wrap']],
		// ];

		$this->controls['contentBorder'] = array(
			'group' => 'content_style',
			'label' => esc_html__( 'Border', 'bricksfly-elements-for-bricks' ),
			'type'  => 'border',
			'css'   => array(
				array(
					'property' => 'border',
					'selector' => '.content-wrap',
				),
			),
		);

		$this->controls['headingImage'] = array(
			'group' => 'content_style',
			'label' => esc_html__( 'Image', 'bricksfly-elements-for-bricks' ),
			'type'  => 'separator',
		);

		$this->controls['imgWidth'] = array(
			'group' => 'content_style',
			'label' => esc_html__( 'Width', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => array(
				'px' => array(
					'min' => 1,
					'max' => 500,
				),
				'%'  => array(
					'min' => 1,
					'max' => 100,
				),
			),
			'css'   => array(
				array(
					'property' => 'width',
					'selector' => 'img',
				),
			),
		);

		$this->controls['imgHeight'] = array(
			'group' => 'content_style',
			'label' => esc_html__( 'Height', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => array(
				'px' => array(
					'min' => 1,
					'max' => 500,
				),
				'vh' => array(
					'min' => 1,
					'max' => 100,
				),
			),
			'css'   => array(
				array(
					'property' => 'height',
					'selector' => 'img',
				),
			),
		);

		$this->controls['imgObjectFit'] = array(
			'group'   => 'content_style',
			'label'   => esc_html__( 'Object Fit', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'options' => array(
				'fill'    => esc_html__( 'Fill', 'bricksfly-elements-for-bricks' ),
				'cover'   => esc_html__( 'Cover', 'bricksfly-elements-for-bricks' ),
				'contain' => esc_html__( 'Contain', 'bricksfly-elements-for-bricks' ),
			),
			'css'     => array(
				array(
					'property' => 'object-fit',
					'selector' => 'img',
				),
			),
		);

		$this->controls['imgObjectPosition'] = array(
			'group'    => 'content_style',
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
			'css'      => array(
				array(
					'property' => 'object-position',
					'selector' => 'img',
				),
			),
			'required' => array( 'imgObjectFit', '=', 'cover' ),
		);

		$this->controls['imgBorder'] = array(
			'group' => 'content_style',
			'label' => esc_html__( 'Border', 'bricksfly-elements-for-bricks' ),
			'type'  => 'border',
			'css'   => array(
				array(
					'property' => 'border',
					'selector' => 'img',
				),
			),
		);

		// $this->controls['imgBorderRadius'] = [
		// 'group' => 'content_style',
		// 'label' => esc_html__('Image Border Radius', 'bricksfly-elements-for-bricks'),
		// 'type'  => 'spacing',
		// 'css'   => [['property' => 'border-radius', 'selector' => 'img']],
		// ];

		$this->controls['imageSpace'] = array(
			'group' => 'content_style',
			'label' => esc_html__( 'Spacing', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => array(
				'px' => array(
					'min' => 0,
					'max' => 100,
				),
			),
			'css'   => array(
				array(
					'property' => 'gap',
					'selector' => '.content-wrap',
				),
			),
		);

		$this->controls['headingDate'] = array(
			'group' => 'content_style',
			'label' => esc_html__( 'Date', 'bricksfly-elements-for-bricks' ),
			'type'  => 'separator',
		);

		$this->controls['dateColor'] = array(
			'group' => 'content_style',
			'label' => esc_html__( 'Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'color',
					'selector' => '.date',
				),
			),
		);

		$this->controls['dateTypography'] = array(
			'group' => 'content_style',
			'label' => esc_html__( 'Typography', 'bricksfly-elements-for-bricks' ),
			'type'  => 'typography',
			'css'   => array(
				array(
					'property' => 'font',
					'selector' => '.date',
				),
			),
		);

		$this->controls['dateSpacing'] = array(
			'group' => 'content_style',
			'label' => esc_html__( 'Date Spacing', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => array(
				'px' => array(
					'min' => 0,
					'max' => 100,
				),
			),
			'css'   => array(
				array(
					'property' => 'margin-bottom',
					'selector' => '.date',
				),
			),
		);

		$this->controls['headingTitle'] = array(
			'group' => 'content_style',
			'label' => esc_html__( 'Title', 'bricksfly-elements-for-bricks' ),
			'type'  => 'separator',
		);

		$this->controls['titleColor'] = array(
			'group' => 'content_style',
			'label' => esc_html__( 'Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'color',
					'selector' => '.title',
				),
			),
		);

		$this->controls['titleTypography'] = array(
			'group' => 'content_style',
			'label' => esc_html__( 'Typography', 'bricksfly-elements-for-bricks' ),
			'type'  => 'typography',
			'css'   => array(
				array(
					'property' => 'font',
					'selector' => '.title',
				),
			),
		);

		$this->controls['titleSpacing'] = array(
			'group' => 'content_style',
			'label' => esc_html__( 'Title Spacing', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => array(
				'px' => array(
					'min' => 0,
					'max' => 100,
				),
			),
			'css'   => array(
				array(
					'property' => 'margin-bottom',
					'selector' => '.title',
				),
			),
		);

		$this->controls['headingSubtitle'] = array(
			'group' => 'content_style',
			'label' => esc_html__( 'Subtitle', 'bricksfly-elements-for-bricks' ),
			'type'  => 'separator',
		);

		$this->controls['subtitleColor'] = array(
			'group' => 'content_style',
			'label' => esc_html__( 'Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'color',
					'selector' => '.subtitle',
				),
			),
		);

		$this->controls['subtitleTypography'] = array(
			'group' => 'content_style',
			'label' => esc_html__( 'Typography', 'bricksfly-elements-for-bricks' ),
			'type'  => 'typography',
			'css'   => array(
				array(
					'property' => 'font',
					'selector' => '.subtitle',
				),
			),
		);

		$this->controls['subtitleSpacing'] = array(
			'group' => 'content_style',
			'label' => esc_html__( 'Subtitle Spacing', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => array(
				'px' => array(
					'min' => 0,
					'max' => 100,
				),
			),
			'css'   => array(
				array(
					'property' => 'margin-bottom',
					'selector' => '.subtitle',
				),
			),
		);

		$this->controls['headingDescription'] = array(
			'group' => 'content_style',
			'label' => esc_html__( 'Description', 'bricksfly-elements-for-bricks' ),
			'type'  => 'separator',
		);

		$this->controls['descriptionColor'] = array(
			'group' => 'content_style',
			'label' => esc_html__( 'Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'color',
					'selector' => '.description',
				),
			),
		);

		$this->controls['descriptionTypography'] = array(
			'group' => 'content_style',
			'label' => esc_html__( 'Typography', 'bricksfly-elements-for-bricks' ),
			'type'  => 'typography',
			'css'   => array(
				array(
					'property' => 'font',
					'selector' => '.description',
				),
			),
		);

		// === STYLE: STEP ===
		$this->controls['stepColor'] = array(
			'group' => 'step_style',
			'label' => esc_html__( 'Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'color',
					'selector' => '.step-box .icon',
				),
				array(
					'property' => 'fill',
					'selector' => '.step-box .icon',
				),
			),
		);

		$this->controls['stepBgColor'] = array(
			'group' => 'step_style',
			'label' => esc_html__( 'Background Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'background-color',
					'selector' => '.step-box .icon',
				),
			),
		);

		$this->controls['stepTypography'] = array(
			'group'   => 'step_style',
			'label'   => esc_html__( 'Typography', 'bricksfly-elements-for-bricks' ),
			'type'    => 'typography',
			'css'     => array(
				array(
					'property' => 'font',
					'selector' => '.step-box .icon',
				),
			),
			'exclude' => array( 'font-size', 'text-decoration', 'line-height' ),   // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- Bricks control option, not a WP_Query arg
		);

		$this->controls['stepSize'] = array(
			'group' => 'step_style',
			'label' => esc_html__( 'Icon Size (Width/Height)', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => array(
				'px' => array(
					'min' => 6,
					'max' => 300,
				),
			),
			'css'   => array(
				array(
					'property' => '--icon-size',
					'selector' => '&.aae-timeline',
				),
			),
		);

		$this->controls['stepTextSize'] = array(
			'group' => 'step_style',
			'label' => esc_html__( 'Text Font Size', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'unit'  => 'px',
			'css'   => array(
				array(
					'property' => 'font-size',
					'selector' => '.step-box .icon',
				),
			),
		);

		$this->controls['stepPadding'] = array(
			'group' => 'step_style',
			'label' => esc_html__( 'Padding', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => array(
				'px' => array(
					'min' => 1,
					'max' => 300,
				),
			),
			'css'   => array(
				array(
					'property' => '--icon-padding',
					'selector' => '&.aae-timeline',
				),
			),
		);

		$this->controls['stepBorder'] = array(
			'group' => 'step_style',
			'label' => esc_html__( 'Border', 'bricksfly-elements-for-bricks' ),
			'type'  => 'border',
			'css'   => array(
				array(
					'property' => 'border',
					'selector' => '.step-box .icon',
				),
			),
		);

		$this->controls['stepBorderRadius'] = array(
			'group' => 'step_style',
			'label' => esc_html__( 'Border Radius', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => array(
				'px' => array(
					'min' => 0,
					'max' => 300,
				),
				'%'  => array(
					'min' => 0,
					'max' => 100,
				),
			),
			'css'   => array(
				array(
					'property' => 'border-radius',
					'selector' => '.step-box .icon',
				),
			),
		);

		$this->controls['stepPositionX'] = array(
			'group' => 'step_style',
			'label' => esc_html__( 'Step icon position-x', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => array(
				'px' => array(
					'min' => 0,
					'max' => 300,
				),
				'%'  => array(
					'min' => 0,
					'max' => 100,
				),
			),
			'css'   => array(
				array(
					'property' => 'top',
					'selector' => '.step-box .icon',
				),
			),
		);

		$this->controls['headingStepLine'] = array(
			'group' => 'step_style',
			'label' => esc_html__( 'Step Line', 'bricksfly-elements-for-bricks' ),
			'type'  => 'separator',
		);

		$this->controls['stepLineStyle'] = array(
			'group'   => 'step_style',
			'label'   => esc_html__( 'Line Style', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'options' => array(
				'none'   => esc_html__( 'None', 'bricksfly-elements-for-bricks' ),
				'solid'  => esc_html__( 'Solid', 'bricksfly-elements-for-bricks' ),
				'dashed' => esc_html__( 'Dashed', 'bricksfly-elements-for-bricks' ),
				'dotted' => esc_html__( 'Dotted', 'bricksfly-elements-for-bricks' ),
				'double' => esc_html__( 'Double', 'bricksfly-elements-for-bricks' ),
			),
			'default' => 'dashed',
			'css'     => array(
				array(
					'property' => 'border-left-style',
					'selector' => '.line',
				),
			),
		);

		$this->controls['stepLineThickness'] = array(
			'group'    => 'step_style',
			'label'    => esc_html__( 'Line Thickness', 'bricksfly-elements-for-bricks' ),
			'type'     => 'number',
			'units'    => array(
				'px' => array(
					'min' => 0,
					'max' => 10,
				),
			),
			'css'      => array(
				array(
					'property' => 'border-left-width',
					'selector' => '.line',
				),
			),
			'required' => array( 'stepLineStyle', '!=', 'none' ),
		);

		$this->controls['stepLineColor'] = array(
			'group'    => 'step_style',
			'label'    => esc_html__( 'Line Color', 'bricksfly-elements-for-bricks' ),
			'type'     => 'color',
			'css'      => array(
				array(
					'property' => 'border-left-color',
					'selector' => '.line',
				),
			),
			'required' => array( 'stepLineStyle', '!=', 'none' ),
		);

		$this->controls['headingIndicator'] = array(
			'group'    => 'step_style',
			'label'    => esc_html__( 'Indicator', 'bricksfly-elements-for-bricks' ),
			'type'     => 'separator',
			'required' => array( 'showIndicator', '!=', '' ),
		);

		$this->controls['indicatorColor'] = array(
			'group'    => 'step_style',
			'label'    => esc_html__( 'Indicator Color', 'bricksfly-elements-for-bricks' ),
			'type'     => 'color',
			'css'      => array(
				array(
					'property' => 'background-color',
					'selector' => '.indicator',
				),
			),
			'required' => array( 'showIndicator', '!=', '' ),
		);

		$this->controls['indicatorWidth'] = array(
			'group'    => 'step_style',
			'label'    => esc_html__( 'Indicator Width', 'bricksfly-elements-for-bricks' ),
			'type'     => 'number',
			'units'    => array(
				'px' => array(
					'min' => 1,
					'max' => 500,
				),
			),
			'css'      => array(
				array(
					'property' => 'width',
					'selector' => '.indicator',
				),
			),
			'required' => array( 'showIndicator', '!=', '' ),
		);

		$this->controls['indicatorHeight'] = array(
			'group'    => 'step_style',
			'label'    => esc_html__( 'Indicator Height', 'bricksfly-elements-for-bricks' ),
			'type'     => 'number',
			'units'    => array(
				'px' => array(
					'min' => 1,
					'max' => 50,
				),
			),
			'css'      => array(
				array(
					'property' => 'height',
					'selector' => '.indicator',
				),
			),
			'required' => array( 'showIndicator', '!=', '' ),
		);

		$this->controls['indicatorGap'] = array(
			'group'    => 'step_style',
			'label'    => esc_html__( 'Indicator Gap', 'bricksfly-elements-for-bricks' ),
			'type'     => 'number',
			'units'    => array(
				'px' => array(
					'min' => 1,
					'max' => 500,
				),
			),
			'css'      => array(
				array(
					'property' => '--indicator-gap',
					'selector' => '.indicator',
				),
			),
			'required' => array( 'showIndicator', '!=', '' ),
		);
	}

	public function enqueue_scripts() {
		wp_enqueue_style( 'bricks-font-awesome-6' );
		wp_enqueue_style( 'bricks-font-awesome-6-brands' );

		wp_enqueue_style(
			'aae-timeline',
			BRICKSFLY_URL . 'public/build/elements/timeline.css',
			array(),
			'1.0.0'
		);
	}

	public function render() {
		$settings = $this->settings;
		$items    = $settings['timelineItems'] ?? array();

		if ( empty( $items ) ) {
			echo wp_kses_post( $this->render_element_placeholder( array( 'title' => esc_html__( 'Add timeline items.', 'bricksfly-elements-for-bricks' ) ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}

		$style     = $settings['elementList'] ?? '1';
		$title_tag = $settings['titleTag'] ?? 'h3';
		$allowed   = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p' );
		if ( ! in_array( $title_tag, $allowed, true ) ) {
			$title_tag = 'h3';
		}
		$image_size     = $settings['imageSize'] ?? 'full';
		$show_indicator = ! empty( $settings['showIndicator'] );

		$this->set_attribute( '_root', 'class', array( 'aae-timeline-wrapper', 'aae-timeline', 'style-' . $style ) );

		echo wp_kses_post( '<div ' . $this->render_attributes( '_root' ) . '>' );

		foreach ( $items as $index => $item ) {
			$this->render_timeline_item( $settings, $item, $index, $title_tag, $image_size, $show_indicator );
		}

		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	private function render_timeline_item( $settings, $item, $index, $title_tag, $image_size, $show_indicator ) {
		$link_key = 'link_' . $index;
		$has_link = ! empty( $item['link']['url'] ) || ! empty( $item['link']['type'] );
		if ( $has_link ) {
			$this->set_link_attributes( $link_key, $item['link'] );
		}

		echo '<div class="timeline-item">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		// Step
		echo '<div class="step-box">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div class="icon">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		if ( $show_indicator ) {
			echo '<div class="indicator"></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		$step_type = $item['stepType'] ?? 'icon';
		if ( $step_type === 'icon' && ! empty( $item['stepIcon'] ) ) {
			echo wp_kses_post( self::render_icon( $item['stepIcon'], array( 'aria-hidden' => 'true' ) ) );
		} elseif ( $step_type === 'text' ) {
			echo esc_html( $item['stepText'] ?? '' );
		}
		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div class="line"></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		// Content wrap
		echo '<div class="content-wrap">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		$this->render_thumbnail( $item, $image_size, $has_link, $link_key );

		echo '<div class="content">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( ! empty( $item['timelineDate'] ) ) {
			echo '<div class="date">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			if ( ! empty( $settings['dateIcon'] ) ) {
				echo wp_kses_post( self::render_icon( $settings['dateIcon'], array( 'aria-hidden' => 'true' ) ) );
			}
			echo esc_html( $item['timelineDate'] );
			echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		if ( ! empty( $item['timelineTitle'] ) ) {
			echo '<' . esc_html( $title_tag ) . ' class="title">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			if ( $has_link ) {
				echo wp_kses_post( '<a ' . $this->render_attributes( $link_key ) . '>' );
				echo wp_kses(
					$item['timelineTitle'],
					array(
						'br'     => array(),
						'span'   => array( 'class' => array() ),
						'strong' => array(),
						'em'     => array(),
						'b'      => array(),
						'i'      => array(),
					)
				);
				echo '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} else {
				echo wp_kses(
					$item['timelineTitle'],
					array(
						'br'     => array(),
						'span'   => array( 'class' => array() ),
						'strong' => array(),
						'em'     => array(),
						'b'      => array(),
						'i'      => array(),
					)
				);
			}
			echo '</' . esc_html( $title_tag ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		if ( ! empty( $item['timelineSub'] ) ) {
			echo '<div class="subtitle">' . esc_html( $item['timelineSub'] ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		if ( ! empty( $item['timelineDesc'] ) ) {
			echo '<div class="description">' . wp_kses_post( nl2br( $item['timelineDesc'] ) ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	private function render_thumbnail( $item, $image_size, $has_link, $link_key ) {
		$image_data = $item['timelineImage'] ?? null;
		if ( empty( $image_data ) ) {
			return;
		}

		$image_html = '';
		if ( is_array( $image_data ) ) {
			if ( ! empty( $image_data['id'] ) ) {
				$image_html = wp_get_attachment_image( $image_data['id'], $image_size );
			}
			if ( ! $image_html && ! empty( $image_data['url'] ) ) {
				$image_html = '<img src="' . esc_url( $image_data['url'] ) . '" alt="">';
			}
		} elseif ( is_numeric( $image_data ) && $image_data > 0 ) {
			$image_html = wp_get_attachment_image( (int) $image_data, $image_size );
		}

		if ( ! $image_html ) {
			return;
		}

		echo '<div class="thumb">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		if ( $has_link ) {
			echo wp_kses_post( '<a ' . $this->render_attributes( $link_key ) . '>' . $image_html . '</a>' );
		} else {
			echo wp_kses_post( $image_html );
		}
		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
