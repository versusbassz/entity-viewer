import React from "react";
import ReactDOM from "react-dom";

import "../styles/main.scss";

import { MetaboxContextProvider } from "./context";
import { MetaboxHOC } from "./components/metabox/MetaboxHOC";

window.addEventListener('DOMContentLoaded', () => {
  const metabox_node = document.getElementById("js-vsm-metabox");

  if (metabox_node) {
    ReactDOM.render((
      <React.StrictMode>
        <MetaboxContextProvider>
          <MetaboxHOC />
        </MetaboxContextProvider>
      </React.StrictMode>
    ), metabox_node);
  }
});
