import {
  Accordion,
  AccordionContent,
  AccordionItem,
  AccordionTrigger,
} from "@/components/ui/accordion";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Checkbox } from "@/components/ui/checkbox";
import RequiredPluginList from "@/components/shared/RequiredPluginList";
import { useEffect, useState, useContext } from "react";
import { AppContext } from "../context/app.context";

const RequiredFeatures = () => {
  const { setTabKey } = useContext(AppContext);
  const [currenTemplate, setCurrenTemplate] = useState({});
  const [selectedPlugins, setSelectedPlugins] = useState([]);
  const [allowAttachment, setAllowAttachment] = useState(true);
  // "original" keeps the template's own design, "site" follows this site's styles.
  const [design, setDesign] = useState("original");
  const [loading, setIsLoading] = useState(true);

  const url = new URL(window.location.href);
  const templateid = url.searchParams.get("templateid");

  const changeRoute = (value) => {
    const pageQuery = url.searchParams.get("page");
    const template = url.searchParams.get("template");
    url.search = "";
    url.hash = "";
    url.search = `page=${pageQuery}`;
    url.searchParams.set("template", template);
    url.searchParams.set("templateid", templateid);
    url.searchParams.set("tab", value);
    if (
      BRICKSFLY_ADDONS_ADMIN?.import_plugins &&
      selectedPlugins &&
      selectedPlugins.length
    ) {
      url.searchParams.set("plugins", selectedPlugins.toString());
    }
    url.searchParams.set("attachment", allowAttachment);
    url.searchParams.set("design", design);
    window.history.replaceState({}, "", url);
    setTabKey(value);
  };

  useEffect(() => {
    if (templateid) getTemplateData(templateid);
  }, []);

  const getTemplateData = async (id) => {
    try {
      const url = new URL(
        `${BRICKSFLY_ADDONS_ADMIN?.st_template_domain}wp-json/wp/v2/brk-starter-page`
      );
      if (id) url.searchParams.append("tplid", id);

      await fetch(url.toString())
        .then((response) => response.json())
        .then((data) => {
          if (data?.templates) {
            const result = Object.entries(data.templates).find(
              ([key, value]) => value.id == id
            )?.[1];
            // A page with no plugin requirement still shows this screen, for
            // the design and image choices.
            if (!result?.dependencies?.plugins?.length) {
              setCurrenTemplate(result || {});
              setIsLoading(false);
              return;
            }
            validateData(result);
          }
        });
    } catch (error) {
      console.error(error);
    }
  };

  const validateData = async (mainContent) => {
    try {
      await fetch(BRICKSFLY_ADDONS_ADMIN.ajaxurl, {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
          Accept: "application/json",
        },
        body: new URLSearchParams({
          action: "bricksfly_template_dependency_status",
          nonce: BRICKSFLY_ADDONS_ADMIN.nonce,
          dependencies: JSON.stringify(mainContent?.dependencies),
        }),
      })
        .then((response) => response.json())
        .then((return_content) => {
          if (return_content.success) {
            const result = return_content?.data?.dependencies;
            setSelectedPlugins((prev) => {
              const requiredSlugs = result?.plugins
                .filter((p) => p.required)
                .map((p) => p.slug);
              return Array.from(new Set([...prev, ...requiredSlugs]));
            });
            mainContent.dependencies = result;
            setCurrenTemplate(mainContent);
          }
        });
    } catch (error) {
      console.error(error);
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="bg-background w-[692px] rounded-2xl p-1.5 shadow-auth-card">
      {loading ? (
        <div className="flex justify-center items-center h-[10vh]">
          <p className="text-lg font-semibold">Loading...</p>
        </div>
      ) : (
        <div className="border border-border-secondary rounded-xl">
          <div className="border-b border-border-secondary p-8 pb-6">
            <div className="mb-7">
              <h3 className="text-2xl font-medium">Import settings</h3>
              <p className="mt-1.5 text-text-secondary">
                Choose how this page is added to your site.
              </p>
            </div>
            <div>
              <Accordion
                type="multiple"
                defaultValue={["plugins"]}
                className="w-full space-y-3"
              >
                {currenTemplate?.dependencies?.plugins?.length ? (
                  <AccordionItem
                    value="plugins"
                    className="border px-4 rounded-xl"
                  >
                    <AccordionTrigger>
                      <div className="flex justify-between items-center gap-4 w-full mr-2.5">
                        <h3 className="text-lg text-medium">
                          Required Plugins
                        </h3>
                        <p className="text-text-secondary">
                          {currenTemplate?.dependencies?.plugins?.length} Plugins
                        </p>
                      </div>
                    </AccordionTrigger>
                    <AccordionContent className="mt-2">
                      <RequiredPluginList
                        plugins={currenTemplate?.dependencies?.plugins}
                        selectedPlugins={selectedPlugins}
                        setSelectedPlugins={setSelectedPlugins}
                        onRefresh={() => validateData(currenTemplate)}
                      />
                    </AccordionContent>
                  </AccordionItem>
                ) : (
                  ""
                )}
              </Accordion>
            </div>
            <div className="mt-6">
              <h3 className="text-lg font-medium mb-3">Design</h3>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                {[
                  {
                    value: "original",
                    title: "Original style",
                    desc: "Looks like the preview.",
                  },
                  {
                    value: "site",
                    title: "This site's style",
                    desc: "Uses your site's fonts and styles.",
                  },
                ].map((opt) => (
                  <label
                    key={opt.value}
                    htmlFor={`bf-design-${opt.value}`}
                    className={`flex items-start gap-2.5 border rounded-xl p-3.5 cursor-pointer ${
                      design === opt.value
                        ? "border-primary bg-primary/5"
                        : "border-border-secondary"
                    }`}
                  >
                    <input
                      type="radio"
                      id={`bf-design-${opt.value}`}
                      name="bf-design"
                      value={opt.value}
                      checked={design === opt.value}
                      onChange={() => setDesign(opt.value)}
                      className="mt-1"
                    />
                    <span>
                      <span className="block text-base font-medium">
                        {opt.title}
                      </span>
                      <span className="block text-sm text-text-secondary mt-0.5">
                        {opt.desc}
                      </span>
                    </span>
                  </label>
                ))}
              </div>
            </div>
            <div className="flex items-center space-x-2.5 mt-6">
              <Checkbox
                id="demo-import-attachment"
                checked={allowAttachment}
                onCheckedChange={setAllowAttachment}
              />
              <label
                htmlFor="demo-import-attachment"
                className="text-base font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
              >
                Import images (recommended)
              </label>
            </div>
          </div>
          <div className="px-8 pt-4 pb-6 flex justify-end items-center gap-3">
            <Button
              variant="secondary"
              onClick={() => changeRoute("stater-template")}
            >
              Go Back
            </Button>
            <Button onClick={() => changeRoute("demo-importing")}>
              Import page
            </Button>
          </div>
        </div>
      )}
    </div>
  );
};

export default RequiredFeatures;
