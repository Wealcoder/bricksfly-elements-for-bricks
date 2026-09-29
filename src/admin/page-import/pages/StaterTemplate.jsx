import TemplateLeftFilter from "@/components/template/left/TemplateLeftFilter";
import TemplateRightContent from "../components/template/TemplateRightContent";
import { ScrollArea } from "@/components/ui/scroll-area";
import { useEffect, useRef, useState, useCallback } from "react";
import { debounceFn } from "@/lib/utils";
import FreeProSwitch from "@/components/template/FreeProSwitch";
import { Search } from "lucide-react";
import {
  Sheet,
  SheetContent,
  SheetDescription,
  SheetHeader,
  SheetTitle,
} from "@/components/ui/sheet";

const StaterTemplate = () => {
  const viewportRef = useRef(null);
  const prevScrollTop = useRef(0);
  const [hasReachedBottom, setHasReachedBottom] = useState(false);
  const [searchKey, setSearchKey] = useState("");
  const [filterKey, setFilterKey] = useState("");
  const [allTemplate, setAllTemplate] = useState({});
  const [pageNum, setPageNum] = useState(1);
  const [loading, setLoading] = useState(true);
  const [types, setTypes] = useState([]);
  const [tier, setTier] = useState("all"); // all pages, free ones first
  const [selectedCategory, setSelectedCategory] = useState([]);
  const [openSidebar, setOpenSidebar] = useState(false);

  useEffect(() => {
    const meta = {
      searchKey,
      filterKey,
      pageNum,
      types,
      tier,
      selectedCategory,
      allTemplate,
      wishlist: BRICKSFLY_ADDONS_ADMIN.addons_config?.wishlist?.toString() || "",
    };
    getAllTemplate(meta);
  }, [searchKey, filterKey, pageNum, types, tier, selectedCategory]);

  const getAllTemplate = useCallback(
    debounceFn(async (meta) => {
      setLoading(true);
      try {
        const url = new URL(
          `${BRICKSFLY_ADDONS_ADMIN?.st_template_domain}wp-json/wp/v2/brk-starter-page`
        );

        if (meta.searchKey) url.searchParams.append("s", meta.searchKey);
        if (meta.pageNum) {
          url.searchParams.append("page", meta.pageNum);
          url.searchParams.append("per_page", 40);
        }
        if (meta.filterKey) {
          if (meta.filterKey === "popular") {
            url.searchParams.append("popular", 1);
          } else {
            url.searchParams.append("orderby", "date");
          }
        }
        if (meta.selectedCategory && meta.selectedCategory.length) {
          // Page Types (brk-starter-page-type) term ids; the library reads `brk-cat`.
          url.searchParams.append("brk-cat", meta.selectedCategory.toString());
        }
        if (meta?.types?.includes("favorites")) {
          url.searchParams.append("favourites", 1);
        } else if (meta?.types?.includes("wishlist")) {
          url.searchParams.append("wishlist", meta.wishlist);
        }
        if (meta.tier === "pro") {
          url.searchParams.append("premium", "yes");
        } else if (meta.tier === "free") {
          url.searchParams.append("premium", "no");
        } else {
          url.searchParams.append("free_first", 1);
        }

        await fetch(url.toString())
          .then((response) => response.json())
          .then((data) => {
            if (meta.pageNum === 1) {
              setAllTemplate(data);
            } else {
              const updateData = {
                ...data,
                templates: [
                  ...(meta?.allTemplate?.templates || []),
                  ...data.templates,
                ],
              };
              setAllTemplate(updateData);
            }
          });
      } catch (error) {
        console.error(error);
      } finally {
        setLoading(false);
      }
    }),
    [],
  );

  useEffect(() => {
    const handleScroll = () => {
      const viewport = viewportRef.current?.querySelector(
        "[data-radix-scroll-area-viewport]",
      );
      if (!viewport) return;

      const { scrollTop, scrollHeight, clientHeight } = viewport;
      const isScrollingDown = scrollTop > prevScrollTop.current;
      prevScrollTop.current = scrollTop;

      if (
        isScrollingDown &&
        scrollTop + clientHeight >= scrollHeight - 5 &&
        !hasReachedBottom &&
        allTemplate?.templates?.length !== allTemplate?.total
      ) {
        setHasReachedBottom(true);
        setPageNum((prev) => prev + 1);
      }
    };

    const viewport = viewportRef.current?.querySelector(
      "[data-radix-scroll-area-viewport]",
    );
    if (viewport) viewport.addEventListener("scroll", handleScroll);
    return () => {
      if (viewport) viewport.removeEventListener("scroll", handleScroll);
    };
  }, [hasReachedBottom, allTemplate]);

  useEffect(() => {
    if (hasReachedBottom) setHasReachedBottom(false);
  }, [allTemplate]);

  return (
    <div className="flex">
      <div className="hidden lg:block w-[278px] border-r border-border h-[calc(100vh-85px)]">
        <TemplateLeftFilter
          types={types}
          setTypes={setTypes}
          tier={tier}
          setTier={setTier}
          selectedCategory={selectedCategory}
          setSelectedCategory={setSelectedCategory}
          setPageNum={setPageNum}
          taxonomy="brk-starter-page-type"
        />
      </div>
      <Sheet open={openSidebar} onOpenChange={setOpenSidebar}>
        <SheetContent
          className="lg:hidden w-[278px] border-r border-border h-[calc(100vh-48px)] mt-0"
          side={"left"}
        >
          <SheetHeader className="hidden">
            <SheetTitle></SheetTitle>
            <SheetDescription></SheetDescription>
          </SheetHeader>
          <TemplateLeftFilter
            types={types}
            setTypes={setTypes}
            tier={tier}
            setTier={setTier}
            selectedCategory={selectedCategory}
            setSelectedCategory={setSelectedCategory}
            setPageNum={setPageNum}
            taxonomy="brk-starter-page-type"
          />
        </SheetContent>
      </Sheet>
      <ScrollArea className="h-[calc(100vh-85px)] flex-1" ref={viewportRef}>
        <>
          <div className="flex flex-wrap justify-end items-center gap-4 mx-[31px] mt-6 mb-5">
            <button
              type="button"
              onClick={() => setOpenSidebar(true)}
              className="lg:hidden h-10 px-4 border border-[#00000026] rounded-[10px] text-sm font-medium"
            >
              Categories
            </button>
            <div className="relative">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-[#797979]" />
              <input
                type="search"
                value={searchKey}
                onChange={(e) => {
                  setSearchKey(e.target.value);
                  setPageNum(1);
                }}
                placeholder="Search pages"
                aria-label="Search pages"
                className="h-10 w-[260px] ps-9 pe-3 border border-[#00000026] rounded-[10px] bg-transparent text-sm"
              />
            </div>
            <FreeProSwitch
              value={tier === "pro" ? "premium" : tier || "all"}
              onChange={(mode) => {
                setTier(mode === "premium" ? "pro" : mode);
                setPageNum(1);
              }}
            />
          </div>
          {loading && !allTemplate?.templates?.length ? (
            <div className="flex justify-center items-center h-[10vh]">
              <p className="text-lg font-semibold">Loading...</p>
            </div>
          ) : (
            <TemplateRightContent
              searchKey={searchKey}
              setSearchKey={setSearchKey}
              filterKey={filterKey}
              setFilterKey={setFilterKey}
              setPageNum={setPageNum}
              allTemplate={allTemplate}
              setOpenSidebar={setOpenSidebar}
            />
          )}
          {loading && allTemplate?.templates?.length ? (
            <div className="flex justify-center items-center h-[25vh]">
              <p className="text-lg font-semibold">Loading...</p>
            </div>
          ) : (
            ""
          )}
        </>
      </ScrollArea>
    </div>
  );
};

export default StaterTemplate;
