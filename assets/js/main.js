import React from "react";
import ReactDOM from "react-dom";

import "../styles/main.scss";
import { Metabox } from "./Metabox";

window.addEventListener('DOMContentLoaded', () => {
  const jsonData = document.getElementById('js-vsm-fields-data').value;
  const dataParsed = JSON.parse(jsonData);

  ReactDOM.render(<Metabox data={dataParsed} />, document.getElementById("js-vsm-metabox"));
});
