import { Button } from "@/components/ui/button";
import {
  Sheet,
  SheetContent,
  SheetDescription,
  SheetHeader,
  SheetTitle,
  SheetTrigger,
} from "@/components/ui/sheet";
import { RiCloseLine, RiNotificationLine } from "react-icons/ri";
import { Badge } from "../ui/badge";
import { Separator } from "../ui/separator";
import { ScrollArea } from "../ui/scroll-area";
import { useNotification } from "@/hooks/app.hooks";

const showContent = (data) => {
  switch (data.type) {
    case "notice":
      const date = new Date(data.date);
      return (
        <div>
          <h3 className="text-lg font-medium">{data.title}</h3>
          <p className="mt-2 text-sm">{data.description}</p>
          <p className="mt-2 text-xs text-brand">{date.toLocaleDateString()}</p>
        </div>
      );
    case "promotion":
      return (
        <div>
          <h3 className="text-lg font-medium">{data.title}</h3>
          <p className="mt-2 text-sm">{data.description}</p>
          <Button variant={"secondary"} className="mt-6">
            Get Pro Version
          </Button>
        </div>
      );
    default:
      return <></>;
  }
};

const Notification = () => {
  const { notice, removeNotice } = useNotification();

  return (
    <Sheet>
      <SheetTrigger asChild>
        <Button variant="secondary" size="icon" className="relative">
          <Badge className="absolute top-[9px] right-2" variant="solid" />
          <RiNotificationLine size={20} />
        </Button>
      </SheetTrigger>
      <SheetContent className="p-0">
        <SheetHeader className={"hidden"}>
          <SheetTitle></SheetTitle>
          <SheetDescription></SheetDescription>
        </SheetHeader>
        <div className="py-4 ps-6 pe-4 border-b border-[#0D1A331A]">
          <h2 className="text-lg font-medium">Updates</h2>
        </div>
        <ScrollArea className="h-[90vh] px-5 py-6">
          {notice && notice.length ? (
            notice?.map((item, i) => (
              <div key={`${item?.title ?? "notice"}-${item?.date ?? i}-${i}`}>
                <div className="relative pe-8">
                  {showContent(item)}
                  <button
                    type="button"
                    aria-label="Dismiss notification"
                    onClick={() => removeNotice(i)}
                    className="absolute top-0 right-0 p-1 bg-white text-muted-foreground hover:text-foreground cursor-pointer"
                  >
                    <RiCloseLine size={18} />
                  </button>
                </div>
                {notice.length - 1 !== i && <Separator className="my-6" />}
              </div>
            ))
          ) : (
            <div>
              <p className="mt-2 text-sm">No Notification Found</p>
            </div>
          )}
        </ScrollArea>
      </SheetContent>
    </Sheet>
  );
};

export default Notification;
