import { useActivate } from "@/hooks/app.hooks";
import { Button, buttonVariants } from "../ui/button";
import { toast } from "sonner";
import { RiVipCrown2Line } from "react-icons/ri";
import { cn } from "@/lib/utils";

const GetProButton = ({ btnClassName }) => {
  const { activated } = useActivate();
  const role = THEBRBRE_ADDONS_ADMIN.user_role;

  const activePlugin = async () => {
    await fetch(THEBRBRE_ADDONS_ADMIN.ajaxurl, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
        Accept: "application/json",
      },

      body: new URLSearchParams({
        action: "thebrbre_active_plugin",
        action_base: "the-bricksfly-pro/the-bricksfly-pro.php",
        nonce: THEBRBRE_ADDONS_ADMIN.nonce,
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
        ) : (
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
        ))}
    </div>
  );
};

export default GetProButton;
