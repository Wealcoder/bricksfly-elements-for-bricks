import { AppContextProvider } from "./context/app.context";
import "../dashboard/index.css";
import MainLayout from "./layouts/MainLayout";

wp.element.render(
  <AppContextProvider>
    <MainLayout />
  </AppContextProvider>,
  document.getElementById("bf-page-importer")
);
