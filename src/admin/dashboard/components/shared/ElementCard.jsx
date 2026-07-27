import { Dot } from "lucide-react";
import { Badge } from "../ui/badge";
import { Switch } from "../ui/switch";
import { useEffect } from "react";
import { cn } from "@/lib/utils";
import ExtensionCardSettings from "../extentions/ExtensionCardSettings";

const ElementCard = ({
  element,
  slug,
  className,
  updateActiveItem,
  isDisable = false,
  exSettings,
  preview = true,
  settingOpen = null,
}) => {
  const hash = window.location.hash;
  const hashValue = hash?.replace("#", "");

  // No license concept anymore — every element (free or Pro) is unlocked as
  // long as it isn't still "coming soon". Whether the Pro plugin itself is
  // installed/active is a separate concern handled elsewhere (GetProButton).
  const checkStatus = () => true;

  const setCheck = (value, slug) => {
    if (updateActiveItem) {
      updateActiveItem({ value, slug });
    }
  };

  useEffect(() => {
    if (hashValue === slug) {
      const target = document.getElementById(hashValue);
      if (target) {
        target.scrollIntoView({
          behavior: "smooth",
          block: "center",
        });
      }
    }
  }, [hashValue]);

  return (
    <div
      className={cn(
        "flex items-center justify-between gap-3 px-4 py-[15px] bg-background rounded-lg  box-border",
        hashValue === slug
          ? "shadow-[0px_0px_0px_2px_rgba(252,104,72,0.25),0px_1px_2px_0px_rgba(10,13,20,0.03)]"
          : "shadow-common-2",
        className,
      )}
      id={slug || ""}
    >
      {element ? (
        <>
          <div
            className={cn(
              "flex items-center gap-3",
              element?.is_upcoming ? "opacity-50 pointer-events-none" : "",
            )}
          >
            <div
              className={cn(
                "border rounded-full h-11 w-11 flex justify-center items-center shadow-common text-[20px]",
                element?.icon,
              )}
            />

            <div className="flex flex-col gap-1">
              <div className="flex items-center">
                <h2 className="text-[15px] leading-6 font-medium">
                  {element?.label}
                </h2>
                {element?.is_upcoming ? (
                  <>
                    <Dot
                      className="w-3.5 h-3.5 text-icon-secondary"
                      strokeWidth={2}
                    />
                    <Badge variant="pro">COMING</Badge>
                  </>
                ) : element?.is_pro ? (
                  <>
                    <Dot
                      className="w-3.5 h-3.5 text-icon-secondary"
                      strokeWidth={2}
                    />
                    <Badge variant="pro">PRO</Badge>
                  </>
                ) : (
                  ""
                )}
              </div>
              <div className="flex items-center">
                <a
                  href={element?.doc_url}
                  target="_blank"
                  className={cn(
                    "text-sm",
                    element?.doc_url
                      ? "text-label hover:text-text"
                      : "pointer-events-none text-[#CACFD8]",
                  )}
                >
                  Documentation
                </a>

                {preview && (
                  <>
                    <Dot
                      className="w-3.5 h-3.5 text-icon-secondary"
                      strokeWidth={2}
                    />
                    <a
                      href={element?.demo_url}
                      target="_blank"
                      className={cn(
                        "text-sm",
                        element?.demo_url
                          ? "text-label hover:text-text"
                          : "pointer-events-none text-[#CACFD8]",
                      )}
                    >
                      Preview
                    </a>
                  </>
                )}
              </div>
            </div>
          </div>
          <div className="flex justify-end items-center gap-2">
            {exSettings && (
              <div>
                <ExtensionCardSettings
                  disabled={!checkStatus()}
                  defaultOpen={settingOpen === slug ? true : false}
                >
                  {exSettings}
                </ExtensionCardSettings>
              </div>
            )}

            {element?.is_upcoming ? (
              ""
            ) : (
              <div>
                <Switch
                  disabled={element?.is_upcoming || isDisable}
                  checked={element?.is_active}
                  onCheckedChange={(value) => setCheck(value, slug)}
                />
              </div>
            )}
          </div>
        </>
      ) : (
        ""
      )}
    </div>
  );
};

export default ElementCard;
