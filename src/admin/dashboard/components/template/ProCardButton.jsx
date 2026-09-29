import { useState } from "react";
import { buttonVariants } from "@/components/ui/button";
import { cn } from "@/lib/utils";

const PRO_BASENAME =
  "bricksfly-elements-for-bricks-pro/bricksfly-elements-for-bricks-pro.php";

/**
 * Button on a Pro template / page card, from `pro_action` (see
 * bricksfly_pro_action() in includes/helper.php):
 *  - BricksFly Pro not installed → "Get Pro" (BricksFly Pro page, new tab)
 *  - installed but inactive      → "Activate Pro" (activates it, reloads)
 *  - active                      → BricksFly Pro's own action (e.g. its license screen)
 */
const ProCardButton = ({ className }) => {
  const action = BRICKSFLY_ADDONS_ADMIN?.pro_action || {};
  const [busy, setBusy] = useState(false);
  const classes = cn(buttonVariants(), className);

  if (action.type === "activate") {
    const activate = async () => {
      setBusy(true);
      try {
        const response = await fetch(BRICKSFLY_ADDONS_ADMIN.ajaxurl, {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: new URLSearchParams({
            action: "bricksfly_active_plugin",
            action_base: PRO_BASENAME,
            nonce: BRICKSFLY_ADDONS_ADMIN.nonce,
          }),
        });
        const result = await response.json();
        if (result?.success) {
          window.location.reload();
          return;
        }
        // Could not activate from here: open the Plugins screen instead.
        window.location.href = action.url;
      } catch (e) {
        window.location.href = action.url;
      }
      setBusy(false);
    };

    return (
      <button type="button" onClick={activate} disabled={busy} className={classes}>
        {busy ? "Activating…" : action.label || "Activate Pro"}
      </button>
    );
  }

  return (
    <a
      href={action.url || "https://bricksfly.com/pricing/"}
      target={action.new_tab === false ? undefined : "_blank"}
      rel={action.new_tab === false ? undefined : "noopener noreferrer"}
      className={classes}
    >
      {action.label || "Get Pro"}
    </a>
  );
};

export default ProCardButton;
