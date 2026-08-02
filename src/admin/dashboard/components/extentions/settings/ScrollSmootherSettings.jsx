import { useForm } from "react-hook-form";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Switch } from "@/components/ui/switch";
import { toast } from "sonner";
import { DesktopIcon } from "@radix-ui/react-icons";

import { Button } from "@/components/ui/button";
import {
  Form,
  FormControl,
  FormDescription,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@/components/ui/form";
import { Input } from "@/components/ui/input";
import { Checkbox } from "@/components/ui/checkbox";
import { DialogClose } from "@/components/ui/dialog";
import { z } from "zod";
import { zodResolver } from "@hookform/resolvers/zod";
import { useMemo, useRef, useState } from "react";
import { Badge } from "@/components/ui/badge";
import {
  LaptopIcon,
  MonitorIcon,
  SmartphoneIcon,
  TabletIcon,
} from "lucide-react";

const iconFor = (icon) => {
  switch (icon) {
    case "laptop":
      return MonitorIcon;
    case "tablet-portrait":
    case "tablet-landscape":
      return TabletIcon;
    case "phone-portrait":
    case "phone-landscape":
      return SmartphoneIcon;
    default:
      return LaptopIcon;
  }
};

const deviceSchema = z.object({
  enabled: z.boolean(),
  smotherLevel: z.coerce
    .number({
      invalid_type_error: "Smooth must be a number",
    })
    .optional(),
});

// Breakpoint keys are dynamic (defaults + custom), so use a record schema.
const FormSchema = z.record(z.string(), deviceSchema);

// const FormSchema = z.object({
//   smooth: z.coerce
//     .number({
//       invalid_type_error: "Smooth must be a number",
//     })
//     .optional(),
//   mobile: z.boolean().optional(),
//   disableMode: z.boolean().optional(),
//   media: z.string().regex(/^(?:\d+px|min-width:\s?\d+px|max-width:\s?\d+px)$/, {
//     message:
//       "Invalid format. Use '900px', 'min-width: 800px', or 'max-width: 1024px'.",
//   }),
// });

const ScrollSmootherSettings = () => {
  const dialogCloseRef = useRef(null);

  // Source of truth: PHP-side ResponsiveHelper::getBreakpoints() (live Bricks
  // breakpoints when active, shared no-Bricks fallback otherwise). Trust the
  // localized payload; the helper guarantees a populated, label/icon-equipped
  // list, so we don't duplicate a parallel fallback here.
  const breakpoints = useMemo(() => {
    const bp = BRICKSFLY_ADDONS_ADMIN?.breakpoints;
    return Array.isArray(bp) ? bp : [];
  }, []);

  // Per-breakpoint defaults: enabled only on the base breakpoint.
  const defaultValues = useMemo(
    () =>
      breakpoints.reduce((acc, bp) => {
        acc[bp.key] = { enabled: !!bp.base, smotherLevel: "1.35" };
        return acc;
      }, {}),
    [breakpoints],
  );

  const initialValues = useMemo(() => {
    const persisted = BRICKSFLY_ADDONS_ADMIN?.smoothScroller || {};
    return breakpoints.reduce((acc, bp) => {
      acc[bp.key] = { ...defaultValues[bp.key], ...(persisted[bp.key] || {}) };
      return acc;
    }, {});
  }, [breakpoints, defaultValues]);

  const [tabValue, setTabValue] = useState(breakpoints[0]?.key);

  const form = useForm({
    resolver: zodResolver(FormSchema),
    defaultValues: initialValues,
  });

  const convertToMinWidth = (value) => {
    if (/^\d+px$/.test(value)) {
      return `min-width: ${value}`;
    }
    return value;
  };

  async function onSubmit(formData) {
    // const convertedMedia = convertToMinWidth(data.media);

    // console.log(formData);
    // return null;

    if (!BRICKSFLY_ADDONS_ADMIN.nonce || !BRICKSFLY_ADDONS_ADMIN.ajaxurl) return null;

    await fetch(`${BRICKSFLY_ADDONS_ADMIN.ajaxurl}`, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
        Accept: "application/json",
      },
      credentials: "same-origin",
      body: new URLSearchParams({
        action: "bricksfly_save_smooth_scroller_settings",
        smooth: JSON.stringify(formData),
        nonce: BRICKSFLY_ADDONS_ADMIN.nonce,
      }),
    })
      .then((response) => {
        return response.json();
      })
      .then((return_content) => {
        BRICKSFLY_ADDONS_ADMIN.smoothScroller = JSON.parse(return_content);
        if (dialogCloseRef.current) {
          dialogCloseRef.current.click();
        }

        toast.success("Save Successful", {
          position: "top-right",
        });
      });
  }

  const resetHandler = async () => {
    await onSubmit(defaultValues);
    form.reset({ ...defaultValues });
  };

  return (
    <div className="py-4 px-6 pb-7">
      <div className="flex items-center gap-2 mt-2">
        <img
          // src={`${BRICKSFLY_ADDONS_ADMIN.root_url}public/images/extensions/scroll_smother.png`}
          src={`${BRICKSFLY_ADDONS_ADMIN.plugin_url}public/images/extensions/scroll_smother.png`}
          alt="logo"
          className="w-[65px] h-[65px]"
        />

        <div>
          <h2 className=" flex items-center gap-2">
            <span className="text-[20px] font-medium text-[var(--900,#181B25)]">
              {" "}
              Scroll Smoother
            </span>
            <Badge
              className="bg-[linear-gradient(109deg,#ffab472e_0%,#ffab472e_100%)] text-[#717784]"
              variant="pro"
            >
              PRO
            </Badge>
          </h2>
          <p className="text-sm text-text-secondary mt-2">
            Enter Smooth Scroller value below
          </p>
        </div>
      </div>

      <div className="mt-7">
        <Tabs value={tabValue} onValueChange={setTabValue}>
          <div className="flex justify-between items-center">
            <TabsList className="gap-1 h-11">
              {breakpoints.map((bp) => {
                const TabIcon = iconFor(bp.icon);

                return (
                  <TabsTrigger
                    key={bp.key}
                    value={bp.key}
                    className="data-[state=active]:bg-[#E1E4EA] bg-[#F5F7FA]"
                    sx={{ boxShadow: "none" }}
                  >
                    <TabIcon
                      size={16}
                      color={tabValue === bp.key ? "#181B25" : "#525866"}
                    />

                    <span
                      style={{
                        color: tabValue === bp.key ? "#181B25" : "#525866",
                      }}
                      className="text-[12px] ml-1"
                    >
                      {bp.label}
                    </span>
                  </TabsTrigger>
                );
              })}
            </TabsList>
          </div>

          <Form {...form}>
            <form onSubmit={form.handleSubmit(onSubmit)}>
              {breakpoints.map((bp) => (
                <TabsContent
                  key={bp.key}
                  value={bp.key}
                  className="bg-background p-4 rounded-lg mt-0"
                >
                  <div className="mt-3">
                    <FormField
                      control={form.control}
                      name={`${bp.key}.enabled`}
                      render={({ field }) => (
                        <FormItem className="flex flex-row items-center gap-3">
                          <FormLabel className="min-w-[135px]">
                            {" "}
                            Enable On {bp.label}
                          </FormLabel>
                          <FormControl>
                            <Switch
                              checked={field.value}
                              onCheckedChange={field.onChange}
                              sx={{ marginTop: "0" }}
                            />
                          </FormControl>
                        </FormItem>
                      )}
                    />
                  </div>

                  <div className="mt-5 max-w-[180px]">
                    <FormField
                      control={form.control}
                      name={`${bp.key}.smotherLevel`}
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel className="text-[12px] text-[var(--600,#525866)] mb-2">
                            Set the scroll smoother level
                          </FormLabel>
                          <FormControl>
                            <Input
                              type="number"
                              step="0.01"
                              {...field}
                              value={field.value ?? ""}
                              onChange={(e) => {
                                const value = e.target.value;

                                field.onChange(
                                  value !== "" ? parseFloat(value) : "",
                                );

                                // field.onChange(
                                //   value === "" ? undefined : Number(value)
                                // );
                              }}
                            />
                          </FormControl>
                        </FormItem>
                      )}
                    />
                  </div>
                </TabsContent>
              ))}

              <div className="flex gap-2.5 items-center mt-9">
                <Button
                  className="p-[20px] rounded-[8px]"
                  variant="secondary"
                  onClick={resetHandler}
                >
                  Reset
                </Button>
                <Button className="p-[20px] rounded-[8px]" type="submit">
                  {" "}
                  Save Settings{" "}
                </Button>
              </div>
            </form>
          </Form>
        </Tabs>
      </div>
    </div>
  );
};

export default ScrollSmootherSettings;
