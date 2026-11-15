import { useEffect, useRef, useState } from "react";
import { createPortal } from "react-dom";
import { cn } from "@/lib/utils";
import ProCardButton from "@/components/template/ProCardButton";

// Full-screen preview of a page's demo inside the library, like the section
// library's preview: Back, title, previous / next page, device switcher,
// Open in new tab and the same Import / Pro action as the card.
const DEVICES = [
  {
    key: "desktop",
    label: "Desktop",
    width: "100%",
    icon: (
      <svg viewBox="0 0 24 24" aria-hidden="true" className="w-[18px] h-[18px]">
        <path d="M3 5h18v11H3zM8 20h8M12 16v4" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" />
      </svg>
    ),
  },
  {
    key: "tablet",
    label: "Tablet",
    width: "820px",
    icon: (
      <svg viewBox="0 0 24 24" aria-hidden="true" className="w-[18px] h-[18px]">
        <rect x="5" y="3" width="14" height="18" rx="2" fill="none" stroke="currentColor" strokeWidth="1.8" />
        <path d="M11 18h2" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" />
      </svg>
    ),
  },
  {
    key: "mobile",
    label: "Mobile",
    width: "390px",
    icon: (
      <svg viewBox="0 0 24 24" aria-hidden="true" className="w-[18px] h-[18px]">
        <rect x="7" y="3" width="10" height="18" rx="2" fill="none" stroke="currentColor" strokeWidth="1.8" />
        <path d="M11 18h2" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" />
      </svg>
    ),
  },
];

const stepBtn =
  "inline-flex items-center justify-center w-[30px] h-[30px] p-0 rounded-md border border-white/25 bg-transparent text-[#cfd0d6] cursor-pointer transition-colors hover:border-[#FFD53E] hover:text-[#FFD53E] focus-visible:outline-none focus-visible:border-[#FFD53E] disabled:opacity-35 disabled:cursor-default disabled:hover:border-white/25 disabled:hover:text-[#cfd0d6]";

const PagePreview = ({
  templates,
  index,
  onClose,
  onStep,
  onImport,
  canImport,
}) => {
  const template = templates[index];
  const [device, setDevice] = useState("desktop");
  const [loading, setLoading] = useState(true);
  const [slow, setSlow] = useState(false);
  const timer = useRef(0);
  const url = template?.demo_link || "";

  // Keyboard: Esc closes, arrows move between pages. Lock the page scroll while open.
  useEffect(() => {
    const onKey = (e) => {
      if (e.key === "Escape") {
        e.preventDefault();
        onClose();
      } else if (e.key === "ArrowLeft" || e.key === "ArrowRight") {
        e.preventDefault();
        onStep(e.key === "ArrowRight" ? 1 : -1);
      }
    };
    document.addEventListener("keydown", onKey, true);
    const prev = document.body.style.overflow;
    document.body.style.overflow = "hidden";
    return () => {
      document.removeEventListener("keydown", onKey, true);
      document.body.style.overflow = prev;
    };
  }, [onClose, onStep]);

  // New page: show the spinner again, and a hint if the demo is slow or blocks framing.
  useEffect(() => {
    setLoading(true);
    setSlow(false);
    clearTimeout(timer.current);
    timer.current = setTimeout(() => setSlow(true), 10000);
    return () => clearTimeout(timer.current);
  }, [url]);

  if (!template || !url) return null;

  const dev = DEVICES.find((d) => d.key === device) || DEVICES[0];
  const framed = dev.key !== "desktop";

  return createPortal(
    <div
      className="fixed inset-0 z-[100055] flex flex-col bg-[#e9eaee]"
      role="dialog"
      aria-modal="true"
      aria-label={`Preview: ${template.title}`}
      data-bf-page-preview
    >
      <div className="flex items-center gap-4 shrink-0 px-4 py-2.5 bg-[#1c1c21] text-white flex-wrap md:flex-nowrap">
        <button
          type="button"
          onClick={onClose}
          className="inline-flex items-center gap-1.5 shrink-0 px-3.5 py-2 rounded-md border border-white/25 bg-transparent text-white text-[13px] font-medium cursor-pointer transition-colors hover:border-[#FFD53E] hover:text-[#FFD53E] focus-visible:outline-none focus-visible:border-[#FFD53E]"
        >
          <span aria-hidden="true">&larr;</span> Back to library
        </button>

        <span className="flex-1 min-w-0 truncate text-sm font-semibold" title={template.title}>
          {template.title}
        </span>

        <span className="inline-flex items-center gap-1.5 shrink-0 text-[#cfd0d6] text-xs">
          <button
            type="button"
            className={stepBtn}
            onClick={() => onStep(-1)}
            disabled={index <= 0}
            aria-label="Previous page"
            title="Previous page"
          >
            <svg viewBox="0 0 24 24" aria-hidden="true" className="w-[18px] h-[18px]">
              <path d="M15 5l-7 7 7 7" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round" />
            </svg>
          </button>
          <span className="min-w-[48px] text-center tabular-nums">
            {index + 1} / {templates.length}
          </span>
          <button
            type="button"
            className={stepBtn}
            onClick={() => onStep(1)}
            disabled={index >= templates.length - 1}
            aria-label="Next page"
            title="Next page"
          >
            <svg viewBox="0 0 24 24" aria-hidden="true" className="w-[18px] h-[18px]">
              <path d="M9 5l7 7-7 7" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round" />
            </svg>
          </button>
        </span>

        <span className="hidden md:inline-flex shrink-0 gap-0.5 p-[3px] rounded-lg bg-white/10" role="group" aria-label="Preview width">
          {DEVICES.map((d) => (
            <button
              key={d.key}
              type="button"
              title={d.label}
              aria-label={d.label}
              aria-pressed={d.key === dev.key}
              onClick={() => setDevice(d.key)}
              data-device={d.key}
              className={cn(
                "inline-flex items-center justify-center w-[34px] h-7 p-0 border-0 rounded-md cursor-pointer transition-colors",
                d.key === dev.key ? "bg-[#FFD53E] text-[#1c1c21]" : "bg-transparent text-[#cfd0d6] hover:text-white"
              )}
            >
              {d.icon}
            </button>
          ))}
        </span>

        <span className="inline-flex items-center gap-3.5 shrink-0">
          <a
            href={url}
            target="_blank"
            rel="noopener noreferrer"
            className="hidden lg:inline text-[#cfd0d6] text-[13px] whitespace-nowrap no-underline hover:text-white hover:underline"
          >
            Open in new tab &#8599;
          </a>
          {canImport ? (
            <button
              type="button"
              onClick={() => onImport(template)}
              className="inline-flex items-center gap-1 h-8 px-4 rounded-full bg-[#FFD53E] hover:bg-[#E8C238] text-[#525866] text-xs font-semibold cursor-pointer border-0 transition-colors"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M14 11.332V12.6654C14 13.4018 13.4031 13.9987 12.6667 13.9987H3.33333C2.59696 13.9987 2 13.4018 2 12.6654V11.332" stroke="#525866" strokeLinecap="square" />
                <path d="M5 7.66667L8.00003 10.6667L11 7.66667M8.00003 10V2" stroke="#525866" strokeLinecap="square" />
              </svg>
              Import
            </button>
          ) : (
            <ProCardButton className="h-8 px-4 bg-[#FFD53E] hover:bg-[#E8C238] border-0 text-[#525866] rounded-full text-xs font-semibold gap-1" />
          )}
        </span>
      </div>

      <div className="relative flex justify-center flex-1 min-h-0 overflow-auto">
        {loading && (
          <span
            className="absolute top-1/2 left-1/2 z-[2] -mt-3.5 -ml-3.5 w-7 h-7 rounded-full border-[3px] border-[#1c1c21]/15 border-t-[#1c1c21] animate-spin"
            aria-hidden="true"
          />
        )}
        {loading && slow && (
          <div className="absolute bottom-4 left-1/2 z-[3] -translate-x-1/2 px-4 py-2.5 rounded-lg bg-[#1c1c21] text-white text-[13px] shadow-[0_6px_20px_rgba(15,15,18,0.3)]">
            Taking long? Some sites block previews.
            <a href={url} target="_blank" rel="noopener noreferrer" className="ml-1.5 text-[#FFD53E]">
              Open in new tab
            </a>
          </div>
        )}
        <div
          className={cn(
            "relative z-[1] max-w-full h-full bg-white transition-[width] duration-200 ease-out",
            framed && "my-4 h-[calc(100%-32px)] rounded-[10px] overflow-hidden shadow-[0_8px_30px_rgba(15,15,18,0.25)]"
          )}
          style={{ width: dev.width }}
          data-device-holder
        >
          <iframe
            key={url}
            src={url}
            title={template.title}
            referrerPolicy="no-referrer"
            onLoad={() => {
              clearTimeout(timer.current);
              setLoading(false);
              setSlow(false);
            }}
            className="block w-full h-full border-0 bg-white"
          />
        </div>
      </div>
    </div>,
    document.body
  );
};

export default PagePreview;
