import { cn } from "@/lib/utils";
import { RiArrowRightUpLine } from "react-icons/ri";
import { buttonVariants } from "../ui/button";

const AffiliateProgram = () => {
  return (
    <div className="relative rounded-2xl overflow-hidden bg-[linear-gradient(90deg,#FFDFAA_0.01%,#FFC3C0_100%)] px-6 py-8 md:px-10 md:py-9 flex items-center justify-between gap-6">
      <div className="flex flex-col gap-6 max-w-[384px]">
        <div className="flex flex-col gap-3">
          <h2 className="font-serif text-[32px] leading-tight text-[#181B25]">
            Affiliate Program
          </h2>
          <p className="text-sm text-[#525866]">
            Earn more while you share what you love. Join now, share your
            link, and start earning with every sale!
          </p>
        </div>
        <a
          href={"https://bricksfly.com/affiliate-program/"}
          target="_blank"
          className={cn(buttonVariants({ variant: "secondary" }), "w-fit")}
        >
          Join Now
          <RiArrowRightUpLine
            size={16}
            className="rtl:rotate-360 rtl:scale-x-[-1]"
          />
        </a>
      </div>
      <div className="relative shrink-0 hidden md:block w-[220px]">
        <img
          src={`${THEBRBRE_ADDONS_ADMIN.plugin_url}public/images/affiliate-illustration.png`}
          alt=""
          className="w-full h-auto"
        />
        <div className="absolute bottom-2 -left-3 inline-flex items-center gap-1 bg-[#A2A3F5] border-[0.5px] border-[#3F414D] rounded-full py-1 pl-1 pr-3">
          <span className="w-[13px] h-[13px] rounded-full bg-[#FCFCFC] border-[0.5px] border-[#37354E] flex items-center justify-center text-[8px] font-medium text-[#37354E]">
            $
          </span>
          <span className="font-serif text-xs text-[#37354E]">
            2500 Earn
          </span>
        </div>
      </div>
    </div>
  );
};

export default AffiliateProgram;
