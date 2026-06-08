import { cn } from "@/lib/utils";
import { RiPlayCircleLine } from "react-icons/ri";
import { buttonVariants } from "../ui/button";
import TutorialDialog from "./dialog/TutorialDialog";
import { useState } from "react";

const Tutorial = () => {
  const [open, setOpen] = useState(false);
  return (
    <div className="col-span-2 border rounded-2xl p-5 ps-6 flex justify-between items-center gap-6 shadow-common">
      <div className="w-[362px]">
        <h2 className="text-xl font-medium ">
          <span dir="ltr">
            Watch The Beginner's Guide on How to Use Animation Addons.
          </span>
        </h2>
        <p className="text-sm mt-[10px] text-text-secondary">
          <span dir="ltr">
            Get started with ease by watching our step-by-step beginner's
            tutorial on Bricks.
          </span>
        </p>
        <a
          href={"https://www.youtube.com/@AnimationAddonsforElementor"}
          className={cn(buttonVariants({ variant: "secondary" }), "mt-7")}
          target="_blank"
        >
          <span className="me-1.5 flex">
            <RiPlayCircleLine size={20} />
          </span>
          Watch Tutorials
        </a>
      </div>
      <div className="flex-1">
        <div className="relative">
          <img
            className="w-full h-full object-cover"
            src={`${AAB_ADDONS_ADMIN.plugin_url}public/images/tutorial-thumb.png`}
            alt="thumbnail"
          />
          <div
            className="absolute top-[93px] left-0 right-0 mx-auto w-fit cursor-pointer"
            onClick={() => setOpen(true)}
          >
            <img width={50} height={50} src={`${AAB_ADDONS_ADMIN.plugin_url}public/images/play-button.png`} alt="play" />
          </div>
        </div>
      </div>
      <TutorialDialog open={open} setOpen={setOpen} />
    </div>
  );
};

export default Tutorial;
