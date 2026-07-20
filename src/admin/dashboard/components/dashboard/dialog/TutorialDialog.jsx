import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";

const TutorialDialog = ({
  open,
  setOpen,
  videoUrl = "https://www.youtube.com/embed/tRbvgq2gJF4?si=Ft1slmDRA316RZCr",
  title = "Video player",
}) => {
  return (
    <Dialog open={open} onOpenChange={(value) => setOpen(value)}>
      <DialogContent className={"max-w-[1000px]"} hideClose>
        <DialogHeader>
          <DialogTitle className="hidden"></DialogTitle>
          <DialogDescription>
            {open && (
              <iframe
                key={videoUrl}
                width="100%"
                height="100%"
                src={videoUrl}
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
