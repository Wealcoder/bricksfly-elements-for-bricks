import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Button } from "../ui/button";
import ElementCard from "../shared/ElementCard";
import React, { useEffect, useState } from "react";
import { Switch } from "../ui/switch";
import { Label } from "../ui/label";
import { deviceMediaMatch, filterElements, isEqual } from "@/lib/utils";
import { useActiveItem, useNotification, useElements } from "@/hooks/app.hooks";
import { toast } from "sonner";
import { ScrollArea, ScrollBar } from "../ui/scroll-area";

const ShowElements = ({
  searchKey,
  filterKey,
  searchParam,
  urlParams,
  setElementCount,
  settingOpen,
}) => {
  const { allElements } = useElements();
  const { updateNotice } = useNotification();
  const { updateActiveElement, updateActiveGroupElement } = useActiveItem();

  const [tabValue, setTabValue] = useState("all");
  const [catElements, setCatElements] = useState({});
  const [norResult, setNoResult] = useState(false);

  const [elementTabList, setElementTabList] = useState([]);

  useEffect(() => {
    if (allElements) {
      const result = [];
      for (let el in allElements.elements) {
        let data = {
          title: allElements.elements[el].title?.replace("Widgets", ""),
          value: el,
        };
        result.push(data);
      }

      setElementTabList(result);
    }
  }, [allElements]);

  useEffect(() => {
    if (allElements) {
      if (searchKey) {
        const searchResult = findSearchResult();
        if (!(searchResult && Object.keys(searchResult).length)) {
          setNoResult(true);
        } else {
          setNoResult(false);
        }
        const result = filterElements(searchResult, filterKey);
        setCatElements(result);
      } else {
        setNoResult(false);
        const result = filterElements(allElements.elements, filterKey);
        setCatElements(result);
      }
    }
  }, [allElements, filterKey, searchKey]);

  useEffect(() => {
    if (searchKey) {
      setTabValue("all");
    }
  }, [searchKey]);

  useEffect(() => {
    if (searchParam) {
      setTabValue(searchParam);
    }
  }, [searchParam, urlParams]);

  const findSearchResult = () => {
    const result = Object.fromEntries(
      Object.entries(allElements.elements)
        .map(([key, value]) => {
          const filteredElements = Object.fromEntries(
            Object.entries(value.elements || {}).filter(([key2, value2]) =>
              value2.label.toLowerCase().includes(searchKey.toLowerCase()),
            ),
          );

          return [key, { ...value, elements: filteredElements }];
        })
        .filter(([key, value]) => Object.keys(value.elements).length > 0),
    );

    return result;
  };

  const setCheck = (data) => {
    updateActiveGroupElement(data);
  };

  const saveElement = async () => {
    const isChanged = isEqual(
      allElements,
      JSON.parse(JSON.stringify(AAB_ADDONS_ADMIN?.addons_config?.widgets)) ||
        {},
    );

    if (isChanged && Object.keys(isChanged).length) {
      const date = new Date();
      const utcDate = date.toISOString();

      const sampleData = {
        type: "notice",
        title: "Elements Activity Log",
        description:
          "Your element settings have been successfully updated in the following time period.",
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
        action: "aab_save_settings",
        fields: JSON.stringify(allElements),
        nonce: AAB_ADDONS_ADMIN.nonce,
        settings: "aab_save_widgets",
      }),
    })
      .then((response) => {
        return response.json();
      })
      .then((return_content) => {
        setElementCount((prev) => ({ ...prev, active: return_content.total }));
        toast.success("Save Successful", {
          position: "top-right",
        });
      });
  };

  return (
    <Tabs defaultValue={"all"} value={tabValue} onValueChange={setTabValue}>
      <div className="flex justify-between items-center">
        <ScrollArea className="max-w-[500px] lg:max-w-[565px] xl:max-w-[900px] rounded-lg bg-background-secondary">
          <TabsList className="h-11">
            <TabsTrigger
              key={"all-elements_tab"}
              value={"all"}
              className="px-4"
            >
              All
            </TabsTrigger>

            {elementTabList?.map((tab) => (
              <TabsTrigger key={tab.value} value={tab.value}>
                {tab.title}
              </TabsTrigger>
            ))}
          </TabsList>
          <ScrollBar orientation="horizontal" />
        </ScrollArea>

        <div className="flex gap-2.5 items-center justify-end">
          {/* <Button variant="secondary">Reset</Button> */}
          <Button onClick={() => saveElement()}>Save Settings</Button>
        </div>
      </div>
      <TabsContent
        key={"all-elements_content"}
        value={"all"}
        className="bg-background-secondary p-3 rounded-lg"
      >
        {norResult ? (
          <div className="bg-background flex justify-center items-center p-5 rounded">
            <h3 className="text-base font-medium">No Result Found</h3>
          </div>
        ) : (
          Object.keys(catElements)?.map((tab) => (
            <div className="mt-3 first:mt-0">
              <div className="bg-background flex justify-between items-center p-5 rounded">
                <h3 className="text-base font-medium">
                  {catElements[tab].title}
                </h3>
                <div className="flex items-center space-x-2">
                  <Switch
                    id={tab}
                    checked={catElements[tab].is_active}
                    onCheckedChange={(value) => setCheck({ value, slug: tab })}
                  />
                  <Label htmlFor={tab}>Enable All</Label>
                </div>
              </div>
              <div className="grid grid-cols-2 xl:grid-cols-3 gap-1 mt-1">
                {Object.keys(catElements[tab].elements)?.map((content, i) => (
                  <React.Fragment key={`tab_content-${i}`}>
                    <ElementCard
                      element={catElements[tab].elements[content]}
                      slug={content}
                      updateActiveItem={updateActiveElement}
                      className="rounded p-5"
                      settingOpen={settingOpen}
                    />
                  </React.Fragment>
                ))}
                {Array.from({
                  length:
                    deviceMediaMatch() -
                    (Object.keys(catElements[tab].elements)?.length %
                      deviceMediaMatch() ===
                    0
                      ? deviceMediaMatch()
                      : Object.keys(catElements[tab].elements)?.length %
                        deviceMediaMatch()),
                }).map((_, index) => (
                  <ElementCard
                    key={`tab_content_empty-${index}`}
                    className="rounded"
                  />
                ))}
              </div>
            </div>
          ))
        )}
      </TabsContent>
      {Object.keys(catElements)?.map((tab) => (
        <TabsContent
          key={tab}
          value={tab}
          className="bg-background-secondary p-3 rounded-lg"
        >
          <div>
            <div className="bg-background flex justify-between items-center p-5 rounded">
              <h3 className="text-base font-medium">
                {catElements[tab].title}
              </h3>
              <div className="flex items-center space-x-2">
                <Switch
                  id={tab}
                  checked={catElements[tab].is_active}
                  onCheckedChange={(value) => setCheck({ value, slug: tab })}
                />
                <Label htmlFor={tab}>Enable All</Label>
              </div>
            </div>
            <div className="grid grid-cols-2 xl:grid-cols-3 gap-1 mt-1">
              {Object.keys(catElements[tab].elements)?.map((content, i) => (
                <React.Fragment key={`tab_content-${i}`}>
                  <ElementCard
                    element={catElements[tab].elements[content]}
                    slug={content}
                    updateActiveItem={updateActiveElement}
                    className="rounded p-5"
                  />
                </React.Fragment>
              ))}
              {Array.from({
                length:
                  deviceMediaMatch() -
                  (Object.keys(catElements[tab].elements)?.length %
                    deviceMediaMatch() ===
                  0
                    ? deviceMediaMatch()
                    : Object.keys(catElements[tab].elements)?.length %
                      deviceMediaMatch()),
              }).map((_, index) => (
                <ElementCard
                  key={`tab_content_empty-${index}`}
                  className="rounded"
                />
              ))}
            </div>
          </div>
        </TabsContent>
      ))}
    </Tabs>
  );
};

export default ShowElements;
