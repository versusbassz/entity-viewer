import "../styles/main.scss";

jQuery(document).ready(function($) {
  var $metabox = $('.js-metaviewer-metabox');
  var $header = $metabox.find('.js-metaviewer-metabox-header');
  var $content = $metabox.find('.js-metaviewer-metabox-content');

  $header.click(function () {
    $metabox.toggleClass('js-metaviewer-metabox-closed');
    handle_open_close_state();
  });

  var $data_table = $('.js-metaviewer-data');

  var $pretty_links = $data_table.find('.js-pretty-code-button');
  var $pretty_all_link = $data_table.find('.js-pretty-code-button-all');

  var table = $data_table.stupidtable();

  table.bind('aftertablesort', function (event, data) {
    var arrow_class = 'vs-arrow';

    var th = $(this).find("th");
    th.find('.' + arrow_class).remove();

    var dir = $.fn.stupidtable.dir;

    var arrow_dir_class = data.direction === dir.ASC ? "vs-arrow_dir_up" : "vs-arrow_dir_down";
    th.eq(data.column).append('<span class="' + arrow_class + ' ' + arrow_dir_class + '"></span>');
  });

  $pretty_links.click(function (e) {

    var types = ['plain', 'pretty'];

    var $target = $( e.target );
    var prev_type = $target.attr('data-current-type');

    var new_type = types.filter(function(item) {
      return item !== prev_type;
    })[0];

    if (new_type === 'pretty') {
      $target.addClass('vs-pretty-code-button_activated');
    } else {
      $target.removeClass('vs-pretty-code-button_activated');
    }

    $target.attr('data-current-type', new_type);

    var $value_cell = $target.parent().siblings('.vs-table__column_content_value');
    $value_cell
      .find('[data-type="' + new_type + '"]')
      .show()
      .siblings()
      .hide();
  });

  $pretty_all_link.click(function (e) {

    var types = ['plain', 'pretty'];

    var $target = $(e.target);
    var previous_type = $target.attr('data-current-type');

    var new_type = types.filter(function(item) {
      return item !== previous_type;
    })[0];

    if (new_type === 'pretty') {
      $target.addClass('vs-pretty-code-button_activated');
    } else {
      $target.removeClass('vs-pretty-code-button_activated');
    }

    $target.attr('data-current-type', new_type);

    $pretty_links.attr('data-current-type', previous_type);
    $pretty_links.trigger('click');
  });

  // Saving open/close metabox's state on entities' pages without Metabox API
  var open_close_cookie_name = 'vs-metaviewer-metabox-closed-for-' + $metabox.attr('data-entity-type');
  var open_close_cookie_values = ['opened', 'closed'];
  var open_close_handler_enabled = false;
  var open_close_handler_was_lauched = false;

  if ($metabox.length) {
    open_close_handler_enabled = true;
    handle_open_close_state();
  }

  function handle_open_close_state() {

    if (! open_close_handler_enabled) {
      return;
    }

    var cookie_value = Cookies.get(open_close_cookie_name);

    if (! jQuery.inArray(cookie_value, open_close_cookie_values)) {
      cookie_value = 'opened';
      Cookies.set(open_close_cookie_name, 'opened');
    }

    var current_state = $metabox.hasClass('js-metaviewer-metabox-closed') ? 'closed' : 'opened';

    if (open_close_handler_was_lauched) {
      Cookies.set(open_close_cookie_name, current_state);
    } else {

      open_close_handler_was_lauched = true;

      if (current_state !== cookie_value && cookie_value === 'closed') {
        $metabox.addClass('js-metaviewer-metabox-closed');
      }
    }
  }

  // "Search" functionality. See the specification in Wiki on Github.
  const $search = $('.js-vsm-search');
  const $search_reset = $('.js-vsm-search-reset');
  const $rows = $('.js-metaviewer-data').find('.js-vsm-data-row');

  $search.change(handleSearchChange);
  $search.keyup(handleSearchChange);

  function handleSearchChange(e) {
    const $input = $(e.target);
    const query = $input.val();

    query ? $search_reset.show() : $search_reset.hide();

    if (! query) {
      $rows.show();
      return;
    }

    $rows.each((index, element) => {
      const $row = $(element);
      $row.text().toLowerCase().includes(query.toLowerCase()) ? $row.show() : $row.hide();
    });
  }

  // Disable sending a form on pressed Enter in "Search" input
  $search.keypress((e) => {
    if (e.which == 13)  {
      e.preventDefault();
      return false;
    }
  });

  // Reset "Search" input on "Reset" button click
  $search_reset.click(() => {
    $search.val('');
    $search.trigger('change')
  });
});
