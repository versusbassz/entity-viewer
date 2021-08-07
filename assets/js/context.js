import React, { useContext, useEffect, useState } from "react";
import { str } from "./utils/i18n";

// refreshing metabox data dynamically (via AJAX)
const getRefreshTabsCallback = (setTabs, metaboxSettings) => async (setLoading, setLastUpdated, setShowDone) => {
  if (! window.vsm.ajax_url) {
    console.log("Empty metabox settings. The AJAX-request hasn't been sent.", metaboxSettings)
    return;
  }

  const urlParams = (new URLSearchParams(metaboxSettings.query_args)).toString();
  const url = window.vsm.ajax_url + '?' + urlParams;
  setLoading(true);

  const response = await fetch(url);

  const log = (data) => console.log("entity-viewer response: ", data);

  if (response.ok) {
    let isJsonError = false;
    let json;

    try {
      json = await response.json();
    } catch (e) {
      isJsonError = true;
      json = e;
    }

    if (! isJsonError && json.tabs && Object.keys(json.tabs).length) {
      setTabs(json.tabs);
      setLastUpdated(Date.now());
      setShowDone(true);
    } else {
      alert('[Entity viewer] ' + str("incorrect_response"));
      log(json);
    }
  } else {
    alert('[Entity viewer] ' + str("http_error").replace('{{status}}', response.status));
    log(await response.text());
  }

  setLoading(false);
};

export const MetaboxContext = React.createContext(null);

export function MetaboxContextProvider({ children }) {
  const [initialStateLoaded, setInitialStateLoaded] = useState(false);
  const [metaboxSettings, setMetaboxSettings] = useState({});
  const [tabs, setTabs] = useState([]);

  useEffect(() => {
    const jsonData = document.getElementById('js-vsm-tabs-data').value;
    const data = JSON.parse(jsonData);

    // getting initial data
    setMetaboxSettings(data?.metabox);
    setTabs(data?.tabs);
    setInitialStateLoaded(true);
  }, []);

  const refreshTabs = getRefreshTabsCallback(setTabs, metaboxSettings);

  return (
    <MetaboxContext.Provider value={{ initialStateLoaded, metaboxSettings, tabs, refreshTabs }}>
      {children}
    </MetaboxContext.Provider>
  );
}

export const useMetaboxContext = () => useContext(MetaboxContext);
