import React, { useState } from "react";
import classNames from "classnames";

import { str } from "../../utils/i18n";

export const Spoiler = ({children, enabled = false}) => {
  if (! enabled) return children;

  const [opened, setOpened] = useState(false);

  const open = () => {
    ! opened && setOpened(true)
  };
  const close = () => {
    opened && setOpened(false);
  }

  const limiterClasses = ! opened ? ["vsm-spoiler__limiter"] : [];

  return (
    <div className="vsm-spoiler">
      {opened && <ToggleButton type="top" onClick={close} />}

      <div className={classNames(limiterClasses)} onClick={open}>
        {! opened && <div className="vsm-spoiler__overflow" />}
        {children}
      </div>

      {opened && <ToggleButton type="bottom" onClick={close} />}
    </div>
  );
};

const ToggleButton = ({ type, onClick }) => {
  const classes = classNames([
    "vsm-spoiler__button",
    `vsm-spoiler__button--${type}`
  ]);

  return <div className={classes} onClick={onClick}>{str("close")}</div>;
};
