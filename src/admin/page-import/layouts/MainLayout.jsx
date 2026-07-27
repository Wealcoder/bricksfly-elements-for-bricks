import TemplateHeader from "@/components/header/TemplateHeader";
import { ScrollArea } from "@/components/ui/scroll-area";
import { ShowContent } from "../config/showFullContent";
import { hideElements } from "@/lib/utils";
import { useEffect, useState, Suspense, useContext } from "react";
import { AppContext } from "../context/app.context";

const MainLayout = () => {
  const {
    mainState: { tabKey },
    setTabKey,
  } = useContext(AppContext);

  useEffect(() => {
    const urlParams = new URLSearchParams(window.location.search);
    const tabValue = urlParams.get("tab");
    if (tabValue) {
      setTabKey(tabValue);
    }
  }, []);

  return (
    <div className="wcf-anim2024-wrapper">
      <div className="wcf-anim2024-style">
        <Suspense
          fallback={
            <div className="flex justify-center items-center h-screen">
              <p className="text-lg font-semibold">Loading...</p>
            </div>
          }
        >
          {ShowContent({ tabKey })}
        </Suspense>
      </div>
    </div>
  );
};

MainLayout.SecondLayout = ({ children }) => {
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    hideElements();
    const timer = setTimeout(() => {
      setLoading(false);
    }, 1000);
    return () => clearTimeout(timer);
  }, []);

  return (
    <>
      {loading ? (
        <div className="flex justify-center items-center h-screen">
          <p className="text-lg font-semibold">Loading...</p>
        </div>
      ) : (
        <div className="bg-background">
          {/* Page importer: no license concept anymore, so there's nothing to
              activate/manage here — hide the Pro button entirely. */}
          <TemplateHeader showProButton={false} />
          <div>{children}</div>
        </div>
      )}
    </>
  );
};

MainLayout.ThirdLayout = ({ children }) => {
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    hideElements();
    const timer = setTimeout(() => {
      setLoading(false);
    }, 1000);
    return () => clearTimeout(timer);
  }, []);

  return (
    <>
      {loading ? (
        <div className="flex justify-center items-center h-screen">
          <p className="text-lg font-semibold">Loading...</p>
        </div>
      ) : (
        <div className="bg-background-secondary">
          <TemplateHeader activeBtn={false} />
          <ScrollArea className="h-[calc(100vh-85px)]">
            <div className="flex justify-center items-center min-h-[calc(100vh-85px)] py-5">
              {children}
            </div>
          </ScrollArea>
        </div>
      )}
    </>
  );
};

export default MainLayout;
