<?php
/**
 * Admin notices: registration, display, snooze and dismiss.
 *
 * @package Bricks_Fly
 */

namespace wealcoder\bricksfly\Admin\Notices;

defined( 'ABSPATH' ) || exit();

/**
 * Registers BricksFly admin notices and handles their snooze / dismiss requests.
 */
class Notices {

	/**
	 * Single instance.
	 *
	 * @var Notices|null
	 */
	private static $instance = null;

	/**
	 * Single instance of the class.
	 *
	 * @return Notices
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Registered notices, keyed by notice ID.
	 *
	 * @var array
	 */
	protected $notices = array();

	/**
	 * Hook into WordPress.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'add_admin_notices' ) );

		add_action( 'wp_ajax_bricksfly_notice_dismiss_notice', array( $this, 'ajax_dismiss_notice' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Register the notices for this request. Runs on admin-ajax.php as well, so
	 * snooze / dismiss requests can find the notice they refer to.
	 */
	public function add_admin_notices() {
		$this->add_campaign_notices();
	}

	/**
	 * Seasonal upgrade offers (image banners in assets/images/notices/).
	 * Each runs from the start of `start` to the end of `end`, site time.
	 * To add one, drop its 1100x150 @2x PNG in that folder and add an entry.
	 * Every campaign has its own notice ID, so dismissing one never hides the next.
	 *
	 * @return array[] Campaigns: id, start, end (Y-m-d), image, url, alt.
	 */
	protected function campaigns() {
		return array(
			array(
				'id'    => 'halloween_2026',
				'start' => '2026-10-01',
				'end'   => '2026-11-05',
				'image' => 'halloween-2026.png',
				'url'   => 'https://bricksfly.com/pricing/?utm_source=bricksfly-free&utm_medium=admin-notice&utm_campaign=halloween-2026',
				'alt'   => __( 'Halloween deal: upgrade to BricksFly Pro at 40 percent off. Offer ends November 5.', 'bricksfly-elements-for-bricks' ),
			),
		);
	}

	/**
	 * Show the running campaign to admins who do not have BricksFly Pro
	 * installed (active or not), on the Dashboard, Plugins and BricksFly
	 * screens only.
	 */
	protected function add_campaign_notices() {
		$now     = current_datetime();
		$running = array();
		foreach ( $this->campaigns() as $campaign ) {
			$start = new \DateTimeImmutable( $campaign['start'] . ' 00:00:00', wp_timezone() );
			$end   = new \DateTimeImmutable( $campaign['end'] . ' 00:00:00 +1 day', wp_timezone() );
			if ( $now >= $start && $now < $end ) {
				$running[] = $campaign;
			}
		}

		// Checked after the dates: finding Pro reads the installed plugins list.
		if ( empty( $running ) || bricksfly_is_pro_active() || bricksfly_is_pro_installed() ) {
			return;
		}

		foreach ( $running as $campaign ) {
			$this->add(
				array(
					'notice_id'   => 'bricksfly_notice_' . $campaign['id'],
					'message'     => __DIR__ . '/views/campaign.php',
					'dismissible' => false,
					'class'       => 'aab-promo-notice',
					'screens'     => array( 'dashboard', 'plugins', 'toplevel_page_bricksfly_addons_page', 'bricksfly_page_*' ),
					'campaign'    => $campaign,
				)
			);
		}
	}

	/**
	 * Close / snooze / "don't show again" buttons inside a notice. Loaded only
	 * on screens that show at least one notice.
	 */
	public function enqueue_assets() {
		$visible = array_filter( $this->notices, array( $this, 'should_display' ) );
		if ( empty( $visible ) ) {
			return;
		}

		wp_register_script( 'bricksfly-notices', false, array(), BRICKSFLY_VERSION, true );
		wp_enqueue_script( 'bricksfly-notices' );
		wp_add_inline_script(
			'bricksfly-notices',
			"document.addEventListener('click', function (event) {
				var button = event.target.closest('.aab-notice .aab-snooze-btn, .aab-notice .notice-dismiss-btn');
				if (!button) {
					return;
				}
				var notice = button.closest('.aab-notice');
				var body = new FormData();
				body.append('action', notice.dataset.action);
				body.append('nonce', notice.dataset.nonce);
				body.append('notice_id', notice.dataset.notice_id);
				if ('true' === button.dataset.snooze && button.dataset.snoozeTime) {
					body.append('snooze', 'true');
					body.append('snooze_time', button.dataset.snoozeTime);
				}
				notice.remove();
				fetch(window.ajaxurl, { method: 'POST', credentials: 'same-origin', body: body });
			});"
		);

		wp_register_style( 'bricksfly-notices', false, array(), BRICKSFLY_VERSION );
		wp_enqueue_style( 'bricksfly-notices' );
		wp_add_inline_style(
			'bricksfly-notices',
			'.notice.aab-promo-notice{border:0;background:none;box-shadow:none;padding:0;margin:15px 0}
			.aab-promo{position:relative;max-width:1100px}
			.aab-promo__link{display:block;border-radius:10px;overflow:hidden;line-height:0}
			.aab-promo__link img{display:block;width:100%;height:auto}
			.aab-promo__close{position:absolute;left:97.4%;top:17.2%;width:3.4%;min-width:24px;aspect-ratio:1;transform:translate(-50%,-50%);padding:0;border:0;border-radius:50%;background:transparent;cursor:pointer}
			.aab-promo__close:focus-visible{outline:2px solid #fcd34d;outline-offset:2px}
			.aab-promo__actions{margin:6px 0 0}
			.aab-promo__actions .button-link{color:#646970;font-size:12px}'
		);
	}

	/**
	 * AJAX: snooze or permanently dismiss a notice.
	 */
	public function ajax_dismiss_notice() {
		if ( ! check_ajax_referer( 'bricksfly_notice_dismiss_notice', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error();
			exit;
		}
		$notice_id   = isset( $_POST['notice_id'] ) ? sanitize_text_field( wp_unslash( $_POST['notice_id'] ) ) : '';
		$snooze      = isset( $_POST['snooze'] ) ? filter_var( wp_unslash( $_POST['snooze'] ), FILTER_VALIDATE_BOOLEAN ) : false;
		$snooze_time = isset( $_POST['snooze_time'] ) ? absint( wp_unslash( $_POST['snooze_time'] ) ) : 7 * DAY_IN_SECONDS;
		$notice      = array_key_exists( $notice_id, $this->notices ) ? $this->notices[ $notice_id ] : null;
		if ( ! is_null( $notice ) ) {
			if ( $snooze ) {
				$this->snooze( $notice_id, $snooze_time );
			} else {
				$this->dismiss( $notice_id );
			}
			wp_send_json_success();
			exit;
		}
		wp_send_json_error();
		exit;
	}

	/**
	 * Print the notices that apply to the current user and screen.
	 */
	public function admin_notices() {
		if ( empty( $this->notices ) ) {
			return;
		}

		foreach ( $this->notices as $notice ) {
			if ( $this->should_display( $notice ) ) {
				$classes = array_unique( array_filter( wp_parse_list( $notice['class'] ) ) );
				$style   = ! empty( $notice['style'] ) ? $notice['style'] : '';
				$message = $notice['message'];
				if ( '.php' === substr( $message, -4 ) ) {
					$path = wp_normalize_path( $message );
					if ( file_exists( $path ) ) {
						ob_start();
						include $path;
						$message = ob_get_clean();
					}
				}
				if ( $notice['dismissible'] ) {
					$classes[] = 'is-dismissible';
				}
				if ( empty( $message ) ) {
					continue;
				}
				if ( ! preg_match( '/<[^>]+>/', $message ) ) {
					$message = wpautop( $message );
				}

				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Literal format string; values escaped per-arg below.
				printf(
					'<div class="notice aab-notice notice-%1$s %2$s" data-notice_id="%3$s" data-nonce="%4$s" data-action="%5$s" style="%6$s">%7$s%8$s</div>',
					esc_attr( $notice['type'] ),
					esc_attr( implode( ' ', $classes ) ),
					esc_attr( $notice['notice_id'] ),
					esc_attr( wp_create_nonce( 'bricksfly_notice_dismiss_notice' ) ),
					esc_attr( 'bricksfly_notice_dismiss_notice' ),
					esc_attr( $style ),
					wp_kses_post( wptexturize( $message ) ),
					$notice['dismissible'] ? '<button type="button" class="notice-dismiss"><span class="screen-reader-text">' . esc_html__( 'Dismiss this notice', 'bricksfly-elements-for-bricks' ) . '</span></button>' : ''
				);
			}
		}
	}

	/**
	 * Register a notice.
	 *
	 * @param array|string $args {
	 *     Notice arguments, or just the message.
	 *
	 *     @type string   $message     HTML, plain text, or the path of a .php view.
	 *     @type string   $type        info | success | warning | error.
	 *     @type bool     $dismissible Add WordPress's own dismiss button.
	 *     @type string   $capability  Capability required to see the notice.
	 *     @type string   $notice_id   Unique ID; also the option / transient name used to hide it.
	 *     @type string   $class       Extra CSS classes.
	 *     @type string   $style       Inline style for the wrapper.
	 *     @type string[] $screens     Screen IDs to show on; a trailing * matches a prefix. Empty = every screen.
	 * }
	 */
	public function add( $args ) {
		if ( is_string( $args ) ) {
			$args = array( 'message' => $args );
		}
		$args = wp_parse_args(
			$args,
			array(
				'message'     => '',
				'type'        => 'info',
				'dismissible' => true,
				'capability'  => 'manage_options',
				'notice_id'   => '',
				'class'       => '',
				'style'       => '',
				'screens'     => array(),
			)
		);
		if ( empty( $args['message'] ) ) {
			return;
		}
		if ( empty( $args['notice_id'] ) ) {
			$args['notice_id'] = 'bricksfly_notice_' . md5( $args['message'] . $args['type'] );
		}
		if ( true === filter_var( $args['dismissible'], FILTER_VALIDATE_BOOLEAN ) && $this->is_dismissed( $args['notice_id'] ) ) {
			return;
		}
		$this->notices[ $args['notice_id'] ] = $args;
	}

	/**
	 * Registered notices.
	 *
	 * @return array
	 */
	public function get_notices() {
		return $this->notices;
	}

	/**
	 * Whether a notice is dismissed or currently snoozed.
	 *
	 * @param string $id Notice ID.
	 * @return bool
	 */
	public function is_dismissed( $id ) {
		// get_transient(), not the raw option, so snoozes also work with a persistent object cache.
		if ( 'yes' === get_option( $id ) || 'yes' === get_transient( $id ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Whether a notice should show for the current user and screen.
	 *
	 * @param array $notice Notice arguments.
	 * @return bool
	 */
	public function should_display( $notice ) {
		if ( ( $notice['notice_id'] && $this->is_dismissed( $notice['notice_id'] ) ) || ( $notice['capability'] && ! current_user_can( $notice['capability'] ) ) ) {
			return false;
		}
		if ( ! empty( $notice['screens'] ) && ! $this->is_screen( $notice['screens'] ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Whether the current admin screen is one of the given screen IDs.
	 *
	 * @param string[] $screens Screen IDs; a trailing * matches a prefix.
	 * @return bool
	 */
	protected function is_screen( $screens ) {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen ) {
			return false;
		}
		foreach ( (array) $screens as $id ) {
			if ( $id === $screen->id || ( '*' === substr( $id, -1 ) && 0 === strpos( $screen->id, substr( $id, 0, -1 ) ) ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Hide a notice for good.
	 *
	 * @param string $id Notice ID.
	 */
	public function dismiss( $id ) {
		update_option( $id, 'yes', false );
		delete_transient( $id );
	}

	/**
	 * Hide a notice for a while.
	 *
	 * @param string $id   Notice ID.
	 * @param int    $time Seconds to hide it for.
	 */
	public function snooze( $id, $time = WEEK_IN_SECONDS ) {
		set_transient( $id, 'yes', absint( $time ) );
	}
}

Notices::instance();
