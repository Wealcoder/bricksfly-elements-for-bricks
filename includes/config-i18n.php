<?php

/**
 * Translatable strings extracted from config.php.
 *
 * config.php loads at plugin bootstrap (before WP's `init` hook), so it
 * cannot wrap label/title/description values with `__()` directly without
 * triggering "translation loaded too early" notices in WP 6.7+.
 *
 * This file mirrors every translatable string from config.php as a literal
 * `__()` call so .pot extraction tools (WP-CLI i18n make-pot) pick them up.
 * The actual runtime translation happens via `aab_translate_config_tree()`
 * in includes/helper.php, hooked to the `wcf_addons_dashboard_config` filter.
 *
 * Loaded on `init` so translations resolve correctly. The function calls
 * here are no-ops at runtime — their only purpose is .pot extraction.
 *
 * Keep this file in sync with config.php whenever a label, title, or
 * description string is added, removed, or changed.
 */

defined('ABSPATH') || exit;

add_action('init', function () {
	__('Add floating items to your page, such as buttons or icons, that stay in view as users scroll', 'the-bricksfly');
	__('Add stunning movement to your headlines and capture attention in just a second.', 'the-bricksfly');
	__('Advanced Accordion', 'the-bricksfly');
	__('Advanced Button', 'the-bricksfly');
	__('Advanced Button Pro', 'the-bricksfly');
	__('Advanced Button Pro widget with enhanced styling and animation options to create eye-catching call-to-action buttons that drive engagement and conversions on your website.', 'the-bricksfly');
	__('Advanced Tooltip', 'the-bricksfly');
	__('Advanced Widgets', 'the-bricksfly');
	__('Animated Heading', 'the-bricksfly');
	__('Animated Off-Canvas', 'the-bricksfly');
	__('Animation', 'the-bricksfly');
	__('Animations', 'the-bricksfly');
	__('Boost credibility and visibility by highlighting trusted logos with a smooth, auto-scrolling slider.', 'the-bricksfly');
	__('Brand Slider', 'the-bricksfly');
	__('Bricksfly Pro', 'the-bricksfly');
	__('Captivate your audience with stunning off-canvas reveals that keep users engaged longer on site!', 'the-bricksfly');
	__('Classic Testimonial', 'the-bricksfly');
	__('Counter', 'the-bricksfly');
	__('Cursor', 'the-bricksfly');
	__('Cursor Hover Effect', 'the-bricksfly');
	__('Cursor Move Effect', 'the-bricksfly');
	__('Customize social share icons to match your design and encourage content sharing with ease.', 'the-bricksfly');
	__('Customize the featured image of any post for a perfect fit across all device screens.', 'the-bricksfly');
	__('Draggable', 'the-bricksfly');
	__('DrawSVG', 'the-bricksfly');
	__('Dynamic Widgets', 'the-bricksfly');
	__('Easel', 'the-bricksfly');
	__('Effects', 'the-bricksfly');
	__('Elevate your website’s look by incorporating a video box that blends seamlessly with your style.', 'the-bricksfly');
	__('Flip', 'the-bricksfly');
	__('Floating Elements', 'the-bricksfly');
	__('Form Widgets', 'the-bricksfly');
	__('GSAP Extensions', 'the-bricksfly');
	__('GSAP Library', 'the-bricksfly');
	__('GSDevTools', 'the-bricksfly');
	__('General Extensions', 'the-bricksfly');
	__('General Settings', 'the-bricksfly');
	__('General Widgets', 'the-bricksfly');
	__('Header & Footer Widgets', 'the-bricksfly');
	__('Help visitors meet your team with photos, job titles, and clickable social media icons.', 'the-bricksfly');
	__('Highlight customer feedback with modern layouts, smooth sliders, and customizable design elements.', 'the-bricksfly');
	__('Horizontal', 'the-bricksfly');
	__('Icon Box', 'the-bricksfly');
	__('Image Accordion', 'the-bricksfly');
	__('Image Animation', 'the-bricksfly');
	__('Image Reveal on Hover', 'the-bricksfly');
	__('Impress visitors with live stats and milestones using fully animated number counters.', 'the-bricksfly');
	__('Inertia', 'the-bricksfly');
	__('Keep your pages clean and visitors happy with smart, collapsible advanced accordion designs.', 'the-bricksfly');
	__('Library', 'the-bricksfly');
	__('Link your social accounts and customize the look to perfectly match your branding.', 'the-bricksfly');
	__('Modern Testimonial', 'the-bricksfly');
	__('MorphSVG', 'the-bricksfly');
	__('MotionPath', 'the-bricksfly');
	__('MotionPathHelper', 'the-bricksfly');
	__('Observer', 'the-bricksfly');
	__('Parallax Effect', 'the-bricksfly');
	__('Physics2D', 'the-bricksfly');
	__('PhysicsProps', 'the-bricksfly');
	__('Pixi', 'the-bricksfly');
	__('Plugins', 'the-bricksfly');
	__('Post Featured Image', 'the-bricksfly');
	__('Preloader', 'the-bricksfly');
	__('Present images creatively with collapsible sections, enhancing user experience and saving screen space.', 'the-bricksfly');
	__('Refine your content presentation with flexible icon styling, typography, and layout options.', 'the-bricksfly');
	__('ScrambleText', 'the-bricksfly');
	__('Scroll Indicator', 'the-bricksfly');
	__('Scroll Smoother', 'the-bricksfly');
	__('Scroll To Top', 'the-bricksfly');
	__('ScrollSmoother', 'the-bricksfly');
	__('ScrollTo', 'the-bricksfly');
	__('ScrollTrigger', 'the-bricksfly');
	__('Share engaging video content with smooth transitions to tell your story effectively.', 'the-bricksfly');
	__('Share real stories and success quotes using animated testimonial sliders and flexible content settings.', 'the-bricksfly');
	__('Showcase real stories and client praise with a sleek, responsive testimonial carousel.', 'the-bricksfly');
	__('Site Settings', 'the-bricksfly');
	__('Slider', 'the-bricksfly');
	__('Social Icons', 'the-bricksfly');
	__('Social Share', 'the-bricksfly');
	__('SplitText', 'the-bricksfly');
	__('Sticky/Pin Element', 'the-bricksfly');
	__('Team', 'the-bricksfly');
	__('Testimonial', 'the-bricksfly');
	__('Text', 'the-bricksfly');
	__('Text Animation', 'the-bricksfly');
	__('Tilt Effect', 'the-bricksfly');
	__('Timeline', 'the-bricksfly');
	__('Use animated timelines to present events or project stages in a clear, organized progression.', 'the-bricksfly');
	__('Use the Video Mask Widget to bring artistic, interactive flair to your sites videos.', 'the-bricksfly');
	__('Use the Video Popup Widget to display videos in popups, making content more engaging.', 'the-bricksfly');
	__('Use the Youtube Video Widget to bring artistic, interactive flair to your sites videos.', 'the-bricksfly');
	__('Video Box', 'the-bricksfly');
	__('Video Mask', 'the-bricksfly');
	__('Video Popup', 'the-bricksfly');
	__('Video Story', 'the-bricksfly');
	__('Video Widgets', 'the-bricksfly');
	__('Wrapper Link', 'the-bricksfly');
	__('Youtube Video', 'the-bricksfly');
}, 0);
