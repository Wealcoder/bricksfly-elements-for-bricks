import { AppContext } from "../context/app.context";
import { useContext } from "react";

export const useActivate = () => {
  const { mainState } = useContext(AppContext);
  return { activated: mainState.activated };
};

export const useTNavigation = () => {
  const {
    mainState: { tabKey },
    setTabKey,
  } = useContext(AppContext);

  return { tabKey, setTabKey };
};
