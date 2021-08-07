import React from "react";

export const ToggleButton = ({onClick, enabled}) => {
  let classes = ["vsm-pretty-code-button"];
  if (enabled) classes.push("vsm-pretty-code-button_activated");

  return (<button type="button" className={classes.join(" ")} onClick={onClick}>{"{}"}</button>);
};
