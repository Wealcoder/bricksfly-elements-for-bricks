const icon = (name) =>
  `${AAB_ADDONS_ADMIN.plugin_url}public/images/documentation/${name}`;

export const DocumentList = [
  {
    title: "GSAP Extensions",
    subTitle: "Enhance your animations with additional GSAP features",
    url: "https://bricksfly.com/docs/gsap-extensions/",
    icon: icon("icon-gsap-extensions.png"),
  },
  {
    title: "Animation Widgets",
    subTitle: "Prebuilt widgets to add motion and interactivity with ease",
    url: "https://bricksfly.com/docs/animation-widgets/",
    icon: icon("icon-animation-widgets.png"),
  },
  {
    title: "General extensions",
    subTitle: "Essential tools and utilities that enhance core functionality",
    url: "https://bricksfly.com/docs/general-extensions/",
    icon: icon("icon-general-extensions.png"),
  },
];
