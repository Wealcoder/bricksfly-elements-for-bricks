import { RiArrowLeftLine } from "react-icons/ri";
import LargeLogo from "./LargeLogo";
import GetProButton from "../shared/GetProButton";
import LicenseStatusBadge from "./LicenseStatusBadge";

const TemplateHeader = ({
  activeBtn = true,
  showProButton = true,
  showLicenseStatus = false,
}) => {
  const getDashboardUrl = () => {
    const url = new URL(window.location.href);

    url.search = "";
    url.hash = "";
    url.searchParams.set("page", "bricksfly_addons_settings");

    return url;

    // const newUrl = url.toString();

    // return newUrl;
  };

  const changeRoute = (value = null) => {
    const url = getDashboardUrl();
    // url.searchParams.set("tab", value);
    window.history.replaceState({}, "", url);
    window.location.reload();
  };
  return (
    <div className="bg-background px-8 py-5 flex justify-between gap-11 items-center border-b border-border">
      <div className="flex gap-4 items-center">
        {activeBtn ? (
          <div onClick={() => changeRoute()} className="cursor-pointer">
            <RiArrowLeftLine
              size={20}
              className="text-icon-secondary hover:text-[#101828]"
            />
          </div>
        ) : (
          ""
        )}
        <LargeLogo homeUrl={getDashboardUrl} />
      </div>
      {activeBtn && showProButton ? (
        <div className="flex justify-end gap-3 items-center">
          <GetProButton />
        </div>
      ) : showLicenseStatus ? (
        <div className="flex justify-end gap-3 items-center">
          <LicenseStatusBadge />
        </div>
      ) : (
        ""
      )}
    </div>
  );
};

export default TemplateHeader;
