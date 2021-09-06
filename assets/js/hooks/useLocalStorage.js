import React, { useEffect, useRef, useState } from "react";
import ls from 'localstorage-slim';

import { CONSOLE_PREFIX, LS_TTL } from "../constants";

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

    ls.set(key, JSON.stringify(storageItems), {ttl: LS_TTL});
  }, [currentValue]);

  const getStorageValue = () => {
    const rawData = ls.get(key);
    if (rawData === null) {
      return null;
    }

    try {
      return JSON.parse(rawData);
    } catch (e) {
      console.error(`${CONSOLE_PREFIX} Error during parsing localStorage value`, rawData)
      ls.remove(key);
      return null;
    }
  };

  return [currentValue, setCurrentValue];
};
