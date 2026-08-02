const LargeLogo = ({ homeUrl }) => {
  const urlLink = homeUrl();
  const newUrl = urlLink.toString();

  return (
    <a href={newUrl}>
      <img
        width={140}
        height={40}
        src={`${BRICKSFLY_ADDONS_ADMIN.plugin_url}public/images/Logo-2.png`}
        alt="Logo"
      />
    </a>
  );
};

export default LargeLogo;
