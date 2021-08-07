import React from "react";
import classNames from "classnames";

export const Tabs = ({ tabs, onChange, current }) => {
  return (
    <div className="vsm-tabs-switcher">
      {tabs.map(({id, title}) => {
        let buttonClass = classNames([
          "vsm-tabs-switcher__item",
          id === current ? "vsm-tabs-switcher__item_active" : "",
        ]);

        return <div className={buttonClass} key={id} onClick={() => onChange(id)}>{title}</div>;
      })}
    </div>
  );
}
