<?php
/**
 * TEMPORARY import diagnostic for the LIVE server.
 *
 * Visit (logged in as an admin):
 *   https://test.bricksfly.com/wp-admin/admin.php?page=bf_addons_settings&bft_import_diag=1
 * or any admin page with &bft_import_diag=1 appended.
 *
 * It runs, in order, every outbound + filesystem step the Template Importer
 * depends on, and prints exactly which one fails on this host. DELETE this file
 * (and the require line in the-bricksfly.php) once the issue is resolved.
 */

defined('ABSPATH') || exit;

add_action('admin_init', function () {
    if (empty($_GET['bft_import_diag']) || ! current_user_can('manage_options')) {
        return;
    }

    header('Content-Type: text/plain; charset=utf-8');

    $base = defined('AAB_TEMPLATE_STARTER_BASE_URL') ? AAB_TEMPLATE_STARTER_BASE_URL : 'https://www.themecrowdy.com/';
    $catalog  = $base . 'wp-json/wp/v2/brk-templates';
    $download = $base . 'wp-json/brk-starter-templates/download';

    $line = function ($label, $val) {
        echo str_pad($label, 34) . ': ' . $val . "\n";
    };

    echo "=== BricksFly import diagnostic ===\n\n";
    $line('PHP version', PHP_VERSION);
    $line('WP version', get_bloginfo('version'));
    $line('is_multisite', is_multisite() ? 'yes' : 'no');
    $line('blog id', (string) get_current_blog_id());
    $line('template base', $base);
    $line('WP_HTTP_BLOCK_EXTERNAL', defined('WP_HTTP_BLOCK_EXTERNAL') && WP_HTTP_BLOCK_EXTERNAL ? 'TRUE (external blocked!)' : 'not set');
    if (defined('WP_ACCESSIBLE_HOSTS')) {
        $line('WP_ACCESSIBLE_HOSTS', WP_ACCESSIBLE_HOSTS);
    }
    $line('curl available', function_exists('curl_version') ? curl_version()['version'] : 'NO');
    $line('allow_url_fopen', ini_get('allow_url_fopen') ? 'on' : 'off');
    echo "\n";

    // 1) DNS resolution
    $host = wp_parse_url($base, PHP_URL_HOST);
    $ip   = gethostbyname($host);
    $line('DNS ' . $host, $ip === $host ? 'FAILED to resolve' : $ip);
    echo "\n";

    // 2) Plain wp_remote_get to catalog (what the browser also calls)
    echo "--- 2) wp_remote_get catalog (timeout 30) ---\n";
    $r = wp_remote_get($catalog, array('timeout' => 30, 'sslverify' => true));
    if (is_wp_error($r)) {
        $line('  result', 'WP_Error: ' . $r->get_error_code() . ' - ' . $r->get_error_message());
    } else {
        $line('  http code', (string) wp_remote_retrieve_response_code($r));
        $line('  body bytes', (string) strlen((string) wp_remote_retrieve_body($r)));
    }
    echo "\n";

    // 2b) same but sslverify=false (mirrors validate_download_file)
    echo "--- 2b) wp_remote_get catalog sslverify=false ---\n";
    $r = wp_remote_get($catalog, array('timeout' => 30, 'sslverify' => false));
    if (is_wp_error($r)) {
        $line('  result', 'WP_Error: ' . $r->get_error_code() . ' - ' . $r->get_error_message());
    } else {
        $line('  http code', (string) wp_remote_retrieve_response_code($r));
    }
    echo "\n";

    // 3) wp_safe_remote_get (what Downloader uses) — URL validation differs
    echo "--- 3) wp_safe_remote_get catalog (Downloader path) ---\n";
    $r = wp_safe_remote_get($catalog, array('timeout' => 30));
    if (is_wp_error($r)) {
        $line('  result', 'WP_Error: ' . $r->get_error_code() . ' - ' . $r->get_error_message());
    } else {
        $line('  http code', (string) wp_remote_retrieve_response_code($r));
    }
    echo "\n";

    // 4) Uploads dir writability (Downloader streams the file here)
    echo "--- 4) uploads dir writability ---\n";
    $up = wp_upload_dir();
    $line('  uploads path', $up['path']);
    $line('  error', $up['error'] ? $up['error'] : '(none)');
    $test = trailingslashit($up['path']) . 'bft-write-test-' . wp_generate_password(6, false) . '.txt';
    $ok   = @file_put_contents($test, 'test');
    $line('  write test', false === $ok ? 'FAILED' : 'ok (' . $ok . ' bytes)');
    if (false !== $ok) {
        @unlink($test);
    }
    echo "\n";

    // 5) Stream-download a small real file to disk (exact Downloader call)
    echo "--- 5) streamed download to disk (wp_safe_remote_get stream=true) ---\n";
    $dest = trailingslashit($up['path']) . 'bft-dl-test.json';
    $r = wp_safe_remote_get($catalog, array(
        'timeout'     => 45,
        'redirection' => 5,
        'stream'      => true,
        'filename'    => $dest,
    ));
    if (is_wp_error($r)) {
        $line('  result', 'WP_Error: ' . $r->get_error_code() . ' - ' . $r->get_error_message());
    } else {
        $line('  http code', (string) wp_remote_retrieve_response_code($r));
        $line('  file exists', file_exists($dest) ? 'yes' : 'no');
        $line('  file size', file_exists($dest) ? (string) filesize($dest) : 'n/a');
    }
    if (file_exists($dest)) {
        @unlink($dest);
    }
    echo "\n=== end diagnostic ===\n";
    exit;
}, 1);
