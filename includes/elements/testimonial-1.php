<?php

if (!defined('ABSPATH')) exit;

class Aae_Bricks_Testimonial extends \Bricks\Element
{
    public $category     = 'bricks fly';
    public $name         = 'aae-testimonial';
    public $icon         = 'ti-comment-alt aab-element-marker';
    public $css_selector = '.aae-testimonial-wrapper';
    public $scripts      = ['aaeTestimonial'];

    public function get_label()
    {
        return esc_html__('Testimonial', 'the-bricksfly');
    }

    public function get_keywords()
    {
        return ['testimonial', 'review', 'slider', 'feedback', 'swiper'];
    }

    public function set_control_groups()
    {
        $this->control_groups['content'] = [
            'title' => esc_html__('Testimonial', 'the-bricksfly'),
            'tab'   => 'content',
        ];

        $this->control_groups['slider_options'] = [
            'title' => esc_html__('Slider Options', 'the-bricksfly'),
            'tab'   => 'content',
        ];

        $this->control_groups['slide_style'] = [
            'title' => esc_html__('Slide', 'the-bricksfly'),
            'tab'   => 'style',
        ];

        $this->control_groups['navigation_style'] = [
            'title' => esc_html__('Navigation', 'the-bricksfly'),
            'tab'   => 'style',
        ];

        $this->control_groups['image_style'] = [
            'title' => esc_html__('Image', 'the-bricksfly'),
            'tab'   => 'style',
        ];

        $this->control_groups['content_style'] = [
            'title' => esc_html__('Content', 'the-bricksfly'),
            'tab'   => 'style',
        ];

        $this->control_groups['name_style'] = [
            'title' => esc_html__('Name', 'the-bricksfly'),
            'tab'   => 'style',
        ];

        $this->control_groups['designation_style'] = [
            'title' => esc_html__('Designation', 'the-bricksfly'),
            'tab'   => 'style',
        ];
    }

    public function set_controls()
    {
        // === CONTENT TAB ===

        // Testimonial Style
        $this->controls['elementList'] = [
            'group'   => 'content',
            'label'   => esc_html__('Testimonial Style', 'the-bricksfly'),
            'type'    => 'select',
            'options' => [
                ''   => esc_html__('Default', 'the-bricksfly'),
                '1'  => esc_html__('One', 'the-bricksfly'),
                '2'  => esc_html__('Two', 'the-bricksfly'),
                '3'  => esc_html__('Three', 'the-bricksfly'),
                '4'  => esc_html__('Four', 'the-bricksfly'),
                '5'  => esc_html__('Five', 'the-bricksfly'),
                '6'  => esc_html__('Six', 'the-bricksfly'),
                '7'  => esc_html__('Seven', 'the-bricksfly'),
                '8'  => esc_html__('Eight', 'the-bricksfly'),
                '9'  => esc_html__('Nine', 'the-bricksfly'),
                '10' => esc_html__('Ten', 'the-bricksfly'),
            ],
            'default' => '',
        ];

        // Repeater: Testimonials
        $this->controls['testimonials'] = [
            'group'   => 'content',
            'label'   => esc_html__('Testimonials', 'the-bricksfly'),
            'type'    => 'repeater',
            'fields'  => [
                'testimonialContent' => [
                    'label'   => esc_html__('Content', 'the-bricksfly'),
                    'type'    => 'textarea',
                    'default' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'the-bricksfly'),
                ],
                'testimonialImage' => [
                    'label' => esc_html__('Image', 'the-bricksfly'),
                    'type'  => 'image',
                ],
                'testimonialName' => [
                    'label'   => esc_html__('Name', 'the-bricksfly'),
                    'type'    => 'text',
                    'default' => esc_html__('John Doe', 'the-bricksfly'),
                ],
                'testimonialJob' => [
                    'label'   => esc_html__('Designation', 'the-bricksfly'),
                    'type'    => 'text',
                    'default' => esc_html__('Designer', 'the-bricksfly'),
                ],
                'link' => [
                    'label' => esc_html__('Link', 'the-bricksfly'),
                    'type'  => 'link',
                ],
            ],
            'default' => [
                [
                    'testimonialContent' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'the-bricksfly'),
                    'testimonialName'    => esc_html__('John Doe', 'the-bricksfly'),
                    'testimonialJob'     => esc_html__('Designer', 'the-bricksfly'),
                ],
                [
                    'testimonialContent' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'the-bricksfly'),
                    'testimonialName'    => esc_html__('Jane Smith', 'the-bricksfly'),
                    'testimonialJob'     => esc_html__('Developer', 'the-bricksfly'),
                ],
                [
                    'testimonialContent' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'the-bricksfly'),
                    'testimonialName'    => esc_html__('Bob Wilson', 'the-bricksfly'),
                    'testimonialJob'     => esc_html__('Manager', 'the-bricksfly'),
                ],
            ],
        ];

        // Image Size
        $this->controls['imageSize'] = [
            'group'   => 'content',
            'label'   => esc_html__('Image Size', 'the-bricksfly'),
            'type'    => 'select',
            'options' => [
                'thumbnail' => esc_html__('Thumbnail', 'the-bricksfly'),
                'medium'    => esc_html__('Medium', 'the-bricksfly'),
                'large'     => esc_html__('Large', 'the-bricksfly'),
                'full'      => esc_html__('Full', 'the-bricksfly'),
            ],
            'default' => 'full',
        ];

        // === SLIDER OPTIONS ===

        $this->controls['slidesToShow'] = [
            'group'   => 'slider_options',
            'label'   => esc_html__('Slides to Show', 'the-bricksfly'),
            'type'    => 'select',
            'options' => [
                'auto' => esc_html__('Auto', 'the-bricksfly'),
                '1'  => '1',
                '2'  => '2',
                '3'  => '3',
                '4'  => '4',
                '5'  => '5',
                '6'  => '6',
                '7'  => '7',
                '8'  => '8',
                '9'  => '9',
                '10' => '10',
            ],
            'default' => '1',
        ];

        $this->controls['autoplay'] = [
            'group'   => 'slider_options',
            'label'   => esc_html__('Autoplay', 'the-bricksfly'),
            'type'    => 'checkbox',
            'default' => true,
        ];

        $this->controls['autoplayDelay'] = [
            'group'    => 'slider_options',
            'label'    => esc_html__('Autoplay Delay (ms)', 'the-bricksfly'),
            'type'     => 'number',
            'default'  => 3000,
            'required' => ['autoplay', '=', true],
        ];

        $this->controls['autoplayInteraction'] = [
            'group'    => 'slider_options',
            'label'    => esc_html__('Pause on Interaction', 'the-bricksfly'),
            'type'     => 'checkbox',
            'default'  => true,
            'required' => ['autoplay', '=', true],
        ];

        $this->controls['allowTouchMove'] = [
            'group'   => 'slider_options',
            'label'   => esc_html__('Allow Touch Move', 'the-bricksfly'),
            'type'    => 'checkbox',
            'default' => false,
        ];

        $this->controls['loop'] = [
            'group'   => 'slider_options',
            'label'   => esc_html__('Loop', 'the-bricksfly'),
            'type'    => 'checkbox',
            'default' => true,
        ];

        $this->controls['speed'] = [
            'group'   => 'slider_options',
            'label'   => esc_html__('Animation Speed (ms)', 'the-bricksfly'),
            'type'    => 'number',
            'default' => 500,
        ];

        $this->controls['spaceBetween'] = [
            'group'   => 'slider_options',
            'label'   => esc_html__('Space Between (px)', 'the-bricksfly'),
            'type'    => 'number',
            'default' => 20,
        ];

        $this->controls['navigation'] = [
            'group'   => 'slider_options',
            'label'   => esc_html__('Navigation', 'the-bricksfly'),
            'type'    => 'select',
            'options' => [
                'both'   => esc_html__('Arrows and Dots', 'the-bricksfly'),
                'arrows' => esc_html__('Arrows', 'the-bricksfly'),
                'dots'   => esc_html__('Dots', 'the-bricksfly'),
                'none'   => esc_html__('None', 'the-bricksfly'),
            ],
            'default' => 'arrows',
        ];

        $this->controls['navigationPreviousIcon'] = [
            'group'    => 'slider_options',
            'label'    => esc_html__('Previous Arrow Icon', 'the-bricksfly'),
            'type'     => 'icon',
            'required' => ['navigation', '=', ['both', 'arrows']],
        ];

        $this->controls['navigationNextIcon'] = [
            'group'    => 'slider_options',
            'label'    => esc_html__('Next Arrow Icon', 'the-bricksfly'),
            'type'     => 'icon',
            'required' => ['navigation', '=', ['both', 'arrows']],
        ];

        $this->controls['direction'] = [
            'group'   => 'slider_options',
            'label'   => esc_html__('Direction', 'the-bricksfly'),
            'type'    => 'select',
            'options' => [
                'ltr' => esc_html__('Left', 'the-bricksfly'),
                'rtl' => esc_html__('Right', 'the-bricksfly'),
            ],
            'default' => 'ltr',
        ];

        // === STYLE TAB: Slide ===

        $this->controls['slideBackground'] = [
            'group' => 'slide_style',
            'label' => esc_html__('Background', 'the-bricksfly'),
            'type'  => 'background',
            'css'   => [
                [
                    'property' => 'background',
                    'selector' => '.slide',
                ],
            ],
        ];

        $this->controls['slidePadding'] = [
            'group' => 'slide_style',
            'label' => esc_html__('Padding', 'the-bricksfly'),
            'type'  => 'dimensions',
            'css'   => [
                [
                    'property' => 'padding',
                    'selector' => '.slide',
                ],
            ],
        ];

        $this->controls['sectionBorder'] = [
            'group' => 'slide_style',
            'label' => esc_html__('Border', 'the-bricksfly'),
            'type'  => 'border',
            'css'   => [
                [
                    'property' => 'border',
                    'selector' => '.slide',
                ],
            ],
        ];

        $this->controls['slideWidth'] = [
            'group' => 'slide_style',
            'label' => esc_html__('Width', 'the-bricksfly'),
            'type'  => 'number',
            'units' => true,
            'css'   => [
                [
                    'property' => 'max-width',
                    'selector' => '.wcf__slider',
                ],
            ],
            'required' => ['elementList', '!=', '8'],
        ];

        // === STYLE TAB: Navigation ===

        $this->controls['navigationWidth'] = [
            'group'    => 'navigation_style',
            'label'    => esc_html__('Navigation Width', 'the-bricksfly'),
            'type'     => 'number',
            'units'    => true,
            'css'      => [
                [
                    'property' => 'width',
                    'selector' => '.ts-navigation',
                ],
            ],
            'required' => ['navigation', '=', ['arrows', 'both']],
        ];

        $this->controls['arrowsSize'] = [
            'group'    => 'navigation_style',
            'label'    => esc_html__('Arrows Size', 'the-bricksfly'),
            'type'     => 'number',
            'units'    => true,
            'css'      => [
                [
                    'property' => 'font-size',
                    'selector' => '.wcf-arrow',
                ],
            ],
            'required' => ['navigation', '=', ['arrows', 'both']],
        ];

        // Offsets are written as inline styles on .ts-navigation in render().
        // - arrowsOffset → flex `gap` between the two arrows (always positive).
        // - arrowsOffsetVertical → `top` on the relatively-positioned nav,
        //   so siblings (pagination dots) stay put; defaults to % unit so
        //   "-50%" is a natural way to center over the slider.
        // % only — keeps the offsets responsive across viewports.
        // Driven through Bricks's `'css'` array (NOT inline style on
        // .ts-navigation) so the builder patches the generated CSS live as the
        // slider is dragged. Inline style would pin the value to the last
        // server-rendered state — that's what made editor and frontend
        // disagree before.
        $this->controls['arrowsOffset'] = [
            'group'       => 'navigation_style',
            'label'       => esc_html__('Arrows Offset', 'the-bricksfly'),
            'type'        => 'slider',
            'units'       => [
                '%' => ['min' => 0, 'max' => 200],
            ],
            'default'     => '5%',
            'placeholder' => '5%',
            'description' => esc_html__('Space between the previous and next arrow.', 'the-bricksfly'),
            'css'         => [
                ['property' => 'gap', 'selector' => '.ts-navigation'],
            ],
            'required'    => ['navigation', '=', ['arrows', 'both']],
        ];

        $this->controls['arrowsOffsetVertical'] = [
            'group'    => 'navigation_style',
            'label'    => esc_html__('Arrows Offset (Vertical)', 'the-bricksfly'),
            'type'     => 'slider',
            'units'    => [
                '%' => ['min' => -100, 'max' => 100],
            ],
            'default'     => '95%',
            'placeholder' => '95%',
            'description' => esc_html__('Negative pulls arrows up over the slider; positive pushes them down.', 'the-bricksfly'),
            'css'         => [
                ['property' => 'top', 'selector' => '.ts-navigation'],
            ],
            'required'    => ['navigation', '=', ['arrows', 'both']],
        ];

        $this->controls['arrowsBorder'] = [
            'group'    => 'navigation_style',
            'label'    => esc_html__('Arrows Border', 'the-bricksfly'),
            'type'     => 'border',
            'css'      => [
                [
                    'property' => 'border',
                    'selector' => '.wcf-arrow',
                ],
            ],
            'required' => ['navigation', '=', ['arrows', 'both']],
        ];

        $this->controls['arrowsColor'] = [
            'group'    => 'navigation_style',
            'label'    => esc_html__('Arrows Color', 'the-bricksfly'),
            'type'     => 'color',
            'css'      => [
                [
                    'property' => 'color',
                    'selector' => '.wcf-arrow',
                ],
                [
                    'property' => 'fill',
                    'selector' => '.wcf-arrow svg',
                ],
            ],
            'required' => ['navigation', '=', ['arrows', 'both']],
        ];

        $this->controls['arrowsBgColor'] = [
            'group'    => 'navigation_style',
            'label'    => esc_html__('Arrows Background', 'the-bricksfly'),
            'type'     => 'background',
            'css'      => [
                [
                    'property' => 'background',
                    'selector' => '.wcf-arrow',
                ],
            ],
            'required' => ['navigation', '=', ['arrows', 'both']],
        ];

        $this->controls['arrowsHoverColor'] = [
            'group'    => 'navigation_style',
            'label'    => esc_html__('Arrows Hover Color', 'the-bricksfly'),
            'type'     => 'color',
            'css'      => [
                [
                    'property' => 'color',
                    'selector' => '.wcf-arrow:hover',
                ],
                [
                    'property' => 'fill',
                    'selector' => '.wcf-arrow:hover svg',
                ],
            ],
            'required' => ['navigation', '=', ['arrows', 'both']],
        ];

        $this->controls['arrowsHoverBgColor'] = [
            'group'    => 'navigation_style',
            'label'    => esc_html__('Arrows Hover Background', 'the-bricksfly'),
            'type'     => 'background',
            'css'      => [
                [
                    'property' => 'background',
                    'selector' => '.wcf-arrow:hover',
                ],
            ],
            'required' => ['navigation', '=', ['arrows', 'both']],
        ];

        $this->controls['arrowsHoverBorderColor'] = [
            'group'    => 'navigation_style',
            'label'    => esc_html__('Arrows Hover Border Color', 'the-bricksfly'),
            'type'     => 'color',
            'css'      => [
                [
                    'property' => 'border-color',
                    'selector' => '.wcf-arrow:hover',
                ],
            ],
            'required' => ['navigation', '=', ['arrows', 'both']],
        ];

        $this->controls['dotsSize'] = [
            'group'    => 'navigation_style',
            'label'    => esc_html__('Dots Size', 'the-bricksfly'),
            'type'     => 'number',
            'units'    => true,
            'css'      => [
                [
                    'property' => 'width',
                    'selector' => '.swiper-pagination-bullet',
                ],
                [
                    'property' => 'height',
                    'selector' => '.swiper-pagination-bullet',
                ],
            ],
            'required' => ['navigation', '=', ['dots', 'both']],
        ];

        // .ts-pagination is absolutely positioned (position: absolute;
        // width: 100%; flex centered — see base CSS). This control writes
        // `bottom: <value>` on it. px is the natural default unit.
        $this->controls['dotsOffset'] = [
            'group'       => 'navigation_style',
            'label'       => esc_html__('Dots Offset', 'the-bricksfly'),
            'type'        => 'number',
            'units'       => true,
            'placeholder' => '0px',
            'description' => esc_html__('Distance of the dots row from the bottom of the slider.', 'the-bricksfly'),
            'css'         => [
                ['property' => 'bottom', 'selector' => '.ts-pagination'],
            ],
            'required'    => ['navigation', '=', ['dots', 'both']],
        ];

        $this->controls['dotsInactiveColor'] = [
            'group'    => 'navigation_style',
            'label'    => esc_html__('Dots Color', 'the-bricksfly'),
            'type'     => 'color',
            'css'      => [
                [
                    'property' => 'background',
                    'selector' => '.swiper-pagination-bullet:not(.swiper-pagination-bullet-active)',
                ],
                [
                    'property' => 'color',
                    'selector' => '.swiper-pagination-current, .swiper-pagination-total',
                ],
            ],
            'required' => ['navigation', '=', ['dots', 'both']],
        ];

        $this->controls['dotsActiveColor'] = [
            'group'    => 'navigation_style',
            'label'    => esc_html__('Dots Active Color', 'the-bricksfly'),
            'type'     => 'color',
            'css'      => [
                [
                    'property' => 'background',
                    'selector' => '.swiper-pagination-bullet',
                ],
                [
                    'property' => 'color',
                    'selector' => '.swiper-pagination-current',
                ],
            ],
            'required' => ['navigation', '=', ['dots', 'both']],
        ];

        // === STYLE TAB: Image ===

        $this->controls['imgWidth'] = [
            'group' => 'image_style',
            'label' => esc_html__('Width', 'the-bricksfly'),
            'type'  => 'number',
            'units' => true,
            'css'   => [
                [
                    'property' => 'width',
                    'selector' => '.image img',
                ],
            ],
        ];

        $this->controls['imgHeight'] = [
            'group' => 'image_style',
            'label' => esc_html__('Height', 'the-bricksfly'),
            'type'  => 'number',
            'units' => true,
            'css'   => [
                [
                    'property' => 'height',
                    'selector' => '.image img',
                ],
            ],
        ];

        $this->controls['objectFit'] = [
            'group'   => 'image_style',
            'label'   => esc_html__('Object Fit', 'the-bricksfly'),
            'type'    => 'select',
            'options' => [
                ''        => esc_html__('Default', 'the-bricksfly'),
                'fill'    => esc_html__('Fill', 'the-bricksfly'),
                'cover'   => esc_html__('Cover', 'the-bricksfly'),
                'contain' => esc_html__('Contain', 'the-bricksfly'),
            ],
            'css' => [
                [
                    'property' => 'object-fit',
                    'selector' => '.image img',
                ],
            ],
        ];

        $this->controls['objectPosition'] = [
            'group'    => 'image_style',
            'label'    => esc_html__('Object Position', 'the-bricksfly'),
            'type'     => 'select',
            'options'  => [
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
            'default'  => 'center center',
            'css'      => [
                [
                    'property' => 'object-position',
                    'selector' => '.image img',
                ],
            ],
            'required' => ['objectFit', '=', 'cover'],
        ];

        $this->controls['imageBorder'] = [
            'group' => 'image_style',
            'label' => esc_html__('Border', 'the-bricksfly'),
            'type'  => 'border',
            'css'   => [
                [
                    'property' => 'border',
                    'selector' => '.image img',
                ],
            ],
        ];

        // === STYLE TAB: Content ===

        $this->controls['contentTypography'] = [
            'group' => 'content_style',
            'label' => esc_html__('Typography', 'the-bricksfly'),
            'type'  => 'typography',
            'css'   => [
                [
                    'property' => 'font',
                    'selector' => '.feedback',
                ],
            ],
        ];

        $this->controls['contentPadding'] = [
            'group' => 'content_style',
            'label' => esc_html__('Padding', 'the-bricksfly'),
            'type'  => 'dimensions',
            'css'   => [
                [
                    'property' => 'padding',
                    'selector' => '.feedback',
                ],
            ],
        ];

        $this->controls['contentMargin'] = [
            'group' => 'content_style',
            'label' => esc_html__('Margin', 'the-bricksfly'),
            'type'  => 'dimensions',
            'css'   => [
                [
                    'property' => 'margin',
                    'selector' => '.feedback',
                ],
            ],
        ];

        // === STYLE TAB: Name ===

        $this->controls['nameTypography'] = [
            'group' => 'name_style',
            'label' => esc_html__('Typography', 'the-bricksfly'),
            'type'  => 'typography',
            'css'   => [
                [
                    'property' => 'font',
                    'selector' => '.name',
                ],
            ],
        ];

        $this->controls['nameMargin'] = [
            'group' => 'name_style',
            'label' => esc_html__('Margin', 'the-bricksfly'),
            'type'  => 'dimensions',
            'css'   => [
                [
                    'property' => 'margin',
                    'selector' => '.name',
                ],
            ],
        ];

        // === STYLE TAB: Designation ===

        $this->controls['designationTypography'] = [
            'group' => 'designation_style',
            'label' => esc_html__('Typography', 'the-bricksfly'),
            'type'  => 'typography',
            'css'   => [
                [
                    'property' => 'font',
                    'selector' => '.designation',
                ],
            ],
        ];

        $this->controls['designationMargin'] = [
            'group' => 'designation_style',
            'label' => esc_html__('Margin', 'the-bricksfly'),
            'type'  => 'dimensions',
            'css'   => [
                [
                    'property' => 'margin',
                    'selector' => '.designation',
                ],
            ],
        ];
    }

    public function enqueue_scripts()
    {

        wp_enqueue_style("bricks-font-awesome-6");
        wp_enqueue_style("bricks-font-awesome-6-brands");

        // Swiper CSS
        $posts_slider_css = AAB_ADDONS_PATH . 'public/build/elements/posts-slider.css';
        wp_enqueue_style(
            'post-slider',
            AAB_ADDONS_URL . 'public/build/elements/posts-slider.css',
            [],
            file_exists($posts_slider_css) ? filemtime($posts_slider_css) : AAB_ADDONS_VERSION
        );

        // Use the Swiper bundled with Bricks (registered by Bricks core as 'bricks-swiper').
        wp_enqueue_style('bricks-swiper');
        wp_enqueue_script('bricks-swiper');

        // Testimonial CSS — use filemtime() so cache busts on every edit
        // (hardcoded '1.0.0' caused stale browser cache to mask updates).
        $testimonial_css = AAB_ADDONS_PATH . 'public/build/elements/testimonial.css';
        wp_enqueue_style(
            'aae-testimonial',
            AAB_ADDONS_URL . 'public/build/elements/testimonial.css',
            ['bricks-swiper', 'post-slider'],
            file_exists($testimonial_css) ? filemtime($testimonial_css) : AAB_ADDONS_VERSION
        );

        // Testimonial JS — same cache-bust treatment.
        $testimonial_js = AAB_ADDONS_PATH . 'public/build/elements/testimonial.js';
        wp_enqueue_script(
            'aae-testimonial',
            AAB_ADDONS_URL . 'public/build/elements/testimonial.js',
            ['bricks-swiper'],
            file_exists($testimonial_js) ? filemtime($testimonial_js) : AAB_ADDONS_VERSION,
            true
        );
    }

    public function render()
    {
        $settings = $this->settings;

        if (empty($settings['testimonials'])) {
            return $this->render_element_placeholder(['title' => esc_html__('No testimonials added.', 'the-bricksfly')]);
        }

        $element_list = $settings['elementList'] ?? '';
        $nav_type     = $settings['navigation'] ?? 'both';
        $show_arrows  = in_array($nav_type, ['arrows', 'both'], true);
        $show_dots    = in_array($nav_type, ['dots', 'both'], true);
        $image_size   = $settings['imageSize'] ?? 'full';

        // Swiper requires slidesPerView to be a number or the string 'auto';
        // anything else (including a numeric string like "3") silently falls
        // back to 1 — which is what was making the frontend disagree with
        // the CSS-calc preview in the editor. Default is 1 across all
        // testimonial elements for consistency.
        $slides_to_show  = $settings['slidesToShow'] ?? '1';
        $slides_per_view = ($slides_to_show === 'auto') ? 'auto' : (int) $slides_to_show;
        $space_between   = (int) ($settings['spaceBetween'] ?? 20);

        // Build Swiper settings as data attribute
        $slider_settings = [
            'loop'           => !empty($settings['loop']),
            'speed'          => (int) ($settings['speed'] ?? 500),
            'allowTouchMove' => !empty($settings['allowTouchMove']),
            'slidesPerView'  => $slides_per_view,
            'spaceBetween'   => $space_between,
        ];

        if (!empty($settings['autoplay'])) {
            $slider_settings['autoplay'] = [
                'delay'                => (int) ($settings['autoplayDelay'] ?? 3000),
                'disableOnInteraction' => !empty($settings['autoplayInteraction']),
            ];
        }

        if ($show_arrows) {
            $slider_settings['navigation'] = [
                'nextEl' => '#brx-content .brxe-aae-testimonial[data-element-id="' . $this->id . '"] .wcf-arrow-next',
                'prevEl' => '#brx-content .brxe-aae-testimonial[data-element-id="' . $this->id . '"] .wcf-arrow-prev',
            ];
        }

        if ($show_dots) {
            $slider_settings['pagination'] = [
                'el'        => '#brx-content .brxe-aae-testimonial[data-element-id="' . $this->id . '"] .swiper-pagination',
                'clickable' => true,
            ];

            if (in_array($element_list, ['5', '6', '7', '8', '9'], true)) {
                $slider_settings['pagination']['type'] = 'fraction';
            }
        }

        // Root classes
        $root_classes = ['aae-testimonial-wrapper', 'wcf__testimonial'];
        if ($element_list !== '') {
            $root_classes[] = 'style-' . $element_list;
        }
        $this->set_attribute('_root', 'class', $root_classes);
        $this->set_attribute('_root', 'data-element-id', $this->id);

        // Pre-init slide-width preview vars on the root (consumed by
        // testimonial.css before Swiper initializes).
        $this->set_attribute(
            '_root',
            'style',
            sprintf('--slides-to-show: %s; --space-between: %dpx;', esc_attr((string) $slides_to_show), $space_between)
        );

        // arrowsOffset / arrowsOffsetVertical / dotsOffset are now applied
        // via Bricks's `'css'` array on the controls themselves — no inline
        // style needed here.

        $direction = $settings['direction'] ?? 'ltr';

        echo wp_kses_post('<div ' . $this->render_attributes('_root') . '>');

        echo '<div class="wcf__slider swiper" dir="' . esc_attr($direction) . '">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '<div class="swiper-wrapper" data-settings="' . esc_attr(json_encode($slider_settings)) . '">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

        foreach ($settings['testimonials'] as $index => $item) {
            $has_link = !empty($item['link']['url']);
            $link_attrs = '';
            if ($has_link) {
                $link_attrs = ' href="' . esc_url($item['link']['url']) . '"';
                if (!empty($item['link']['newTab'])) {
                    $link_attrs .= ' target="_blank"';
                }
                if (!empty($item['link']['noFollow'])) {
                    $link_attrs .= ' rel="nofollow"';
                }
            }

            echo '<div class="swiper-slide">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo '<div class="slide">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

            // Image
            echo '<div class="image">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            $image_html = '';
            if (!empty($item['testimonialImage']['id'])) {
                $image_html = wp_get_attachment_image(
                    $item['testimonialImage']['id'],
                    $image_size,
                    false,
                    ['class' => 'swiper-slide-image', 'alt' => esc_attr($item['testimonialName'] ?? '')]
                );
            } elseif (!empty($item['testimonialImage']['url'])) {
                $image_html = '<img class="swiper-slide-image" src="' . esc_url($item['testimonialImage']['url']) . '" alt="' . esc_attr($item['testimonialName'] ?? '') . '">';
            }

            if ($has_link && $image_html) {
                echo wp_kses_post('<a' . $link_attrs . '>' . $image_html . '</a>');
            } else {
                echo wp_kses_post( $image_html );
            }
            echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

            // Feedback
            echo '<div class="feedback">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo wp_kses_post($item['testimonialContent'] ?? '');
            echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

            // Name
            if ($has_link) {
                echo wp_kses_post('<a class="name"' . $link_attrs . '>' . esc_html($item['testimonialName'] ?? '') . '</a>');
            } else {
                echo '<div class="name">' . esc_html($item['testimonialName'] ?? '') . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            }

            // Designation
            echo '<div class="designation">' . esc_html($item['testimonialJob'] ?? '') . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

            echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }

        echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

        // Navigation & Pagination (only if more than 1 slide)
        if (count($settings['testimonials']) > 1) {
            if ($show_arrows) {
                echo '<div class="ts-navigation">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo '<div class="wcf-arrow wcf-arrow-prev" role="button" tabindex="0">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                $this->render_swiper_button('previous');
                echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo '<div class="wcf-arrow wcf-arrow-next" role="button" tabindex="0">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                $this->render_swiper_button('next');
                echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            }

            if ($show_dots) {
                echo '<div class="ts-pagination">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo '<div class="swiper-pagination"></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            }
        }

        echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    private function render_swiper_button($type)
    {
        $settings  = $this->settings;
        $direction = $type === 'next' ? 'right' : 'left';
        $icon_key  = $type === 'next' ? 'navigationNextIcon' : 'navigationPreviousIcon';

        if (!empty($settings[$icon_key])) {
            echo wp_kses_post(self::render_icon($settings[$icon_key], ['aria-hidden' => 'true']));
        } else {
            echo '<i class="fas fa-chevron-' . esc_attr($direction) . '" aria-hidden="true"></i>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
    }
}
