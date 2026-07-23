<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class AAB_Bricks_Post_Social_Share extends \Bricks\Element {

	public $category     = 'bricks fly';
	public $name         = 'aab-post-social-share';
	public $icon         = 'ti-sharethis aab-element-marker';
	public $css_selector = '.aab-social-share';
	public $scripts      = [ 'aabPostSocialShare' ];

	public function get_label() {
		return esc_html__('Social Share', 'the-bricksfly');
	}

	public function get_keywords() {
		return [ 'social', 'share', 'post', 'facebook', 'twitter' ];
	}

	public function enqueue_scripts() {
		wp_enqueue_style( 'bricks-font-awesome-6' );
		wp_enqueue_style( 'bricks-font-awesome-6-brands' );

		wp_enqueue_style(
			'aab-post-social-share',
			AAB_ADDONS_URL . 'public/build/elements/post-social-share.css',
			[],
			'1.0.0'
		);

		wp_enqueue_script(
			'aab-post-social-share',
			AAB_ADDONS_URL . 'public/build/elements/post-social-share.js',
			[],
			'1.0.0',
			true
		);
	}

	public function set_control_groups() {
		$this->control_groups['content'] = [
			'title' => esc_html__('Content', 'the-bricksfly'),
			'tab'   => 'content',
		];

		$this->control_groups['layout_style'] = [
			'title' => esc_html__('Layout', 'the-bricksfly'),
			'tab'   => 'style',
		];

		$this->control_groups['item_style'] = [
			'title' => esc_html__('Item', 'the-bricksfly'),
			'tab'   => 'style',
		];

		$this->control_groups['icon_style'] = [
			'title' => esc_html__('Icon', 'the-bricksfly'),
			'tab'   => 'style',
		];

		$this->control_groups['separator_style'] = [
			'title' => esc_html__('Separator', 'the-bricksfly'),
			'tab'   => 'style',
		];
	}

	public function set_controls() {

		// --- Content ---

		$this->controls['shareStyle'] = [
			'tab'     => 'content',
			'group'   => 'content',
			'label'   => esc_html__( 'Style', 'the-bricksfly' ),
			'type'    => 'select',
			'inline'  => true,
			'options' => [
				'default' => esc_html__( 'One', 'the-bricksfly' ),
				'style-1' => esc_html__( 'Two', 'the-bricksfly' ),
				'style-2' => esc_html__( 'Three', 'the-bricksfly' ),
			],
			'default' => 'default',
		];

		$this->controls['showTitle'] = [
			'tab'     => 'content',
			'group'   => 'content',
			'label'   => esc_html__( 'Show Title', 'the-bricksfly' ),
			'type'    => 'checkbox',
			'default' => true,
		];

		$this->controls['showShareIcon'] = [
			'tab'   => 'content',
			'group' => 'content',
			'label' => esc_html__( 'Show Share Icon', 'the-bricksfly' ),
			'type'  => 'checkbox',
		];

		$this->controls['shareText'] = [
			'tab'      => 'content',
			'group'    => 'content',
			'label'    => esc_html__( 'Share Text', 'the-bricksfly' ),
			'type'     => 'text',
			'default'  => esc_html__( 'Share', 'the-bricksfly' ),
			'required' => [ 'showShareIcon', '!=', '' ],
		];

		$this->controls['shareIcon'] = [
			'tab'      => 'content',
			'group'    => 'content',
			'label'    => esc_html__( 'Share Icon', 'the-bricksfly' ),
			'type'     => 'icon',
			'default'  => [
				'library' => 'fontawesome',
				'icon'    => 'fas fa-share-nodes',
			],
		];

		$this->controls['showShareCount'] = [
			'tab'   => 'content',
			'group' => 'content',
			'label' => esc_html__( 'Show Share Count', 'the-bricksfly' ),
			'type'  => 'checkbox',
		];

		$this->controls['showSeparator'] = [
			'tab'   => 'content',
			'group' => 'content',
			'label' => esc_html__( 'Show Separator', 'the-bricksfly' ),
			'type'  => 'checkbox',
		];

		$this->controls['separatorIcon'] = [
			'tab'      => 'content',
			'group'    => 'content',
			'label'    => esc_html__( 'Separator Icon', 'the-bricksfly' ),
			'type'     => 'icon',
			'required' => [ 'showSeparator', '!=', '' ],
		];

		// Repeater
		$this->controls['shareItems'] = [
			'tab'           => 'content',
			'group'         => 'content',
			'label'         => esc_html__( 'Social Share', 'the-bricksfly' ),
			'type'          => 'repeater',
			'titleProperty' => 'title',
			'fields'        => [
				'title' => [
					'label'   => esc_html__( 'Title', 'the-bricksfly' ),
					'type'    => 'text',
					'default' => esc_html__( 'Facebook', 'the-bricksfly' ),
				],
				'vendor' => [
					'label'   => esc_html__( 'Vendor', 'the-bricksfly' ),
					'type'    => 'select',
					'options' => [
						'facebook'  => esc_html__( 'Facebook', 'the-bricksfly' ),
						'twitter'   => esc_html__( 'Twitter', 'the-bricksfly' ),
						'linkedin'  => esc_html__( 'LinkedIn', 'the-bricksfly' ),
						'pinterest' => esc_html__( 'Pinterest', 'the-bricksfly' ),
						'tumblr'    => esc_html__( 'Tumblr', 'the-bricksfly' ),
						'blogger'   => esc_html__( 'Blogger', 'the-bricksfly' ),
						'reddit'    => esc_html__( 'Reddit', 'the-bricksfly' ),
					],
					'default' => 'facebook',
				],
				'icon' => [
					'label' => esc_html__( 'Icon', 'the-bricksfly' ),
					'type'  => 'icon',
				],
			],
			'default' => [
				[
					'title'  => 'Facebook',
					'vendor' => 'facebook',
					'icon'   => [ 'library' => 'fontawesome', 'icon' => 'fab fa-facebook-f' ],
				],
				[
					'title'  => 'Twitter',
					'vendor' => 'twitter',
					'icon'   => [ 'library' => 'fontawesome', 'icon' => 'fab fa-twitter' ],
				],
				[
					'title'  => 'LinkedIn',
					'vendor' => 'linkedin',
					'icon'   => [ 'library' => 'fontawesome', 'icon' => 'fab fa-linkedin-in' ],
				],
			],
		];

		// --- Style: Layout ---

		$this->controls['layoutDirection'] = [
			'tab'     => 'style',
			'group'   => 'layout_style',
			'label'   => esc_html__( 'Direction', 'the-bricksfly' ),
			'type'    => 'direction',
			'css'     => [
				[
					'property' => 'flex-direction',
					'selector' => '.aab-share-list',
				],
			],
		];

		$this->controls['layoutJustify'] = [
			'tab'   => 'style',
			'group' => 'layout_style',
			'label' => esc_html__( 'Justify Content', 'the-bricksfly' ),
			'type'  => 'justify-content',
			'css'   => [
				[
					'property' => 'justify-content',
					'selector' => '.aab-share-list',
				],
			],
		];

		$this->controls['layoutAlign'] = [
			'tab'   => 'style',
			'group' => 'layout_style',
			'label' => esc_html__( 'Align Items', 'the-bricksfly' ),
			'type'  => 'align-items',
			'css'   => [
				[
					'property' => 'align-items',
					'selector' => '.aab-share-list',
				],
			],
		];

		$this->controls['layoutGap'] = [
			'tab'   => 'style',
			'group' => 'layout_style',
			'label' => esc_html__( 'Gap', 'the-bricksfly' ),
			'type'  => 'number',
			'units' => [
				'px' => [ 'min' => 0, 'max' => 100, 'step' => 5 ],
			],
			'css' => [
				[
					'property' => 'gap',
					'selector' => '.aab-share-list',
				],
			],
		];

		$this->controls['layoutWrap'] = [
			'tab'     => 'style',
			'group'   => 'layout_style',
			'label'   => esc_html__( 'Wrap', 'the-bricksfly' ),
			'type'    => 'select',
			'inline'  => true,
			'options' => [
				'nowrap' => esc_html__( 'No Wrap', 'the-bricksfly' ),
				'wrap'   => esc_html__( 'Wrap', 'the-bricksfly' ),
			],
			'css' => [
				[
					'property' => 'flex-wrap',
					'selector' => '.aab-share-list',
				],
			],
		];

		// --- Style: Item ---

		$this->controls['itemTextColor'] = [
			'tab'   => 'style',
			'group' => 'item_style',
			'label' => esc_html__( 'Text Color', 'the-bricksfly' ),
			'type'  => 'color',
			'css'   => [
				[
					'property' => 'color',
					'selector' => '.aab-share-list a .info-s-title',
				],
				[
					'property' => 'color',
					'selector' => '.aab-share-list a .aab-share-count',
				],
			],
		];

		$this->controls['itemTypography'] = [
			'tab'   => 'style',
			'group' => 'item_style',
			'label' => esc_html__( 'Typography', 'the-bricksfly' ),
			'type'  => 'typography',
			'css'   => [
				[
					'property' => 'font',
					'selector' => '.aab-share-list a .info-s-title, .aab-share-list a .aab-share-count',
				],
			],
		];

		$this->controls['itemBackground'] = [
			'tab'   => 'style',
			'group' => 'item_style',
			'label' => esc_html__( 'Background', 'the-bricksfly' ),
			'type'  => 'background',
			'css'   => [
				[
					'property' => 'background',
					'selector' => '.aab-share-list a',
				],
			],
		];

		$this->controls['itemBorder'] = [
			'tab'   => 'style',
			'group' => 'item_style',
			'label' => esc_html__( 'Border', 'the-bricksfly' ),
			'type'  => 'border',
			'css'   => [
				[
					'property' => 'border',
					'selector' => '.aab-share-list a',
				],
			],
		];

		$this->controls['itemPadding'] = [
			'tab'   => 'style',
			'group' => 'item_style',
			'label' => esc_html__( 'Padding', 'the-bricksfly' ),
			'type'  => 'dimensions',
			'css'   => [
				[
					'property' => 'padding',
					'selector' => '.aab-share-list a',
				],
			],
		];

		$this->controls['itemWidth'] = [
			'tab'   => 'style',
			'group' => 'item_style',
			'label' => esc_html__( 'Width', 'the-bricksfly' ),
			'type'  => 'number',
			'units' => [
				'px' => [ 'min' => 0, 'max' => 200, 'step' => 5 ],
			],
			'css' => [
				[
					'property' => 'width',
					'selector' => '.aab-share-list a',
				],
			],
		];

		$this->controls['itemHeight'] = [
			'tab'   => 'style',
			'group' => 'item_style',
			'label' => esc_html__( 'Height', 'the-bricksfly' ),
			'type'  => 'number',
			'units' => [
				'px' => [ 'min' => 0, 'max' => 200, 'step' => 5 ],
			],
			'css' => [
				[
					'property' => 'height',
					'selector' => '.aab-share-list a',
				],
			],
		];

		$this->controls['itemDirection'] = [
			'tab'   => 'style',
			'group' => 'item_style',
			'label' => esc_html__( 'Inner Direction', 'the-bricksfly' ),
			'type'  => 'direction',
			'css'   => [
				[
					'property' => 'flex-direction',
					'selector' => '.aab-share-list a',
				],
			],
		];

		$this->controls['itemJustify'] = [
			'tab'   => 'style',
			'group' => 'item_style',
			'label' => esc_html__( 'Justify Content', 'the-bricksfly' ),
			'type'  => 'justify-content',
			'css'   => [
				[
					'property' => 'justify-content',
					'selector' => '.aab-share-list a',
				],
			],
		];

		$this->controls['itemAlignItems'] = [
			'tab'   => 'style',
			'group' => 'item_style',
			'label' => esc_html__( 'Align Items', 'the-bricksfly' ),
			'type'  => 'align-items',
			'css'   => [
				[
					'property' => 'align-items',
					'selector' => '.aab-share-list a',
				],
			],
		];

		$this->controls['itemInnerGap'] = [
			'tab'   => 'style',
			'group' => 'item_style',
			'label' => esc_html__( 'Inner Gap', 'the-bricksfly' ),
			'type'  => 'number',
			'units' => [
				'px' => [ 'min' => 0, 'max' => 100, 'step' => 5 ],
			],
			'css' => [
				[
					'property' => 'gap',
					'selector' => '.aab-share-list a',
				],
			],
		];

		// Hover
		$this->controls['itemHoverTextColor'] = [
			'tab'   => 'style',
			'group' => 'item_style',
			'label' => esc_html__( 'Hover Text Color', 'the-bricksfly' ),
			'type'  => 'color',
			'css'   => [
				[
					'property' => 'color',
					'selector' => '.aab-share-list a:hover .info-s-title',
				],
			],
		];

		$this->controls['itemHoverIconColor'] = [
			'tab'   => 'style',
			'group' => 'item_style',
			'label' => esc_html__( 'Hover Icon Color', 'the-bricksfly' ),
			'type'  => 'color',
			'css'   => [
				[
					'property' => 'color',
					'selector' => '.aab-share-list a:hover i',
				],
				[
					'property' => 'fill',
					'selector' => '.aab-share-list a:hover svg',
				],
			],
		];

		$this->controls['itemHoverBg'] = [
			'tab'   => 'style',
			'group' => 'item_style',
			'label' => esc_html__( 'Hover Background', 'the-bricksfly' ),
			'type'  => 'background',
			'css'   => [
				[
					'property' => 'background',
					'selector' => '.aab-share-list a:hover',
				],
			],
		];

		// --- Style: Icon ---

		$this->controls['iconSize'] = [
			'tab'   => 'style',
			'group' => 'icon_style',
			'label' => esc_html__( 'Icon Size', 'the-bricksfly' ),
			'type'  => 'number',
			'units' => [
				'px' => [ 'min' => 0, 'max' => 200, 'step' => 5 ],
			],
			'css' => [
				[
					'property' => 'font-size',
					'selector' => '.aab-share-list a i, .aab-share-list a svg',
				],
			],
		];

		$this->controls['iconColor'] = [
			'tab'   => 'style',
			'group' => 'icon_style',
			'label' => esc_html__( 'Color', 'the-bricksfly' ),
			'type'  => 'color',
			'css'   => [
				[
					'property' => 'color',
					'selector' => '.aab-share-list a i',
				],
				[
					'property' => 'fill',
					'selector' => '.aab-share-list a svg',
				],
			],
		];

		$this->controls['iconBgColor'] = [
			'tab'   => 'style',
			'group' => 'icon_style',
			'label' => esc_html__( 'Background', 'the-bricksfly' ),
			'type'  => 'color',
			'css'   => [
				[
					'property' => 'background',
					'selector' => '.aab-share-list a i, .aab-share-list a svg',
				],
			],
		];

		$this->controls['iconBorder'] = [
			'tab'   => 'style',
			'group' => 'icon_style',
			'label' => esc_html__( 'Border', 'the-bricksfly' ),
			'type'  => 'border',
			'css'   => [
				[
					'property' => 'border',
					'selector' => '.aab-share-list a i, .aab-share-list a svg',
				],
			],
		];

		$this->controls['iconPadding'] = [
			'tab'   => 'style',
			'group' => 'icon_style',
			'label' => esc_html__( 'Padding', 'the-bricksfly' ),
			'type'  => 'dimensions',
			'css'   => [
				[
					'property' => 'padding',
					'selector' => '.aab-share-list a i, .aab-share-list a svg',
				],
			],
		];

		$this->controls['iconHoverBgColor'] = [
			'tab'   => 'style',
			'group' => 'icon_style',
			'label' => esc_html__( 'Hover Background', 'the-bricksfly' ),
			'type'  => 'color',
			'css'   => [
				[
					'property' => 'background',
					'selector' => '.aab-share-list a:hover i, .aab-share-list a:hover svg',
				],
			],
		];

		$this->controls['iconHoverColor'] = [
			'tab'   => 'style',
			'group' => 'icon_style',
			'label' => esc_html__( 'Hover Color', 'the-bricksfly' ),
			'type'  => 'color',
			'css'   => [
				[
					'property' => 'color',
					'selector' => '.aab-share-list a:hover i',
				],
				[
					'property' => 'fill',
					'selector' => '.aab-share-list a:hover svg',
				],
			],
		];

		$this->controls['iconHoverBorderColor'] = [
			'tab'   => 'style',
			'group' => 'icon_style',
			'label' => esc_html__( 'Hover Border Color', 'the-bricksfly' ),
			'type'  => 'color',
			'css'   => [
				[
					'property' => 'border-color',
					'selector' => '.aab-share-list a:hover i, .aab-share-list a:hover svg',
				],
			],
		];

		// --- Style: Separator ---

		$this->controls['separatorSize'] = [
			'tab'      => 'style',
			'group'    => 'separator_style',
			'label'    => esc_html__( 'Icon Size', 'the-bricksfly' ),
			'type'     => 'number',
			'units'    => [
				'px' => [ 'min' => 0, 'max' => 200, 'step' => 5 ],
			],
			'css'      => [
				[
					'property' => 'font-size',
					'selector' => '.aab-separator-icon',
				],
			],
			'required' => [ 'showSeparator', '!=', '' ],
		];

		$this->controls['separatorColor'] = [
			'tab'      => 'style',
			'group'    => 'separator_style',
			'label'    => esc_html__( 'Color', 'the-bricksfly' ),
			'type'     => 'color',
			'css'      => [
				[
					'property' => 'color',
					'selector' => '.aab-separator-icon',
				],
				[
					'property' => 'fill',
					'selector' => '.aab-separator-icon',
				],
			],
			'required' => [ 'showSeparator', '!=', '' ],
		];
	}

	/**
	 * Generate share URL for a vendor
	 */
	private function get_share_url( $vendor ) {
		$permalink = get_the_permalink();
		$title     = get_the_title();

		switch ( $vendor ) {
			case 'facebook':
				return add_query_arg( [ 'u' => $permalink ], 'https://www.facebook.com/sharer/sharer.php' );

			case 'twitter':
				return add_query_arg( [ 'url' => $permalink, 'text' => $title ], 'https://twitter.com/intent/tweet' );

			case 'linkedin':
				return add_query_arg( [
					'url'     => $permalink,
					'mini'    => true,
					'title'   => $title,
					'summary' => $title,
					'source'  => $permalink,
				], 'https://www.linkedin.com/shareArticle' );

			case 'pinterest':
				return add_query_arg( [
					'media'       => get_the_post_thumbnail_url( get_the_ID(), 'full' ),
					'url'         => $permalink,
					'description' => $title,
				], 'https://pinterest.com/pin/create/button/' );

			case 'reddit':
				return add_query_arg( [ 'url' => $permalink, 'title' => $title ], 'https://www.reddit.com/submit' );

			case 'tumblr':
				return add_query_arg( [ 'url' => $permalink, 'name' => $title ], 'https://www.tumblr.com/share/link' );

			case 'blogger':
				return add_query_arg( [ 'u' => $permalink, 'n' => $title ], 'https://www.blogger.com/blog-this.g' );

			default:
				return '#';
		}
	}

	public function render() {
		$settings = $this->settings;
		$shares   = ! empty( $settings['shareItems'] ) ? $settings['shareItems'] : [];

		if ( empty( $shares ) ) {
			return $this->render_element_placeholder( [
				'icon-class' => 'ti-sharethis',
				'text'       => esc_html__( 'No share items added.', 'the-bricksfly' ),
			] );
		}

		$style          = ! empty( $settings['shareStyle'] ) ? $settings['shareStyle'] : 'default';
		$show_title     = ! empty( $settings['showTitle'] );
		$show_share_icon = ! empty( $settings['showShareIcon'] );
		$share_text     = ! empty( $settings['shareText'] ) ? $settings['shareText'] : '';
		$show_count     = ! empty( $settings['showShareCount'] );
		$show_separator = ! empty( $settings['showSeparator'] );

		$post_id        = get_the_ID();
		$current_shares = $show_count ? get_post_meta( $post_id, 'aab_post_shares', true ) : [];

		$this->set_attribute( '_root', 'class', [ 'aab-social-share', 'aab-share-' . $style ] );

		echo wp_kses_post('<div ' . $this->render_attributes( '_root' ) . '>');
		echo '<ul class="aab-share-list">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		foreach ( $shares as $index => $share ) {
			$vendor = ! empty( $share['vendor'] ) ? $share['vendor'] : '';
			if ( empty( $vendor ) ) {
				continue;
			}

			$url = $this->get_share_url( $vendor );

			echo '<li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '<a href="' . esc_url( $url ) . '" data-type="' . esc_attr( $vendor ) . '" target="_blank" rel="nofollow noopener">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			// Icon wrapper (for style-1 / style-2)
			if ( 'default' !== $style && ! empty( $share['icon'] ) ) {
				echo '<span class="aab-share-icn">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo wp_kses_post(self::render_icon( $share['icon'], [ 'aria-hidden' => 'true' ] ));
				echo '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} elseif ( ! empty( $share['icon'] ) ) {
				echo wp_kses_post(self::render_icon( $share['icon'], [ 'aria-hidden' => 'true' ] ));
			}

			// Title
			if ( $show_title && ! empty( $share['title'] ) ) {
				echo '<span class="info-s-title">' . esc_html( $share['title'] ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			// Share icon + text
			if ( $show_share_icon ) {
				echo '<span class="aab-share-action">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				if ( ! empty( $settings['shareIcon'] ) ) {
					echo wp_kses_post(self::render_icon( $settings['shareIcon'], [ 'aria-hidden' => 'true' ] ));
				}
				if ( $share_text ) {
					echo ' ' . esc_html( $share_text ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				echo '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			// Separator
			if ( $show_separator && ! empty( $settings['separatorIcon'] ) ) {
				echo '<span class="aab-separator-icon">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo wp_kses_post(self::render_icon( $settings['separatorIcon'], [ 'aria-hidden' => 'true' ] ));
				echo '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			// Share count
			if ( $show_count ) {
				$count = is_array( $current_shares ) && isset( $current_shares[ $vendor ] ) ? intval( $current_shares[ $vendor ] ) : 0;
				echo '<span data-type="' . esc_attr( $vendor ) . '" class="aab-share-count">' . esc_html( $count ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			echo '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '</ul>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
