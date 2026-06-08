<?php

if (!defined('ABSPATH')) exit;

class Aae_Bricks_Testimonial2 extends \Bricks\Element
{
    public $category     = 'bricks fly';
    public $name         = 'aae-testimonial-2';
    public $icon         = 'ti-comment-alt aab-element-marker';
    public $css_selector = '.aae-testimonial-2-wrapper';
    public $scripts      = ['aaeTestimonial2'];

    public function get_label()
    {
        return esc_html__('Classic Testimonial', 'bricksfly');
    }

    public function get_keywords()
    {
        return ['testimonial', 'classic', 'review', 'slider', 'feedback', 'swiper'];
    }

    public function set_control_groups()
    {
        $this->control_groups['content'] = [
            'title' => esc_html__('Testimonial', 'bricksfly'),
            'tab'   => 'content',
        ];

        $this->control_groups['slider_options'] = [
            'title' => esc_html__('Slider Options', 'bricksfly'),
            'tab'   => 'content',
        ];

        $this->control_groups['slide_style'] = [
            'title' => esc_html__('Slide', 'bricksfly'),
            'tab'   => 'style',
        ];

        $this->control_groups['navigation_style'] = [
            'title' => esc_html__('Navigation', 'bricksfly'),
            'tab'   => 'style',
        ];

        $this->control_groups['image_style'] = [
            'title' => esc_html__('Image', 'bricksfly'),
            'tab'   => 'style',
        ];

        $this->control_groups['content_style'] = [
            'title' => esc_html__('Content', 'bricksfly'),
            'tab'   => 'style',
        ];

        $this->control_groups['name_style'] = [
            'title' => esc_html__('Name', 'bricksfly'),
            'tab'   => 'style',
        ];

        $this->control_groups['designation_style'] = [
            'title' => esc_html__('Designation', 'bricksfly'),
            'tab'   => 'style',
        ];

        $this->control_groups['quote_style'] = [
            'title' => esc_html__('Quote', 'bricksfly'),
            'tab'   => 'style',
        ];
    }

    public function set_controls()
    {
        // === CONTENT TAB ===

        // Testimonial Style
        $this->controls['elementList'] = [
            'group'   => 'content',
            'label'   => esc_html__('Testimonial Style', 'bricksfly'),
            'type'    => 'select',
            'options' => [
                '1' => esc_html__('One', 'bricksfly'),
                '2' => esc_html__('Two', 'bricksfly'),
                '3' => esc_html__('Three', 'bricksfly'),
            ],
            'default' => '1',
            'inline'  => true,
        ];

        // Repeater: Testimonials
        $this->controls['testimonials'] = [
            'group'         => 'content',
            'label'         => esc_html__('Testimonials', 'bricksfly'),
            'type'          => 'repeater',
            'titleProperty' => 'testimonialName',
            'fields'        => [
                'testimonialContent' => [
                    'label'   => esc_html__('Content', 'bricksfly'),
                    'type'    => 'textarea',
                    'default' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'bricksfly'),
                ],
                'testimonialImage' => [
                    'label' => esc_html__('Image', 'bricksfly'),
                    'type'  => 'image',
                ],
                'testimonialName' => [
                    'label'   => esc_html__('Name', 'bricksfly'),
                    'type'    => 'text',
                    'default' => esc_html__('John Doe', 'bricksfly'),
                ],
                'testimonialJob' => [
                    'label'   => esc_html__('Designation', 'bricksfly'),
                    'type'    => 'text',
                    'default' => esc_html__('Designer', 'bricksfly'),
                ],
                'link' => [
                    'label' => esc_html__('Link', 'bricksfly'),
                    'type'  => 'link',
                ],
            ],
            'default' => [
                [
                    'testimonialContent' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'bricksfly'),
                    'testimonialName'    => esc_html__('John Doe', 'bricksfly'),
                    'testimonialJob'     => esc_html__('Designer', 'bricksfly'),
                ],
                [
                    'testimonialContent' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'bricksfly'),
                    'testimonialName'    => esc_html__('Jane Smith', 'bricksfly'),
                    'testimonialJob'     => esc_html__('Developer', 'bricksfly'),
                ],
                [
                    'testimonialContent' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'bricksfly'),
                    'testimonialName'    => esc_html__('Bob Wilson', 'bricksfly'),
                    'testimonialJob'     => esc_html__('Manager', 'bricksfly'),
                ],
                [
                    'testimonialContent' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'bricksfly'),
                    'testimonialName'    => esc_html__('Alice Brown', 'bricksfly'),
                    'testimonialJob'     => esc_html__('Director', 'bricksfly'),
                ],
                [
                    'testimonialContent' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'bricksfly'),
                    'testimonialName'    => esc_html__('Tom Davis', 'bricksfly'),
                    'testimonialJob'     => esc_html__('Engineer', 'bricksfly'),
                ],
            ],
        ];

        // Quote Icon
        $this->controls['quoteIcon'] = [
            'group' => 'content',
            'label' => esc_html__('Quote Icon', 'bricksfly'),
            'type'  => 'icon',
            'default' => [
                'library' => 'fontawesome',
                'icon'    => 'fas fa-quote-left',
            ],
        ];

        // === SLIDER OPTIONS ===

        $this->controls['slidesToShow'] = [
            'group'   => 'slider_options',
            'label'   => esc_html__('Slides to Show', 'bricksfly'),
            'type'    => 'number',
            'min'     => 1,
            'max'     => 10,
            'default' => 1,
        ];

        $this->controls['autoplay'] = [
            'group'   => 'slider_options',
            'label'   => esc_html__('Autoplay', 'bricksfly'),
            'type'    => 'checkbox',
            'default' => true,
        ];

        $this->controls['autoplayDelay'] = [
            'group'    => 'slider_options',
            'label'    => esc_html__('Autoplay Delay (ms)', 'bricksfly'),
            'type'     => 'number',
            'default'  => 3000,
            'required' => ['autoplay', '!=', ''],
        ];

        $this->controls['autoplayInteraction'] = [
            'group'    => 'slider_options',
            'label'    => esc_html__('Pause on Interaction', 'bricksfly'),
            'type'     => 'checkbox',
            'default'  => true,
            'required' => ['autoplay', '!=', ''],
        ];

        $this->controls['allowTouchMove'] = [
            'group'   => 'slider_options',
            'label'   => esc_html__('Allow Touch Move', 'bricksfly'),
            'type'    => 'checkbox',
            'default' => false,
        ];

        $this->controls['loop'] = [
            'group'   => 'slider_options',
            'label'   => esc_html__('Loop', 'bricksfly'),
            'type'    => 'checkbox',
            'default' => true,
        ];

        $this->controls['speed'] = [
            'group'   => 'slider_options',
            'label'   => esc_html__('Animation Speed (ms)', 'bricksfly'),
            'type'    => 'number',
            'default' => 500,
        ];

        $this->controls['spaceBetween'] = [
            'group'   => 'slider_options',
            'label'   => esc_html__('Space Between (px)', 'bricksfly'),
            'type'    => 'number',
            'default' => 20,
        ];

        $this->controls['navigation'] = [
            'group'   => 'slider_options',
            'label'   => esc_html__('Navigation', 'bricksfly'),
            'type'    => 'select',
            'options' => [
                'both'   => esc_html__('Arrows and Dots', 'bricksfly'),
                'arrows' => esc_html__('Arrows', 'bricksfly'),
                'dots'   => esc_html__('Dots', 'bricksfly'),
                'none'   => esc_html__('None', 'bricksfly'),
            ],
            'default' => 'arrows',
            'inline'  => true,
        ];

        $this->controls['prevIcon'] = [
            'group'    => 'slider_options',
            'label'    => esc_html__('Previous Arrow Icon', 'bricksfly'),
            'type'     => 'icon',
            'default'  => [
                'library' => 'fontawesome',
                'icon'    => 'fas fa-chevron-left',
            ],
            'required' => ['navigation', '=', ['both', 'arrows']],
        ];

        $this->controls['nextIcon'] = [
            'group'    => 'slider_options',
            'label'    => esc_html__('Next Arrow Icon', 'bricksfly'),
            'type'     => 'icon',
            'default'  => [
                'library' => 'fontawesome',
                'icon'    => 'fas fa-chevron-right',
            ],
            'required' => ['navigation', '=', ['both', 'arrows']],
        ];

        $this->controls['sliderDirection'] = [
            'group'   => 'slider_options',
            'label'   => esc_html__('Direction', 'bricksfly'),
            'type'    => 'select',
            'options' => [
                'ltr' => esc_html__('Left to Right', 'bricksfly'),
                'rtl' => esc_html__('Right to Left', 'bricksfly'),
            ],
            'default' => 'ltr',
            'inline'  => true,
        ];

        // === STYLE TAB ===

        // Slide Style
        $this->controls['slideBackground'] = [
            'group' => 'slide_style',
            'label' => esc_html__('Background', 'bricksfly'),
            'type'  => 'background',
            'css'   => [['property' => 'background', 'selector' => '.slide']],
            'exclude' => ['video'],
        ];

        $this->controls['slideBorder'] = [
            'group' => 'slide_style',
            'label' => esc_html__('Border', 'bricksfly'),
            'type'  => 'border',
            'css'   => [['property' => 'border', 'selector' => '.slide']],
        ];

        // Navigation Style
        $this->controls['arrowsSize'] = [
            'group' => 'navigation_style',
            'label' => esc_html__('Arrow Size', 'bricksfly'),
            'type'  => 'slider',
            'units' => [
                'px' => ['min' => 10, 'max' => 60, 'step' => 1],
            ],
            'css'      => [['property' => 'font-size', 'selector' => '.wcf-arrow']],
            'required' => ['navigation', '=', ['both', 'arrows']],
        ];

        $this->controls['arrowsColor'] = [
            'group' => 'navigation_style',
            'label' => esc_html__('Arrow Color', 'bricksfly'),
            'type'  => 'color',
            'css'   => [
                ['property' => 'color', 'selector' => '.wcf-arrow'],
                ['property' => 'fill', 'selector' => '.wcf-arrow svg'],
            ],
            'required' => ['navigation', '=', ['both', 'arrows']],
        ];

        $this->controls['arrowsHoverColor'] = [
            'group' => 'navigation_style',
            'label' => esc_html__('Arrow Hover Color', 'bricksfly'),
            'type'  => 'color',
            'css'   => [
                ['property' => 'color', 'selector' => '.wcf-arrow:hover'],
                ['property' => 'fill', 'selector' => '.wcf-arrow:hover svg'],
            ],
            'required' => ['navigation', '=', ['both', 'arrows']],
        ];

        // Driven through Bricks's `'css'` array so the builder patches the
        // <style> tag live as the slider is dragged. Inline style on
        // .ts-navigation would pin the value to the last server-rendered
        // state and break editor live preview.
        $this->controls['arrowsOffset'] = [
            'group'       => 'navigation_style',
            'label'       => esc_html__('Arrows Offset', 'bricksfly'),
            'type'        => 'slider',
            'units'       => [
                '%' => ['min' => 0, 'max' => 100],
            ],
            'default'     => '5%',
            'placeholder' => '5%',
            'description' => esc_html__('Space between the previous and next arrow.', 'bricksfly'),
            'css'         => [
                ['property' => 'gap', 'selector' => '.ts-navigation'],
            ],
            'required'    => ['navigation', '=', ['both', 'arrows']],
        ];

        $this->controls['arrowsOffsetVertical'] = [
            'group'       => 'navigation_style',
            'label'       => esc_html__('Arrows Offset (Vertical)', 'bricksfly'),
            'type'        => 'slider',
            'units'       => [
                '%' => ['min' => -100, 'max' => 100],
            ],
            'default'     => '95%',
            'placeholder' => '95%',
            'description' => esc_html__('Negative pulls arrows up over the slider; positive pushes them down.', 'bricksfly'),
            'css'         => [
                ['property' => 'top', 'selector' => '.ts-navigation'],
            ],
            'required'    => ['navigation', '=', ['both', 'arrows']],
        ];

        // number + units: true so Bricks emits CSS values with the unit
        // appended ("10px"). slider + units:['px' => …] sometimes drops the
        // unit and emits invalid `width: 10` which the browser ignores.
        $this->controls['dotsSize'] = [
            'group' => 'navigation_style',
            'label' => esc_html__('Dot Size', 'bricksfly'),
            'type'  => 'number',
            'units' => true,
            'css'   => [
                ['property' => 'width',  'selector' => '.swiper-pagination-bullet'],
                ['property' => 'height', 'selector' => '.swiper-pagination-bullet'],
            ],
            'required' => ['navigation', '=', ['both', 'dots']],
        ];

        // .ts-pagination is absolutely positioned (position: absolute;
        // width: 100%; flex centered — see base CSS). This control writes
        // `bottom: <value>` on it. px is the natural default unit.
        $this->controls['dotsOffset'] = [
            'group'       => 'navigation_style',
            'label'       => esc_html__('Dots Offset', 'bricksfly'),
            'type'        => 'number',
            'units'       => true,
            'placeholder' => '0px',
            'description' => esc_html__('Distance of the dots row from the bottom of the slider.', 'bricksfly'),
            'css'         => [
                ['property' => 'bottom', 'selector' => '.ts-pagination'],
            ],
            'required'    => ['navigation', '=', ['both', 'dots']],
        ];

        $this->controls['dotsColor'] = [
            'group' => 'navigation_style',
            'label' => esc_html__('Dot Color', 'bricksfly'),
            'type'  => 'color',
            'css'   => [['property' => 'background', 'selector' => '.swiper-pagination-bullet:not(.swiper-pagination-bullet-active)']],
            'required' => ['navigation', '=', ['both', 'dots']],
        ];

        $this->controls['dotsActiveColor'] = [
            'group' => 'navigation_style',
            'label' => esc_html__('Dot Active Color', 'bricksfly'),
            'type'  => 'color',
            'css'   => [['property' => 'background', 'selector' => '.swiper-pagination-bullet']],
            'required' => ['navigation', '=', ['both', 'dots']],
        ];

        // Image Style

        // Image Size — width of the image column on the slide's grid.
        // 100% = image fills the slide (content gets 0). 50% = image takes
        // half, content takes the other half.
        $this->controls['imageSize'] = [
            'group'       => 'image_style',
            'label'       => esc_html__('Image Size', 'bricksfly'),
            'type'        => 'slider',
            'units'       => [
                '%' => ['min' => 0, 'max' => 100],
            ],
            'default'     => '50%',
            'placeholder' => '50%',
            'description' => esc_html__('How much of the slide width the image column takes; the content fills the rest.', 'bricksfly'),
            'css'         => [
                [
                    'property' => 'grid-template-columns',
                    'selector' => '.slide',
                    'value'    => '%s 1fr',
                ],
            ],
        ];

        $this->controls['imageBorder'] = [
            'group' => 'image_style',
            'label' => esc_html__('Border', 'bricksfly'),
            'type'  => 'border',
            'css'   => [['property' => 'border', 'selector' => '.picture img']],
        ];

        // Content Style
        $this->controls['contentTypography'] = [
            'group' => 'content_style',
            'label' => esc_html__('Typography', 'bricksfly'),
            'type'  => 'typography',
            'css'   => [['property' => 'typography', 'selector' => '.feedback']],
        ];

        $this->controls['contentTextShadow'] = [
            'group' => 'content_style',
            'label' => esc_html__('Text Shadow', 'bricksfly'),
            'type'  => 'text-shadow',
            'css'   => [['property' => 'text-shadow', 'selector' => '.feedback']],
        ];

        $this->controls['contentPadding'] = [
            'group' => 'content_style',
            'label' => esc_html__('Padding', 'bricksfly'),
            'type'  => 'dimensions',
            'css'   => [['property' => 'padding', 'selector' => '.feedback']],
        ];

        // Name Style
        $this->controls['nameTypography'] = [
            'group' => 'name_style',
            'label' => esc_html__('Typography', 'bricksfly'),
            'type'  => 'typography',
            'css'   => [['property' => 'typography', 'selector' => '.name']],
        ];

        $this->controls['nameTextShadow'] = [
            'group' => 'name_style',
            'label' => esc_html__('Text Shadow', 'bricksfly'),
            'type'  => 'text-shadow',
            'css'   => [['property' => 'text-shadow', 'selector' => '.name']],
        ];

        $this->controls['namePadding'] = [
            'group' => 'name_style',
            'label' => esc_html__('Padding', 'bricksfly'),
            'type'  => 'dimensions',
            'css'   => [['property' => 'padding', 'selector' => '.name']],
        ];

        // Designation Style
        $this->controls['designationTypography'] = [
            'group' => 'designation_style',
            'label' => esc_html__('Typography', 'bricksfly'),
            'type'  => 'typography',
            'css'   => [['property' => 'typography', 'selector' => '.designation']],
        ];

        $this->controls['designationTextShadow'] = [
            'group' => 'designation_style',
            'label' => esc_html__('Text Shadow', 'bricksfly'),
            'type'  => 'text-shadow',
            'css'   => [['property' => 'text-shadow', 'selector' => '.designation']],
        ];

        // Quote Style
        $this->controls['quoteColor'] = [
            'group'    => 'quote_style',
            'label'    => esc_html__('Quote Color', 'bricksfly'),
            'type'     => 'color',
            'css'      => [
                ['property' => 'color', 'selector' => '.quote-icon i'],
                ['property' => 'fill', 'selector' => '.quote-icon svg'],
            ],
        ];

        // number + units: true so Bricks emits CSS values with the unit
        // appended (e.g. "50px") — slider with `units: ['px' => [...]]`
        // sometimes drops the unit, producing invalid `font-size: 50`.
        // Each selector gets its own entry (no comma list) for reliability.
        $this->controls['quoteSize'] = [
            'group' => 'quote_style',
            'label' => esc_html__('Quote Size', 'bricksfly'),
            'type'  => 'number',
            'units' => true,
            'css'   => [
                ['property' => 'font-size', 'selector' => '.quote-icon'],
                ['property' => 'font-size', 'selector' => '.quote-icon i'],
                ['property' => 'width',     'selector' => '.quote-icon svg'],
                ['property' => 'height',    'selector' => '.quote-icon svg'],
            ],
        ];
    }

    public function enqueue_scripts()
    {
        // Use the Swiper bundled with Bricks (registered by Bricks core as 'bricks-swiper').
        wp_enqueue_style('bricks-swiper');
        wp_enqueue_script('bricks-swiper');

        wp_enqueue_style("bricks-font-awesome-6");
        wp_enqueue_style("bricks-font-awesome-6-brands");

        // filemtime() so the URL changes whenever the file does — without
        // this the browser caches the old file forever (hardcoded '1.0.0').
        $css_file = AAB_ADDONS_PATH . 'public/css/testimonial-2.css';
        wp_enqueue_style(
            'aae-testimonial-2',
            AAB_ADDONS_URL . 'public/build/elements/testimonial-2.css',
            ['bricks-swiper'],
            file_exists($css_file) ? filemtime($css_file) : AAB_ADDONS_VERSION
        );

        $js_file = AAB_ADDONS_PATH . 'public/js/testimonial-2.js';
        wp_enqueue_script(
            'aae-testimonial-2',
            AAB_ADDONS_URL . 'public/build/elements/testimonial-2.js',
            ['bricks-swiper'],
            file_exists($js_file) ? filemtime($js_file) : AAB_ADDONS_VERSION,
            true
        );
    }

    public function render()
    {
        $settings = $this->settings;

        if (empty($settings['testimonials'])) {
            return $this->render_element_placeholder([
                'icon-class' => 'ti-comment-alt',
                'text'       => esc_html__('No testimonials added.', 'bricksfly'),
            ]);
        }

        $style       = $settings['elementList'] ?? '1';
        $show_arrows = in_array($settings['navigation'] ?? 'both', ['arrows', 'both']);
        $show_dots   = in_array($settings['navigation'] ?? 'both', ['dots', 'both']);
        // Always fetch the full WP variant; the visible size is CSS-driven
        // by the `imageSize` slider (% of slide width).
        $image_size  = 'full';

        // Build Swiper options
        $slider_options = [
            'loop'           => !empty($settings['loop']),
            'speed'          => (int) ($settings['speed'] ?? 500),
            'allowTouchMove' => !empty($settings['allowTouchMove']),
            'slidesPerView'  => (int) ($settings['slidesToShow'] ?? 1),
            'spaceBetween'   => (int) ($settings['spaceBetween'] ?? 20),
        ];

        if (!empty($settings['autoplay'])) {
            $slider_options['autoplay'] = [
                'delay'                => (int) ($settings['autoplayDelay'] ?? 3000),
                'disableOnInteraction' => !empty($settings['autoplayInteraction']),
            ];
        }

        if ($show_arrows) {
            $slider_options['navigation'] = [
                'nextEl' => '#aae-ts2-' . $this->id . ' .wcf-arrow-next',
                'prevEl' => '#aae-ts2-' . $this->id . ' .wcf-arrow-prev',
            ];
        }

        if ($show_dots) {
            $slider_options['pagination'] = [
                'el'        => '#aae-ts2-' . $this->id . ' .swiper-pagination',
                'clickable' => true,
            ];
        }

        // Do NOT override the root id — Bricks scopes its generated per-element
        // CSS to `#brxe-XXX`. Overriding the id breaks every style control on
        // this element. The custom id was only used for Swiper navigation/
        // pagination selectors below, but the JS already resolves those via
        // wrapper.querySelector(), so they're redundant.
        $root_classes = ['aae-testimonial-2-wrapper', 'style-' . $style];
        $this->set_attribute('_root', 'class', $root_classes);
        $this->set_attribute('_root', 'data-swiper-options', wp_json_encode($slider_options));

        // arrowsOffset / arrowsOffsetVertical / dotsOffset are now applied
        // via Bricks's `'css'` array on the controls themselves — no inline
        // style needed here.

        $direction = $settings['sliderDirection'] ?? 'ltr';
?>
        <div <?php echo $this->render_attributes('_root'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
                ?>>

            <div class="wcf__slider swiper" dir="<?php echo esc_attr($direction); ?>">
                <div class="swiper-wrapper">
                    <?php foreach ($settings['testimonials'] as $index => $item) : ?>
                        <div class="swiper-slide">
                            <div class="slide">
                                <div class="picture">
                                    <?php
                                    $has_link = !empty($item['link']['url']);

                                    if ($has_link) {
                                        $this->set_link_attributes("ts2-link-{$index}", $item['link']);
                                    }

                                    $image_html = '';
                                    if (!empty($item['testimonialImage']['id'])) {
                                        $image_html = wp_get_attachment_image(
                                            $item['testimonialImage']['id'],
                                            $image_size,
                                            false,
                                            ['class' => 'swiper-slide-image']
                                        );
                                    } elseif (!empty($item['testimonialImage']['url'])) {
                                        $image_html = '<img class="swiper-slide-image" src="' . esc_url($item['testimonialImage']['url']) . '" alt="">';
                                    }

                                    if ($has_link && $image_html) {
                                        echo '<a ' . $this->render_attributes("ts2-link-{$index}") . '>' . $image_html . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    } else {
                                        echo $image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    }
                                    ?>
                                </div>
                                <div class="content">
                                    <div class="image quote-icon">
                                        <?php
                                        if (!empty($settings['quoteIcon'])) {
                                            echo self::render_icon($settings['quoteIcon'], ['aria-hidden' => 'true']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                        }
                                        ?>
                                    </div>
                                    <div class="feedback">
                                        <?php echo wp_kses_post($item['testimonialContent'] ?? ''); ?>
                                    </div>
                                    <?php if ($has_link) : ?>
                                        <a class="name" <?php echo $this->render_attributes("ts2-link-{$index}"); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
                                                        ?>><?php echo esc_html($item['testimonialName'] ?? ''); ?></a>
                                    <?php else : ?>
                                        <div class="name"><?php echo esc_html($item['testimonialName'] ?? ''); ?></div>
                                    <?php endif; ?>
                                    <div class="designation"><?php echo esc_html($item['testimonialJob'] ?? ''); ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if (count($settings['testimonials']) > 1) : ?>
                <?php if ($show_arrows) : ?>
                    <div class="ts-navigation">
                        <div class="wcf-arrow wcf-arrow-prev" role="button" tabindex="0">
                            <?php $this->render_arrow_icon('prev'); ?>
                        </div>
                        <div class="wcf-arrow wcf-arrow-next" role="button" tabindex="0">
                            <?php $this->render_arrow_icon('next'); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($show_dots) : ?>
                    <div class="ts-pagination">
                        <div class="swiper-pagination"></div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
<?php
    }

    private function render_arrow_icon($type)
    {
        $settings = $this->settings;
        $key = $type === 'prev' ? 'prevIcon' : 'nextIcon';

        if (!empty($settings[$key])) {
            echo self::render_icon($settings[$key], ['aria-hidden' => 'true']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        } else {
            $direction = $type === 'prev' ? 'left' : 'right';
            echo '<i class="fas fa-chevron-' . esc_attr($direction) . '"></i>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
    }
}
