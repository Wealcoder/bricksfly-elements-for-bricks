<?php

if (!defined('ABSPATH')) exit;

class THEBRBRE_Bricks_Toggle_Switch extends \Bricks\Element
{
    public $category     = 'bricks fly';
    public $name         = 'aae-toggle-switch';
    public $icon         = 'ti-control-shuffle aab-element-marker';
    public $css_selector = '.aae-toggle-switch-wrapper';
    public $scripts      = ['aaeToggleSwitch'];

    public function get_label()
    {
        return esc_html__('Toggle Switch', 'the-bricksfly');
    }

    public function get_keywords()
    {
        return ['toggle', 'switch', 'switcher', 'tab', 'pricing', 'monthly', 'yearly'];
    }

    public function set_control_groups()
    {
        $this->control_groups['toggle'] = [
            'title' => esc_html__('Toggle Switch', 'the-bricksfly'),
            'tab'   => 'content',
        ];

        $this->control_groups['wrapper_style'] = [
            'title' => esc_html__('Switcher Wrapper', 'the-bricksfly'),
            'tab'   => 'style',
        ];

        $this->control_groups['switcher_style'] = [
            'title'    => esc_html__('Switcher', 'the-bricksfly'),
            'tab'      => 'style',
            'required' => ['elementList', '=', '1'],
        ];

        $this->control_groups['title_style'] = [
            'title' => esc_html__('Title', 'the-bricksfly'),
            'tab'   => 'style',
        ];

        $this->control_groups['active_title_style'] = [
            'title' => esc_html__('Active Title', 'the-bricksfly'),
            'tab'   => 'style',
        ];
    }

    public function set_controls()
    {
        // === CONTENT: Toggle Switch ===

        $this->controls['elementList'] = [
            'group'   => 'toggle',
            'label'   => esc_html__('Style', 'the-bricksfly'),
            'type'    => 'select',
            'options' => [
                '1' => esc_html__('One', 'the-bricksfly'),
                '2' => esc_html__('Two', 'the-bricksfly'),
            ],
            'default' => '1',
            'inline'  => true,
        ];

        $this->controls['toggleSwitcher'] = [
            'group'         => 'toggle',
            'label'         => esc_html__('Toggle Switcher', 'the-bricksfly'),
            'type'          => 'repeater',
            'titleProperty' => 'switchTitle',
            'description'   => esc_html__('Only the first 2 items are used (before / after).', 'the-bricksfly'),
            'fields'        => [
                'switchTitle' => [
                    'label'       => esc_html__('Title', 'the-bricksfly'),
                    'type'        => 'text',
                    'placeholder' => esc_html__('Monthly', 'the-bricksfly'),
                ],
                'contentType' => [
                    'label'   => esc_html__('Content Type', 'the-bricksfly'),
                    'type'    => 'select',
                    'options' => [
                        'content'  => esc_html__('Content', 'the-bricksfly'),
                        'template' => esc_html__('Saved Templates', 'the-bricksfly'),
                    ],
                    'default' => 'content',
                    'inline'  => true,
                ],
                'switchTemplate' => [
                    'label'       => esc_html__('Saved Template', 'the-bricksfly'),
                    'type'        => 'select',
                    'options'     => bricks_is_builder() ? \Bricks\Templates::get_templates_list(['section', 'content', 'popup'], get_the_ID()) : [],
                    'searchable'  => true,
                    'placeholder' => esc_html__('Select template', 'the-bricksfly'),
                    'required'    => ['contentType', '=', 'template'],
                ],
                'switchContent' => [
                    'label'    => esc_html__('Content', 'the-bricksfly'),
                    'type'     => 'editor',
                    'default'  => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'the-bricksfly'),
                    'required' => ['contentType', '=', 'content'],
                ],
            ],
            'default' => [
                ['switchTitle' => 'Monthly', 'contentType' => 'content', 'switchContent' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.'],
                ['switchTitle' => 'Yearly',  'contentType' => 'content', 'switchContent' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.'],
            ],
        ];

        $this->controls['toggleGap'] = [
            'group' => 'toggle',
            'label' => esc_html__('Gap', 'the-bricksfly'),
            'type'  => 'slider',
            'units' => [
                'px' => ['min' => 0, 'max' => 200],
                // '%'  => ['min' => 0, 'max' => 100],
            ],
            'default' => 10,
            // 'default' => [
            //     'value' => 10,
            //     'unit'  => 'px',
            // ],
            // 'css' => [
            //     [
            //         'property' => 'gap',
            //         'selector' => '.slide-toggle-wrapper',
            //         'value'    => '%value%%unit%', // ← append unit placeholder
            //     ],
            // ],
        ];

        $this->controls['toggleBottomSpace'] = [
            'group' => 'toggle',
            'label' => esc_html__('Bottom Space', 'the-bricksfly'),
            'type'  => 'slider',
            'units' => [
                'px' => ['min' => 0, 'max' => 200],
                // '%' => ['min' => 0, 'max' => 100]
            ],
            'default' => 20,
            // 'default' => [
            //     'value' => 20,
            //     'unit'  => 'px',
            // ],
            // 'css'   => [
            //     ['property' => 'margin-bottom', 'selector' => '.slide-toggle-wrapper'],
            // ],
        ];

        // === STYLE: Switcher Wrapper ===

        $this->controls['wrapBackground'] = [
            'group' => 'wrapper_style',
            'label' => esc_html__('Background', 'the-bricksfly'),
            'type'  => 'color',
            'css'   => [
                ['property' => 'background-color', 'selector' => '.slide-toggle-wrapper'],
            ],
        ];

        $this->controls['wrapPadding'] = [
            'group' => 'wrapper_style',
            'label' => esc_html__('Padding', 'the-bricksfly'),
            'type'  => 'dimensions',
            'css'   => [
                ['property' => 'padding', 'selector' => '.slide-toggle-wrapper'],
            ],
        ];

        $this->controls['wrapBorder'] = [
            'group' => 'wrapper_style',
            'label' => esc_html__('Border', 'the-bricksfly'),
            'type'  => 'border',
            'css'   => [
                ['property' => 'border', 'selector' => '.slide-toggle-wrapper'],
            ],
        ];

        // === STYLE: Switcher (style 1 only) ===

        $this->controls['switcherWidth'] = [
            'group'   => 'switcher_style',
            'label'   => esc_html__('Width', 'the-bricksfly'),
            'type'    => 'number',
            'units'   => ['px' => ['min' => 0, 'max' => 200]],
            'default' => '40',
            'placeholder' => '40',
            'css'     => [
                ['property' => '--switcher-width', 'selector' => '.slide-toggle-wrapper'],
                ['property' => 'width',            'selector' => '.switcher'],
            ],
        ];

        $this->controls['switcherHeight'] = [
            'group'   => 'switcher_style',
            'label'   => esc_html__('Height', 'the-bricksfly'),
            'type'    => 'number',
            'units'   => ['px' => ['min' => 0, 'max' => 200]],
            'default' => '20',
            'placeholder' => '20',
            'css'     => [
                ['property' => 'height', 'selector' => '.switcher'],
            ],
        ];

        $this->controls['switcherBackground'] = [
            'group' => 'switcher_style',
            'label' => esc_html__('Background', 'the-bricksfly'),
            'type'  => 'color',
            'css'   => [
                ['property' => 'background-color', 'selector' => '.switcher'],
                ['property' => 'border-color',     'selector' => '.switcher'],
            ],
        ];

        $this->controls['switcherRadius'] = [
            'group' => 'switcher_style',
            'label' => esc_html__('Border', 'the-bricksfly'),
            'type'  => 'border',
            'css'   => [
                ['property' => 'border', 'selector' => '.switcher'],
            ],
        ];

        $this->controls['switcherActiveBackground'] = [
            'group' => 'switcher_style',
            'label' => esc_html__('Active Background', 'the-bricksfly'),
            'type'  => 'color',
            'css'   => [
                ['property' => 'background-color', 'selector' => 'input:checked + .switcher'],
                ['property' => 'border-color',     'selector' => 'input:checked + .switcher'],
            ],
        ];

        $this->controls['indicatorHeading'] = [
            'group' => 'switcher_style',
            'label' => esc_html__('Indicator', 'the-bricksfly'),
            'type'  => 'separator',
        ];

        $this->controls['indicatorWidth'] = [
            'group' => 'switcher_style',
            'label' => esc_html__('Indicator Size', 'the-bricksfly'),
            'type'  => 'number',
            'units' => ['px' => ['min' => 0, 'max' => 200]],
            'default' => '16',
            'placeholder' => '16',
            'css'   => [
                ['property' => '--switcher-indicator-width', 'selector' => '.slide-toggle-wrapper'],
            ],
        ];

        $this->controls['indicatorSpace'] = [
            'group' => 'switcher_style',
            'label' => esc_html__('Indicator Space', 'the-bricksfly'),
            'type'  => 'number',
            'default' => '2',
            'placeholder' => '2',
            'units' => ['px' => ['min' => 0, 'max' => 200]],
            'css'   => [
                ['property' => '--switcher-border-width', 'selector' => '.slide-toggle-wrapper'],
            ],
        ];

        $this->controls['indicatorBackground'] = [
            'group' => 'switcher_style',
            'label' => esc_html__('Background', 'the-bricksfly'),
            'type'  => 'color',
            'css'   => [
                ['property' => 'background-color', 'selector' => '.switcher::before'],
                ['property' => 'border-color',     'selector' => '.switcher::before'],
            ],
        ];

        $this->controls['indicatorRadius'] = [
            'group' => 'switcher_style',
            'label' => esc_html__('Border', 'the-bricksfly'),
            'type'  => 'border',
            'css'   => [
                ['property' => 'border', 'selector' => '.switcher::before'],
            ],
        ];

        // === STYLE: Title ===

        $this->controls['titleTypography'] = [
            'group' => 'title_style',
            'label' => esc_html__('Typography', 'the-bricksfly'),
            'type'  => 'typography',
            'css'   => [
                ['property' => 'typography', 'selector' => '.before_label'],
                ['property' => 'typography', 'selector' => '.after_label'],
            ],
        ];


        $this->controls['titleBgColor'] = [
            'group' => 'title_style',
            'label' => esc_html__('Title Background', 'the-bricksfly'),
            'type'  => 'color',
            'css'   => [
                ['property' => 'background-color', 'selector' => '.before_label'],
                ['property' => 'background-color', 'selector' => '.after_label'],
            ],
            'required' => ['elementList', '=', '2'],
        ];

            $this->controls['TitleBorder'] = [
            'tab'   => 'style',
            'group' => 'title_style',
            'label' => esc_html__( 'Title Border', 'the-bricksfly' ),
            'type'  => 'border',
            'css'   => [
                [
                    'property' => 'border',
                    'selector' => '.after_label, .before_label',
                ],
            ],
        ];

        // $this->controls['titleColor'] = [
        //     'group' => 'title_style',
        //     'label' => esc_html__('Text Color', 'the-bricksfly'),
        //     'type'  => 'color',
        //     'css'   => [
        //         ['property' => 'color', 'selector' => '.before_label'],
        //         ['property' => 'color', 'selector' => '.after_label'],
        //     ],
        // ];

        // === STYLE: Active Title ===

        $this->controls['activeTitleColor'] = [
            'group' => 'active_title_style',
            'label' => esc_html__('Text Color', 'the-bricksfly'),
            'type'  => 'color',
            'css'   => [
                ['property' => 'color', 'selector' => '.before_label.active'],
                ['property' => 'color', 'selector' => '.after_label.active'],
            ],
        ];

        $this->controls['activeTitleBackground'] = [
            'group'    => 'active_title_style',
            'label'    => esc_html__('Title Background', 'the-bricksfly'),
            'type'     => 'color',
            'css'      => [
                ['property' => 'background-color', 'selector' => '.before_label::after'],
                ['property' => 'background-color', 'selector' => '.after_label::after'],
            ],
            'required' => ['elementList', '=', '2'],
        ];

        $this->controls['activeTitlePadding'] = [
            'group'    => 'active_title_style',
            'label'    => esc_html__('Padding', 'the-bricksfly'),
            'type'     => 'dimensions',
            'css'      => [
                ['property' => 'padding', 'selector' => '.before_label'],
                ['property' => 'padding', 'selector' => '.after_label'],
            ],
            'required' => ['elementList', '=', '2'],
        ];      

        $this->controls['activeTitleBorder'] = [
            'tab'   => 'style',
            'group' => 'active_title_style',
            'label' => esc_html__( 'Active Title Border', 'the-bricksfly' ),
            'type'  => 'border',
            'css'   => [
                [
                    'property' => 'border',
                    'selector' => '.active.after_label, .active.before_label',
                ],
            ],
        ];
    }

    public function enqueue_scripts()
    {
        wp_enqueue_style(
            'aae-toggle-switch',
            THEBRBRE_URL . 'public/build/elements/toggle-switch.css',
            [],
            '1.0.0'
        );

        wp_enqueue_script(
            'aae-toggle-switch',
            THEBRBRE_URL . 'public/build/elements/toggle-switch.js',
            [],
            '1.0.0',
            true
        );
    }

    public function render()
    {

        // $gap = $this->get_setting('toggleGap'); 

        if (! empty($gap['value'])) {
            $unit  = $gap['unit'] ?? 'px';
            $value = $gap['value'] . $unit;
            $this->set_attribute('_root', 'style', "gap: {$value}");
        }



        $settings = $this->settings;
        $items    = !empty($settings['toggleSwitcher']) ? $settings['toggleSwitcher'] : [];
        $style    = !empty($settings['elementList']) ? $settings['elementList'] : '1';

        $toggleGap_value = '10px';
        $toggleBottomSpace_value = '20px';

        if (!empty($settings['toggleGap'])) {
            $value = intval($settings['toggleGap']);
            $toggleGap_value =  "{$value}px";
        }

        if (!empty($settings['toggleBottomSpace'])) {
            $value = intval($settings['toggleBottomSpace']);
            $toggleBottomSpace_value =  "{$value}px";
        }

        // // toggleGap
        // if (isset($settings['toggleGap'])) {
        //     $value = intval($settings['toggleGap']);
        //     $this->set_attribute('_root', 'style', "--toggle-gap: {$value}px");
        // }

        // // toggleBottomSpace
        // if (isset($settings['toggleBottomSpace'])) {
        //     $value = intval($settings['toggleBottomSpace']);
        //     $this->set_attribute('_root', 'style', "--toggle-bt-space-gap: {$value}px");
        // }


        $this->set_attribute('_root', 'style', "--toggle-gap: {$toggleGap_value}; --toggle-bt-space-gap: {$toggleBottomSpace_value};");


        if (count($items) < 2) {
            return $this->render_element_placeholder([
                'icon-class' => 'ti-control-shuffle',
                'text'       => esc_html__('Toggle Switch needs at least 2 items.', 'the-bricksfly'),
            ]);
        }

        $this->set_attribute('_root', 'class', ['aae-toggle-switch-wrapper', 'style-' . $style]);

        $input_id = 'aae-toggle-' . $this->id;

        $before = $items[0];
        $after  = $items[1];
?>
        <?php echo wp_kses_post('<div ' . $this->render_attributes('_root') . '>'); ?>

            <div class="slide-toggle-wrapper">
                <label for="<?php echo esc_attr($input_id); ?>" class="before_label active">
                    <?php echo esc_html($before['switchTitle'] ?? ''); ?>
                </label>
                <input type="checkbox" id="<?php echo esc_attr($input_id); ?>">
                <label for="<?php echo esc_attr($input_id); ?>" class="switcher"></label>
                <label for="<?php echo esc_attr($input_id); ?>" class="after_label">
                    <?php echo esc_html($after['switchTitle'] ?? ''); ?>
                </label>
            </div>

            <div class="toggle-content">
                <?php foreach ($items as $index => $item) :
                    $pane_class = 'toggle-pane' . (0 === $index ? ' show' : '');
                    $type       = $item['contentType'] ?? 'content';
                ?>
                    <div class="<?php echo esc_attr($pane_class); ?>">
                        <?php
                        if ('template' === $type) {
                            if (!empty($item['switchTemplate'])) {
                                echo do_shortcode('[bricks_template id="' . intval($item['switchTemplate']) . '"]');
                            }
                        } else {
                            echo wp_kses_post($item['switchContent'] ?? '');
                        }
                        ?>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
<?php
    }
}
