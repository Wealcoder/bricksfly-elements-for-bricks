import { RiArrowRightUpLine, RiVipCrown2Line } from "react-icons/ri";
import { Separator } from "../ui/separator";
import ElementCard from "../shared/ElementCard";
import { useState } from "react";
import { toast } from "sonner";
import { useActiveItem } from "@/hooks/app.hooks";
import { cn } from "@/lib/utils";
import { buttonVariants } from "../ui/button";

const AddonProElement = () => {
  const [elements, setElements] = useState(
    AAB_ADDONS_ADMIN.addons_config.dashboardProWidget,
  );

  const { updateActiveElement: proElement } = useActiveItem();

  const updateActiveElement = async (data) => {
    const result = Object.fromEntries(
      Object.entries(elements || {}).filter(([key, value]) => {
        if (key === data.slug) {
          value.is_active = data.value;
          return [key, value];
        } else {
          return [key, value];
        }
      }),
    );
    setElements(result);

    await fetch(AAB_ADDONS_ADMIN.ajaxurl, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
        Accept: "application/json",
      },

      body: new URLSearchParams({
        action: "thebrbre_save_dashboard_settings",
        fields: JSON.stringify(result),
        nonce: AAB_ADDONS_ADMIN.nonce,
      }),
    })
      .then((response) => {
        return response.json();
      })
      .then((return_content) => {
        proElement(data);
        toast.success("Save Successful", {
          position: "top-right",
        });
      });
  };

  return (
    <div className="col-span-2 border rounded-2xl p-5 shadow-common">
      <div className="flex justify-between gap-11">
        <div className="flex gap-2 items-center">
          <RiVipCrown2Line size={20} color="#FFA132" />
          <p className="font-medium">BricksFly Pro Elements</p>
        </div>
        <div>
          <a
            href={"https://bricksfly.com/docs/changelogs/"}
            target="_blank"
            className={cn(buttonVariants({ variant: "secondary", size: "sm" }))}
          >
            Changelog{" "}
            <RiArrowRightUpLine
              size={18}
              className="rtl:rotate-360 rtl:scale-x-[-1] ms-1"
            />
          </a>
        </div>
      </div>
      <Separator className="mt-4 mb-5" />
      <div className="grid grid-cols-2 justify-between gap-2.5 p-3 bg-background-secondary rounded-lg">
        {Object.keys(elements)?.map((content, i) => (
          <React.Fragment key={`tab_content-${i}`}>
            <ElementCard
              element={elements[content]}
              slug={content}
              updateActiveItem={updateActiveElement}
            />
          </React.Fragment>
        ))}
      </div>
    </div>
  );
};

export default AddonProElement;
