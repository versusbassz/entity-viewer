import React, { useState } from "react";

import { EntityPropsTable } from "../tables/EntityPropsTable";
import { MetaTable } from "../tables/MetaTable";
import { RefreshButton } from "../misc/RefreshButton";
import { Tabs } from "../misc/Tabs";
import { useMetaboxContext } from "../../context";
import { useLocalStorage } from "../../hooks/useLocalStorage";
import { str } from "../../utils/i18n";

export const MetaboxContent = () => {
  let {metaboxSettings, tabs, refreshTabs} = useMetaboxContext();

  const [search, setSearch] = useState("");

  // Tabs
  const [currentTab, setCurrentTab] = useLocalStorage("all", 'vsm-metabox-current-tab', metaboxSettings.entity_type);

  const handleTabChange = tab_id => {
    setCurrentTab(tab_id);
  };

  if (! Object.keys(tabs).length) {
    return <div className="vsm-message vsm-message_type_not-exists">{str("tabs_not_found")}</div>;
  }

  let tabsData = Object.keys(tabs).map(id => {
    return {id, title: tabs[id].tab_title};
  });
  tabsData = [{id: "all", title: str("tabs_all")}, ...tabsData];

  return (
    <>
      <div className="vsm-top-panel">

        {/* Search */}
        <div className="vsm-search">
          <input
            type="text"
            className="vsm-search__input"
            placeholder={str("search_placeholder")}
            value={search}
            onChange={(e) => {
              e.preventDefault();
              setSearch(e.target.value);
            }}
            onKeyPress={(e) => {
              if (e.which == 13) { // Enter
                e.preventDefault();
                return false;
              }
            }}
          />
          <button
            type="button"
            className="button-secondary vsm-search__reset"
            style={search ? {} : {display: "none"}}
            onClick={() => setSearch("")}
          >Reset</button>
        </div>

        {/* Refresh button */}
        <div className="vsm-top-panel__refresh">
          <RefreshButton refreshFields={refreshTabs} fetchedInitial={metaboxSettings.fetched_initial} />
        </div>

        {/* Tabs - headers */}
        <div className="vsm-top-panel__tabs-switcher">
          <Tabs tabs={tabsData} onChange={handleTabChange} current={currentTab} />
        </div>

      </div>

      {/* Tabs - content */}
      <div className="vsm-tabs-contents">
        {Object.entries(tabs).map(([key, data]) => {
          if (currentTab !== "all" && currentTab !== key) {
            return null;
          }

          return (
            <TabContent key={key} title={data.section_title} error={data.error}>
              {key === "entity" && <EntityPropsTable search={search} fieldsData={data} />}
              {key === "meta" && <MetaTable search={search} fieldsData={data} />}
            </TabContent>
          );
        })}
      </div>
    </>
  );
}

const TabContent = ({ children, title, error }) => {
  return (
    <div className="vsm-tab-content">
      <div className="vsm-tab-content__title">Section: {title}</div>

      {error ? (
        <div className="notice notice-error inline">
          <p>{str("error")}: {error}</p>
        </div>
      ) : (
        <>{children}</>
      )}

    </div>
  );
};
