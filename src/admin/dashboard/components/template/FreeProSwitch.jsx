import { cn } from "@/lib/utils";

// Free / Pro switch shown in the template toolbars. "all" lists every item
// with free ones first; "free" and "premium" (Pro) filter the list.
const OPTIONS = [
  { value: "all", label: "All" },
  { value: "free", label: "Free" },
  { value: "premium", label: "Pro" },
];

const FreeProSwitch = ({ value, onChange }) => {
  const current = value || "all";

  return (
    <div
      role="radiogroup"
      aria-label="Free or Pro templates"
      className="flex items-center p-1 gap-1 bg-[#F4F4F4] rounded-full"
    >
      {OPTIONS.map((option) => {
        const active = current === option.value;
        return (
          <button
            key={option.value}
            type="button"
            role="radio"
            aria-checked={active}
            onClick={() => onChange(option.value)}
            className={cn(
              "h-8 px-4 rounded-full text-sm font-medium transition-colors",
              active
                ? option.value === "free"
                  ? "bg-[#07B22B] text-white"
                  : option.value === "premium"
                    ? "bg-[#FFD53E] text-[#18181A]"
                    : "bg-white text-[#18181A] shadow-sm"
                : "text-[#525866] hover:text-[#18181A]",
            )}
          >
            {option.label}
          </button>
        );
      })}
    </div>
  );
};

export default FreeProSwitch;
