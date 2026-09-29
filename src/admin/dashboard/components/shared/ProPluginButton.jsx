import { useActivate } from "@/hooks/app.hooks";
import { Button, buttonVariants } from "../ui/button";
import { toast } from "sonner";
import { RiVipCrown2Line } from "react-icons/ri";
import { cn } from "@/lib/utils";

const PRO_BASENAME =
  "bricksfly-elements-for-bricks-pro/bricksfly-elements-for-bricks-pro.php";

/**
 * Button pointing to the separate BricksFly Pro plugin:
 *  - not installed        → "Get BricksFly Pro" link
 *  - installed, inactive  → "Activate Plugin" (user click activates it)
 *  - active               → whatever BricksFly Pro supplies via `pro_cta`
 */
const ProPluginButton = ({ className }) => {
  const { activated } = useActivate();
  const action =
    activated?.integrations?.plugins?.elements?.[
      "bricksfly-elements-for-bricks-pro"
    ]?.action;
  const cta = BRICKSFLY_ADDONS_ADMIN?.pro_cta || {};

  const activatePlugin = async () => {
    await fetch(BRICKSFLY_ADDONS_ADMIN.ajaxurl, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
        Accept: "application/json",
      },
      body: new URLSearchParams({
        action: "bricksfly_active_plugin",
        action_base: PRO_BASENAME,
        nonce: BRICKSFLY_ADDONS_ADMIN.nonce,
      }),
    })
      .then((response) => response.json())
      .then((return_content) => {
        if (return_content?.success) {
          toast.success(return_content?.data?.message, {
            position: "top-right",
          });
          window.location.reload();
        }
      });
  };

  if (action === "Active") {
    return (
      <Button
        variant="pro"
        onClick={() => activatePlugin()}
        className={className}
      >
        <span className="me-2 flex">
          <RiVipCrown2Line size={20} />
        </span>
        Activate Plugin
      </Button>
    );
  }

  const isInstalledAndActive = action === "Activated";
  const href = isInstalledAndActive
    ? cta.url || "https://bricksfly.com/pricing/"
    : "https://bricksfly.com/pricing/";
  const label = isInstalledAndActive
    ? cta.label || "BricksFly Pro"
    : "Get BricksFly Pro";

  return (
    <a
      href={href}
      target={isInstalledAndActive ? undefined : "_blank"}
      rel={isInstalledAndActive ? undefined : "noopener noreferrer"}
      className={cn(buttonVariants({ variant: "pro" }), className)}
    >
      <span className="me-2 flex">
        <RiVipCrown2Line size={20} />
      </span>
      {label}
    </a>
  );
};

export default ProPluginButton;
