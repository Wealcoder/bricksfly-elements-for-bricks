import ProPluginButton from "./ProPluginButton";

// Header button pointing to the separate BricksFly Pro plugin (admins only).
const GetProButton = ({ btnClassName }) => {
  const role = BRICKSFLY_ADDONS_ADMIN.user_role;

  if (!role?.includes("administrator")) {
    return null;
  }

  return (
    <div>
      <ProPluginButton className={btnClassName} />
    </div>
  );
};

export default GetProButton;
