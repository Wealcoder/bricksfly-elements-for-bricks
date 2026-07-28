<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class THEBRBRE_Bricks_Post_Meta extends \Bricks\Element {

	public $category     = 'bricks fly';
	public $name         = 'aab-post-meta';
	public $icon         = 'ti-info-alt aab-element-marker';
	public $css_selector = '.wcf--meta-list';
	public $scripts      = [];

	public function get_label() {
		return esc_html__('Post Meta', 'bricksfly-elements-for-bricks');
	}

	public function get_keywords() {
		return [ 'post', 'meta', 'author', 'category', 'date', 'comment', 'reading time', 'view' ];
	}

	public function enqueue_scripts() {
		wp_enqueue_style( 'bricks-font-awesome-6' );
		wp_enqueue_style( 'bricks-font-awesome-6-brands' );

		wp_enqueue_style(
			'aab-post-meta',
			THEBRBRE_URL . 'public/build/elements/meta-info.css',
			[],
			'1.0.0'
		);
	}

	public function set_control_groups() {
		$this->control_groups['layout']    = [ 'title' => esc_html__('Layout', 'bricksfly-elements-for-bricks'),    'tab' => 'content' ];
		$this->control_groups['content']   = [ 'title' => esc_html__('Content', 'bricksfly-elements-for-bricks'),   'tab' => 'content' ];

		$this->control_groups['general']   = [ 'title' => esc_html__('General', 'bricksfly-elements-for-bricks'),   'tab' => 'style' ];
		$this->control_groups['separator'] = [ 'title' => esc_html__('Separator', 'bricksfly-elements-for-bricks'), 'tab' => 'style' ];
		$this->control_groups['category']  = [ 'title' => esc_html__('Category', 'bricksfly-elements-for-bricks'),  'tab' => 'style' ];
		$this->control_groups['author']    = [ 'title' => esc_html__('Author', 'bricksfly-elements-for-bricks'),    'tab' => 'style' ];
		$this->control_groups['date']      = [ 'title' => esc_html__('Date', 'bricksfly-elements-for-bricks'),      'tab' => 'style' ];
		$this->control_groups['view']      = [ 'title' => esc_html__('View / Reading Time', 'bricksfly-elements-for-bricks'), 'tab' => 'style' ];
		$this->control_groups['comment']   = [ 'title' => esc_html__('Comment', 'bricksfly-elements-for-bricks'),   'tab' => 'style' ];
		$this->control_groups['ptime']     = [ 'title' => esc_html__('Time Ago', 'bricksfly-elements-for-bricks'),  'tab' => 'style' ];
	}

	public function set_controls() {

		/* ---------- Layout ---------- */

		$this->controls['layoutStyle'] = [
			'tab'     => 'content',
			'group'   => 'layout',
			'label'   => esc_html__( 'Layout Style', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'options' => [
				'1' => esc_html__( 'One', 'bricksfly-elements-for-bricks' ),
				'2' => esc_html__( 'Two', 'bricksfly-elements-for-bricks' ),
			],
			'default' => '1',
			'inline'  => true,
		];

		$this->controls['layoutAlign'] = [
			'tab'   => 'content',
			'group' => 'layout',
			'label' => esc_html__( 'Alignment', 'bricksfly-elements-for-bricks' ),
			'type'  => 'select',

			'options' => [
				'flex-start'    => esc_html__( 'Start', 'bricksfly-elements-for-bricks' ),
				'center'        => esc_html__( 'Center', 'bricksfly-elements-for-bricks' ),
				'flex-end'      => esc_html__( 'End', 'bricksfly-elements-for-bricks' ),
				'space-between' => esc_html__( 'Space Between', 'bricksfly-elements-for-bricks' ),
				'space-around'  => esc_html__( 'Space Around', 'bricksfly-elements-for-bricks' ),
				'space-evenly'  => esc_html__( 'Space Evenly', 'bricksfly-elements-for-bricks' ),
			],

			'inline' => true,

			'css' => [
				[
					'property' => 'justify-content',
					'selector' => '&.wcf--meta-list',
				],
			],

			'default' => 'flex-start',
		];

		$this->controls['shareSeparator'] = [
			'tab'     => 'content',
			'group'   => 'layout',
			'label'   => esc_html__( 'Share Separator', 'bricksfly-elements-for-bricks' ),
			'type'    => 'checkbox',
			'default' => false,
		];

		$this->controls['shareSeparatorIcon'] = [
			'tab'      => 'content',
			'group'    => 'layout',
			'label'    => esc_html__( 'Separator Icon', 'bricksfly-elements-for-bricks' ),
			'type'     => 'icon',
			'default'  => [ 'library' => 'fontawesome', 'icon' => 'fas fa-share-nodes' ],
			'required' => [ 'shareSeparator', '=', true ],
		];

		$this->controls['showTitle'] = [
			'tab'      => 'content',
			'group'    => 'layout',
			'label'    => esc_html__( 'Show Title', 'bricksfly-elements-for-bricks' ),
			'type'     => 'checkbox',
			'default'  => false,
			'required' => [ 'layoutStyle', '=', '1' ],
		];

		/* ---------- Content (repeater) ---------- */

		$this->controls['metaList'] = [
			'tab'           => 'content',
			'group'         => 'content',
			'label'         => esc_html__( 'Meta List', 'bricksfly-elements-for-bricks' ),
			'type'          => 'repeater',
			'titleProperty' => 'listTitle',
			'fields'        => [
				'listTitle' => [
					'label'   => esc_html__( 'Title', 'bricksfly-elements-for-bricks' ),
					'type'    => 'text',
					'default' => esc_html__( 'List Title', 'bricksfly-elements-for-bricks' ),
				],
				'listType' => [
					'label'   => esc_html__( 'Meta', 'bricksfly-elements-for-bricks' ),
					'type'    => 'select',
					'options' => [
						''             => esc_html__( '— Select —', 'bricksfly-elements-for-bricks' ),
						'category'     => esc_html__( 'Category', 'bricksfly-elements-for-bricks' ),
						'date'         => esc_html__( 'Date', 'bricksfly-elements-for-bricks' ),
						'view'         => esc_html__( 'View', 'bricksfly-elements-for-bricks' ),
						'author'       => esc_html__( 'Author', 'bricksfly-elements-for-bricks' ),
						'reading_time' => esc_html__( 'Reading Time', 'bricksfly-elements-for-bricks' ),
						'comment'      => esc_html__( 'Comment', 'bricksfly-elements-for-bricks' ),
						'review'       => esc_html__( 'Review', 'bricksfly-elements-for-bricks' ),
						'read-later'   => esc_html__( 'Save', 'bricksfly-elements-for-bricks' ),
						'time-ago'     => esc_html__( 'Post Time Ago', 'bricksfly-elements-for-bricks' ),
						'last-update'  => esc_html__( 'Last Updated', 'bricksfly-elements-for-bricks' ),
					],
					'default' => '',
				],
				'listIcon' => [
					'label' => esc_html__( 'Icon', 'bricksfly-elements-for-bricks' ),
					'type'  => 'icon',
				],
				'metaSeparator' => [
					'label'       => esc_html__( 'Separator', 'bricksfly-elements-for-bricks' ),
					'type'        => 'text',
					'default'     => '|',
					'placeholder' => esc_html__( 'Enter your separator', 'bricksfly-elements-for-bricks' ),
				],
				'multipleCategory' => [
					'label'    => esc_html__( 'Multiple Category', 'bricksfly-elements-for-bricks' ),
					'type'     => 'checkbox',
					'default'  => true,
					'required' => [ 'listType', '=', 'category' ],
				],
				'categoryLimit' => [
					'label'    => esc_html__( 'Category Limit', 'bricksfly-elements-for-bricks' ),
					'type'     => 'number',
					'min'      => 1,
					'max'      => 10,
					'step'     => 1,
					'required' => [
						[ 'listType', '=', 'category' ],
						[ 'multipleCategory', '=', true ],
					],
				],
			],
			'default' => [
				[
					'listTitle'        => esc_html__( 'Date', 'bricksfly-elements-for-bricks' ),
					'listType'         => 'date',
					'metaSeparator'    => '|',
					'multipleCategory' => true,
				],
				[
					'listTitle'        => esc_html__( 'Author', 'bricksfly-elements-for-bricks' ),
					'listType'         => 'author',
					'metaSeparator'    => '|',
					'multipleCategory' => true,
				],
				[
					'listTitle'        => esc_html__( 'Category', 'bricksfly-elements-for-bricks' ),
					'listType'         => 'category',
					'metaSeparator'    => '|',
					'multipleCategory' => true,
				],
			],
		];

		/* ---------- Style: General ---------- */

		$this->controls['textColor'] = [
			'tab' => 'style', 'group' => 'general',
			'label' => esc_html__( 'Text Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => [
				[ 'property' => 'color', 'selector' => '.wcf--meta-list li' ],
				[ 'property' => 'color', 'selector' => '.wcf--meta-list li a' ],
				[ 'property' => 'color', 'selector' => '.wcf--meta-date' ],
				[ 'property' => 'color', 'selector' => '.wcf--meta-view' ],
				[ 'property' => 'fill',  'selector' => '.wcf--meta-list li svg' ],
			],
		];

		$this->controls['textTypo'] = [
			'tab' => 'style', 'group' => 'general',
			'label' => esc_html__( 'Typography', 'bricksfly-elements-for-bricks' ),
			'type'  => 'typography',
			'css'   => [
				[ 'property' => 'font', 'selector' => '.wcf--meta-list li' ],
				[ 'property' => 'font', 'selector' => '.wcf--meta-list li a' ],
				[ 'property' => 'font', 'selector' => '.wcf--meta-date' ],
				[ 'property' => 'font', 'selector' => '.wcf--meta-view' ],
			],
		];

		$this->controls['labelColor'] = [
			'tab' => 'style', 'group' => 'general',
			'label' => esc_html__( 'Label Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => [
				[ 'property' => 'color', 'selector' => '.wcf--meta-list li .label' ],
				[ 'property' => 'fill',  'selector' => '.wcf--meta-list li .label svg' ],
			],
			'required' => [ 'layoutStyle', '=', '2' ],
		];

		$this->controls['labelGap'] = [
			'tab' => 'style', 'group' => 'general',
			'label' => esc_html__( 'Label Gap', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
			'css'   => [
				[ 'property' => 'gap', 'selector' => '.wcf--meta-list.style-2 li' ],
				[ 'property' => 'gap', 'selector' => '.wcf--meta-list.style-1 li' ],
			],
		];

		$this->controls['labelTypo'] = [
			'tab' => 'style', 'group' => 'general',
			'label' => esc_html__( 'Label Typography', 'bricksfly-elements-for-bricks' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'font', 'selector' => '.wcf--meta-list li .label' ] ],
			'required' => [ 'layoutStyle', '=', '2' ],
		];

		$this->controls['metaColGap'] = [
			'tab' => 'style', 'group' => 'general',
			'label' => esc_html__( 'Column Gap', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
			'css'   => [ [ 'property' => 'column-gap', 'selector' => '&.wcf--meta-list' ] ],
		];

		$this->controls['metaRowGap'] = [
			'tab' => 'style', 'group' => 'general',
			'label' => esc_html__( 'Row Gap', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
			'css'   => [ [ 'property' => 'row-gap', 'selector' => '&.wcf--meta-list' ] ],
		];

		$this->controls['metaSpacing'] = [
			'tab' => 'style', 'group' => 'general',
			'label' => esc_html__( 'Spacing (Margin)', 'bricksfly-elements-for-bricks' ),
			'type'  => 'spacing',
			'css'   => [ [ 'property' => 'margin', 'selector' => '&.wcf--meta-list' ] ],
		];

		/* ---------- Style: Separator ---------- */

		$this->controls['separatorColor'] = [
			'tab' => 'style', 'group' => 'separator',
			'label' => esc_html__( 'Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => [ [ 'property' => 'background-color', 'selector' => '.wcf--meta-list.style-2 > li::after' ] ],
			'required' => [ 'layoutStyle', '=', '2' ],
		];

		$this->controls['separatorWidth'] = [
			'tab' => 'style', 'group' => 'separator',
			'label' => esc_html__( 'Width', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => [ 'px' => [ 'min' => 0, 'max' => 20 ] ],
			'css'   => [ [ 'property' => 'width', 'selector' => '.wcf--meta-list.style-2 > li::after' ] ],
			'required' => [ 'layoutStyle', '=', '2' ],
		];

		$this->controls['separatorHeight'] = [
			'tab' => 'style', 'group' => 'separator',
			'label' => esc_html__( 'Height', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => [ 'px' => [ 'min' => 0, 'max' => 200 ], '%' => [ 'min' => 0, 'max' => 100 ] ],
			'css'   => [ [ 'property' => 'height', 'selector' => '.wcf--meta-list.style-2 > li::after' ] ],
			'required' => [ 'layoutStyle', '=', '2' ],
		];

		$this->controls['separatorPosition2'] = [
			'tab' => 'style', 'group' => 'separator',
			'label' => esc_html__( 'Position (Layout 2)', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => [ 'px' => [ 'min' => 0, 'max' => 200 ], '%' => [ 'min' => 0, 'max' => 100 ] ],
			'css'   => [
				[ 'property' => 'inset-inline-end', 'selector' => '.wcf--meta-list.style-2 > li::after', 'value' => 'calc(0px - %s)' ],
			],
			'required' => [ 'layoutStyle', '=', '2' ],
		];

		$this->controls['separatorPosition'] = [
			'tab' => 'style', 'group' => 'separator',
			'label' => esc_html__( 'Position (Layout 1)', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => [ 'px' => [ 'min' => 0, 'max' => 200 ] ],
			'css'   => [ [ 'property' => 'margin-inline-start', 'selector' => '.wcf-separator::after' ] ],
			'required' => [ 'layoutStyle', '=', '1' ],
		];

		$this->controls['shareSeparatorIconSize'] = [
			'tab' => 'style', 'group' => 'separator',
			'label' => esc_html__( 'Share Icon Size', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => [ 'px' => [ 'min' => 0, 'max' => 200 ] ],
			'default' => '16px',
			'css'   => [ [ 'property' => 'font-size', 'selector' => 'span.wcf_separator_icon' ] ],
		];

		$this->controls['shareSeparatorIconColor'] = [
			'tab' => 'style', 'group' => 'separator',
			'label' => esc_html__( 'Share Icon Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => [
				[ 'property' => 'color', 'selector' => 'span.wcf_separator_icon' ],
				[ 'property' => 'fill',  'selector' => 'span.wcf_separator_icon' ],
			],
		];

		/* ---------- Style: Category ---------- */

		$this->controls['categoryAlign'] = [
			'tab' => 'style', 'group' => 'category',
			'label' => esc_html__( 'Alignment', 'bricksfly-elements-for-bricks' ),
			'type'  => 'text-align',
			'css'   => [
				[ 'property' => 'align-items', 'selector' => '.wcf--category-wrap' ],
				[ 'property' => 'text-align',  'selector' => '.wcf--category-wrap' ],
			],
			'required' => [ 'layoutStyle', '=', '2' ],
		];

		$this->controls['categoryColGap'] = [
			'tab' => 'style', 'group' => 'category',
			'label' => esc_html__( 'Column Gap', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
			'css'   => [ [ 'property' => 'column-gap', 'selector' => '.wcf--category-list' ] ],
		];

		$this->controls['categoryRowGap'] = [
			'tab' => 'style', 'group' => 'category',
			'label' => esc_html__( 'Row Gap', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
			'css'   => [ [ 'property' => 'row-gap', 'selector' => '.wcf--category-list' ] ],
		];

		$this->controls['categoryHoverList'] = [
			'tab' => 'style', 'group' => 'category',
			'label' => esc_html__( 'Hover Style', 'bricksfly-elements-for-bricks' ),
			'type'  => 'select',
			'options' => [
				'hover-none'      => esc_html__( 'None', 'bricksfly-elements-for-bricks' ),
				'hover-divide'    => esc_html__( 'Divided', 'bricksfly-elements-for-bricks' ),
				'hover-cross'     => esc_html__( 'Cross', 'bricksfly-elements-for-bricks' ),
				'hover-cropping'  => esc_html__( 'Cropping', 'bricksfly-elements-for-bricks' ),
				'rollover-top'    => esc_html__( 'Rollover Top', 'bricksfly-elements-for-bricks' ),
				'rollover-left'   => esc_html__( 'Rollover Left', 'bricksfly-elements-for-bricks' ),
				'parallal-border' => esc_html__( 'Parallel Border', 'bricksfly-elements-for-bricks' ),
				'rollover-cross'  => esc_html__( 'Rollover Cross', 'bricksfly-elements-for-bricks' ),
			],
			'default' => 'hover-none',
		];

		$this->controls['categoryTypo'] = [
			'tab' => 'style', 'group' => 'category',
			'label' => esc_html__( 'Typography', 'bricksfly-elements-for-bricks' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'font', 'selector' => '.wcf--meta-list .wcf--meta-category a' ] ],
		];

		$this->controls['categoryPadding'] = [
			'tab' => 'style', 'group' => 'category',
			'label' => esc_html__( 'Padding', 'bricksfly-elements-for-bricks' ),
			'type'  => 'spacing',
			'css'   => [ [ 'property' => 'padding', 'selector' => '.wcf--meta-category a' ] ],
			'required' => [ 'layoutStyle', '=', '1' ],
		];

		$this->controls['categoryBorder'] = [
			'tab' => 'style', 'group' => 'category',
			'label' => esc_html__( 'Border', 'bricksfly-elements-for-bricks' ),
			'type'  => 'border',
			'css'   => [ [ 'property' => 'border', 'selector' => '.wcf--meta-category a' ] ],
			'required' => [ 'layoutStyle', '=', '1' ],
		];

		$this->controls['categoryRadius'] = [
			'tab' => 'style', 'group' => 'category',
			'label' => esc_html__( 'Border Radius', 'bricksfly-elements-for-bricks' ),
			'type'  => 'spacing',
			'css'   => [ [ 'property' => 'border-radius', 'selector' => '.wcf--meta-category a' ] ],
			'required' => [ 'layoutStyle', '=', '1' ],
		];

		$this->controls['categoryColor'] = [
			'tab' => 'style', 'group' => 'category',
			'label' => esc_html__( 'Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => [
				[ 'property' => 'color', 'selector' => '.wcf--meta-list .wcf--meta-category a' ],
				[ 'property' => 'color', 'selector' => '.wcf--meta-list .wcf--meta-category' ],
			],
		];

		$this->controls['categoryHoverColor'] = [
			'tab' => 'style', 'group' => 'category',
			'label' => esc_html__( 'Hover Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => [ [ 'property' => 'color', 'selector' => '.wcf--meta-list .wcf--meta-category a:hover' ] ],
		];

		$this->controls['categoryBg'] = [
			'tab' => 'style', 'group' => 'category',
			'label' => esc_html__( 'Background', 'bricksfly-elements-for-bricks' ),
			'type'  => 'background',
			'css'   => [ [ 'property' => 'background', 'selector' => '.wcf--meta-category a' ] ],
			'required' => [ 'layoutStyle', '=', '1' ],
		];

		$this->controls['categoryHoverBg'] = [
			'tab' => 'style', 'group' => 'category',
			'label' => esc_html__( 'Hover Background', 'bricksfly-elements-for-bricks' ),
			'type'  => 'background',
			'css'   => [ [ 'property' => 'background', 'selector' => '.wcf--meta-category a:hover' ] ],
			'required' => [ 'layoutStyle', '=', '1' ],
		];

		$this->controls['categoryShadow'] = [
			'tab' => 'style', 'group' => 'category',
			'label' => esc_html__( 'Shadow', 'bricksfly-elements-for-bricks' ),
			'type'  => 'box-shadow',
			'css'   => [ [ 'property' => 'box-shadow', 'selector' => '.wcf--meta-category a' ] ],
			'required' => [ 'layoutStyle', '=', '1' ],
		];

		$this->controls['categoryHoverShadow'] = [
			'tab' => 'style', 'group' => 'category',
			'label' => esc_html__( 'Hover Shadow', 'bricksfly-elements-for-bricks' ),
			'type'  => 'box-shadow',
			'css'   => [ [ 'property' => 'box-shadow', 'selector' => '.wcf--meta-category a:hover' ] ],
			'required' => [ 'layoutStyle', '=', '1' ],
		];

		$this->controls['categoryLabelColor'] = [
			'tab' => 'style', 'group' => 'category',
			'label' => esc_html__( 'Label Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => [ [ 'property' => 'color', 'selector' => '.wcf--category-title' ] ],
			'required' => [ 'layoutStyle', '=', '2' ],
		];

		$this->controls['categoryLabelTypo'] = [
			'tab' => 'style', 'group' => 'category',
			'label' => esc_html__( 'Label Typography', 'bricksfly-elements-for-bricks' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'font', 'selector' => '.wcf--category-title' ] ],
			'required' => [ 'layoutStyle', '=', '2' ],
		];

		$this->controls['categoryLabelSpacing'] = [
			'tab' => 'style', 'group' => 'category',
			'label' => esc_html__( 'Label Spacing', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
			'css'   => [ [ 'property' => 'margin-bottom', 'selector' => '.wcf--category-title' ] ],
			'required' => [ 'layoutStyle', '=', '2' ],
		];

		$this->controls['categoryIconSize'] = [
			'tab' => 'style', 'group' => 'category',
			'label' => esc_html__( 'Icon Size', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => [ 'px' => [ 'min' => 0, 'max' => 200 ] ],
			'css'   => [
				[ 'property' => 'font-size', 'selector' => '.wcf--category-wrap > i' ],
				[ 'property' => 'font-size', 'selector' => '.wcf--category-wrap > svg' ],
				[ 'property' => 'font-size', 'selector' => '.wcf--category-title i' ],
				[ 'property' => 'font-size', 'selector' => '.wcf--category-title svg' ],
			],
		];

		$this->controls['categoryIconColor'] = [
			'tab' => 'style', 'group' => 'category',
			'label' => esc_html__( 'Icon Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => [
				[ 'property' => 'color', 'selector' => '.wcf--category-wrap > i' ],
				[ 'property' => 'color', 'selector' => '.wcf--category-title i' ],
				[ 'property' => 'fill',  'selector' => '.wcf--category-wrap > svg' ],
				[ 'property' => 'fill',  'selector' => '.wcf--category-title svg' ],
			],
		];

		/* ---------- Style: Author ---------- */

		$this->controls['authorTypo'] = [
			'tab' => 'style', 'group' => 'author',
			'label' => esc_html__( 'Typography', 'bricksfly-elements-for-bricks' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'font', 'selector' => '.wcf--meta-list .wcf--meta-author a' ] ],
		];

		$this->controls['authorColor'] = [
			'tab' => 'style', 'group' => 'author',
			'label' => esc_html__( 'Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => [
				[ 'property' => 'color', 'selector' => '.wcf--meta-list .wcf--meta-author a' ],
				[ 'property' => 'color', 'selector' => '.wcf--meta-list .wcf--meta-author' ],
			],
		];

		$this->controls['authorHoverColor'] = [
			'tab' => 'style', 'group' => 'author',
			'label' => esc_html__( 'Hover Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => [ [ 'property' => 'color', 'selector' => '.wcf--meta-list .wcf--meta-author a:hover' ] ],
		];

		$this->controls['authorLabelColor'] = [
			'tab' => 'style', 'group' => 'author',
			'label' => esc_html__( 'Label Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => [ [ 'property' => 'color', 'selector' => '.wcf--author-title' ] ],
			'required' => [ 'layoutStyle', '=', '2' ],
		];

		$this->controls['authorLabelTypo'] = [
			'tab' => 'style', 'group' => 'author',
			'label' => esc_html__( 'Label Typography', 'bricksfly-elements-for-bricks' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'font', 'selector' => '.wcf--author-title' ] ],
			'required' => [ 'layoutStyle', '=', '2' ],
		];

		$this->controls['authorIconSize'] = [
			'tab' => 'style', 'group' => 'author',
			'label' => esc_html__( 'Icon / Avatar Size', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => [ 'px' => [ 'min' => 0, 'max' => 200 ] ],
			'css'   => [
				[ 'property' => 'font-size', 'selector' => '.wcf--meta-author i' ],
				[ 'property' => 'font-size', 'selector' => '.wcf--meta-author svg' ],
				[ 'property' => 'width',     'selector' => '.wcf-author-img img' ],
				[ 'property' => 'height',    'selector' => '.wcf-author-img img' ],
			],
		];

		$this->controls['authorIconColor'] = [
			'tab' => 'style', 'group' => 'author',
			'label' => esc_html__( 'Icon Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => [
				[ 'property' => 'color', 'selector' => '.wcf--meta-author i' ],
				[ 'property' => 'fill',  'selector' => '.wcf--meta-author svg' ],
			],
			'required' => [ 'layoutStyle', '=', '1' ],
		];

		$this->controls['authorImgRadius'] = [
			'tab' => 'style', 'group' => 'author',
			'label' => esc_html__( 'Avatar Radius', 'bricksfly-elements-for-bricks' ),
			'type'  => 'spacing',
			'css'   => [ [ 'property' => 'border-radius', 'selector' => '.wcf-author-img img' ] ],
			'required' => [ 'layoutStyle', '=', '2' ],
		];

		/* ---------- Style: Date ---------- */

		$this->controls['dateAlign'] = [
			'tab' => 'style', 'group' => 'date',
			'label' => esc_html__( 'Alignment', 'bricksfly-elements-for-bricks' ),
			'type'  => 'text-align',
			'css'   => [
				[ 'property' => 'align-items', 'selector' => '.wcf--date-wrap' ],
				[ 'property' => 'text-align',  'selector' => '.wcf--date-wrap' ],
			],
			'required' => [ 'layoutStyle', '=', '2' ],
		];

		$this->controls['dateColor'] = [
			'tab' => 'style', 'group' => 'date',
			'label' => esc_html__( 'Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => [ [ 'property' => 'color', 'selector' => '.wcf--meta-list .wcf--meta-date' ] ],
		];

		$this->controls['dateTypo'] = [
			'tab' => 'style', 'group' => 'date',
			'label' => esc_html__( 'Typography', 'bricksfly-elements-for-bricks' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'font', 'selector' => '.wcf--meta-list .wcf--meta-date' ] ],
		];

		$this->controls['dateLabelColor'] = [
			'tab' => 'style', 'group' => 'date',
			'label' => esc_html__( 'Label Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => [ [ 'property' => 'color', 'selector' => '.wcf--date-title' ] ],
			'required' => [ 'layoutStyle', '=', '2' ],
		];

		$this->controls['dateLabelTypo'] = [
			'tab' => 'style', 'group' => 'date',
			'label' => esc_html__( 'Label Typography', 'bricksfly-elements-for-bricks' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'font', 'selector' => '.wcf--date-title' ] ],
			'required' => [ 'layoutStyle', '=', '2' ],
		];

		$this->controls['dateIconSize'] = [
			'tab' => 'style', 'group' => 'date',
			'label' => esc_html__( 'Icon Size', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => [ 'px' => [ 'min' => 0, 'max' => 200 ] ],
			'css'   => [
				[ 'property' => 'font-size', 'selector' => '.wcf--meta-date i' ],
				[ 'property' => 'font-size', 'selector' => '.wcf--meta-date svg' ],
				[ 'property' => 'font-size', 'selector' => '.wcf--date-title i' ],
				[ 'property' => 'font-size', 'selector' => '.wcf--date-title svg' ],
			],
		];

		$this->controls['dateIconColor'] = [
			'tab' => 'style', 'group' => 'date',
			'label' => esc_html__( 'Icon Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => [
				[ 'property' => 'color', 'selector' => '.wcf--meta-list .wcf--meta-date i' ],
				[ 'property' => 'color', 'selector' => '.wcf--meta-list .wcf--date-title i' ],
				[ 'property' => 'fill',  'selector' => '.wcf--meta-list .wcf--meta-date svg' ],
				[ 'property' => 'fill',  'selector' => '.wcf--meta-list .wcf--date-title svg' ],
			],
		];

		/* ---------- Style: View / Reading Time ---------- */

		$this->controls['viewAlign'] = [
			'tab' => 'style', 'group' => 'view',
			'label' => esc_html__( 'Alignment', 'bricksfly-elements-for-bricks' ),
			'type'  => 'text-align',
			'css'   => [
				[ 'property' => 'align-items', 'selector' => '.wcf--view-wrap' ],
				[ 'property' => 'text-align',  'selector' => '.wcf--view-wrap' ],
			],
			'required' => [ 'layoutStyle', '=', '2' ],
		];

		$this->controls['viewDirection'] = [
			'tab' => 'style', 'group' => 'view',
			'label' => esc_html__( 'Direction', 'bricksfly-elements-for-bricks' ),
			'type'  => 'select',
			'options' => [
				'row'            => esc_html__( 'Row', 'bricksfly-elements-for-bricks' ),
				'column'         => esc_html__( 'Column', 'bricksfly-elements-for-bricks' ),
				'row-reverse'    => esc_html__( 'Row Reverse', 'bricksfly-elements-for-bricks' ),
				'column-reverse' => esc_html__( 'Column Reverse', 'bricksfly-elements-for-bricks' ),
			],
			'inline' => true,
			'css'    => [ [ 'property' => 'flex-direction', 'selector' => '.wcf--meta-view' ] ],
			'required' => [ 'layoutStyle', '=', '2' ],
		];

		$this->controls['viewGap'] = [
			'tab' => 'style', 'group' => 'view',
			'label' => esc_html__( 'Gap', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
			'css'   => [ [ 'property' => 'gap', 'selector' => '.wcf--meta-view' ] ],
			'required' => [ 'layoutStyle', '=', '2' ],
		];

		$this->controls['viewColor'] = [
			'tab' => 'style', 'group' => 'view',
			'label' => esc_html__( 'Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => [ [ 'property' => 'color', 'selector' => '.wcf--meta-list .wcf--meta-view' ] ],
		];

		$this->controls['viewTypo'] = [
			'tab' => 'style', 'group' => 'view',
			'label' => esc_html__( 'Typography', 'bricksfly-elements-for-bricks' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'font', 'selector' => '.wcf--meta-list .wcf--meta-view' ] ],
		];

		$this->controls['viewLabelColor'] = [
			'tab' => 'style', 'group' => 'view',
			'label' => esc_html__( 'Label Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => [ [ 'property' => 'color', 'selector' => '.wcf--view-title' ] ],
			'required' => [ 'layoutStyle', '=', '2' ],
		];

		$this->controls['viewLabelTypo'] = [
			'tab' => 'style', 'group' => 'view',
			'label' => esc_html__( 'Label Typography', 'bricksfly-elements-for-bricks' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'font', 'selector' => '.wcf--view-title' ] ],
			'required' => [ 'layoutStyle', '=', '2' ],
		];

		$this->controls['viewIconSize'] = [
			'tab' => 'style', 'group' => 'view',
			'label' => esc_html__( 'Icon Size', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => [ 'px' => [ 'min' => 0, 'max' => 200 ] ],
			'css'   => [
				[ 'property' => 'font-size', 'selector' => '.wcf--meta-view i' ],
				[ 'property' => 'font-size', 'selector' => '.wcf--meta-view svg' ],
				[ 'property' => 'font-size', 'selector' => '.wcf--view-title i' ],
				[ 'property' => 'font-size', 'selector' => '.wcf--view-title svg' ],
			],
		];

		$this->controls['viewIconColor'] = [
			'tab' => 'style', 'group' => 'view',
			'label' => esc_html__( 'Icon Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => [
				[ 'property' => 'color', 'selector' => '.wcf--meta-list .wcf--meta-view i' ],
				[ 'property' => 'color', 'selector' => '.wcf--meta-list .wcf--view-title i' ],
				[ 'property' => 'fill',  'selector' => '.wcf--meta-list .wcf--meta-view svg' ],
				[ 'property' => 'fill',  'selector' => '.wcf--meta-list .wcf--view-title svg' ],
			],
		];

		/* ---------- Style: Comment ---------- */

		$this->controls['commentAlign'] = [
			'tab' => 'style', 'group' => 'comment',
			'label' => esc_html__( 'Alignment', 'bricksfly-elements-for-bricks' ),
			'type'  => 'text-align',
			'css'   => [
				[ 'property' => 'align-items', 'selector' => '.wcf--comment-wrap' ],
				[ 'property' => 'text-align',  'selector' => '.wcf--comment-wrap' ],
			],
			'required' => [ 'layoutStyle', '=', '2' ],
		];

		$this->controls['commentColor'] = [
			'tab' => 'style', 'group' => 'comment',
			'label' => esc_html__( 'Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => [ [ 'property' => 'color', 'selector' => '.wcf--meta-list .wcf--meta-comment' ] ],
		];

		$this->controls['commentTypo'] = [
			'tab' => 'style', 'group' => 'comment',
			'label' => esc_html__( 'Typography', 'bricksfly-elements-for-bricks' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'font', 'selector' => '.wcf--meta-list .wcf--meta-comment' ] ],
		];

		$this->controls['commentLabelColor'] = [
			'tab' => 'style', 'group' => 'comment',
			'label' => esc_html__( 'Label Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => [ [ 'property' => 'color', 'selector' => '.wcf--comment-title' ] ],
			'required' => [ 'layoutStyle', '=', '2' ],
		];

		$this->controls['commentIconSize'] = [
			'tab' => 'style', 'group' => 'comment',
			'label' => esc_html__( 'Icon Size', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => [ 'px' => [ 'min' => 0, 'max' => 200 ] ],
			'css'   => [
				[ 'property' => 'font-size', 'selector' => '.wcf--meta-comment i' ],
				[ 'property' => 'font-size', 'selector' => '.wcf--meta-comment svg' ],
				[ 'property' => 'font-size', 'selector' => '.wcf--comment-title i' ],
				[ 'property' => 'font-size', 'selector' => '.wcf--comment-title svg' ],
			],
		];

		$this->controls['commentIconColor'] = [
			'tab' => 'style', 'group' => 'comment',
			'label' => esc_html__( 'Icon Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => [
				[ 'property' => 'color', 'selector' => '.wcf--meta-comment i' ],
				[ 'property' => 'color', 'selector' => '.wcf--comment-title i' ],
				[ 'property' => 'fill',  'selector' => '.wcf--meta-comment svg' ],
				[ 'property' => 'fill',  'selector' => '.wcf--comment-title svg' ],
			],
		];

		/* ---------- Style: Time Ago ---------- */

		$this->controls['ptimeAlign'] = [
			'tab' => 'style', 'group' => 'ptime',
			'label' => esc_html__( 'Alignment', 'bricksfly-elements-for-bricks' ),
			'type'  => 'text-align',
			'css'   => [
				[ 'property' => 'align-items', 'selector' => '.post-time-ago-wrap' ],
				[ 'property' => 'text-align',  'selector' => '.post-time-ago-wrap' ],
			],
			'required' => [ 'layoutStyle', '=', '2' ],
		];

		$this->controls['ptimeColor'] = [
			'tab' => 'style', 'group' => 'ptime',
			'label' => esc_html__( 'Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => [ [ 'property' => 'color', 'selector' => '.wcf--meta-list .post-time-ago' ] ],
		];

		$this->controls['ptimeTypo'] = [
			'tab' => 'style', 'group' => 'ptime',
			'label' => esc_html__( 'Typography', 'bricksfly-elements-for-bricks' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'font', 'selector' => '.wcf--meta-list .post-time-ago' ] ],
		];

		$this->controls['ptimeLabelColor'] = [
			'tab' => 'style', 'group' => 'ptime',
			'label' => esc_html__( 'Label Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => [ [ 'property' => 'color', 'selector' => '.time-ago-title' ] ],
			'required' => [ 'layoutStyle', '=', '2' ],
		];

		$this->controls['ptimeIconSize'] = [
			'tab' => 'style', 'group' => 'ptime',
			'label' => esc_html__( 'Icon Size', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => [ 'px' => [ 'min' => 0, 'max' => 200 ] ],
			'css'   => [
				[ 'property' => 'font-size', 'selector' => '.post-time-ago i' ],
				[ 'property' => 'font-size', 'selector' => '.post-time-ago svg' ],
				[ 'property' => 'font-size', 'selector' => '.time-ago-title i' ],
				[ 'property' => 'font-size', 'selector' => '.time-ago-title svg' ],
			],
		];

		$this->controls['ptimeIconColor'] = [
			'tab' => 'style', 'group' => 'ptime',
			'label' => esc_html__( 'Icon Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => [
				[ 'property' => 'color', 'selector' => '.post-time-ago i' ],
				[ 'property' => 'color', 'selector' => '.time-ago-title i' ],
				[ 'property' => 'fill',  'selector' => '.post-time-ago svg' ],
				[ 'property' => 'fill',  'selector' => '.time-ago-title svg' ],
			],
		];
	}

	/**
	 * In a Bricks template (e.g., a Single template) we may not be in the
	 * proper post context — fall back to a recent post so the builder preview
	 * has something to render against.
	 */
	protected function maybe_setup_template_post() {
		$post_type = get_post_type();
		if ( 'bricks_template' !== $post_type ) {
			return false;
		}

		$recent = wp_get_recent_posts( [ 'numberposts' => 1, 'post_status' => 'publish' ] );
		if ( empty( $recent[0]['ID'] ) ) {
			return false;
		}

		global $post;
		$post = get_post( (int) $recent[0]['ID'] );
		setup_postdata( $post );

		return true;
	}

	public function render() {
		$settings   = $this->settings;
		$meta_list  = ! empty( $settings['metaList'] ) && is_array( $settings['metaList'] ) ? $settings['metaList'] : [];
		$layout     = isset( $settings['layoutStyle'] ) ? (string) $settings['layoutStyle'] : '1';
		$show_title = ! empty( $settings['showTitle'] );

		if ( empty( $meta_list ) ) {
			$this->render_element_placeholder( [
				'title' => esc_html__( 'Add at least one meta item.', 'bricksfly-elements-for-bricks' ),
			] );
			return;
		}

		$switched = $this->maybe_setup_template_post();

		$this->set_attribute( '_root', 'class', [ 'wcf--meta-list', 'style-' . sanitize_html_class( $layout ) ] );

		echo wp_kses_post('<ul ' . $this->render_attributes( '_root' ) . '>');

		foreach ( $meta_list as $meta ) {
			$type = isset( $meta['listType'] ) ? $meta['listType'] : '';

			switch ( $type ) {
				case 'date':         $this->render_date( $meta, $layout, $show_title );        break;
				case 'category':     $this->render_categories( $meta, $layout, $show_title, $settings ); break;
				case 'author':       $this->render_author( $meta, $layout, $show_title );      break;
				case 'view':         $this->render_view_count( $meta, $layout, $show_title );  break;
				case 'reading_time': $this->render_reading_time( $meta, $layout, $show_title );break;
				case 'comment':      $this->render_comments( $meta, $layout, $show_title, $settings ); break;
				case 'time-ago':     $this->render_post_time_ago( $meta, $layout, $show_title );break;
				case 'review':       $this->render_reviews_count( $meta, $layout, $show_title );break;
				case 'read-later':   $this->render_read_later( $meta, $layout, $show_title );  break;
				case 'last-update':  $this->render_last_updated( $meta, $layout, $show_title );break;
			}
		}

		echo '</ul>';

		if ( $switched ) {
			wp_reset_postdata();
		}
	}

	/* ---------- Renderers ---------- */

	protected function open_li( $classes, $separator ) {
		$cls = is_array( $classes ) ? implode( ' ', array_map( 'sanitize_html_class', $classes ) ) : sanitize_html_class( $classes );
		echo '<li class="' . esc_attr( $cls ) . '" data-separator="' . esc_attr( $separator ) . '">';
	}

	protected function maybe_title_inline( $meta, $show_title ) {
		if ( $show_title && ! empty( $meta['listTitle'] ) ) {
			echo '<span>' . wp_kses_post( $meta['listTitle'] ) . '</span> ';
		}
	}

	protected function maybe_icon( $meta ) {
		if ( ! empty( $meta['listIcon'] ) ) {
			echo wp_kses_post(self::render_icon( $meta['listIcon'], [ 'aria-hidden' => 'true' ] ));
		}
	}

	protected function label_block( $meta ) {
		echo '<div class="' . esc_attr( $this->label_block_class( $meta ) ) . ' label">';
		$this->maybe_icon( $meta );
		echo esc_html( ! empty( $meta['listTitle'] ) ? $meta['listTitle'] : '' );
		echo '</div>';
	}

	protected function label_block_class( $meta ) {
		$map = [
			'date'         => 'wcf--date-title',
			'last-update'  => 'wcf--date-title',
			'category'     => 'wcf--category-title',
			'author'       => 'wcf--author-title',
			'view'         => 'wcf--view-title',
			'reading_time' => 'wcf--view-title',
			'review'       => 'wcf--view-title',
			'read-later'   => 'wcf--view-title',
			'comment'      => 'wcf--comment-title',
			'time-ago'     => 'time-ago-title',
		];
		$type = isset( $meta['listType'] ) ? $meta['listType'] : '';
		return isset( $map[ $type ] ) ? $map[ $type ] : '';
	}

	protected function render_date( $meta, $layout, $show_title ) {
		$separator = isset( $meta['metaSeparator'] ) ? $meta['metaSeparator'] : '';
		$date      = get_the_date( get_option( 'date_format' ) );

		if ( '1' === $layout ) {
			$this->open_li( [ 'wcf--meta-date', 'wcf-separator' ], $separator );
			$this->maybe_title_inline( $meta, $show_title );
			$this->maybe_icon( $meta );
			echo esc_html( $date );
			echo '</li>';
		} else {
			echo '<li class="wcf--date-wrap">';
			$this->label_block( $meta );
			echo '<div class="wcf--meta-date">' . esc_html( $date ) . '</div>';
			echo '</li>';
		}
	}

	protected function render_last_updated( $meta, $layout, $show_title ) {
		$separator = isset( $meta['metaSeparator'] ) ? $meta['metaSeparator'] : '';
		$date      = get_the_modified_time( get_option( 'date_format' ) );

		if ( '1' === $layout ) {
			$this->open_li( [ 'wcf--meta-date', 'wcf-separator' ], $separator );
			$this->maybe_title_inline( $meta, $show_title );
			$this->maybe_icon( $meta );
			echo wp_kses_post( $date );
			echo '</li>';
		} else {
			echo '<li class="wcf--date-wrap">';
			$this->label_block( $meta );
			echo '<div class="wcf--meta-date">' . wp_kses_post( $date ) . '</div>';
			echo '</li>';
		}
	}

	protected function render_categories( $meta, $layout, $show_title, $settings ) {
		$separator = isset( $meta['metaSeparator'] ) ? $meta['metaSeparator'] : '';
		$multiple  = ! empty( $meta['multipleCategory'] );
		$limit     = isset( $meta['categoryLimit'] ) && is_numeric( $meta['categoryLimit'] ) ? (int) $meta['categoryLimit'] : 0;
		$hover_cls = isset( $settings['categoryHoverList'] ) ? $settings['categoryHoverList'] : 'hover-none';

		$cats = get_the_category();
		if ( empty( $cats ) ) {
			return;
		}
		// shuffle to mirror Elementor source behavior
		shuffle( $cats );

		if ( '1' === $layout ) {
			if ( $multiple ) {
				echo '<li class="wcf--category-wrap">';
				$this->maybe_title_inline( $meta, $show_title );
				$this->maybe_icon( $meta );
				echo '<ul class="wcf--category-list">';
				foreach ( $cats as $key => $term ) {
					if ( $limit && $key >= $limit ) break;
					echo '<li class="wcf--meta-category wcf-separator" data-separator="' . esc_attr( $separator ) . '">';
					echo '<a class="wcf-btn-default btn-' . esc_attr( $hover_cls ) . '" href="' . esc_url( get_category_link( $term->term_id ) ) . '">';
					echo esc_html( get_cat_name( $term->term_id ) );
					echo '</a>';
					echo '</li>';
				}
				echo '</ul>';
				echo '</li>';
			} else {
				$term = $cats[0];
				$this->open_li( [ 'wcf--meta-category', 'wcf-separator' ], $separator );
				$this->maybe_title_inline( $meta, $show_title );
				$this->maybe_icon( $meta );
				echo '<a class="wcf-btn-default btn-' . esc_attr( $hover_cls ) . '" href="' . esc_url( get_category_link( $term->term_id ) ) . '">';
				echo esc_html( get_cat_name( $term->term_id ) );
				echo '</a>';
				echo '</li>';
			}
		} else {
			if ( $multiple ) {
				echo '<li class="wcf--category-wrap">';
				$this->label_block( $meta );
				echo '<ul class="wcf--category-list">';
				foreach ( $cats as $key => $term ) {
					if ( $limit && $key >= $limit ) break;
					echo '<li class="wcf--meta-category wcf-separator" data-separator="' . esc_attr( $separator ) . '">';
					echo '<a class="wcf-btn-default btn-' . esc_attr( $hover_cls ) . '" href="' . esc_url( get_category_link( $term->term_id ) ) . '">';
					echo esc_html( get_cat_name( $term->term_id ) );
					echo '</a>';
					echo '</li>';
				}
				echo '</ul>';
				echo '</li>';
			} else {
				$term = $cats[0];
				echo '<li class="wcf--meta-category">';
				$this->label_block( $meta );
				echo '<a class="wcf-btn-default btn-' . esc_attr( $hover_cls ) . '" href="' . esc_url( get_category_link( $term->term_id ) ) . '">';
				echo esc_html( get_cat_name( $term->term_id ) );
				echo '</a>';
				echo '</li>';
			}
		}
	}

	protected function render_author( $meta, $layout, $show_title ) {
		global $post;
		if ( ! $post ) {
			return;
		}
		$separator   = isset( $meta['metaSeparator'] ) ? $meta['metaSeparator'] : '';
		$author_id   = $post->post_author;
		$author_name = get_the_author_meta( 'display_name', $author_id );
		$author_url  = get_author_posts_url( $author_id );

		if ( '1' === $layout ) {
			$this->open_li( [ 'wcf--meta-author', 'wcf-separator' ], $separator );
			$this->maybe_icon( $meta );
			echo '<a href="' . esc_url( $author_url ) . '">';
			$this->maybe_title_inline( $meta, $show_title );
			echo esc_html( $author_name );
			echo '</a>';
			echo '</li>';
		} else {
			$avatar = get_avatar( $author_id, 55 );
			echo '<li class="wcf--author-wrap">';
			echo '<div class="wcf-author-img">' . wp_kses_post( $avatar ) . '</div>';
			echo '<div class="wcf--author-info">';
			echo '<div class="wcf--author-title label">' . esc_html( $meta['listTitle'] ?? '' ) . '</div>';
			echo '<div class="wcf--meta-author"><a href="' . esc_url( $author_url ) . '">' . esc_html( $author_name ) . '</a></div>';
			echo '</div>';
			echo '</li>';
		}
	}

	protected function render_view_count( $meta, $layout, $show_title ) {
		$separator = isset( $meta['metaSeparator'] ) ? $meta['metaSeparator'] : '';
		$views     = (string) get_post_meta( get_the_ID(), 'thebrbre_post_views_count', true );
		if ( $views === '' ) {
			$views = '0';
		}

		if ( '1' === $layout ) {
			$this->open_li( [ 'wcf--meta-view', 'wcf-separator' ], $separator );
			$this->maybe_title_inline( $meta, $show_title );
			$this->maybe_icon( $meta );
			echo esc_html( $views ) . '&nbsp;';
			echo esc_html__( 'Views', 'bricksfly-elements-for-bricks' );
			echo '</li>';
		} else {
			echo '<li class="wcf--view-wrap">';
			$this->label_block( $meta );
			echo '<div class="wcf--meta-view">' . esc_html( $views ) . '&nbsp;<span>' . esc_html__( 'Views', 'bricksfly-elements-for-bricks' ) . '</span></div>';
			echo '</li>';
		}
	}

	protected function render_reading_time( $meta, $layout, $show_title ) {
		$separator = isset( $meta['metaSeparator'] ) ? $meta['metaSeparator'] : '';
		$content   = get_the_content();
		$word_cnt  = str_word_count( wp_strip_all_tags( $content ) );
		$time      = max( 1, (int) ceil( $word_cnt / 200 ) );
		$suffix    = $time <= 1 ? esc_html__( 'minute read', 'bricksfly-elements-for-bricks' ) : esc_html__( 'minutes read', 'bricksfly-elements-for-bricks' );

		if ( '1' === $layout ) {
			$this->open_li( [ 'wcf--meta-view', 'wcf-separator' ], $separator );
			$this->maybe_title_inline( $meta, $show_title );
			$this->maybe_icon( $meta );
			echo esc_html( $time ) . ' ' . esc_html( $suffix );
			echo '</li>';
		} else {
			echo '<li class="wcf--view-wrap">';
			$this->label_block( $meta );
			echo '<div class="wcf--meta-view">' . esc_html( $time ) . '&nbsp;<span>' . esc_html( $suffix ) . '</span></div>';
			echo '</li>';
		}
	}

	protected function render_comments( $meta, $layout, $show_title, $settings ) {
		$separator   = isset( $meta['metaSeparator'] ) ? $meta['metaSeparator'] : '';
		$share_icon  = ! empty( $settings['shareSeparator'] ) && ! empty( $settings['shareSeparatorIcon'] ) ? $settings['shareSeparatorIcon'] : null;

		if ( '1' === $layout ) {
			$this->open_li( [ 'wcf--meta-comment', 'wcf-separator' ], $separator );
			$this->maybe_title_inline( $meta, $show_title );
			$this->maybe_icon( $meta );
			if ( $share_icon ) {
				echo wp_kses_post('<span class="separator wcf_separator_icon">' . self::render_icon( $share_icon, [ 'aria-hidden' => 'true' ] ) . '</span>');
			}
			ob_start(); comments_number(); echo wp_kses_post( ob_get_clean() );
			echo '</li>';
		} else {
			echo '<li class="wcf--comment-wrap">';
			$this->label_block( $meta );
			echo '<div class="wcf--meta-comment">';
			ob_start(); comments_number(); echo wp_kses_post( ob_get_clean() );
			echo '</div>';
			echo '</li>';
		}
	}

	protected function render_post_time_ago( $meta, $layout, $show_title ) {
		$separator    = isset( $meta['metaSeparator'] ) ? $meta['metaSeparator'] : '';
		$posted_ts    = get_the_time( 'U' );
		$current_ts   = current_time( 'timestamp' );
		$diff         = max( 0, $current_ts - $posted_ts );
		$time_ago     = $this->humanize_diff( $diff );

		if ( '1' === $layout ) {
			$this->open_li( [ 'post-time-ago', 'wcf-separator' ], $separator );
			$this->maybe_title_inline( $meta, $show_title );
			$this->maybe_icon( $meta );
			echo esc_html( $time_ago );
			echo '</li>';
		} else {
			echo '<li class="post-time-ago-wrap">';
			$this->label_block( $meta );
			echo '<div class="post-time-ago">' . esc_html( $time_ago ) . '</div>';
			echo '</li>';
		}
	}

	protected function humanize_diff( $diff ) {
		if ( $diff < MINUTE_IN_SECONDS ) {
			$n = max( 1, (int) $diff );
			return $n . ' second' . ( $n > 1 ? 's' : '' ) . ' ago';
		}
		if ( $diff < HOUR_IN_SECONDS ) {
			$n = (int) floor( $diff / MINUTE_IN_SECONDS );
			return $n . ' minute' . ( $n > 1 ? 's' : '' ) . ' ago';
		}
		if ( $diff < DAY_IN_SECONDS ) {
			$n = (int) floor( $diff / HOUR_IN_SECONDS );
			return $n . ' hour' . ( $n > 1 ? 's' : '' ) . ' ago';
		}
		if ( $diff < WEEK_IN_SECONDS ) {
			$n = (int) floor( $diff / DAY_IN_SECONDS );
			return $n . ' day' . ( $n > 1 ? 's' : '' ) . ' ago';
		}
		if ( $diff < ( 30 * DAY_IN_SECONDS ) ) {
			$n = (int) floor( $diff / WEEK_IN_SECONDS );
			return $n . ' week' . ( $n > 1 ? 's' : '' ) . ' ago';
		}
		if ( $diff < ( 365 * DAY_IN_SECONDS ) ) {
			$n = (int) floor( $diff / ( 30 * DAY_IN_SECONDS ) );
			return $n . ' month' . ( $n > 1 ? 's' : '' ) . ' ago';
		}
		$n = (int) floor( $diff / ( 365 * DAY_IN_SECONDS ) );
		return $n . ' year' . ( $n > 1 ? 's' : '' ) . ' ago';
	}

	protected function render_reviews_count( $meta, $layout, $show_title ) {
		$separator = isset( $meta['metaSeparator'] ) ? $meta['metaSeparator'] : '';

		if ( ! post_type_exists( 'thebrbre_post_rating' ) ) {
			echo '<li class="wcf--meta-view">' . esc_html__( '0 review (Pro feature)', 'bricksfly-elements-for-bricks' ) . '</li>';
			return;
		}

		$post_id = get_the_ID();
		$cache_key = 'bricksfly_ratings_' . $post_id;
		$ratings = wp_cache_get( $cache_key, 'bricksfly-elements-for-bricks' );

		if ( false === $ratings ) {
			$ratings = get_posts( [
				'post_type'   => 'thebrbre_post_rating',
				'post_status' => 'publish',
				'numberposts' => -1,
				'fields'      => 'ids',
				// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Lookup is cached; rating volume is low.
				'meta_query'  => [
					[ 'key' => 'post_id', 'value' => $post_id ],
				],
			] );
			wp_cache_set( $cache_key, $ratings, 'bricksfly-elements-for-bricks', HOUR_IN_SECONDS );
		}
		$total = is_array( $ratings ) ? count( $ratings ) : 0;

		if ( '1' === $layout ) {
			$this->open_li( [ 'wcf--meta-view', 'wcf-separator' ], $separator );
			$this->maybe_title_inline( $meta, $show_title );
			$this->maybe_icon( $meta );
			echo esc_html( $total ) . ' ' . esc_html__( 'reviews', 'bricksfly-elements-for-bricks' );
			echo '</li>';
		} else {
			echo '<li class="wcf--view-wrap">';
			$this->label_block( $meta );
			echo '<div class="wcf--meta-view">' . esc_html( $total ) . '&nbsp;<span>' . esc_html__( 'reviews', 'bricksfly-elements-for-bricks' ) . '</span></div>';
			echo '</li>';
		}
	}

	protected function render_read_later( $meta, $layout, $show_title ) {
		$separator = isset( $meta['metaSeparator'] ) ? $meta['metaSeparator'] : '';
		$post_id   = (int) get_the_ID();

		if ( '1' === $layout ) {
			$this->open_li( [ 'wcf--meta-view', 'wcf-separator' ], $separator );
			$this->maybe_title_inline( $meta, $show_title );
			$this->maybe_icon( $meta );
			echo '<span class="aae-post-read-later" data-post-id="' . esc_attr( $post_id ) . '">' . esc_html__( 'Save', 'bricksfly-elements-for-bricks' ) . '</span>';
			echo '</li>';
		} else {
			echo '<li class="wcf--view-wrap">';
			$this->label_block( $meta );
			echo '<div class="wcf--meta-view"><span class="aae-post-read-later" data-post-id="' . esc_attr( $post_id ) . '">' . esc_html__( 'Save', 'bricksfly-elements-for-bricks' ) . '</span></div>';
			echo '</li>';
		}
	}
}
