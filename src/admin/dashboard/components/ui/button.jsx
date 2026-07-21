import * as React from "react";
import { Slot } from "@radix-ui/react-slot";
import { cva } from "class-variance-authority";

import { cn } from "@/lib/utils";

const buttonVariants = cva(
  "inline-flex items-center justify-center whitespace-nowrap rounded-[10px] text-sm font-medium transition-colors focus-visible:outline-none disabled:pointer-events-none disabled:opacity-50 shadow-common border-0 cursor-pointer",
  {
    variants: {
      variant: {
        default:
          "bg-button text-button-text-secondary hover:bg-button-hover hover:text-button-text-secondary-hover",
        general:
          "bg-white text-[#2B303B] hover:text-button-text-secondary-hover hover:shadow-general-btn",
        secondary:
          "bg-button-secondary text-button-text-secondary hover:bg-button-secondary-hover hover:text-button-text-secondary-hover border hover:border-button-secondary-hover hover:shadow-none",
        pgActive:
          "bg-button-secondary-hover text-button-text-secondary-hover border border-button-secondary-hover shadow-none",
        link: "text-text hover:text-text-hover shadow-none bg-transparent",
        pro: "bg-[#FFD53E] text-button-text-secondary hover:bg-[#FFD53E] hover:text-button-text-secondary hover:shadow-pro",
      },
      size: {
        default: "h-10 px-3 py-2.5",
        sm: "h-8 rounded-lg px-[10px] text-sm",
        lg: "h-10 rounded-md px-8",
        icon: "h-10 w-10",
      },
    },
    defaultVariants: {
      variant: "default",
      size: "default",
    },
  }
);

const Button = React.forwardRef(
  ({ className, variant, size, asChild = false, ...props }, ref) => {
    const Comp = asChild ? Slot : "button";
    return (
      <Comp
        className={cn(buttonVariants({ variant, size, className }))}
        ref={ref}
        {...props}
      />
    );
  }
);
Button.displayName = "Button";

export { Button, buttonVariants };
