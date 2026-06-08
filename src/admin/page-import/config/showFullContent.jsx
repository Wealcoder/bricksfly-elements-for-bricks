import { lazy } from "react";
import MainLayout from "../layouts/MainLayout";
import CompleteImport from "../pages/CompleteImport";
import FailImport from "../pages/FailImport";

const StaterTemplate = lazy(() => import("../pages/StaterTemplate"));
const RequiredFeatures = lazy(() => import("../pages/RequiredFeatures"));
const DemoImporting = lazy(() => import("../pages/DemoImporting"));

export const ShowContent = (item) => {
  switch (item.tabKey) {
    case "stater-template":
      return (
        <MainLayout.SecondLayout>
          <StaterTemplate />
        </MainLayout.SecondLayout>
      );
    case "required-features":
      return (
        <MainLayout.ThirdLayout>
          <RequiredFeatures />
        </MainLayout.ThirdLayout>
      );
    case "demo-importing":
      return (
        <MainLayout.ThirdLayout>
          <DemoImporting />
        </MainLayout.ThirdLayout>
      );
    case "complete-import":
      return (
        <MainLayout.ThirdLayout>
          <CompleteImport />
        </MainLayout.ThirdLayout>
      );
    case "fail-import":
      return (
        <MainLayout.ThirdLayout>
          <FailImport />
        </MainLayout.ThirdLayout>
      );
    default:
      return (
        <MainLayout.SecondLayout>
          <StaterTemplate />
        </MainLayout.SecondLayout>
      );
  }
};
