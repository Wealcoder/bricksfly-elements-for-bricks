import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Button } from "../ui/button";
import ElementCard from "../shared/ElementCard";
import React, { useEffect, useState } from "react";
import { Switch } from "../ui/switch";
import { Label } from "../ui/label";
import {
  Accordion,
  AccordionContent,
  AccordionItem,
  AccordionTrigger,
} from "@/components/ui/accordion2";
import { Dot } from "lucide-react";
import { Badge } from "../ui/badge";
import {
  deviceMediaMatch,
  filterGeneralExtension,
  filterGsapExtension,
  isEqual,
} from "@/lib/utils";
import {
  useActiveItem,
  useExtensions,
  useNotification,
} from "@/hooks/app.hooks";
import ExtensionGsapSettings from "./ExtensionGsapSettings";
import { ExtensionSettingConfig } from "@/config/extensionSettingConfig";
import { toast } from "sonner";

const ShowExtensions = ({
  filterKey,
  tabParam,
  pluginIdParam,
  setExtensionCount,
}) => {
  const exSettings = ExtensionSettingConfig;
  const { allExtensions } = useExtensions();
  const { updateNotice } = useNotification();
  const {
    updateActiveGeneralExtension,
    updateActiveGeneralGroupExtension,
    updateActiveGeneralAllExtension,
    updateActiveGsapExtension,
    updateActiveGsapGroupExtension,
    updateActiveGsapAllExtension,
  } = useActiveItem();

  const [filteredGsapExtensions, setFilteredGsapExtensions] = useState(
    allExtensions.elements["gsap-extensions"],
  );
  const [filteredGeneralExtensions, setFilteredGeneralExtensions] = useState(
    allExtensions.elements["general-extensions"],
  );
  const [openAccordion, setOpenAccordion] = useState([]);
  const [openGeneralAccordion, setOpenGeneralAccordion] = useState([]);
  const [tabValue, setTabValue] = useState("gsap");

  useEffect(() => {
    if (filterKey) {
      const gsapResult = filterGsapExtension(
        allExtensions.elements["gsap-extensions"],
        filterKey,
      );

      setFilteredGsapExtensions(gsapResult);
      const generalResult = filterGeneralExtension(
        allExtensions.elements["general-extensions"],
        filterKey,
      );
      setFilteredGeneralExtensions(generalResult);
    }
  }, [filterKey, allExtensions]);

  useEffect(() => {
    if (tabParam) {
      setTabValue(tabParam);
    }
  }, [tabParam]);

  useEffect(() => {
    if (pluginIdParam) {
      setOpenAccordion(pluginIdParam);
    }
  }, [pluginIdParam]);

  const saveExtension = async () => {
    const isChanged = isEqual(
      allExtensions,
      JSON.parse(JSON.stringify(AAB_ADDONS_ADMIN?.addons_config?.extensions)) ||
        {},
    );

    if (isChanged && Object.keys(isChanged).length) {
      const date = new Date();
      const utcDate = date.toISOString();

      const sampleData = {
        type: "notice",
        title: "Extensions Activity Log ",
        description:
          "Your extension settings have been successfully updated in the following time period.",
        date: utcDate,
      };

      updateNotice(sampleData);
    }

    await fetch(AAB_ADDONS_ADMIN.ajaxurl, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
        Accept: "application/json",
      },

      body: new URLSearchParams({
        action: "thebrbre_save_settings",
        fields: JSON.stringify(allExtensions),
        nonce: AAB_ADDONS_ADMIN.nonce,
        settings: "thebrbre_save_extensions",
      }),
    })
      .then((response) => {
        return response.json();
      })
      .then((return_content) => {
        setExtensionCount((prev) => ({
          ...prev,
          active: return_content.total,
        }));
        toast.success("Save Successful", {
          position: "top-right",
        });
      });
  };

  return (
    <Tabs value={tabValue} onValueChange={setTabValue}>
      <div className="flex justify-between items-center">
        <TabsList className="gap-1 h-11">
          <TabsTrigger value={"gsap"}>GSAP Extensions</TabsTrigger>
          <TabsTrigger value={"general"}>General Extensions</TabsTrigger>
        </TabsList>
        <div className="flex gap-2.5 items-center justify-end">
          {/* <Button variant="secondary">Reset</Button> */}
          <Button onClick={() => saveExtension()}>Save Settings</Button>
        </div>
      </div>
      <TabsContent
        value={`gsap`}
        className="bg-background-secondary p-2.5 rounded-lg"
      >
        <div>
          <div className="bg-background flex justify-between items-center p-5 rounded">
            <h3 className="text-base font-medium">
              {filteredGsapExtensions?.title}
            </h3>
            <div className="flex items-center space-x-2">
              <Switch
                id={`enable-gsap`}
                checked={filteredGsapExtensions?.is_active}
                onCheckedChange={(value) =>
                  updateActiveGsapAllExtension({ value })
                }
              />
              <Label htmlFor={`enable-gsap`}>Enable All</Label>
            </div>
          </div>

          <Accordion
            type="multiple"
            value={openAccordion}
            onValueChange={(value) => setOpenAccordion(value)}
            className="w-full mt-2 space-y-1.5"
          >
            {Object.keys(filteredGsapExtensions?.elements)?.map((extension) => (
              <AccordionItem key={extension} value={extension}>
                <div className="p-[2px]">
                  <div className="flex items-center bg-background justify-between gap-3 py-3 px-4">
                    <AccordionTrigger className="rounded cursor-pointer w-full">
                      <div className="flex flex-col gap-1">
                        <div className="text-[15px] leading-6 font-medium flex items-center">
                          {filteredGsapExtensions?.elements[extension].title}
                          {filteredGsapExtensions?.elements[extension]
                            ?.is_pro ? (
                            <>
                              <Dot
                                className="w-3.5 h-3.5 text-icon-secondary"
                                strokeWidth={2}
                              />
                              <Badge variant="pro">PRO</Badge>
                            </>
                          ) : (
                            ""
                          )}
                        </div>
                      </div>
                    </AccordionTrigger>
                    <div className="flex gap-1 items-center">
                      {Object.keys(
                        filteredGsapExtensions?.elements[extension]?.elements,
                      )?.length ? (
                        ""
                      ) : (
                        <>
                          <Badge
                            variant="pro"
                            className="px-2.5 py-1.5 h-7 bg-[linear-gradient(180deg,hsl(var(--brand-500))_0%,hsl(var(--brand-600))_100%)] mr-1"
                          >
                            COMING SOON!
                          </Badge>
                        </>
                      )}

                      <ExtensionGsapSettings
                        extension={filteredGsapExtensions?.elements[extension]}
                        contentSx={{
                          width: "440px",
                          maxWidth: "440px",
                          borderRadius: "16px",
                          ...exSettings?.find((item) => item.key === extension)
                            ?.dialogStyle,
                        }}
                      >
                        {
                          exSettings?.find((item) => item.key === extension)
                            ?.component
                        }
                      </ExtensionGsapSettings>

                      <Switch
                        checked={
                          filteredGsapExtensions?.elements[extension]?.is_active
                        }
                        onCheckedChange={(value) => {
                          value
                            ? setOpenAccordion((prev) => [...prev, extension])
                            : setOpenAccordion((prev) =>
                                prev?.filter((el) => el !== extension),
                              );
                          updateActiveGsapGroupExtension({
                            value,
                            slug: extension,
                          });
                        }}
                        disabled={
                          !Object.keys(
                            filteredGsapExtensions?.elements[extension]
                              ?.elements,
                          )?.length
                        }
                      />
                    </div>
                  </div>
                </div>
                <AccordionContent>
                  <div className="grid grid-cols-2 xl:grid-cols-3 gap-1 mt-1 p-[2px]">
                    {Object.keys(
                      filteredGsapExtensions?.elements[extension]?.elements,
                    )?.length ? (
                      <>
                        {Object.keys(
                          filteredGsapExtensions?.elements[extension]?.elements,
                        )?.map((content, i) => (
                          <React.Fragment key={`tab_content-${i}`}>
                            <ElementCard
                              element={
                                filteredGsapExtensions?.elements[extension]
                                  ?.elements[content]
                              }
                              slug={content}
                              updateActiveItem={updateActiveGsapExtension}
                              isDisable={
                                !filteredGsapExtensions?.elements[extension]
                                  ?.is_active
                              }
                              className="rounded p-5"
                            />
                          </React.Fragment>
                        ))}
                        {Array.from({
                          length:
                            deviceMediaMatch() -
                            (Object.keys(
                              filteredGsapExtensions?.elements[extension]
                                ?.elements,
                            )?.length %
                              deviceMediaMatch() ===
                            0
                              ? deviceMediaMatch()
                              : Object.keys(
                                  filteredGsapExtensions?.elements[extension]
                                    ?.elements,
                                )?.length % deviceMediaMatch()),
                        }).map((_, index) => (
                          <ElementCard
                            key={`tab_content_empty-${index}`}
                            className="rounded"
                          />
                        ))}
                      </>
                    ) : (
                      <div className="col-span-3 px-4 py-[15px] bg-background rounded-lg  box-border">
                        <p className="text-center">Coming soon...</p>
                      </div>
                    )}
                  </div>
                </AccordionContent>
              </AccordionItem>
            ))}
          </Accordion>
        </div>
      </TabsContent>
      <TabsContent
        value={`general`}
        className="bg-background-secondary p-2.5 rounded-lg"
      >
        <div>
          <div className="bg-background flex justify-between items-center p-5 rounded">
            <h3 className="text-base font-medium">
              {filteredGeneralExtensions?.title}
            </h3>
            <div className="flex items-center space-x-2">
              <Switch
                id={`enable-general`}
                checked={filteredGeneralExtensions?.is_active}
                onCheckedChange={(value) =>
                  updateActiveGeneralAllExtension({ value })
                }
              />
              <Label htmlFor={`enable-general`}>Enable All</Label>
            </div>
          </div>

          <Accordion
            type="multiple"
            value={openGeneralAccordion}
            onValueChange={(value) => setOpenGeneralAccordion(value)}
            className="w-full mt-2 space-y-1.5"
          >
            {Object.keys(filteredGeneralExtensions?.elements)?.map(
              (extension) => (
                <AccordionItem key={extension} value={extension}>
                  <div className="p-[2px]">
                    <div className="flex items-center bg-background justify-between gap-3 py-3 px-4">
                      <AccordionTrigger className="rounded cursor-pointer w-full">
                        <div className="flex flex-col gap-1">
                          <div className="text-[15px] leading-6 font-medium flex items-center">
                            {
                              filteredGeneralExtensions?.elements[extension]
                                .title
                            }
                            {filteredGeneralExtensions?.elements[extension]
                              ?.is_pro ? (
                              <>
                                <Dot
                                  className="w-3.5 h-3.5 text-icon-secondary"
                                  strokeWidth={2}
                                />
                                <Badge variant="pro">PRO</Badge>
                              </>
                            ) : (
                              ""
                            )}
                          </div>
                        </div>
                      </AccordionTrigger>
                      <div className="flex gap-1 items-center">
                        {Object.keys(
                          filteredGeneralExtensions?.elements[extension]
                            ?.elements,
                        )?.length ? (
                          ""
                        ) : (
                          <>
                            <Badge
                              variant="pro"
                              className="px-2.5 py-1.5 h-7 bg-[linear-gradient(180deg,hsl(var(--brand-500))_0%,hsl(var(--brand-600))_100%)] mr-1"
                            >
                              COMING SOON!
                            </Badge>
                          </>
                        )}

                        <ExtensionGsapSettings
                          extension={
                            filteredGeneralExtensions?.elements[extension]
                          }
                          contentSx={{
                            width: "440px",
                            maxWidth: "440px",
                            borderRadius: "16px",
                            ...exSettings?.find(
                              (item) => item.key === extension,
                            )?.dialogStyle,
                          }}
                        >
                          {
                            exSettings?.find((item) => item.key === extension)
                              ?.component
                          }
                        </ExtensionGsapSettings>

                        <Switch
                          checked={
                            filteredGeneralExtensions?.elements[extension]
                              ?.is_active
                          }
                          onCheckedChange={(value) => {
                            value
                              ? setOpenGeneralAccordion((prev) => [
                                  ...prev,
                                  extension,
                                ])
                              : setOpenGeneralAccordion((prev) =>
                                  prev?.filter((el) => el !== extension),
                                );
                            updateActiveGeneralGroupExtension({
                              value,
                              slug: extension,
                            });
                          }}
                          disabled={
                            !Object.keys(
                              filteredGeneralExtensions?.elements[extension]
                                ?.elements,
                            )?.length
                          }
                        />
                      </div>
                    </div>
                  </div>
                  <AccordionContent>
                    <div className="grid grid-cols-2 xl:grid-cols-3 gap-1 mt-1 p-[2px]">
                      {Object.keys(
                        filteredGeneralExtensions?.elements[extension]
                          ?.elements,
                      )?.length ? (
                        <>
                          {Object.keys(
                            filteredGeneralExtensions?.elements[extension]
                              ?.elements,
                          )?.map((content, i) => (
                            <React.Fragment key={`tab_content-${i}`}>
                              <ElementCard
                                element={
                                  filteredGeneralExtensions?.elements[extension]
                                    ?.elements[content]
                                }
                                slug={content}
                                updateActiveItem={updateActiveGeneralExtension}
                                isDisable={
                                  !filteredGeneralExtensions?.elements[
                                    extension
                                  ]?.is_active
                                }
                                className="rounded p-5"
                              />
                            </React.Fragment>
                          ))}
                          {Array.from({
                            length:
                              deviceMediaMatch() -
                              (Object.keys(
                                filteredGeneralExtensions?.elements[extension]
                                  ?.elements,
                              )?.length %
                                deviceMediaMatch() ===
                              0
                                ? deviceMediaMatch()
                                : Object.keys(
                                    filteredGeneralExtensions?.elements[
                                      extension
                                    ]?.elements,
                                  )?.length % deviceMediaMatch()),
                          }).map((_, index) => (
                            <ElementCard
                              key={`tab_content_empty-${index}`}
                              className="rounded"
                            />
                          ))}
                        </>
                      ) : (
                        <div className="col-span-3 px-4 py-[15px] bg-background rounded-lg  box-border">
                          <p className="text-center">Coming soon...</p>
                        </div>
                      )}
                    </div>
                  </AccordionContent>
                </AccordionItem>
              ),
            )}
          </Accordion>
        </div>
      </TabsContent>
    </Tabs>
  );
};

export default ShowExtensions;
