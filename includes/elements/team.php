<?php

if (!defined('ABSPATH')) exit;

class THEBRBRE_Bricks_Team extends \Bricks\Element
{
    public $category     = 'bricks fly';
    public $name         = 'aae-team';
    public $icon         = 'ti-user aab-element-marker';
    public $css_selector = '.aae-team-wrapper';

    public function get_label()
    {
        return esc_html__('Team', 'bricksfly-elements-for-bricks');
    }

    public function get_keywords()
    {
        return ['team', 'member', 'person', 'staff', 'social'];
    }

    public function set_control_groups()
    {
        $this->control_groups['layout'] = [
            'title' => esc_html__('Layout', 'bricksfly-elements-for-bricks'),
            'tab'   => 'content',
        ];

        $this->control_groups['image'] = [
            'title' => esc_html__('Image', 'bricksfly-elements-for-bricks'),
            'tab'   => 'content',
        ];

        $this->control_groups['content'] = [
            'title' => esc_html__('Content', 'bricksfly-elements-for-bricks'),
            'tab'   => 'content',
        ];

        $this->control_groups['social_media'] = [
            'title' => esc_html__('Social Media', 'bricksfly-elements-for-bricks'),
            'tab'   => 'content',
        ];

        $this->control_groups['image_style'] = [
            'title' => esc_html__('Image', 'bricksfly-elements-for-bricks'),
            'tab'   => 'style',
        ];

        $this->control_groups['content_style'] = [
            'title' => esc_html__('Content', 'bricksfly-elements-for-bricks'),
            'tab'   => 'style',
        ];

        $this->control_groups['social_style'] = [
            'title' => esc_html__('Social Media', 'bricksfly-elements-for-bricks'),
            'tab'   => 'style',
        ];
    }

    public function set_controls()
    {
        // === LAYOUT ===

        $this->controls['elementList'] = [
            'group'   => 'layout',
            'label'   => esc_html__('Style', 'bricksfly-elements-for-bricks'),
            'type'    => 'select',
            'options' => [
                '1' => esc_html__('One', 'bricksfly-elements-for-bricks'),
                '2' => esc_html__('Two', 'bricksfly-elements-for-bricks'),
            ],
            'default' => '1',
            'inline'  => true,
        ];

        $this->controls['align'] = [
            'group' => 'layout',
            'label' => esc_html__('Alignment', 'bricksfly-elements-for-bricks'),
            'type'  => 'text-align',
            'css'   => [['property' => 'text-align', 'selector' => '']],
        ];

        // === IMAGE ===

        $this->controls['memberImage'] = [
            'group' => 'image',
            'label' => esc_html__('Image', 'bricksfly-elements-for-bricks'),
            'type'  => 'image',
        ];

        $this->controls['imageSize'] = [
            'group'   => 'image',
            'label'   => esc_html__('Image Size', 'bricksfly-elements-for-bricks'),
            'type'    => 'select',
            'options' => [
                'thumbnail' => esc_html__('Thumbnail', 'bricksfly-elements-for-bricks'),
                'medium'    => esc_html__('Medium', 'bricksfly-elements-for-bricks'),
                'large'     => esc_html__('Large', 'bricksfly-elements-for-bricks'),
                'full'      => esc_html__('Full', 'bricksfly-elements-for-bricks'),
            ],
            'default' => 'medium',
        ];

        // === CONTENT ===

        $this->controls['memberName'] = [
            'group'       => 'content',
            'label'       => esc_html__('Name', 'bricksfly-elements-for-bricks'),
            'type'        => 'text',
            'default'     => esc_html__( 'Adam Smith', 'bricksfly-elements-for-bricks' ),
            'placeholder' => esc_html__('Enter Member Name', 'bricksfly-elements-for-bricks'),
        ];

        $this->controls['nameTag'] = [
            'group'   => 'content',
            'label'   => esc_html__('Name HTML Tag', 'bricksfly-elements-for-bricks'),
            'type'    => 'select',
            'options' => [
                'h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3',
                'h4' => 'H4', 'h5' => 'H5', 'h6' => 'H6',
                'div' => 'div', 'span' => 'span', 'p' => 'p',
            ],
            'default' => 'h3',
            'inline'  => true,
        ];

        $this->controls['memberDesignation'] = [
            'group'       => 'content',
            'label'       => esc_html__('Designation', 'bricksfly-elements-for-bricks'),
            'type'        => 'text',
            'default'     => esc_html__( 'Developer', 'bricksfly-elements-for-bricks' ),
            'placeholder' => esc_html__('Enter Member Designation', 'bricksfly-elements-for-bricks'),
        ];

        $this->controls['memberDescription'] = [
            'group'   => 'content',
            'label'   => esc_html__('Description', 'bricksfly-elements-for-bricks'),
            'type'    => 'textarea',
            'default' => esc_html__( 'Add team member description here. Remove the text if not necessary.', 'bricksfly-elements-for-bricks' ),
        ];

        $this->controls['detailsLink'] = [
            'group' => 'content',
            'label' => esc_html__('URL', 'bricksfly-elements-for-bricks'),
            'type'  => 'link',
        ];

        // === SOCIAL MEDIA ===

        $this->controls['showSocialIcons'] = [
            'group'   => 'social_media',
            'label'   => esc_html__('Show Social Icons', 'bricksfly-elements-for-bricks'),
            'type'    => 'checkbox',
            'default' => true,
        ];

        $this->controls['socialPosition'] = [
            'group'   => 'social_media',
            'label'   => esc_html__('Placement', 'bricksfly-elements-for-bricks'),
            'type'    => 'select',
            'options' => [
                'bottom' => esc_html__('Bottom', 'bricksfly-elements-for-bricks'),
                'left'   => esc_html__('Left', 'bricksfly-elements-for-bricks'),
                'right'  => esc_html__('Right', 'bricksfly-elements-for-bricks'),
                'center' => esc_html__('Center', 'bricksfly-elements-for-bricks'),
            ],
            'default'  => 'bottom',
            'inline'   => true,
            'required' => [
                ['showSocialIcons', '!=', ''],
                ['elementList', '=', '2'],
            ],
        ];

        $this->controls['socialIcons'] = [
            'group'         => 'social_media',
            'label'         => esc_html__('Social Icons', 'bricksfly-elements-for-bricks'),
            'type'          => 'repeater',
            'titleProperty' => 'socialLabel',
            'fields'        => [
                'socialIcon' => [
                    'label'   => esc_html__('Icon', 'bricksfly-elements-for-bricks'),
                    'type'    => 'icon',
                    'default' => [
                        'library' => 'fontawesome',
                        'icon'    => 'fab fa-wordpress',
                    ],
                ],
                'socialLink' => [
                    'label' => esc_html__('Link', 'bricksfly-elements-for-bricks'),
                    'type'  => 'link',
                ],
                'socialLabel' => [
                    'label'   => esc_html__('Label', 'bricksfly-elements-for-bricks'),
                    'type'    => 'text',
                    'default' => esc_html__( 'Social', 'bricksfly-elements-for-bricks' ),
                ],
            ],
            'default' => [
                ['socialIcon' => ['library' => 'fontawesome', 'icon' => 'fab fa-facebook'], 'socialLabel' => 'Facebook'],
                ['socialIcon' => ['library' => 'fontawesome', 'icon' => 'fab fa-twitter'], 'socialLabel' => 'Twitter'],
                ['socialIcon' => ['library' => 'fontawesome', 'icon' => 'fab fa-youtube'], 'socialLabel' => 'YouTube'],
            ],
            'required' => ['showSocialIcons', '!=', ''],
        ];

        // === STYLE: IMAGE ===

        $this->controls['imgWidth'] = [
            'group' => 'image_style',
            'label' => esc_html__('Width', 'bricksfly-elements-for-bricks'),
            'type'  => 'slider',
            'units' => [
                'px' => ['min' => 1, 'max' => 1000],
                '%'  => ['min' => 1, 'max' => 100],
            ],
            'css' => [['property' => 'width', 'selector' => 'img']],
        ];

        $this->controls['imgMaxWidth'] = [
            'group' => 'image_style',
            'label' => esc_html__('Max Width', 'bricksfly-elements-for-bricks'),
            'type'  => 'slider',
            'units' => [
                'px' => ['min' => 1, 'max' => 1000],
                '%'  => ['min' => 1, 'max' => 100],
            ],
            'css' => [['property' => 'max-width', 'selector' => 'img']],
        ];

        $this->controls['imgHeight'] = [
            'group' => 'image_style',
            'label' => esc_html__('Height', 'bricksfly-elements-for-bricks'),
            'type'  => 'slider',
            'units' => ['px' => ['min' => 1, 'max' => 500]],
            'css'   => [['property' => 'height', 'selector' => 'img']],
        ];

        $this->controls['imgObjectFit'] = [
            'group'   => 'image_style',
            'label'   => esc_html__('Object Fit', 'bricksfly-elements-for-bricks'),
            'type'    => 'select',
            'options' => [
                ''        => esc_html__('Default', 'bricksfly-elements-for-bricks'),
                'fill'    => esc_html__('Fill', 'bricksfly-elements-for-bricks'),
                'cover'   => esc_html__('Cover', 'bricksfly-elements-for-bricks'),
                'contain' => esc_html__('Contain', 'bricksfly-elements-for-bricks'),
            ],
            'css' => [['property' => 'object-fit', 'selector' => 'img']],
        ];

        $this->controls['imgObjectPosition'] = [
            'group'   => 'image_style',
            'label'   => esc_html__('Object Position', 'bricksfly-elements-for-bricks'),
            'type'    => 'select',
            'options' => [
                'center center' => 'Center Center',
                'center left'   => 'Center Left',
                'center right'  => 'Center Right',
                'top center'    => 'Top Center',
                'top left'      => 'Top Left',
                'top right'     => 'Top Right',
                'bottom center' => 'Bottom Center',
                'bottom left'   => 'Bottom Left',
                'bottom right'  => 'Bottom Right',
            ],
            'default' => 'center center',
            'css'     => [['property' => 'object-position', 'selector' => 'img']],
            'required' => ['imgObjectFit', '=', 'cover'],
        ];

        $this->controls['imgBorder'] = [
            'group' => 'image_style',
            'label' => esc_html__('Border', 'bricksfly-elements-for-bricks'),
            'type'  => 'border',
            'css'   => [['property' => 'border', 'selector' => 'img']],
        ];

        $this->controls['imgOpacity'] = [
            'group' => 'image_style',
            'label' => esc_html__('Opacity', 'bricksfly-elements-for-bricks'),
            'type'  => 'slider',
            'units' => ['px' => ['min' => 0, 'max' => 1, 'step' => 0.01]],
            'css'   => [['property' => 'opacity', 'selector' => 'img']],
        ];

        $this->controls['imgCssFilters'] = [
            'group' => 'image_style',
            'label' => esc_html__('CSS Filters', 'bricksfly-elements-for-bricks'),
            'type'  => 'filters',
            'css'   => [['property' => 'filter', 'selector' => 'img']],
        ];

        $this->controls['imgHoverOpacity'] = [
            'group' => 'image_style',
            'label' => esc_html__('Hover Opacity', 'bricksfly-elements-for-bricks'),
            'type'  => 'slider',
            'units' => ['px' => ['min' => 0, 'max' => 1, 'step' => 0.01]],
            'css'   => [['property' => 'opacity', 'selector' => '&:hover img']],
        ];

        $this->controls['imgHoverCssFilters'] = [
            'group' => 'image_style',
            'label' => esc_html__('Hover CSS Filters', 'bricksfly-elements-for-bricks'),
            'type'  => 'filters',
            'css'   => [['property' => 'filter', 'selector' => '&:hover img']],
        ];

        $this->controls['imgTransitionDuration'] = [
            'group' => 'image_style',
            'label' => esc_html__('Transition Duration (s)', 'bricksfly-elements-for-bricks'),
            'type'  => 'slider',
            'units' => ['px' => ['min' => 0, 'max' => 3, 'step' => 0.1]],
            'css'   => [['property' => 'transition-duration', 'selector' => 'img']],
        ];

        // === STYLE: CONTENT ===

        $this->controls['contentBackground'] = [
            'group' => 'content_style',
            'label' => esc_html__('Background', 'bricksfly-elements-for-bricks'),
            'type'  => 'background',
            'css'   => [['property' => 'background', 'selector' => '.content']],
            'exclude' => ['video'],  // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- Bricks control option, not a WP_Query arg
        ];

        $this->controls['contentPadding'] = [
            'group' => 'content_style',
            'label' => esc_html__('Padding', 'bricksfly-elements-for-bricks'),
            'type'  => 'dimensions',
            'css'   => [['property' => 'padding', 'selector' => '.content']],
        ];

        // Name
        $this->controls['nameColor'] = [
            'group' => 'content_style',
            'label' => esc_html__('Name Color', 'bricksfly-elements-for-bricks'),
            'type'  => 'color',
            'css'   => [['property' => 'color', 'selector' => '.name']],
        ];

        $this->controls['nameHoverColor'] = [
            'group' => 'content_style',
            'label' => esc_html__('Name Hover Color', 'bricksfly-elements-for-bricks'),
            'type'  => 'color',
            'css'   => [['property' => 'color', 'selector' => '.name a:hover']],
        ];

        $this->controls['nameTypography'] = [
            'group' => 'content_style',
            'label' => esc_html__('Name Typography', 'bricksfly-elements-for-bricks'),
            'type'  => 'typography',
            'css'   => [['property' => 'font', 'selector' => '.name']],
        ];

        $this->controls['nameSpacing'] = [
            'group'   => 'content_style',
            'label'   => esc_html__('Name Spacing', 'bricksfly-elements-for-bricks'),
            'type'    => 'slider',
            'units'   => ['px' => ['min' => 0, 'max' => 100]],
            'default' => '10px',
            'css'     => [['property' => 'margin-bottom', 'selector' => '.name']],
        ];

        // Designation
        $this->controls['designationColor'] = [
            'group' => 'content_style',
            'label' => esc_html__('Designation Color', 'bricksfly-elements-for-bricks'),
            'type'  => 'color',
            'css'   => [['property' => 'color', 'selector' => '.designation']],
        ];

        $this->controls['designationTypography'] = [
            'group' => 'content_style',
            'label' => esc_html__('Designation Typography', 'bricksfly-elements-for-bricks'),
            'type'  => 'typography',
            'css'   => [['property' => 'font', 'selector' => '.designation']],
        ];

        $this->controls['designationSpacing'] = [
            'group'   => 'content_style',
            'label'   => esc_html__('Designation Spacing', 'bricksfly-elements-for-bricks'),
            'type'    => 'slider',
            'units'   => ['px' => ['min' => 0, 'max' => 100]],
            'default' => '10px',
            'css'     => [['property' => 'margin-bottom', 'selector' => '.designation']],
        ];

        // Description
        $this->controls['descriptionColor'] = [
            'group' => 'content_style',
            'label' => esc_html__('Description Color', 'bricksfly-elements-for-bricks'),
            'type'  => 'color',
            'css'   => [['property' => 'color', 'selector' => '.description']],
        ];

        $this->controls['descriptionTypography'] = [
            'group' => 'content_style',
            'label' => esc_html__('Description Typography', 'bricksfly-elements-for-bricks'),
            'type'  => 'typography',
            'css'   => [['property' => 'font', 'selector' => '.description']],
        ];

        $this->controls['descriptionSpacing'] = [
            'group'   => 'content_style',
            'label'   => esc_html__('Description Spacing', 'bricksfly-elements-for-bricks'),
            'type'    => 'slider',
            'units'   => ['px' => ['min' => 0, 'max' => 100]],
            'default' => '10px',
            'css'     => [['property' => 'margin-bottom', 'selector' => '.description']],
        ];

        // === STYLE: SOCIAL ===

        $this->controls['socialBg'] = [
            'group'    => 'social_style',
            'label'    => esc_html__('Background', 'bricksfly-elements-for-bricks'),
            'type'     => 'background',
            'css'      => [['property' => 'background', 'selector' => '.social-media']],
            'exclude'  => ['video'],  // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- Bricks control option, not a WP_Query arg
            'required' => ['elementList', '=', '2'],
        ];

        $this->controls['socialPadding'] = [
            'group'    => 'social_style',
            'label'    => esc_html__('Padding', 'bricksfly-elements-for-bricks'),
            'type'     => 'dimensions',
            'css'      => [['property' => 'padding', 'selector' => '.social-media']],
            'required' => ['elementList', '=', '2'],
        ];

        $this->controls['iconColor'] = [
            'group' => 'social_style',
            'label' => esc_html__('Icon Color', 'bricksfly-elements-for-bricks'),
            'type'  => 'color',
            'css'   => [
                ['property' => 'color', 'selector' => '.social-media a'],
                ['property' => 'fill', 'selector' => '.social-media a svg'],
            ],
        ];

        $this->controls['iconBgColor'] = [
            'group' => 'social_style',
            'label' => esc_html__('Icon Background', 'bricksfly-elements-for-bricks'),
            'type'  => 'color',
            'css'   => [['property' => 'background-color', 'selector' => '.social-media a']],
        ];

        $this->controls['iconHoverColor'] = [
            'group' => 'social_style',
            'label' => esc_html__('Icon Hover Color', 'bricksfly-elements-for-bricks'),
            'type'  => 'color',
            'css'   => [
                ['property' => 'color', 'selector' => '.social-media a:hover'],
                ['property' => 'fill', 'selector' => '.social-media a:hover svg'],
            ],
        ];

        $this->controls['iconHoverBgColor'] = [
            'group' => 'social_style',
            'label' => esc_html__('Icon Hover Background', 'bricksfly-elements-for-bricks'),
            'type'  => 'color',
            'css'   => [['property' => 'background-color', 'selector' => '.social-media a:hover']],
        ];

        $this->controls['iconSize'] = [
            'group' => 'social_style',
            'label' => esc_html__('Icon Size', 'bricksfly-elements-for-bricks'),
            'type'  => 'number',
            'units' => ['px' => ['min' => 6, 'max' => 300]],
            'css'   => [['property' => '--icon-size', 'selector' => '&']],
        ];

        $this->controls['iconPadding'] = [
            'group' => 'social_style',
            'label' => esc_html__('Icon Padding', 'bricksfly-elements-for-bricks'),
            'type'  => 'number',
            'units' => ['px' => ['min' => 1, 'max' => 300]],
            'css'   => [['property' => '--icon-padding', 'selector' => '&']],
        ];

        $this->controls['iconSpacing'] = [
            'group'   => 'social_style',
            'label'   => esc_html__('Icon Spacing', 'bricksfly-elements-for-bricks'),
            'type'    => 'slider',
            'units'   => ['px' => ['min' => 0, 'max' => 100]],
            'default' => '10px',
            'css'     => [['property' => 'gap', 'selector' => '.social-media']],
        ];

        $this->controls['iconBorder'] = [
            'group' => 'social_style',
            'label' => esc_html__('Icon Border', 'bricksfly-elements-for-bricks'),
            'type'  => 'border',
            'css'   => [['property' => 'border', 'selector' => '.social-media a']],
        ];

        $this->controls['iconHoverBorderColor'] = [
            'group' => 'social_style',
            'label' => esc_html__('Icon Hover Border Color', 'bricksfly-elements-for-bricks'),
            'type'  => 'color',
            'css'   => [['property' => 'border-color', 'selector' => '.social-media a:hover']],
        ];
    }

    public function enqueue_scripts()
    {
        wp_enqueue_style("bricks-font-awesome-6");
        wp_enqueue_style("bricks-font-awesome-6-brands");

        wp_enqueue_style(
            'aae-team',
            THEBRBRE_URL . 'public/build/elements/team.css',
            [],
            '1.0.0'
        );
    }

    public function render()
    {
        $settings = $this->settings;

        $style     = $settings['elementList'] ?? '1';
        $name_tag  = $settings['nameTag'] ?? 'h3';
        $image_size = $settings['imageSize'] ?? 'medium';

        // Validate tag
        $allowed_tags = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p'];
        if (!in_array($name_tag, $allowed_tags)) {
            $name_tag = 'h3';
        }

        $has_link = ! empty( $settings['detailsLink']['type'] ) || ! empty( $settings['detailsLink']['url'] );
        if ($has_link) {
            $this->set_link_attributes('details-link', $settings['detailsLink']);
        }

        $social_placement = ($style === '2' && !empty($settings['socialPosition'])) ? $settings['socialPosition'] : 'bottom';

        $root_classes = ['aae-team-wrapper', 'aae-team', 'style-' . $style];
        if ($style === '2') {
            $root_classes[] = 'aae-social-placement-' . $social_placement;
        }
        $this->set_attribute('_root', 'class', $root_classes);

        echo wp_kses_post('<div ' . $this->render_attributes('_root') . '>');

        // Thumb
        echo '<div class="thumb">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        $this->render_image($settings, $image_size, $has_link);
        if ($style === '2') {
            $this->render_social_icons($settings);
        }
        echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

        // Content
        echo '<div class="content">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        $this->render_name($settings, $name_tag, $has_link);

        if (!empty($settings['memberDesignation'])) {
            echo '<div class="designation">' . esc_html($settings['memberDesignation']) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }

        if (!empty($settings['memberDescription'])) {
            echo '<div class="description">' . esc_html($settings['memberDescription']) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }

        if ($style === '1') {
            $this->render_social_icons($settings);
        }
        echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

        echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    private function render_image($settings, $image_size, $has_link)
    {
        $image_html = '';
        if (!empty($settings['memberImage']['id'])) {
            $image_html = wp_get_attachment_image($settings['memberImage']['id'], $image_size);
        } elseif (!empty($settings['memberImage']['url'])) {
            $image_html = '<img src="' . esc_url($settings['memberImage']['url']) . '" alt="">';
        }

        if (!$image_html) return;

        if ($has_link) {
            echo wp_kses_post('<a ' . $this->render_attributes('details-link') . ' aria-label="' . esc_attr($settings['memberName'] ?? '') . '">');
            echo wp_kses_post( $image_html );
            echo '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        } else {
            echo wp_kses_post( $image_html );
        }
    }

    private function render_name($settings, $tag, $has_link)
    {
        if (empty($settings['memberName'])) return;

        echo '<' . esc_html($tag) . ' class="name">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        if ($has_link) {
            echo wp_kses_post('<a ' . $this->render_attributes('details-link') . '>');
            echo esc_html($settings['memberName']);
            echo '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        } else {
            echo esc_html($settings['memberName']);
        }
        echo '</' . esc_html($tag) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    private function render_social_icons($settings)
    {
        if (empty($settings['showSocialIcons']) || empty($settings['socialIcons'])) return;

        echo '<div class="social-media">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        foreach ($settings['socialIcons'] as $index => $item) {
            if (empty($item['socialIcon'])) continue;

            $link_key = "social-link-{$index}";
            $has_social_link = ! empty( $item['socialLink']['type'] ) || ! empty( $item['socialLink']['url'] );
            if ($has_social_link) {
                $this->set_link_attributes($link_key, $item['socialLink']);
            } else {
                $this->set_attribute($link_key, 'href', '#');
            }

            echo wp_kses_post('<a ' . $this->render_attributes($link_key) . '>');
            echo '<span class="screen-reader-text">' . esc_html( $item['socialLabel'] ?? esc_html__( 'Social', 'bricksfly-elements-for-bricks' ) ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo wp_kses_post(self::render_icon($item['socialIcon'], ['aria-hidden' => 'true']));
            echo '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
        echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}
