import ShowElements from "@/components/elements/ShowElements";
import ElementTopBar from "@/components/elements/ElementTopBar";
import { useEffect, useState } from "react";

const Elements = () => {
  const [searchKey, setSearchKey] = useState("");
  const [searchParam, setSearchParam] = useState("");
  const [filterKey, setFilterKey] = useState("free-pro");
  const [settingOpen, setSettingOpen] = useState(null);

  const [elementCount, setElementCount] = useState(THEBRBRE_ADDONS_ADMIN.widgets);

  const urlParams = new URLSearchParams(window.location.search);

  useEffect(() => {
    const tabValue = urlParams.get("cTab");
    if (tabValue) {
      setSearchParam(tabValue);
    }
    const filterValue = urlParams.get("filter");
    if (filterValue) {
      setFilterKey(filterValue);
    }

    const settingValue = urlParams.get("wiz_setting");
    if (settingValue) {
      setSettingOpen(settingValue);
    }
  }, [urlParams]);

  return (
    <div className="min-h-screen px-8 py-6 border rounded-2xl">
      <div className="pb-6 border-b">
        <ElementTopBar
          filterKey={filterKey}
          setFilterKey={setFilterKey}
          searchKey={searchKey}
          setSearchKey={setSearchKey}
          elementCount={elementCount}
        />
      </div>
      <div className="mt-4">
        <ShowElements
          filterKey={filterKey}
          searchKey={searchKey}
          searchParam={searchParam}
          urlParams={urlParams}
          setElementCount={setElementCount}
          settingOpen={settingOpen}
        />
      </div>
    </div>
  );
};

export default Elements;
