import React, { useEffect, useRef } from "react";

const currentTabStorageName =  'vsm-metabox-current-tab';

export const useCurrentTabStorage = (entityType, currentTab, setCurrentTab) => {

  useEffect(() => {
    const storedValues = getStorageValue();

    if (! storedValues[entityType]) {
      return;
    }

    if (storedValues[entityType] === currentTab) {
      return;
    }

    setCurrentTab(storedValues[entityType]);
  }, []);

  const didMount = useRef(false);

  useEffect(() => {
    // don't run on initial render
    if (! didMount.current) {
      didMount.current = true;
      return;
    }

    let currentValue = getStorageValue();

    if (! currentValue) {
      currentValue = {};
    }

    if (currentTab === currentValue[entityType]) {
      return;
    }

    currentValue[entityType] = currentTab;

    localStorage.setItem(currentTabStorageName, JSON.stringify(currentValue));
  }, [currentTab]);
};

const getStorageValue = () => {
  const rawData = localStorage.getItem(currentTabStorageName);
  if (rawData === null) {
    return null;
  }

  try {
    return JSON.parse(rawData);
  } catch (e) {
    localStorage.removeItem(currentTabStorageName);
    return null;
  }
};
