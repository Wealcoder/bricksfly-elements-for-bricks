/**
 * BricksFly — admin "Import Page" button.
 *
 * Injects an "Import Page" action button right after WordPress's "Add Page"
 * (.page-title-action) on the Pages list screen (edit.php?post_type=page).
 * Mirrors the AAE animation addon's aae-admin-actions.js. Configuration
 * (page_url, logo) is provided via the localized BRICKSFLY_PAGE_IMPORT object.
 */
document.addEventListener('DOMContentLoaded', function () {
    if (typeof BRICKSFLY_PAGE_IMPORT === 'undefined' || !BRICKSFLY_PAGE_IMPORT.page_url) {
        return;
    }

    const heading = document.querySelector(
        '.wrap .wp-heading-inline + .page-title-action'
    );

    if (!heading) {
        return;
    }

    // Guard against double-injection (script enqueued more than once).
    if (document.getElementById('aab-heading-button')) {
        return;
    }

    const btn = document.createElement('a');
    btn.href = BRICKSFLY_PAGE_IMPORT.page_url;
    btn.style.top = '0';
    btn.style.left = '5px';
    btn.style.border = '1px solid #FCCBC0';
    btn.style.borderRadius = '6px';
    btn.style.padding = '0 8px';
    btn.id = 'aab-heading-button';
    btn.className = 'page-title-action'; // same styling as "Add New"
    btn.innerHTML =
        '<div style="display: flex; justify-content: center; align-items: center; gap: 6px;">' +
        (BRICKSFLY_PAGE_IMPORT.logo
            ? '<img src="' + BRICKSFLY_PAGE_IMPORT.logo + '" width="16" height="16" alt="" />'
            : '') +
        '<span style="font-size: 12px; font-weight: 500; color: #FFFFFF">Import Page</span>' +
        '</div>';

    heading.after(btn);
});
