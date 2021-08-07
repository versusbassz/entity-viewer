import React, { useState, useEffect } from "react";
import Cookies from "js-cookie";

import { MetaboxContent } from "./MetaboxContent";
import { useMetaboxContext } from "../../context";

/**
 * The whole metabox (including the header section and the toggleable body)
 */
export const Metabox = () => {
  const { metaboxSettings: { entity_type, metabox_header } } = useMetaboxContext();

  const statusValues = ['opened', 'closed'];
  const cookieName =  'vsm-metabox-status--' + entity_type;

  const [status, setStatus] = useState("opened");

  // set initial toggle status from Cookie
  useEffect(() => {
    const savedStatus = Cookies.get(cookieName);

    if (savedStatus && statusValues.includes(savedStatus)) {
      setStatus(savedStatus);
    }
  }, []);

  // change cookie on every change of toggle status
  useEffect(() => {
    Cookies.set(cookieName, status, {expires: 7});
  }, [status]);

  const contentClasses = ["vsm-metabox__content"];
  status === "closed" && contentClasses.push("vsm-metabox__content_closed");

  const toggleStatus = () => setStatus(status === "opened" ? "closed" : "opened");

  return (
    <div className="vsm-metabox">
      <h2 className="vsm-metabox__header" onClick={toggleStatus}>{metabox_header}</h2>

      <div className={contentClasses.join(" ")}>
        <MetaboxContent />
      </div>
    </div>
  );
};
