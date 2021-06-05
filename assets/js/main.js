import Cookies from "js-cookie";
import React from "react";
import ReactDOM from "react-dom";

import "../styles/main.scss";
import { Metabox } from "./Metabox";

window.addEventListener('DOMContentLoaded', () => {
  ReactDOM.render(<Metabox />, document.getElementById("js-vsm-metabox"));
});

// TODO transform to React logic and remove
jQuery(document).ready(function($) {
  const $metabox = $('.js-metaviewer-metabox');
  const $header = $metabox.find('.js-metaviewer-metabox-header');
  const $content = $metabox.find('.js-metaviewer-metabox-content');

  $header.click(function () {
    $metabox.toggleClass('js-metaviewer-metabox-closed');
    handle_open_close_state();
  });

  // Saving open/close metabox's state on entities' pages without Metabox API
  const open_close_cookie_name = 'vs-metaviewer-metabox-closed-for-' + $metabox.attr('data-entity-type');
  const open_close_cookie_values = ['opened', 'closed'];
  let open_close_handler_enabled = false;
  let open_close_handler_was_lauched = false;

  if ($metabox.length) {
    open_close_handler_enabled = true;
    handle_open_close_state();
  }

  function handle_open_close_state() {

    if (! open_close_handler_enabled) {
      return;
    }

    let cookie_value = Cookies.get(open_close_cookie_name);

    if (! $.inArray(cookie_value, open_close_cookie_values)) {
      cookie_value = 'opened';
      Cookies.set(open_close_cookie_name, 'opened');
    }

    const current_state = $metabox.hasClass('js-metaviewer-metabox-closed') ? 'closed' : 'opened';

    if (open_close_handler_was_lauched) {
      Cookies.set(open_close_cookie_name, current_state);
    } else {

      open_close_handler_was_lauched = true;

      if (current_state !== cookie_value && cookie_value === 'closed') {
        $metabox.addClass('js-metaviewer-metabox-closed');
      }
    }
  }
});
