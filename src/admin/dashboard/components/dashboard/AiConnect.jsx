import { cn } from "@/lib/utils";
import { RiArrowRightLine, RiSparkling2Fill } from "react-icons/ri";
import { buttonVariants } from "../ui/button";

// "Build with AI" strip on the dashboard: the one way into the
// Connect your AI screen. The data comes from the `bricksfly_dashboard_ai_connect`
// filter (BricksFly Pro fills it for administrators): { url, connected, used }.
// Without it the strip is not shown.
const ai = BRICKSFLY_ADDONS_ADMIN.ai_connect;

const AiConnect = () => {
  if (!ai || !ai.url) {
    return null;
  }

  const connected = !!ai.connected;

  return (
    <div className="flex flex-wrap items-center gap-x-6 gap-y-4 border rounded-2xl p-5 shadow-common bg-[linear-gradient(100deg,#F4F0FF_0%,#FFFFFF_60%)]">
      <div className="flex items-center gap-4 flex-1 min-w-[260px]">
        <div className="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#6D4AFF] text-white">
          <RiSparkling2Fill size={22} />
        </div>
        <div className="min-w-0">
          <div className="flex flex-wrap items-center gap-2">
            <p className="font-medium text-text">Build with AI</p>
            {connected ? (
              <span className="inline-flex items-center gap-1.5 rounded-md bg-[#E0FAEC] px-2 py-0.5 text-xs font-medium text-[#1A7544]">
                <span className="h-1.5 w-1.5 rounded-full bg-[#1A7544]" />
                Connected
              </span>
            ) : (
              <span className="rounded-md bg-[#EBF1FF] px-2 py-0.5 text-xs font-medium text-[#335CFF]">
                New
              </span>
            )}
          </div>
          <p className="text-sm text-text-secondary mt-1">
            {connected
              ? `Claude or ChatGPT can build on this site.${ai.used ? ` Last used ${ai.used}.` : " Not used yet."}`
              : "Connect Claude or ChatGPT with one link. Pick BricksFly websites, pages and sections in the chat, and the AI builds them here."}
          </p>
        </div>
      </div>
      <a
        href={ai.url}
        className={cn(
          buttonVariants({ variant: connected ? "secondary" : "default" }),
          "shrink-0",
        )}
      >
        {connected ? "Manage connection" : "Connect your AI"}
        <RiArrowRightLine size={18} className="rtl:rotate-180" />
      </a>
    </div>
  );
};

export default AiConnect;
