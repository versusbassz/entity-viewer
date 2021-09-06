import React, { useEffect, useRef, useState } from "react";

export const useLocalStorage = (initialValue, key, subKey) => {
  const [currentValue, setCurrentValue] = useState(initialValue);

  // set initial value (from storage or the parameter)
  useEffect(() => {
    const storedValues = getStorageValue();

    if (! storedValues || ! storedValues[subKey]) {
      return;
    }

    if (storedValues[subKey] === currentValue) {
      return;
    }

    setCurrentValue(storedValues[subKey]);
  }, []);

  const didMount = useRef(false);

  useEffect(() => {
    // don't run on initial render
    if (! didMount.current) {
      didMount.current = true;
      return;
    }

    let storageItems = getStorageValue();

    if (! storageItems) {
      storageItems = {};
    }

    if (storageItems[subKey] === currentValue) {
      return;
    }

    storageItems[subKey] = currentValue;

    localStorage.setItem(key, JSON.stringify(storageItems));
  }, [currentValue]);

  const getStorageValue = () => {
    const rawData = localStorage.getItem(key);
    if (rawData === null) {
      return null;
    }

    try {
      return JSON.parse(rawData);
    } catch (e) {
      localStorage.removeItem(key);
      return null;
    }
  };

  return [currentValue, setCurrentValue];
};
