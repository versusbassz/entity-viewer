import React from "react";

export const SortingArrow = ({show, dir}) => {
  if (! show) {
    return null;
  }

  const classes = ["vsm-arrow"];
  classes.push(dir === "asc" ? "vsm-arrow_dir_up" : "vsm-arrow_dir_down")

  return (
    <span className={classes.join(" ")} />
  );
}
