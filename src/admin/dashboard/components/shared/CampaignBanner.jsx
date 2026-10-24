// Seasonal BricksFly Pro offer, below the dashboard menu. The data comes from
// Notices::get_dashboard_campaign(): null when nothing runs or BricksFly Pro is
// installed. It has no close button and stays until the campaign ends.
const campaign = BRICKSFLY_ADDONS_ADMIN.campaign;

const CampaignBanner = () => {
  if (!campaign) {
    return null;
  }

  return (
    <div className="mb-6">
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
    </div>
  );
};

export default CampaignBanner;
