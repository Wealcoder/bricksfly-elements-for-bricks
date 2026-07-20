import { Button, buttonVariants } from "@/components/ui/button";
import { ConfettiAnimation } from "@/lib/confettiAnimation";
import { cn } from "@/lib/utils";
import { useEffect, useState, useContext } from "react";
import { AppContext } from "../context/app.context";


const CompleteImport = () => {
  const [pageUrl, setPageUrl] = useState("#");
  const { setTabKey } = useContext(AppContext);

  useEffect(() => {
    ConfettiAnimation();
    importedPage();
  }, []);

  const changeRoute = (value) => {
    const url = new URL(window.location.href);
    const pageQuery = url.searchParams.get("page");
    url.search = "";
    url.hash = "";
    url.search = `page=${pageQuery}`;
    url.searchParams.set("tab", value);
    window.history.replaceState({}, "", url);
    window.location.reload();
  };

  const importedPage = async () => {
    try {
      const formData = new URLSearchParams();
      formData.append("action", "aab_get_latest_imported_pages");
      formData.append("nonce", AAB_ADDONS_ADMIN.nonce);
      formData.append("per_page", 1);

      const response = await fetch(AAB_ADDONS_ADMIN.ajaxurl, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: formData.toString(),
      });

      if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

      const data = await response.json();
      const page = data?.data?.pages[0];
      const url = page
        ? page.permalink +
          (page.permalink.includes("?") ? "&" : "?") +
          "aab-cache=1"
        : "#";

      setPageUrl(url);
    } catch (error) {
      console.error(error);
    }
  };

  return (
    <div className="bg-background w-[680px] rounded-2xl p-1.5 shadow-auth-card">
      <div className="border border-border-secondary rounded-xl p-8 pb-3.5">
        <div className="mb-6">
          <h3 className="text-2xl font-medium">Congratulations!!!</h3>
          <p className="mt-1.5 text-text-secondary">
            Your page is now imported and ready to use.
          </p>
        </div>
        <div className="mb-6">
          <img
            src={`${AAB_ADDONS_ADMIN.plugin_url}public/images/complete-bg.png`}
            className="w-[616px] h-[258px]"
            alt="demo importing"
          />
        </div>
        <div className="flex flex-col gap-1.5">
          <a
            href={pageUrl}
            className={cn(buttonVariants(), "w-full h-11")}
          >
            Go to page
          </a>
          <Button
            variant="link"
            className={"w-full"}
            onClick={() => changeRoute("stater-template")}
          >
            Go to page library
          </Button>
        </div>
      </div>
    </div>
  );
};

export default CompleteImport;
