import { SquareArrowUp } from "lucide-react";
import {
  RiLayout3Line,
  RiSettings2Line,
  RiTextSnippet,
  RiVipCrown2Line,
} from "react-icons/ri";
import QuickAccessCard from "../shared/QuickAccessCard";
import { cn } from "@/lib/utils";

const AccessData = [
  // {
  //   title: "Global Settings",
  //   subTitle: "Customize global settings",
  //   url: THEBRBRE_ADDONS_ADMIN.global_settings_url,
  //   icon: <RiSettings2Line size={22} className="text-[#46A1FF]" />,
  // },
  // {
  //   title: "Theme Builder",
  //   subTitle: "Customize theme builder",
  //   url: THEBRBRE_ADDONS_ADMIN.theme_builder_url,
  //   icon: <RiLayout3Line size={22} className="text-[#7772FC]" />,
  // },
  
  {
    title: "Pro Elements",
    subTitle: "Customize pro elements",
    url: `${THEBRBRE_ADDONS_ADMIN.adminURL}/admin.php?page=thebrbre_addons_settings&tab=elements&filter=pro`,
    icon: <RiVipCrown2Line size={22} className="text-[#FFA132]" />,
  },

  {
    title: "Site Settings",
    subTitle: "Preloader, Cursor, Scroll To Top and more",
    url: `${THEBRBRE_ADDONS_ADMIN.adminURL}/admin.php?page=thebrbre_addons_settings&tab=extensions&cTab=general#site-settings`,
    icon: <RiVipCrown2Line size={22} className="text-[#FFA132]" />,
  },
  // {
  //   title: "Custom Fonts",
  //   slug: "custom-fonts",
  //   subTitle: "Upload Custom fonts",
  //   url: `${THEBRBRE_ADDONS_ADMIN.adminURL}/edit.php?post_type=wcf-custom-fonts`,
  //   icon: <RiTextSnippet size={22} className="text-[#A281FF]" />,
  // },
  // {
  //   title: "Popup",
  //   subTitle: "Customize popups",
  //   url: `${THEBRBRE_ADDONS_ADMIN.adminURL}/admin.php?page=thebrbre_addons_settings&tab=extensions&cTab=general#popup`,
  //   icon: <SquareArrowUp size={22} className="text-[#A281FF]" />,
  // },
  {
    title: "CPT Builder",
    subTitle: "Create custom CPT",
    url: `${THEBRBRE_ADDONS_ADMIN.adminURL}/admin.php?page=bricks-cpt-builder`,
    icon: <RiVipCrown2Line size={22} className="text-[#FFA132]" />,
  },
];

const QuickAccess = () => {
  return (
    <div className="border rounded-2xl p-5">
      <div className="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3">
        {AccessData.map((item, i) => (
          <div
            key={`quick_access-${i}`}
            className={cn(
              "px-4 border-0 lg:border-r lg:[&:nth-child(2n)]:border-r-0 xl:[&:nth-child(2n)]:border-r xl:[&:nth-child(3n)]:border-r-0 border-border-secondary [&>div]:border-t [&>div]:pb-4 lg:[&:nth-child(-n+2)>div]:pt-1 xl:[&:nth-child(-n+3)>div]:pt-1 [&:nth-child(-n+1)>div]:border-t-0 lg:[&:nth-child(-n+2)>div]:border-t-0 xl:[&:nth-child(-n+3)>div]:border-t-0",
            )}
          >
            <QuickAccessCard
              item={item}
              className={"border-border-secondary pt-4"}
            />
          </div>
        ))}
      </div>
    </div>
  );
};

export default QuickAccess;
