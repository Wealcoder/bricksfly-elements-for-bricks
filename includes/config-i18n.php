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
	__('Add floating items to your page, such as buttons or icons, that stay in view as users scroll', 'bricksfly');
	__('Add stunning movement to your headlines and capture attention in just a second.', 'bricksfly');
	__('Advanced Accordion', 'bricksfly');
	__('Advanced Button', 'bricksfly');
	__('Advanced Button Pro', 'bricksfly');
	__('Advanced Button Pro widget with enhanced styling and animation options to create eye-catching call-to-action buttons that drive engagement and conversions on your website.', 'bricksfly');
	__('Advanced Tooltip', 'bricksfly');
	__('Advanced Widgets', 'bricksfly');
	__('Animated Heading', 'bricksfly');
	__('Animated Off-Canvas', 'bricksfly');
	__('Animation', 'bricksfly');
	__('Animations', 'bricksfly');
	__('Boost credibility and visibility by highlighting trusted logos with a smooth, auto-scrolling slider.', 'bricksfly');
	__('Brand Slider', 'bricksfly');
	__('Bricksfly Pro', 'bricksfly');
	__('Captivate your audience with stunning off-canvas reveals that keep users engaged longer on site!', 'bricksfly');
	__('Classic Testimonial', 'bricksfly');
	__('Counter', 'bricksfly');
	__('Cursor', 'bricksfly');
	__('Cursor Hover Effect', 'bricksfly');
	__('Cursor Move Effect', 'bricksfly');
	__('Customize social share icons to match your design and encourage content sharing with ease.', 'bricksfly');
	__('Customize the featured image of any post for a perfect fit across all device screens.', 'bricksfly');
	__('Draggable', 'bricksfly');
	__('DrawSVG', 'bricksfly');
	__('Dynamic Widgets', 'bricksfly');
	__('Easel', 'bricksfly');
	__('Effects', 'bricksfly');
	__('Elevate your website’s look by incorporating a video box that blends seamlessly with your style.', 'bricksfly');
	__('Flip', 'bricksfly');
	__('Floating Elements', 'bricksfly');
	__('Form Widgets', 'bricksfly');
	__('GSAP Extensions', 'bricksfly');
	__('GSAP Library', 'bricksfly');
	__('GSDevTools', 'bricksfly');
	__('General Extensions', 'bricksfly');
	__('General Settings', 'bricksfly');
	__('General Widgets', 'bricksfly');
	__('Header & Footer Widgets', 'bricksfly');
	__('Help visitors meet your team with photos, job titles, and clickable social media icons.', 'bricksfly');
	__('Highlight customer feedback with modern layouts, smooth sliders, and customizable design elements.', 'bricksfly');
	__('Horizontal', 'bricksfly');
	__('Icon Box', 'bricksfly');
	__('Image Accordion', 'bricksfly');
	__('Image Animation', 'bricksfly');
	__('Image Reveal on Hover', 'bricksfly');
	__('Impress visitors with live stats and milestones using fully animated number counters.', 'bricksfly');
	__('Inertia', 'bricksfly');
	__('Keep your pages clean and visitors happy with smart, collapsible advanced accordion designs.', 'bricksfly');
	__('Library', 'bricksfly');
	__('Link your social accounts and customize the look to perfectly match your branding.', 'bricksfly');
	__('Modern Testimonial', 'bricksfly');
	__('MorphSVG', 'bricksfly');
	__('MotionPath', 'bricksfly');
	__('MotionPathHelper', 'bricksfly');
	__('Observer', 'bricksfly');
	__('Parallax Effect', 'bricksfly');
	__('Physics2D', 'bricksfly');
	__('PhysicsProps', 'bricksfly');
	__('Pixi', 'bricksfly');
	__('Plugins', 'bricksfly');
	__('Post Featured Image', 'bricksfly');
	__('Preloader', 'bricksfly');
	__('Present images creatively with collapsible sections, enhancing user experience and saving screen space.', 'bricksfly');
	__('Refine your content presentation with flexible icon styling, typography, and layout options.', 'bricksfly');
	__('ScrambleText', 'bricksfly');
	__('Scroll Indicator', 'bricksfly');
	__('Scroll Smoother', 'bricksfly');
	__('Scroll To Top', 'bricksfly');
	__('ScrollSmoother', 'bricksfly');
	__('ScrollTo', 'bricksfly');
	__('ScrollTrigger', 'bricksfly');
	__('Share engaging video content with smooth transitions to tell your story effectively.', 'bricksfly');
	__('Share real stories and success quotes using animated testimonial sliders and flexible content settings.', 'bricksfly');
	__('Showcase real stories and client praise with a sleek, responsive testimonial carousel.', 'bricksfly');
	__('Site Settings', 'bricksfly');
	__('Slider', 'bricksfly');
	__('Social Icons', 'bricksfly');
	__('Social Share', 'bricksfly');
	__('SplitText', 'bricksfly');
	__('Sticky/Pin Element', 'bricksfly');
	__('Team', 'bricksfly');
	__('Testimonial', 'bricksfly');
	__('Text', 'bricksfly');
	__('Text Animation', 'bricksfly');
	__('Tilt Effect', 'bricksfly');
	__('Timeline', 'bricksfly');
	__('Use animated timelines to present events or project stages in a clear, organized progression.', 'bricksfly');
	__('Use the Video Mask Widget to bring artistic, interactive flair to your sites videos.', 'bricksfly');
	__('Use the Video Popup Widget to display videos in popups, making content more engaging.', 'bricksfly');
	__('Use the Youtube Video Widget to bring artistic, interactive flair to your sites videos.', 'bricksfly');
	__('Video Box', 'bricksfly');
	__('Video Mask', 'bricksfly');
	__('Video Popup', 'bricksfly');
	__('Video Story', 'bricksfly');
	__('Video Widgets', 'bricksfly');
	__('Wrapper Link', 'bricksfly');
	__('Youtube Video', 'bricksfly');
}, 0);
