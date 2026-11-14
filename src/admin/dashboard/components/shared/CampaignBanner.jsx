import { useState } from "react";

// Seasonal offer, below the dashboard menu. The data comes from
// Notices::get_dashboard_campaign(): null when nothing runs or the user
// dismissed it. It stays until the campaign ends or the user clicks the X
// drawn in the banner's top right corner, which dismisses it for good (the
// admin notice of the same campaign goes too).
const campaign = BRICKSFLY_ADDONS_ADMIN.campaign;

const CampaignBanner = () => {
  const [hidden, setHidden] = useState(false);

  if (!campaign || hidden) {
    return null;
  }

  const dismiss = () => {
    setHidden(true);
    const body = new FormData();
    body.append("action", "bricksfly_notice_dismiss_notice");
    body.append("nonce", campaign.nonce);
    body.append("notice_id", campaign.notice_id);
    fetch(campaign.ajax_url, { method: "POST", credentials: "same-origin", body }).catch(() => {});
  };

  return (
    <div className="mb-6 relative">
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
      <button
        type="button"
        onClick={dismiss}
        aria-label="Dismiss this offer"
        title="Dismiss"
        style={{
          position: "absolute",
          left: "97.4%",
          top: "17.2%",
          width: "3.4%",
          minWidth: "24px",
          aspectRatio: "1",
          transform: "translate(-50%, -50%)",
          padding: 0,
          border: 0,
          borderRadius: "50%",
          background: "transparent",
          cursor: "pointer",
        }}
      />
    </div>
  );
};

export default CampaignBanner;
