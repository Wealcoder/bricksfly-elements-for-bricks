<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<div class="aab-review-notice" style="display:flex;align-items:center;gap:15px;padding:10px 0;">
    <div>
        <p style="font-size:14px;margin:0 0 10px;">
            <?php esc_html_e( 'Hey! We hope you are enjoying BricksFly. Could you please do us a big favor and give us a 5-star rating on WordPress? It would help us spread the word!', 'bricksfly' ); ?>
        </p>
        <div style="display:flex;gap:10px;">
            <a href="https://wordpress.org/support/plugin/bricks-animation-addons/reviews/#new-post" target="_blank" class="button button-primary">
                <?php esc_html_e( 'Leave a Review', 'bricksfly' ); ?>
            </a>
            <button type="button" class="button aab-snooze-btn" data-snooze="true" data-snooze-time="<?php echo esc_attr( 7 * DAY_IN_SECONDS ); ?>">
                <?php esc_html_e( 'Maybe Later', 'bricksfly' ); ?>
            </button>
            <button type="button" class="button notice-dismiss-btn">
                <?php esc_html_e( 'Already Reviewed', 'bricksfly' ); ?>
            </button>
        </div>
    </div>
</div>
