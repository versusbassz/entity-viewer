import React, { useState, useEffect, useRef } from "react";
import Cookies from "js-cookie";
import Highlighter from "react-highlight-words";
import { dynamicSort } from "./utils";

export const Metabox = () => {
  const [data, setData] = useState({});

  // getting initial data
  useEffect(() => {
    const jsonData = document.getElementById('js-vsm-fields-data').value;
    setData(JSON.parse(jsonData));
  }, []);

  const refreshFields = async (setLoading, setLastUpdated, setShowDone) => {
    const urlParams = (new URLSearchParams(window.vsm.query_args)).toString();
    const url = window.vsm.ajax_url + '?' + urlParams;
    setLoading(true);

    const response = await fetch(url);

    const log = (data) => console.log("wp-meta-viewer response: ", data);

    if (response.ok) {
      let fields = await response.json();

      if (Array.isArray(fields)) {
        setData({...data, ...{fields: fields}});
        setLastUpdated(Date.now());
        setShowDone(true);
      } else {
        alert("Incorrect response, see dev-tools (console) for details");
        log(fields);
      }
    } else {
      alert("HTTP error: " + response.status + ", see dev-tools (console) for details");
      log(await response.text());
    }

    setLoading(false);
  };

  if (! data.fields) {
    return <div>&quot;Meta viewer&quot; plugin is loading the initial state...</div>;
  }

  return (
    data.metabox_type === "full" ? (
      <MetaboxFull data={data} refreshFields={refreshFields} />
    ) : (
      <MetaboxContent fields={data.fields} fetchedInitial={data.fetched_initial} refreshFields={refreshFields} />
    )
  );
};

export const MetaboxFull = ({data, refreshFields}) => {
  const statusValues = ['opened', 'closed'];
  const cookieName =  'vsm-metabox-status--' + data.entity_type;

  const [status, setStatus] = useState("opened");

  useEffect(() => {
    const savedStatus = Cookies.get(cookieName);

    if (savedStatus && statusValues.includes(savedStatus)) {
      setStatus(savedStatus);
    }
  }, []);

  useEffect(() => {
    Cookies.set(cookieName, status, {expires: 7});
  }, [status]);

  const contentClasses = ["vsm-metabox__content"];
  status === "closed" && contentClasses.push("vsm-metabox__content_closed");

  const toggleStatus = () => setStatus(status === "opened" ? "closed" : "opened");

  return (
    <div className="vsm-metabox">
      <h2 className="vsm-metabox__header" onClick={toggleStatus}>{data.metabox_header}</h2>

      <div className={contentClasses.join(" ")}>
        <MetaboxContent fields={data.fields} fetchedInitial={data.fetched_initial} refreshFields={refreshFields} />
      </div>
    </div>
  );
};

export const MetaboxContent = ({fields, refreshFields, fetchedInitial}) => {
  const serializedFields = fields.filter((item) => item.value_pretty);

  const [ ui, setUI ] = useState({
    showPrettifyAllButton: true,
    sorting: {
      column: "id",
      dir: "asc",
    },
    search: "",
  });

  const [ prettyFields, setPrettyFields ] = useState([]);

  const togglePrettyRow = (id) => {
    if (prettyFields.includes(id)) {
      setPrettyFields(prettyFields.filter((item) => item !== id));
    } else {
      setPrettyFields([...prettyFields, id]);
    }
  };

  const toggleAllFieldsPretty = (enabledBefore) => {
    if (enabledBefore) {
      setPrettyFields([]);
    } else {
      setPrettyFields(serializedFields.map((item) => item.id));
    }
  }

  const allFieldsPretty = serializedFields.length === prettyFields.length;

  const sortFields = (column) => {
    setUI((prev) => {
      const newUIState = {...prev};
      let dir;

      if (prev.sorting.column === column) {
        dir = prev.sorting.dir === "asc" ? "desc" : "asc";
      } else {
        dir = "asc";
      }

      newUIState.sorting = {column, dir};
      return newUIState;
    });
  };

  if (! fields.length) {
    return (
      <div className="vsm-message vsm-message_type_not-exists">There are no meta fields for this item.</div>
    );
  }

  const orderSign = ui.sorting.dir.toLowerCase() === "asc" ? "" : "-";
  let fieldsSorted = [...fields].sort(dynamicSort(`${orderSign}${ui.sorting.column}`));

  // Search logic
  if (ui.search) {
    fieldsSorted = fieldsSorted.filter((field) => {
      let contains = false;

      ['id', 'key', 'value'].forEach((field_name) => {
        let value = Number.isInteger(field[field_name]) ? field[field_name].toString(10) : String(field[field_name]);

        if (! contains && value.toLowerCase().includes(ui.search.toLowerCase())) {
          contains = true;
        }
      });

      return contains;
    });
  }

  return (
    <>
      <div className="vsm-top-panel">
        <div className="vsm-search">
          <input
            type="text"
            className="vsm-search__input"
            placeholder="Search"
            value={ui.search}
            onChange={(e) => {
              e.preventDefault();
              setUI({...ui, ...{search: e.target.value}});
            }}
            onKeyPress={(e) => {
              if (e.which == 13) { // Enter
                e.preventDefault();
                return false;
              }
            }}
          />
          <button
            type="button"
            className="button-secondary vsm-search__reset"
            style={ui.search ? {} : {display: "none"}}
            onClick={() => setUI({...ui, ...{search:""}})}
          >Reset</button>
        </div>

        <div className="vsm-top-panel__refresh">
          <RefreshButton refreshFields={refreshFields} fetchedInitial={fetchedInitial} />
        </div>
      </div>

      {fieldsSorted.length ? (
        <table className="vsm-table">
          <thead>
            <tr>
              <th
                className="vsm-table__column vsm-table__column_sortable vsm-table__column_content_umeta-id"
                onClick={() => sortFields("id")}
              >
                Meta id
                <SortingArrow show={ui.sorting.column === "id"} dir={ui.sorting.dir} />
              </th>

              <th
                className="vsm-table__column vsm-table__column_sortable"
                onClick={() => sortFields("key")}
              >
                Key
                <SortingArrow show={ui.sorting.column === "key"} dir={ui.sorting.dir} />
              </th>

              <th className="vsm-table__column table__column_type_th">
                {ui.showPrettifyAllButton && !! serializedFields.length && (
                  <ToggleButton
                    onClick={() => toggleAllFieldsPretty(allFieldsPretty)}
                    enabled={allFieldsPretty}
                  />
                )}
              </th>
              <th className="vsm-table__column table__column_type_th">Value</th>
            </tr>
          </thead>

          <tbody>
            {fieldsSorted.map((item) => {
              const valueType = prettyFields.includes(item.id) ? "pretty" : "plain";

              return (
                <tr key={item.id.toString()} className="vsm-table__row">
                  <td className="vsm-table__column vsm-table__column_type_td">{item.id}</td>
                  <td className="vsm-table__column vsm-table__column_type_td">{item.key}</td>

                  <td className="vsm-table__column vsm-table__column_type_td">
                    {item.value_pretty && (
                      <ToggleButton
                        onClick={() => togglePrettyRow(item.id)}
                        enabled={prettyFields.includes(item.id)}
                      />
                    )}
                  </td>

                  <td className="vsm-table__column vsm-table__column_type_td vsm-table__column_content_value">
                    {valueType === "pretty" ? (
                      <div className="vsm-value_type_pretty">
                        {item.value_pretty && ui.search ? (
                          <Highlighter
                            highlightClassName="vsm-highlighted"
                            searchWords={[ui.search]}
                            textToHighlight={item.value_pretty}
                          />
                        ) : (
                          item.value_pretty
                        )}
                      </div>
                    ) : (
                      <div className="vsm-value_type_plain">
                        &#39;
                        {item.value && ui.search ? (
                          <Highlighter
                            highlightClassName="vsm-highlighted"
                            searchWords={[ui.search]}
                            textToHighlight={item.value}
                          />
                        ) : (
                          item.value
                        )}
                        &#39;
                      </div>
                    )}
                  </td>
                </tr>
              );
            })}
          </tbody>
        </table>
      ) : (
        <div className="vsm-message vsm-message_type_not-found">There are no meta fields for this search query.</div>
      )}
    </>
  );
}

const ToggleButton = ({onClick, enabled}) => {
  let classes = ["vsm-pretty-code-button"];
  if (enabled) classes.push("vsm-pretty-code-button_activated");

  return (<button type="button" className={classes.join(" ")} onClick={onClick}>{"{}"}</button>);
};

const SortingArrow = ({show, dir}) => {
  if (! show) {
    return null;
  }

  const classes = ["vsm-arrow"];
  classes.push(dir === "asc" ? "vsm-arrow_dir_up" : "vsm-arrow_dir_down")

  return (
    <span className={classes.join(" ")} />
  );
}

const RefreshButton = ({refreshFields, fetchedInitial}) => {
  const [loading, setLoading] = useState(false);
  const [lastUpdated, setLastUpdated] = useState(0);
  const [showDone, setShowDone] = useState(false);

  // php provides timestamps in seconds, js prefers miliseconds
  useEffect(() => setLastUpdated(fetchedInitial * 1000), []);

  // tracking saving state of Gutenberg
  useEffect(() => {
    let prevSavingState = false;
    const editor = wp.data.select('core/editor');

    const unsubscribe = wp.data.subscribe(() => {
      const isSavingPost = editor.isSavingPost();
      if (isSavingPost === prevSavingState || editor.isAutosavingPost()) return;

      prevSavingState = isSavingPost;
      const didPostSaveRequestSucceed = editor.didPostSaveRequestSucceed();

      if (isSavingPost === false && didPostSaveRequestSucceed) {
        refreshFields(setLoading, setLastUpdated, setShowDone);
      }
    });

    return () => unsubscribe();
  })

  const buttonText = loading ? "Loading..." : "Refresh data";

  const visualLastUpdated = lastUpdated
    ? (new Date(lastUpdated)).toLocaleTimeString()
    : "";

  let hideDoneTimer = useRef(false);

  useEffect(() => {
    if (showDone) {
      if (hideDoneTimer.current) clearTimeout(hideDoneTimer.current);
      hideDoneTimer.current = setTimeout(() => setShowDone(false), 1500);
    } else if (! showDone && hideDoneTimer.current) {
      clearTimeout(hideDoneTimer.current);
    }
  }, [showDone]);

  return (
    <div className="vsm-refresh">
      <button
        type="button"
        className="button-secondary vsm-refresh__button"
        style={loading ? {opacity: 0.5} : {}}
        disabled={loading}
        onClick={() => refreshFields(setLoading, setLastUpdated, setShowDone)}
        onMouseUp={(e) => e.target.blur()}
      >{buttonText}</button>

      <span className="vsm-refresh__last-updated">
        {! loading && visualLastUpdated ? `Last updated: ${visualLastUpdated}` : ""}
        {! loading && showDone && (<span className="vsm-refresh__success">Done!</span>)}
      </span>
    </div>
  );
};
