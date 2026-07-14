<?php

namespace AABAddons\Admin\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class Logger extends WPImporterLoggerCLI {

	public $error_output = '';

	public function log( $level, $message, array $context = array() ) {
		$this->error_output( $level, $message, $context = array() );

		if ( $this->level_to_numeric( $level ) < $this->level_to_numeric( $this->min_level ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Literal format string; values escaped above.
		printf(
			'[%s] %s' . PHP_EOL,
			esc_html( strtoupper( $level ) ),
			esc_html( $message )
		);
	}

	public function error_output( $level, $message, array $context = array() ) {
		if ( $this->level_to_numeric( $level ) < $this->level_to_numeric( 'error' ) ) {
			return;
		}

		$this->error_output .= sprintf(
			'[%s] %s<br>',
			strtoupper( $level ),
			$message
		);
	}
}
