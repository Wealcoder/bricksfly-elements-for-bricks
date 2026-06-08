import CustomFontSettings from "@/components/extentions/settings/CustomFontSettings";
import ScrollSmootherSettings from "@/components/extentions/settings/ScrollSmootherSettings";

export const ExtensionSettingConfig = [
  {
    key: "aab-smooth-scroller",
    component: <ScrollSmootherSettings />,
    dialogStyle: {
      width: "fit-content",
      minWidth: "440px",
      maxWidth: "95vw",
    },
  },
  {
    key: "custom-fonts",
    component: <CustomFontSettings />,
  },
];
