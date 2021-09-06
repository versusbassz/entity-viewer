import React from "react";

import { MetaboxContent } from "./MetaboxContent";
import { useMetaboxContext } from "../../context";
import { useLocalStorage } from "../../hooks/useLocalStorage";

/**
 * The whole metabox (including the header section and the toggleable body)
 */
export const Metabox = () => {
  const { metaboxSettings: { entity_type, metabox_header } } = useMetaboxContext();

  const [status, setStatus] = useLocalStorage("opened", "vsm-metabox-status", entity_type);

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
