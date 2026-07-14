import { useActivate } from "@/hooks/app.hooks";
import { Button, buttonVariants } from "../ui/button";
import { toast } from "sonner";
import { RiKey2Line, RiVipCrown2Line } from "react-icons/ri";
import { cn } from "@/lib/utils";
import { useEffect, useState } from "react";
import LicenseDialog from "./LicenseDialog";

const GetProButton = ({ btnClassName, showLicense }) => {
  const { activated } = useActivate();
  const [openLicense, setOpenLicense] = useState(false);
  const role = AAB_ADDONS_ADMIN.user_role;

  // Demo build: no licensing — hide the license/Pro button and dialog entirely.
  if (typeof AAB_ADDONS_ADMIN !== "undefined" && AAB_ADDONS_ADMIN.is_demo) {
    return null;
  }

  useEffect(() => {
    setOpenLicense(showLicense);
  }, [showLicense]);

  const activePlugin = async () => {
    await fetch(AAB_ADDONS_ADMIN.ajaxurl, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
        Accept: "application/json",
      },

      body: new URLSearchParams({
        action: "aab_active_plugin",
        action_base: "the-bricksfly-pro/the-bricksfly-pro.php",
        nonce: AAB_ADDONS_ADMIN.nonce,
      }),
    })
      .then((response) => {
        return response.json();
      })
      .then((return_content) => {
        if (return_content?.success) {
          toast.success(return_content?.data?.message, {
            position: "top-right",
          });

          window.location.reload();
        }
      });
  };
  return (
    <div>
      {role.includes("administrator") &&
        (activated.integrations.plugins.elements["the-bricksfly-pro"].action ===
        "Active" ? (
          <Button
            variant="pro"
            onClick={() => activePlugin()}
            className={btnClassName}
          >
            <span className="me-2 flex">
              <RiVipCrown2Line size={20} />
            </span>
            Active Plugin
          </Button>
        ) : activated.integrations.plugins.elements["the-bricksfly-pro"].action ===
          "Download" ? (
          <a
            href="https://bricksfly.com/"
            target="_blank"
            className={cn(buttonVariants({ variant: "pro" }), btnClassName)}
          >
            <span className="me-2 flex">
              <RiVipCrown2Line size={20} />
            </span>
            Get Pro Version
          </a>
        ) : (
          <Button
            variant="pro"
            onClick={() => {
              setOpenLicense(true);
            }}
            className={btnClassName}
          >
            <span className="me-1.5 flex">
              <RiKey2Line size={20} />
            </span>

            {activated?.product_status?.item_id === 13
              ? "Deactivate License"
              : "Activate License"}
          </Button>
        ))}
      <LicenseDialog open={openLicense} setOpen={setOpenLicense} />
    </div>
  );
};

export default GetProButton;
