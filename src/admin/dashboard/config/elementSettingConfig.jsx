import TikTokSettings from "@/components/elements/settings/TikTokSettings";
import WeatherSettings from "@/components/elements/settings/WeatherSettings";
import YoutubeVideoSettings from "@/components/elements/settings/YoutubeVideoSettings";
import MailchimpSettings from "../components/elements/settings/MailchimpSettings";

export const ElementSettingConfig = [
  {
    key: "youtube-video",
    component: <YoutubeVideoSettings />,
  },
  {
    key: "weather",
    component: <WeatherSettings />,
  },
  {
    key: "tiktok-feed",
    component: <TikTokSettings />,
  },
  {
    key: "mailchimp",
    component: <MailchimpSettings />,
  },
  {
    key: "advanced-mailchimp",
    component: <MailchimpSettings />,
  },
];
