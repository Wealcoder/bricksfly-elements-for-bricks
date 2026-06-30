import { RiCheckboxCircleFill, RiErrorWarningFill } from "react-icons/ri";

/**
 * License status indicator for page headers (e.g. the Page Importer).
 *
 * Reads the activation state from the localized config rather than from React
 * context, so it works in any app that loads it — the Page Importer app does
 * NOT provide the dashboard's activate/notification context, so a context hook
 * would read stale/default data here. `product_status.item_id === 13` is the
 * same "license valid" flag the rest of the UI gates Pro features on (set
 * server-side in page-import.php / dashboard.php from the live license option).
 */
const LicenseStatusBadge = () => {
  const config =
    (typeof AAB_ADDONS_ADMIN !== "undefined" && AAB_ADDONS_ADMIN.addons_config) ||
    {};
  const isActive = config?.product_status?.item_id === 13;

  return (
    <div
      className={
        "flex items-center gap-1.5 rounded-full px-3 py-1.5 text-sm font-medium " +
        (isActive
          ? "bg-[#ECFDF3] text-[#067647] border border-[#ABEFC6]"
          : "bg-[#FEF3F2] text-[#B42318] border border-[#FECDCA]")
      }
      title={
        isActive ? "Your Pro license is active" : "Your Pro license is not active"
      }
    >
      {isActive ? (
        <RiCheckboxCircleFill size={16} />
      ) : (
        <RiErrorWarningFill size={16} />
      )}
      <span>{isActive ? "License Active" : "License Inactive"}</span>
    </div>
  );
};

export default LicenseStatusBadge;
