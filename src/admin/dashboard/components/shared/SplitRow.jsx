import { cn } from "@/lib/utils";

const SplitRow = ({ columns = [50, 50], gap = "gap-3", className, children }) => {
  const template = columns.map((col) => `${col}fr`).join(" ");

  return (
    <div
      className={cn(
        "grid grid-cols-1 lg:[grid-template-columns:var(--split-cols)]",
        gap,
        className,
      )}
      style={{ "--split-cols": template }}
    >
      {children}
    </div>
  );
};

export default SplitRow;
