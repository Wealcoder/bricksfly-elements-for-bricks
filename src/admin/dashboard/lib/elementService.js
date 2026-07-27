export const activeElementFn = (mainContent, data, dispatch) => {
  const result = Object.fromEntries(
    Object.entries(mainContent.elements).map(([key, value]) => {
      const filteredElements = Object.fromEntries(
        Object.entries(value.elements || {}).filter(([key2, value2]) => {
          if (key2 === data.slug) {
            value2.is_active = data.value;
            if (!data.value) {
              value.is_active = data.value;
            }
            return [key2, value2];
          } else {
            return [key2, value2];
          }
        })
      );

      return [key, { ...value, elements: filteredElements }];
    })
  );

  if (!data.value) {
    dispatch({
      type: "setAllElements",
      value: {
        ...mainContent,
        is_active: data.value,
        elements: result,
      },
    });
  } else {
    dispatch({
      type: "setAllElements",
      value: {
        ...mainContent,
        elements: result,
      },
    });
  }
};

export const activeGroupElementFn = (mainContent, data, dispatch) => {
  const result = Object.fromEntries(
    Object.entries(mainContent.elements).map(([key, value]) => {
      const filteredElements = Object.fromEntries(
        Object.entries(value.elements || {}).filter(([key2, value2]) => {
          if (key === data.slug) {
            value2.is_active = data.value;
            return [key2, value2];
          } else {
            return [key2, value2];
          }
        })
      );
      if (key === data.slug) {
        value.is_active = data.value;
      }
      return [key, { ...value, elements: filteredElements }];
    })
  );

  if (!data.value) {
    dispatch({
      type: "setAllElements",
      value: {
        ...mainContent,
        is_active: data.value,
        elements: result,
      },
    });
  } else {
    dispatch({
      type: "setAllElements",
      value: {
        ...mainContent,
        elements: result,
      },
    });
  }
};

export const activeFullElementFn = (mainContent, data, dispatch) => {
  const result = Object.fromEntries(
    Object.entries(mainContent.elements).map(([key, value]) => {
      const filteredElements = Object.fromEntries(
        Object.entries(value.elements || {}).filter(([key2, value2]) => {
          value2.is_active = data.value;
          return [key2, value2];
        })
      );
      value.is_active = data.value;
      return [key, { ...value, elements: filteredElements }];
    })
  );

  dispatch({
    type: "setAllElements",
    value: {
      is_active: data.value,
      elements: result,
    },
  });
};

export const activeFullSetupElementFn = (mainContent, data) => {
  const result = Object.fromEntries(
    Object.entries(mainContent.elements).map(([key, value]) => {
      const filteredElements = Object.fromEntries(
        Object.entries(value.elements || {}).filter(([key2, value2]) => {
          if (value2?.setup) {
            value2.is_active = value2.setup?.includes(data);
          } else {
            value2.is_active = false;
          }
          return [key2, value2];
        })
      );
      value.is_active = false;
      return [key, { ...value, elements: filteredElements }];
    })
  );

  return result;
};
