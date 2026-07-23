<?php

if (!defined('ABSPATH')) exit;

class Aae_Bricks_Timeline extends \Bricks\Element
{
    public $category     = 'bricks fly';
    public $name         = 'aae-timeline';
    public $icon         = 'ti-time aab-element-marker';
    public $css_selector = '.aae-timeline-wrapper';

    public function get_label()
    {
        return esc_html__('Timeline', 'the-bricksfly');
    }

    public function get_keywords()
    {
        return ['timeline', 'step', 'history', 'journey'];
    }

    public function set_control_groups()
    {
        $this->control_groups['layout'] = [
            'title' => esc_html__('Layout', 'the-bricksfly'),
            'tab'   => 'content',
        ];
        $this->control_groups['timeline'] = [
            'title' => esc_html__('Timeline', 'the-bricksfly'),
            'tab'   => 'content',
        ];
        $this->control_groups['timeline_style'] = [
            'title' => esc_html__('Timeline', 'the-bricksfly'),
            'tab'   => 'style',
        ];
        $this->control_groups['content_style'] = [
            'title' => esc_html__('Content', 'the-bricksfly'),
            'tab'   => 'style',
        ];
        $this->control_groups['step_style'] = [
            'title' => esc_html__('Step', 'the-bricksfly'),
            'tab'   => 'style',
        ];
    }

    public function set_controls()
    {
        // === LAYOUT ===
        $this->controls['elementList'] = [
            'group'   => 'layout',
            'label'   => esc_html__('Style', 'the-bricksfly'),
            'type'    => 'select',
            'options' => [
                '1' => esc_html__('One', 'the-bricksfly'),
                '2' => esc_html__('Two', 'the-bricksfly'),
            ],
            'default' => '1',
            'inline'  => true,
        ];

        $this->controls['titleTag'] = [
            'group'   => 'layout',
            'label'   => esc_html__('Title HTML Tag', 'the-bricksfly'),
            'type'    => 'select',
            'options' => [
                'h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4', 'h5' => 'H5', 'h6' => 'H6',
                'div' => 'div', 'span' => 'span', 'p' => 'p',
            ],
            'default' => 'h3',
            'inline'  => true,
        ];

        $this->controls['dateIcon'] = [
            'group'   => 'layout',
            'label'   => esc_html__('Date Icon', 'the-bricksfly'),
            'type'    => 'icon',
            'default' => [
                'library' => 'fontawesome',
                'icon'    => 'fas fa-calendar-alt',
            ],
        ];

        $this->controls['imageSize'] = [
            'group'   => 'layout',
            'label'   => esc_html__('Image Size', 'the-bricksfly'),
            'type'    => 'select',
            'options' => $this->control_options['imageSizes'] ?? [
                'thumbnail' => esc_html__('Thumbnail', 'the-bricksfly'),
                'medium'    => esc_html__('Medium', 'the-bricksfly'),
                'large'     => esc_html__('Large', 'the-bricksfly'),
                'full'      => esc_html__('Full', 'the-bricksfly'),
            ],
            'default' => 'full',
        ];

        $this->controls['imagePosition'] = [
            'group'   => 'layout',
            'label'   => esc_html__('Image Position', 'the-bricksfly'),
            'type'    => 'select',
            'options' => [
                'column' => esc_html__('Top', 'the-bricksfly'),
                'row'    => esc_html__('Aside', 'the-bricksfly'),
            ],
            'default' => 'column',
            'inline'  => true,
            'css'     => [['property' => 'flex-direction', 'selector' => '.content-wrap']],
        ];

        $this->controls['align'] = [
            'group' => 'layout',
            'label' => esc_html__('Alignment', 'the-bricksfly'),
            'type'  => 'text-align',
            'css'   => [['property' => 'text-align', 'selector' => '']],
        ];

        $this->controls['showIndicator'] = [
            'group'   => 'layout',
            'label'   => esc_html__('Indicator', 'the-bricksfly'),
            'type'    => 'checkbox',
            'default' => true,
        ];

        // === TIMELINE (repeater) ===
        $this->controls['timelineItems'] = [
            'group'         => 'timeline',
            'label'         => esc_html__('Timelines', 'the-bricksfly'),
            'type'          => 'repeater',
            'titleProperty' => 'timelineTitle',
            'fields'        => [
                'stepType' => [
                    'label'   => esc_html__('Step Type', 'the-bricksfly'),
                    'type'    => 'select',
                    'options' => [
                        'icon' => esc_html__('Icon', 'the-bricksfly'),
                        'text' => esc_html__('Text', 'the-bricksfly'),
                    ],
                    'default' => 'icon',
                    'inline'  => true,
                ],
                'stepIcon' => [
                    'label'    => esc_html__('Step Icon', 'the-bricksfly'),
                    'type'     => 'icon',
                    'default'  => ['library' => 'fontawesome', 'icon' => 'fas fa-dot-circle'],
                    'required' => ['stepType', '=', 'icon'],
                ],
                'stepText' => [
                    'label'    => esc_html__('Step Text', 'the-bricksfly'),
                    'type'     => 'text',
                    'default'  => '01',
                    'required' => ['stepType', '=', 'text'],
                ],
                'timelineImage' => [
                    'label' => esc_html__('Choose Image', 'the-bricksfly'),
                    'type'  => 'image',
                ],
                'timelineDate' => [
                    'label'   => esc_html__('Date', 'the-bricksfly'),
                    'type'    => 'text',
                    'default' => esc_html__( 'Jan 01, 2021', 'the-bricksfly' ),
                ],
                'timelineTitle' => [
                    'label'   => esc_html__('Title', 'the-bricksfly'),
                    'type'    => 'text',
                    'default' => esc_html__( 'Journey Started at New York', 'the-bricksfly' ),
                ],
                'timelineSub' => [
                    'label'   => esc_html__('Sub Title', 'the-bricksfly'),
                    'type'    => 'text',
                    'default' => esc_html__( 'Designer', 'the-bricksfly' ),
                ],
                'timelineDesc' => [
                    'label'   => esc_html__('Content', 'the-bricksfly'),
                    'type'    => 'textarea',
                    'default' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'the-bricksfly'),
                ],
                'link' => [
                    'label' => esc_html__('Link', 'the-bricksfly'),
                    'type'  => 'link',
                ],
            ],
            'default' => [
                ['timelineTitle' => esc_html__( 'Journey Started at New York', 'the-bricksfly' ), 'timelineSub' => esc_html__( 'Designer', 'the-bricksfly' ), 'timelineDate' => esc_html__( 'Jan 01, 2021', 'the-bricksfly' ), 'stepType' => 'icon'],
                ['timelineTitle' => esc_html__( 'Moved to San Francisco', 'the-bricksfly' ), 'timelineSub' => esc_html__( 'Developer', 'the-bricksfly' ), 'timelineDate' => esc_html__( 'Mar 15, 2022', 'the-bricksfly' ), 'stepType' => 'icon'],
                ['timelineTitle' => esc_html__( 'Launched First Product', 'the-bricksfly' ), 'timelineSub' => esc_html__( 'Founder', 'the-bricksfly' ), 'timelineDate' => esc_html__( 'Jun 20, 2023', 'the-bricksfly' ), 'stepType' => 'icon'],
            ],
        ];

        // === STYLE: TIMELINE ===
        $this->controls['contentGap'] = [
            'group' => 'timeline_style',
            'label' => esc_html__('Space', 'the-bricksfly'),
            'type'  => 'number',
            'default' => 60,
            'placeholder' => '60',
            'units' => ['px' => ['min' => 1, 'max' => 300]],
            'css'   => [['property' => '--content-gap', 'selector' => '&.aae-timeline']],
        ];

        $this->controls['contentBottomSpace'] = [
            'group' => 'timeline_style',
            'label' => esc_html__('Content bottom space', 'the-bricksfly'),
            'type'  => 'number',
            'default' => '10px',
            'placeholder' => '10',
            'units' => ['px' => ['min' => 1, 'max' => 300]],
            'css'   => [['property' => 'margin-bottom', 'selector' => '.timeline-item:not(:last-child) .content-wrap']],
        ];

        // === STYLE: CONTENT ===
        $this->controls['contentBg'] = [
            'group' => 'content_style',
            'label' => esc_html__('Background', 'the-bricksfly'),
            'type'  => 'color',
            'css'   => [['property' => 'background-color', 'selector' => '.content-wrap']],
        ];

        $this->controls['contentWrapperPadding'] = [
            'group' => 'content_style',
            'label' => esc_html__('Wrapper padding', 'the-bricksfly'),
            'type'  => 'dimensions',
            'css'   => [['property' => 'padding', 'selector' => '.content-wrap']],
        ];

        $this->controls['contentPadding'] = [
            'group' => 'content_style',
            'label' => esc_html__('Content padding', 'the-bricksfly'),
            'type'  => 'dimensions',
            'css'   => [['property' => 'padding', 'selector' => '.content-wrap .content']],
        ];

        // $this->controls['contentBorderRadius'] = [
        //     'group' => 'content_style',
        //     'label' => esc_html__('Border Radius', 'the-bricksfly'),
        //     'type'  => 'dimensions',
        //     'css'   => [['property' => 'border-radius', 'selector' => '.content-wrap']],
        // ];

        $this->controls['contentBorder'] = [
            'group' => 'content_style',
            'label' => esc_html__('Border', 'the-bricksfly'),
            'type'  => 'border',
            'css'   => [['property' => 'border', 'selector' => '.content-wrap']],
        ];

        

        $this->controls['headingImage'] = [
            'group' => 'content_style',
            'label' => esc_html__('Image', 'the-bricksfly'),
            'type'  => 'separator',
        ];

        $this->controls['imgWidth'] = [
            'group' => 'content_style',
            'label' => esc_html__('Width', 'the-bricksfly'),
            'type'  => 'number',
            'units' => ['px' => ['min' => 1, 'max' => 500], '%' => ['min' => 1, 'max' => 100]],
            'css'   => [['property' => 'width', 'selector' => 'img']],
        ];

        $this->controls['imgHeight'] = [
            'group' => 'content_style',
            'label' => esc_html__('Height', 'the-bricksfly'),
            'type'  => 'number',
            'units' => ['px' => ['min' => 1, 'max' => 500], 'vh' => ['min' => 1, 'max' => 100]],
            'css'   => [['property' => 'height', 'selector' => 'img']],
        ];

        $this->controls['imgObjectFit'] = [
            'group'   => 'content_style',
            'label'   => esc_html__('Object Fit', 'the-bricksfly'),
            'type'    => 'select',
            'options' => [
                'fill'    => esc_html__('Fill', 'the-bricksfly'),
                'cover'   => esc_html__('Cover', 'the-bricksfly'),
                'contain' => esc_html__('Contain', 'the-bricksfly'),
            ],
            'css' => [['property' => 'object-fit', 'selector' => 'img']],
        ];

        $this->controls['imgObjectPosition'] = [
            'group'   => 'content_style',
            'label'   => esc_html__('Object Position', 'the-bricksfly'),
            'type'    => 'select',
            'options' => [
                'center center' => esc_html__('Center Center', 'the-bricksfly'),
                'center left'   => esc_html__('Center Left', 'the-bricksfly'),
                'center right'  => esc_html__('Center Right', 'the-bricksfly'),
                'top center'    => esc_html__('Top Center', 'the-bricksfly'),
                'top left'      => esc_html__('Top Left', 'the-bricksfly'),
                'top right'     => esc_html__('Top Right', 'the-bricksfly'),
                'bottom center' => esc_html__('Bottom Center', 'the-bricksfly'),
                'bottom left'   => esc_html__('Bottom Left', 'the-bricksfly'),
                'bottom right'  => esc_html__('Bottom Right', 'the-bricksfly'),
            ],
            'css'      => [['property' => 'object-position', 'selector' => 'img']],
            'required' => ['imgObjectFit', '=', 'cover'],
        ];

        $this->controls['imgBorder'] = [
            'group' => 'content_style',
            'label' => esc_html__('Border', 'the-bricksfly'),
            'type'  => 'border',
            'css'   => [['property' => 'border', 'selector' => 'img']],
        ];

        // $this->controls['imgBorderRadius'] = [
        //     'group' => 'content_style',
        //     'label' => esc_html__('Image Border Radius', 'the-bricksfly'),
        //     'type'  => 'spacing',
        //     'css'   => [['property' => 'border-radius', 'selector' => 'img']],
        // ];

        $this->controls['imageSpace'] = [
            'group'   => 'content_style',
            'label'   => esc_html__('Spacing', 'the-bricksfly'),
            'type'    => 'number',
            'units'   => ['px' => ['min' => 0, 'max' => 100]],
            'css'     => [['property' => 'gap', 'selector' => '.content-wrap']],
        ];

        $this->controls['headingDate'] = [
            'group' => 'content_style',
            'label' => esc_html__('Date', 'the-bricksfly'),
            'type'  => 'separator',
        ];

        $this->controls['dateColor'] = [
            'group' => 'content_style',
            'label' => esc_html__('Color', 'the-bricksfly'),
            'type'  => 'color',
            'css'   => [['property' => 'color', 'selector' => '.date']],
        ];

        $this->controls['dateTypography'] = [
            'group' => 'content_style',
            'label' => esc_html__('Typography', 'the-bricksfly'),
            'type'  => 'typography',
            'css'   => [['property' => 'font', 'selector' => '.date']],
        ];

        $this->controls['dateSpacing'] = [
            'group'   => 'content_style',
            'label'   => esc_html__('Date Spacing', 'the-bricksfly'),
            'type'    => 'number',
            'units'   => ['px' => ['min' => 0, 'max' => 100]],
            'css'     => [['property' => 'margin-bottom', 'selector' => '.date']],
        ];

        $this->controls['headingTitle'] = [
            'group' => 'content_style',
            'label' => esc_html__('Title', 'the-bricksfly'),
            'type'  => 'separator',
        ];

        $this->controls['titleColor'] = [
            'group' => 'content_style',
            'label' => esc_html__('Color', 'the-bricksfly'),
            'type'  => 'color',
            'css'   => [['property' => 'color', 'selector' => '.title']],
        ];

        $this->controls['titleTypography'] = [
            'group' => 'content_style',
            'label' => esc_html__('Typography', 'the-bricksfly'),
            'type'  => 'typography',
            'css'   => [['property' => 'font', 'selector' => '.title']],
        ];

        $this->controls['titleSpacing'] = [
            'group'   => 'content_style',
            'label'   => esc_html__('Title Spacing', 'the-bricksfly'),
            'type'    => 'number',
            'units'   => ['px' => ['min' => 0, 'max' => 100]],
            'css'     => [['property' => 'margin-bottom', 'selector' => '.title']],
        ];

        $this->controls['headingSubtitle'] = [
            'group' => 'content_style',
            'label' => esc_html__('Subtitle', 'the-bricksfly'),
            'type'  => 'separator',
        ];

        $this->controls['subtitleColor'] = [
            'group' => 'content_style',
            'label' => esc_html__('Color', 'the-bricksfly'),
            'type'  => 'color',
            'css'   => [['property' => 'color', 'selector' => '.subtitle']],
        ];

        $this->controls['subtitleTypography'] = [
            'group' => 'content_style',
            'label' => esc_html__('Typography', 'the-bricksfly'),
            'type'  => 'typography',
            'css'   => [['property' => 'font', 'selector' => '.subtitle']],
        ];

        $this->controls['subtitleSpacing'] = [
            'group'   => 'content_style',
            'label'   => esc_html__('Subtitle Spacing', 'the-bricksfly'),
            'type'    => 'number',
            'units'   => ['px' => ['min' => 0, 'max' => 100]],
            'css'     => [['property' => 'margin-bottom', 'selector' => '.subtitle']],
        ];

        $this->controls['headingDescription'] = [
            'group' => 'content_style',
            'label' => esc_html__('Description', 'the-bricksfly'),
            'type'  => 'separator',
        ];

        $this->controls['descriptionColor'] = [
            'group' => 'content_style',
            'label' => esc_html__('Color', 'the-bricksfly'),
            'type'  => 'color',
            'css'   => [['property' => 'color', 'selector' => '.description']],
        ];

        $this->controls['descriptionTypography'] = [
            'group' => 'content_style',
            'label' => esc_html__('Typography', 'the-bricksfly'),
            'type'  => 'typography',
            'css'   => [['property' => 'font', 'selector' => '.description']],
        ];

        // === STYLE: STEP ===
        $this->controls['stepColor'] = [
            'group' => 'step_style',
            'label' => esc_html__('Color', 'the-bricksfly'),
            'type'  => 'color',
            'css'   => [
                ['property' => 'color', 'selector' => '.step-box .icon'],
                ['property' => 'fill',  'selector' => '.step-box .icon'],
            ],
        ];

        $this->controls['stepBgColor'] = [
            'group' => 'step_style',
            'label' => esc_html__('Background Color', 'the-bricksfly'),
            'type'  => 'color',
            'css'   => [['property' => 'background-color', 'selector' => '.step-box .icon']],
        ];

        $this->controls['stepTypography'] = [
            'group' => 'step_style',
            'label' => esc_html__('Typography', 'the-bricksfly'),
            'type'  => 'typography',
            'css'   => [['property' => 'font', 'selector' => '.step-box .icon']],
            'exclude' => ['font-size', 'text-decoration', 'line-height'],   // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- Bricks control option, not a WP_Query arg
        ];

        $this->controls['stepSize'] = [
            'group' => 'step_style',
            'label' => esc_html__('Icon Size (Width/Height)', 'the-bricksfly'),
            'type'  => 'number',
            'units' => ['px' => ['min' => 6, 'max' => 300]],
            'css'   => [['property' => '--icon-size', 'selector' => '&.aae-timeline']],
        ];

        $this->controls['stepTextSize'] = [
            'group'   => 'step_style',
            'label'   => esc_html__('Text Font Size', 'the-bricksfly'),
            'type'    => 'number',
            'unit'    => 'px',
            'css'     => [['property' => 'font-size', 'selector' => '.step-box .icon']],
        ];

        $this->controls['stepPadding'] = [
            'group' => 'step_style',
            'label' => esc_html__('Padding', 'the-bricksfly'),
            'type'  => 'number',
            'units' => ['px' => ['min' => 1, 'max' => 300]],
            'css'   => [['property' => '--icon-padding', 'selector' => '&.aae-timeline']],
        ];

        $this->controls['stepBorder'] = [
            'group' => 'step_style',
            'label' => esc_html__('Border', 'the-bricksfly'),
            'type'  => 'border',
            'css'   => [['property' => 'border', 'selector' => '.step-box .icon']],
        ];

        $this->controls['stepBorderRadius'] = [
            'group' => 'step_style',
            'label' => esc_html__('Border Radius', 'the-bricksfly'),
            'type'  => 'number',
            'units' => ['px' => ['min' => 0, 'max' => 300], '%' => ['min' => 0, 'max' => 100]],
            'css'   => [['property' => 'border-radius', 'selector' => '.step-box .icon']],
        ];

        $this->controls['stepPositionX'] = [
            'group' => 'step_style',
            'label' => esc_html__('Step icon position-x', 'the-bricksfly'),
            'type'  => 'number',
            'units' => ['px' => ['min' => 0, 'max' => 300], '%' => ['min' => 0, 'max' => 100]],
            'css'   => [['property' => 'top', 'selector' => '.step-box .icon']],
        ];

        $this->controls['headingStepLine'] = [
            'group' => 'step_style',
            'label' => esc_html__('Step Line', 'the-bricksfly'),
            'type'  => 'separator',
        ];

        $this->controls['stepLineStyle'] = [
            'group'   => 'step_style',
            'label'   => esc_html__('Line Style', 'the-bricksfly'),
            'type'    => 'select',
            'options' => [
                'none'   => esc_html__('None', 'the-bricksfly'),
                'solid'  => esc_html__('Solid', 'the-bricksfly'),
                'dashed' => esc_html__('Dashed', 'the-bricksfly'),
                'dotted' => esc_html__('Dotted', 'the-bricksfly'),
                'double' => esc_html__('Double', 'the-bricksfly'),
            ],
            'default' => 'dashed',
            'css'     => [['property' => 'border-left-style', 'selector' => '.line']],
        ];

        $this->controls['stepLineThickness'] = [
            'group'    => 'step_style',
            'label'    => esc_html__('Line Thickness', 'the-bricksfly'),
            'type'     => 'number',
            'units'    => ['px' => ['min' => 0, 'max' => 10]],
            'css'      => [['property' => 'border-left-width', 'selector' => '.line']],
            'required' => ['stepLineStyle', '!=', 'none'],
        ];

        $this->controls['stepLineColor'] = [
            'group'    => 'step_style',
            'label'    => esc_html__('Line Color', 'the-bricksfly'),
            'type'     => 'color',
            'css'      => [['property' => 'border-left-color', 'selector' => '.line']],
            'required' => ['stepLineStyle', '!=', 'none'],
        ];

        $this->controls['headingIndicator'] = [
            'group'    => 'step_style',
            'label'    => esc_html__('Indicator', 'the-bricksfly'),
            'type'     => 'separator',
            'required' => ['showIndicator', '!=', ''],
        ];

        $this->controls['indicatorColor'] = [
            'group'    => 'step_style',
            'label'    => esc_html__('Indicator Color', 'the-bricksfly'),
            'type'     => 'color',
            'css'      => [['property' => 'background-color', 'selector' => '.indicator']],
            'required' => ['showIndicator', '!=', ''],
        ];

        $this->controls['indicatorWidth'] = [
            'group'    => 'step_style',
            'label'    => esc_html__('Indicator Width', 'the-bricksfly'),
            'type'     => 'number',
            'units'    => ['px' => ['min' => 1, 'max' => 500]],
            'css'      => [['property' => 'width', 'selector' => '.indicator']],
            'required' => ['showIndicator', '!=', ''],
        ];

        $this->controls['indicatorHeight'] = [
            'group'    => 'step_style',
            'label'    => esc_html__('Indicator Height', 'the-bricksfly'),
            'type'     => 'number',
            'units'    => ['px' => ['min' => 1, 'max' => 50]],
            'css'      => [['property' => 'height', 'selector' => '.indicator']],
            'required' => ['showIndicator', '!=', ''],
        ];

        $this->controls['indicatorGap'] = [
            'group'    => 'step_style',
            'label'    => esc_html__('Indicator Gap', 'the-bricksfly'),
            'type'     => 'number',
            'units'    => ['px' => ['min' => 1, 'max' => 500]],
            'css'      => [['property' => '--indicator-gap', 'selector' => '.indicator']],
            'required' => ['showIndicator', '!=', ''],
        ];
    }

    public function enqueue_scripts()
    {
        wp_enqueue_style("bricks-font-awesome-6");
        wp_enqueue_style("bricks-font-awesome-6-brands");

        wp_enqueue_style(
            'aae-timeline',
            AAB_ADDONS_URL . 'public/build/elements/timeline.css',
            [],
            '1.0.0'
        );
    }

    public function render()
    {
        $settings = $this->settings;
        $items    = $settings['timelineItems'] ?? [];

        if (empty($items)) {
            echo wp_kses_post( $this->render_element_placeholder(['title' => esc_html__('Add timeline items.', 'the-bricksfly')]) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            return;
        }

        $style      = $settings['elementList'] ?? '1';
        $title_tag  = $settings['titleTag'] ?? 'h3';
        $allowed    = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p'];
        if (!in_array($title_tag, $allowed, true)) {
            $title_tag = 'h3';
        }
        $image_size     = $settings['imageSize'] ?? 'full';
        $show_indicator = !empty($settings['showIndicator']);

        $this->set_attribute('_root', 'class', ['aae-timeline-wrapper', 'aae-timeline', 'style-' . $style]);

        echo wp_kses_post('<div ' . $this->render_attributes('_root') . '>');

        foreach ($items as $index => $item) {
            $this->render_timeline_item($settings, $item, $index, $title_tag, $image_size, $show_indicator);
        }

        echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    private function render_timeline_item($settings, $item, $index, $title_tag, $image_size, $show_indicator)
    {
        $link_key = 'link_' . $index;
        $has_link = !empty($item['link']['url']) || !empty($item['link']['type']);
        if ($has_link) {
            $this->set_link_attributes($link_key, $item['link']);
        }

        echo '<div class="timeline-item">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

        // Step
        echo '<div class="step-box">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '<div class="icon">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        if ($show_indicator) {
            echo '<div class="indicator"></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
        $step_type = $item['stepType'] ?? 'icon';
        if ($step_type === 'icon' && !empty($item['stepIcon'])) {
            echo wp_kses_post(self::render_icon($item['stepIcon'], ['aria-hidden' => 'true']));
        } elseif ($step_type === 'text') {
            echo esc_html($item['stepText'] ?? '');
        }
        echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '<div class="line"></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

        // Content wrap
        echo '<div class="content-wrap">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        $this->render_thumbnail($item, $image_size, $has_link, $link_key);

        echo '<div class="content">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

        if (!empty($item['timelineDate'])) {
            echo '<div class="date">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            if (!empty($settings['dateIcon'])) {
                echo wp_kses_post(self::render_icon($settings['dateIcon'], ['aria-hidden' => 'true']));
            }
            echo esc_html($item['timelineDate']);
            echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }

        if (!empty($item['timelineTitle'])) {
            echo '<' . esc_html($title_tag) . ' class="title">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            if ($has_link) {
                echo wp_kses_post('<a ' . $this->render_attributes($link_key) . '>');
                echo wp_kses($item['timelineTitle'], ['br' => [], 'span' => ['class' => []], 'strong' => [], 'em' => [], 'b' => [], 'i' => []]);
                echo '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            } else {
                echo wp_kses($item['timelineTitle'], ['br' => [], 'span' => ['class' => []], 'strong' => [], 'em' => [], 'b' => [], 'i' => []]);
            }
            echo '</' . esc_html($title_tag) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }

        if (!empty($item['timelineSub'])) {
            echo '<div class="subtitle">' . esc_html($item['timelineSub']) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }

        if (!empty($item['timelineDesc'])) {
            echo '<div class="description">' . wp_kses_post(nl2br($item['timelineDesc'])) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }

        echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

        echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    private function render_thumbnail($item, $image_size, $has_link, $link_key)
    {
        $image_data = $item['timelineImage'] ?? null;
        if (empty($image_data)) return;

        $image_html = '';
        if (is_array($image_data)) {
            if (!empty($image_data['id'])) {
                $image_html = wp_get_attachment_image($image_data['id'], $image_size);
            }
            if (!$image_html && !empty($image_data['url'])) {
                $image_html = '<img src="' . esc_url($image_data['url']) . '" alt="">';
            }
        } elseif (is_numeric($image_data) && $image_data > 0) {
            $image_html = wp_get_attachment_image((int) $image_data, $image_size);
        }

        if (!$image_html) return;

        echo '<div class="thumb">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        if ($has_link) {
            echo wp_kses_post('<a ' . $this->render_attributes($link_key) . '>' . $image_html . '</a>');
        } else {
            echo wp_kses_post( $image_html ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
        echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}
