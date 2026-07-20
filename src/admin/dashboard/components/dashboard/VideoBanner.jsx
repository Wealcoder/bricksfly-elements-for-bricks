import { useState } from "react";
import TutorialDialog from "./dialog/TutorialDialog";

const VideoBanner = ({
  thumbnail = `${AAB_ADDONS_ADMIN.plugin_url}public/images/animation-addons-video-banner.jpg`,
  videoUrl = "https://youtu.be/5wno9t4gR64?si=qgM8L6HyQHIouiyI",
  title = "Animation Addons",
}) => {
  const [open, setOpen] = useState(false);

  return (
    <div
      className="relative h-full cursor-pointer"
      onClick={() => setOpen(true)}
    >
      <img
        src={thumbnail}
        className="w-full h-full object-cover rounded-[10px]"
        alt={title}
      />
      {/* <div className="absolute inset-0 flex items-center justify-center">
        <img
          width={50}
          height={50}
          src={`${AAB_ADDONS_ADMIN.plugin_url}public/images/play-button.png`}
          alt="play"
        />
      </div> */}
      <TutorialDialog
        open={open}
        setOpen={setOpen}
        videoUrl={videoUrl}
        title={title}
      />
    </div>
  );
};

export default VideoBanner;

/*


*/