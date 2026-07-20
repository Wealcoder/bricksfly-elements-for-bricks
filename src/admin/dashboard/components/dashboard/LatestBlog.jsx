import { RiArrowRightUpLine } from "react-icons/ri";
import { Separator } from "../ui/separator";
import { cn } from "@/lib/utils";
import { buttonVariants } from "../ui/button";
import { LatestBlogList } from "@/config/data/latestBlogList";
import { API_ENDPOINTS } from "@/config/api";
import { useRemoteData } from "@/hooks/useRemoteData";

const LatestBlog = () => {
  const { data: blogs } = useRemoteData(API_ENDPOINTS.blogs, LatestBlogList);
  const hash = window.location.hash;
  const hashValue = hash?.replace("#", "");

  return (
    <div
      className={cn(
        "border rounded-2xl p-5",
        hashValue === "wcf-blog"
          ? "shadow-[0px_0px_0px_2px_rgba(252,104,72,0.25),0px_1px_2px_0px_rgba(10,13,20,0.03)]"
          : "shadow-common",
      )}
      id="wcf-blog"
    >
      <div className="flex justify-between gap-11">
        <p className="font-medium text-text">Blogs</p>
        <div>
          <a
            href={"https://bricksfly.com/blog"}
            target="_blank"
            className={cn(buttonVariants({ variant: "secondary", size: "sm" }))}
          >
            View all
            <RiArrowRightUpLine
              size={18}
              className="rtl:rotate-360 rtl:scale-x-[-1]"
            />
          </a>
        </div>
      </div>
      <Separator className="mt-4 mb-5" />
      <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        {blogs?.map((blog, i) => (
          <div key={`latest_blog-${i}`} className="group flex flex-col gap-[19px]">
            <div className="overflow-hidden rounded-lg">
              <img
                className="w-full h-auto transition-all group-hover:scale-110"
                src={blog.thumbnail}
                alt=""
              />
            </div>
            <div className="flex flex-col gap-[18px]">
              <a href={blog.url} target="_blank">
                <h3 className="text-sm font-medium text-text group-hover:text-brand line-clamp-2">
                  <span dir="ltr">{blog.title}</span>
                </h3>
              </a>
              <div className="flex items-center gap-1.5 text-sm text-text-secondary">
                <span>{blog.createAt}</span>
                <span className="w-1 h-1 rounded-full bg-[#717784] shrink-0" />
                <span>{blog.readingTime}</span>
              </div>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
};

export default LatestBlog;
