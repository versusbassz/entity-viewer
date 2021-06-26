import React from "react";
import ReactDOM from "react-dom";

import "../styles/main.scss";
import { Metabox } from "./Metabox";

window.addEventListener('DOMContentLoaded', () => {
  ReactDOM.render(<Metabox />, document.getElementById("js-vsm-metabox"));
});
