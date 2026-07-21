import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import { toYoutubeEmbedUrl } from "@/lib/utils";

const TutorialDialog = ({
  open,
  setOpen,
  videoUrl = "https://youtu.be/5wno9t4gR64?si=qgM8L6HyQHIouiyI",
  title = "Video player",
}) => {
  const embedUrl = toYoutubeEmbedUrl(videoUrl, { autoplay: true });

  return (
    <Dialog open={open} onOpenChange={(value) => setOpen(value)}>
      <DialogContent className={"max-w-[1000px]"} hideClose>
        <DialogHeader>
          <DialogTitle className="hidden"></DialogTitle>
          <DialogDescription>
            {open && (
              <iframe
                key={embedUrl}
                width="100%"
                height="100%"
                src={embedUrl}
                title={title}
                frameBorder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                referrerPolicy="strict-origin-when-cross-origin"
                allowFullScreen
                className="rounded-md aspect-video"
              ></iframe>
            )}
          </DialogDescription>
        </DialogHeader>
      </DialogContent>
    </Dialog>
  );
};

export default TutorialDialog;
