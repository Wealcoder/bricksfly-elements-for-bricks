import { AppContextProvider } from "./context/app.context";
import "../dashboard/index.css";
import MainLayout from "./layouts/MainLayout";
import ErrorBoundary from "../components/ErrorBoundary";

wp.element.render(
  <ErrorBoundary>
    <AppContextProvider>
      <MainLayout />
    </AppContextProvider>
  </ErrorBoundary>,
  document.getElementById("bricksfly-page-importer")
);
