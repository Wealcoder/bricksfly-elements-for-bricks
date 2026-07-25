import {
  allExtensionFn,
  generalAllExtensionFn,
  generalExtensionFn,
  generalGroupExtensionFn,
  gsapAllExtensionFn,
  gsapExtensionFn,
  gsapGroupExtensionFn,
} from "@/lib/extensionService";
import { activeGroupLibraryFn, libraryFn } from "@/lib/libraryService";
import {
  disableAllElement,
  disableGeneralExtension,
  disableGsapExtension,
} from "@/lib/utils";
import {
  activeFullElementFn,
  activeGroupElementFn,
  activeElementFn,
} from "@/lib/elementService";
import { createContext, useCallback, useReducer } from "react";

const initialState = {
  allElements:
    JSON.parse(JSON.stringify(THEBRBRE_ADDONS_ADMIN?.addons_config?.widgets)) || {},
  allExtensions:
    JSON.parse(JSON.stringify(THEBRBRE_ADDONS_ADMIN?.addons_config?.extensions)) ||
    {},
  allLibrary:
    JSON.parse(
      JSON.stringify(THEBRBRE_ADDONS_ADMIN?.addons_config?.integrations?.library)
    ) || {},
  activated: THEBRBRE_ADDONS_ADMIN?.addons_config || {},
  setupType: "basic",
  notice: [],
  tabKey: "",
  isSkipTerms: true,
};

const reducer = (state, action) => {
  switch (action.type) {
    case "setAllElements":
      return { ...state, allElements: action.value };
    case "setAllExtensions":
      return { ...state, allExtensions: action.value };
    case "setActivated":
      return { ...state, activated: action.value };
    case "setLibrary":
      return { ...state, allLibrary: action.value };
    case "setSetupType":
      return { ...state, setupType: action.value };
    case "setNotice":
      return { ...state, notice: action.value };
    case "setTabKey":
      return { ...state, tabKey: action.value };
    case "setIsSkipTerms":
      return { ...state, isSkipTerms: action.value };

    default:
      throw new Error();
  }
};

const useMainContext = (state) => {
  const [mainState, dispatch] = useReducer(reducer, state);

  const setAllElements = useCallback((data) => {
    dispatch({
      type: "setAllElements",
      value: data,
    });
  }, []);

  const setAllExtensions = useCallback((data) => {
    dispatch({
      type: "setAllExtensions",
      value: data,
    });
  }, []);

  // const setLibrary = useCallback((data) => {
  //   dispatch({
  //     type: "setLibrary",
  //     value: data,
  //   });
  // }, []);

  const setActivated = useCallback((data) => {
    dispatch({
      type: "setActivated",
      value: data,
    });
  }, []);
  const setNotice = useCallback((data) => {
    dispatch({
      type: "setNotice",
      value: data,
    });
  }, []);

  const setTabKey = useCallback((data) => {
    dispatch({
      type: "setTabKey",
      value: data,
    });
  }, []);

  const setIsSkipTerms = useCallback((data) => {
    dispatch({
      type: "setIsSkipTerms",
      value: data,
    });
  }, []);

  const setSetupType = useCallback((data) => {
    if (data && data === "advance") {
      // update element state
      const elementResult = disableAllElement(mainState.allElements.elements);
      setAllElements({ ...mainState.allElements, elements: elementResult });

      // update extension state
      const gsapResult = disableGsapExtension(
        mainState.allExtensions.elements["gsap-extensions"]
      );
      const generalResult = disableGeneralExtension(
        mainState.allExtensions.elements["general-extensions"]
      );

      setAllExtensions({
        ...mainState.allExtensions,
        elements: {
          "general-extensions": generalResult,
          "gsap-extensions": gsapResult,
        },
      });
    } else {
      // update element state to default
      setAllElements(THEBRBRE_ADDONS_ADMIN?.addons_config?.widgets || {});

      // update extension state to default
      setAllExtensions(THEBRBRE_ADDONS_ADMIN?.addons_config?.extensions || {});
    }
    dispatch({
      type: "setSetupType",
      value: data,
    });
  }, []);

  const updateActiveElement = useCallback(
    (data) => {
      activeElementFn(mainState.allElements, data, dispatch);
    },
    [mainState.allElements]
  );

  const updateActiveGroupElement = useCallback(
    (data) => {
      activeGroupElementFn(mainState.allElements, data, dispatch);
    },
    [mainState.allElements]
  );

  const updateActiveFullElement = useCallback(
    (data) => {
      activeFullElementFn(mainState.allElements, data, dispatch);
    },
    [mainState.allElements]
  );

  const updateActiveGeneralExtension = useCallback(
    (data) => {
      generalExtensionFn(mainState.allExtensions, data, dispatch);
    },
    [mainState.allExtensions]
  );

  const updateActiveGeneralGroupExtension = useCallback(
    (data) => {
      generalGroupExtensionFn(mainState.allExtensions, data, dispatch);
    },
    [mainState.allExtensions]
  );

  const updateActiveGeneralAllExtension = useCallback(
    (data) => {
      generalAllExtensionFn(mainState.allExtensions, data, dispatch);
    },
    [mainState.allExtensions]
  );

  const updateActiveGsapExtension = useCallback(
    (data) => {
      gsapExtensionFn(mainState.allExtensions, data, dispatch);
    },
    [mainState.allExtensions]
  );

  const updateLibrary = useCallback(
    (data) => {
      libraryFn(mainState.allLibrary, data, dispatch);
    },
    [mainState.allLibrary]
  );

  const updateActiveGroupLibrary = useCallback(
    (data) => {
      activeGroupLibraryFn(mainState.allLibrary, data, dispatch);
    },
    [mainState.allLibrary]
  );

  const updateActiveGsapGroupExtension = useCallback(
    (data) => {
      gsapGroupExtensionFn(mainState.allExtensions, data, dispatch);
    },
    [mainState.allExtensions]
  );

  const updateActiveGsapAllExtension = useCallback(
    (data) => {
      gsapAllExtensionFn(mainState.allExtensions, data, dispatch);
    },
    [mainState.allExtensions]
  );

  const updateActiveFullExtension = useCallback(
    (data) => {
      allExtensionFn(mainState.allExtensions, data, dispatch);
    },
    [mainState.allExtensions]
  );

  const updateNotice = useCallback(
    async (data) => {
      const result = mainState.notice;
      if (result.length >= 5) {
        result.pop();
      }
      result.unshift(data);

      await fetch(THEBRBRE_ADDONS_ADMIN.ajaxurl, {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
          Accept: "application/json",
        },

        body: new URLSearchParams({
          action: "thebrbre_dashboard_notice_store",
          notice: JSON.stringify(result),
          nonce: THEBRBRE_ADDONS_ADMIN.nonce,
        }),
      })
        .then((response) => {
          return response.json();
        })
        .then((return_content) => {
          setNotice(result);
        });
    },
    [mainState.notice]
  );

  const removeNotice = useCallback(
    async (index) => {
      const current = Array.isArray(mainState.notice) ? mainState.notice : [];
      if (index < 0 || index >= current.length) return;

      const result = current.filter((_, i) => i !== index);

      setNotice(result);

      await fetch(THEBRBRE_ADDONS_ADMIN.ajaxurl, {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
          Accept: "application/json",
        },
        body: new URLSearchParams({
          action: "thebrbre_dashboard_notice_store",
          notice: JSON.stringify(result),
          nonce: THEBRBRE_ADDONS_ADMIN.nonce,
        }),
      });
    },
    [mainState.notice, setNotice]
  );

  return {
    mainState,
    setAllElements,
    setAllExtensions,
    setActivated,
    setNotice,
    setTabKey,
    setIsSkipTerms,
    setSetupType,
    updateActiveElement,
    updateActiveGroupElement,
    updateActiveFullElement,
    updateActiveGeneralExtension,
    updateActiveGeneralGroupExtension,
    updateActiveGeneralAllExtension,
    updateActiveGsapExtension,
    updateLibrary,
    updateActiveGroupLibrary,
    updateActiveGsapGroupExtension,
    updateActiveGsapAllExtension,
    updateActiveFullExtension,
    updateNotice,
    removeNotice,
  };
};

export const AppContext = createContext({
  mainState: initialState,
  setAllElements: () => {},
  setAllExtensions: () => {},
  setActivated: () => {},
  setNotice: () => {},
  setTabKey: () => {},
  setIsSkipTerms: () => {},
  setSetupType: () => {},
  updateActiveElement: () => {},
});

export const AppContextProvider = ({ children }) => {
  return (
    <AppContext.Provider value={useMainContext(initialState)}>
      {children}
    </AppContext.Provider>
  );
};
