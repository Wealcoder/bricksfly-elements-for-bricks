import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import ProPluginButton from "./ProPluginButton";

// Shown when a Pro item is clicked while BricksFly Pro's features aren't
// available. Pro items are part of the separate BricksFly Pro plugin; this
// only points the user to it.
const ProConfirmDialog = ({ open, setOpen }) => {
  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogContent className="w-[380px] bg-background pr-0 gap-0 !rounded-2xl overflow-hidden [&>.wcf-dialog-close-button]:right-4 [&>.wcf-dialog-close-button]:top-4">
        <DialogHeader className={"hidden"}>
          <DialogTitle className={"hidden"}></DialogTitle>
          <DialogDescription className={"hidden"}></DialogDescription>
        </DialogHeader>
        <div>
          <img
            src={`${BRICKSFLY_ADDONS_ADMIN.plugin_url}public/images/pro-dialog.png`}
            className="w-full h-[174px]"
            alt="pro dialog"
          />
          <div className="p-6 pt-2">
            <h2 className="text-xl text-center font-medium">
              <span dir="ltr">This is a BricksFly Pro feature</span>
            </h2>

            <p className="mt-2.5 text-sm text-text-secondary text-center">
              <span dir="ltr">
                It is part of the separate BricksFly Pro plugin.
              </span>
            </p>

            <ProPluginButton className="w-full mt-6" />
          </div>
        </div>
      </DialogContent>
    </Dialog>
  );
};

export default ProConfirmDialog;
