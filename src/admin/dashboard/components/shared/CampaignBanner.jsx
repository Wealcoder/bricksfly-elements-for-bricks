import { useState } from "react";

// Seasonal BricksFly Pro offer, below the dashboard menu. The data comes from
// Notices::get_dashboard_campaign() (null when nothing runs or it is hidden).
// Close hides it for a few days, "Don't show again" for good; both use the
// same AJAX action as the WordPress admin notice.
const campaign = BRICKSFLY_ADDONS_ADMIN.campaign;

// The layout remounts on every tab change; remember a close for this page view.
let closed = false;

const hideCampaign = (snooze) => {
  const body = new URLSearchParams({
    action: campaign.action,
    nonce: campaign.nonce,
    notice_id: campaign.notice_id,
  });
  if (snooze) {
    body.append("snooze", "true");
    body.append("snooze_time", String(campaign.snooze_time));
  }

  fetch(BRICKSFLY_ADDONS_ADMIN.ajaxurl, {
    method: "POST",
    credentials: "same-origin",
    body,
  }).catch(() => {});
};

const CampaignBanner = () => {
  const [visible, setVisible] = useState(Boolean(campaign) && !closed);

  if (!visible) {
    return null;
  }

  const close = (snooze) => {
    closed = true;
    setVisible(false);
    hideCampaign(snooze);
  };

  return (
    <div className="mb-6">
      <div className="relative">
        <a
          href={campaign.url}
          target="_blank"
          rel="noopener"
          className="block overflow-hidden rounded-[10px] leading-none"
        >
          <img
            src={campaign.image}
            width="1100"
            height="150"
            alt={campaign.alt}
            className="block w-full h-auto"
          />
        </a>
        {/* Sits over the close (X) drawn in the banner image. */}
        <button
          type="button"
          onClick={() => close(true)}
          aria-label={campaign.labels.close}
          className="absolute p-0 border-0 rounded-full bg-transparent cursor-pointer"
          style={{
            left: "97.4%",
            top: "17.2%",
            width: "3.4%",
            minWidth: 24,
            aspectRatio: "1",
            transform: "translate(-50%, -50%)",
          }}
        />
      </div>
      <button
        type="button"
        onClick={() => close(false)}
        className="mt-1.5 p-0 border-0 bg-transparent text-xs text-[#646970] underline cursor-pointer"
      >
        {campaign.labels.dismiss}
      </button>
    </div>
  );
};

export default CampaignBanner;
