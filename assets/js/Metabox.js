import React, { useState, useEffect } from "react";
import Cookies from "js-cookie";
import { dynamicSort } from "./utils";

export const Metabox = ({data}) => {
  return (
    data.metabox_type === "full" ? (
      <MetaboxFull data={data} />
    ) : (
      <MetaboxContent fields={data.fields} />
    )
  );
};

export const MetaboxFull = ({data}) => {
  const statusValues = ['opened', 'closed'];
  const cookieName =  'vsm-metabox-status--' + data['entity_type'];

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
        <MetaboxContent fields={data.fields} />
      </div>
    </div>
  );
};

export const MetaboxContent = ({fields}) => {
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
            if (e.which == 13)  {
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
                      <div datatype="pretty">
                        <pre>{item.value_pretty}</pre>
                      </div>
                    ) : (
                      <div datatype="plain">
                        <div>&#39;{item.value}&#39;</div>
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
