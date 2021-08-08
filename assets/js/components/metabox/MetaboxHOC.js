import React from "react";

import { Metabox } from "./Metabox";
import { MetaboxContent } from "./MetaboxContent";
import { useMetaboxContext } from "../../context";
import { str } from "../../utils/i18n";

/**
 * The root component.
 * It's HOC, loads the full metabox (with header & toggling) or only its content
 * depending on a type of an admin page.
 *
 * Contains root state of a metabox.
 */
export const MetaboxHOC = () => {
  const { initialStateLoaded, metaboxSettings: { metabox_type } } = useMetaboxContext();

  if (! initialStateLoaded) {
    return <div>{str("loading_initial_state")}</div>;
  }

  return (
    metabox_type === "full" ? (
      <Metabox />
    ) : (
      <MetaboxContent />
    )
  );
};
