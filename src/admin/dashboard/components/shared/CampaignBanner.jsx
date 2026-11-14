import { useState } from "react";

// Seasonal offer, below the dashboard menu. The data comes from
// Notices::get_dashboard_campaign(): null when nothing runs. It shows on every
// visit until the campaign ends; the X drawn in the banner's top right corner
// only hides it until the next page load (nothing is saved).
const campaign = BRICKSFLY_ADDONS_ADMIN.campaign;

const CampaignBanner = () => {
  const [hidden, setHidden] = useState(false);

  if (!campaign || hidden) {
    return null;
  }

  const dismiss = () => setHidden(true);

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
