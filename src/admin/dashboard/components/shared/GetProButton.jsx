import { useActivate } from "@/hooks/app.hooks";
import { Button, buttonVariants } from "../ui/button";
import { toast } from "sonner";
import { RiKey2Line, RiVipCrown2Line } from "react-icons/ri";
import { cn } from "@/lib/utils";

const GetProButton = ({ btnClassName }) => {
  const { activated } = useActivate();
  const role = BRICKSFLY_ADDONS_ADMIN.user_role;

  const activePlugin = async () => {
    await fetch(BRICKSFLY_ADDONS_ADMIN.ajaxurl, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
        Accept: "application/json",
      },

      body: new URLSearchParams({
        action: "bricksfly_active_plugin",
        action_base: "bricksfly-elements-for-bricks-pro/bricksfly-elements-for-bricks-pro.php",
        nonce: BRICKSFLY_ADDONS_ADMIN.nonce,
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
        (activated.integrations.plugins.elements["bricksfly-elements-for-bricks-pro"].action ===
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
        ) : activated.integrations.plugins.elements["bricksfly-elements-for-bricks-pro"].action ===
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
              window.location.href = BRICKSFLY_ADDONS_ADMIN.license_settings_url;
            }}
            className={btnClassName}
          >
            <span className="me-1.5 flex">
              <RiKey2Line size={20} />
            </span>

            {activated?.product_status?.item_id === 39996
              ? "Manage License"
              : "Activate License"}
          </Button>
        ))}
    </div>
  );
};

export default GetProButton;
