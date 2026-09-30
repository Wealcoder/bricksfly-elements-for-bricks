import { Toaster } from "./components/ui/sonner";
import { AppContextProvider } from "./context/app.context";
import "./index.css";
import MainLayout from "./layouts/MainLayout";
import ErrorBoundary from "../components/ErrorBoundary";

wp.element.render(
  <ErrorBoundary>
    <AppContextProvider>
      <MainLayout />
    </AppContextProvider>
  </ErrorBoundary>,
  document.getElementById("wcf-admin-ds-cr-js"),
);

wp.element.render(<Toaster />, document.getElementById("aab-admin-toast"));
