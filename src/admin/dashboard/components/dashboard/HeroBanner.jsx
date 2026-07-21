const HeroBanner = () => {
  return (
    <div className="relative h-full">
      <img
        src={`${AAB_ADDONS_ADMIN.plugin_url}public/images/animate-anything-banner.png`}
        className="w-full h-full object-cover rounded-[10px]"
        alt="Fly Beyond Bricks"
      />
    </div>
  );
};

export default HeroBanner;
