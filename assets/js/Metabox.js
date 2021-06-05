import React, { useState } from "react";
import { dynamicSort } from "./utils";

export const Metabox = () => {

  const [fields, setFields] = useState([
    {
      id: 1,
      key: 'key1',
      value: 'value1',
      value_pretty: null,
    },
    {
      id: 2,
      key: 'key2',
      value: 'value2',
      value_pretty: null,
    },
    {
      id: 3,
      key: 'zkey3',
      value: 'value3',
      value_pretty: 'value3-pretty',
    },
    {
      id: 4,
      key: 'key4',
      value: 'value4',
      value_pretty: 'value4-pretty',
    },
  ]);
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
      <div className="vs-not-exists-message">There are no meta fields for this item.</div>
    );
  }

  const orderSign = ui.sorting.dir.toLowerCase() === "asc" ? "" : "-";
  let fieldsSorted = [...fields].sort(dynamicSort(`${orderSign}${ui.sorting.column}`));

  // Search logic
  if (ui.search) {
    fieldsSorted = fieldsSorted.filter((field) => {
      let contains = false;

      ['id', 'key', 'value'].forEach((field_name) => {
        let value = Number.isInteger(field[field_name]) ? field[field_name].toString(10) : field[field_name];

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
        <table className="vs-table">
          <thead>
            <tr>
              <th
                className="vs-table__column vs-table__column_sortable vs-table__column_content_umeta-id"
                onClick={() => sortFields("id")}
              >
                Meta id
                <SortingArrow show={ui.sorting.column === "id"} dir={ui.sorting.dir} />
              </th>

              <th
                className="vs-table__column vs-table__column_sortable"
                onClick={() => sortFields("key")}
              >
                Key
                <SortingArrow show={ui.sorting.column === "key"} dir={ui.sorting.dir} />
              </th>

              <th className="vs-table__column table__column_type_th">
                {ui.showPrettifyAllButton && (
                  <ToggleButton
                    onClick={() => toggleAllFieldsPretty(allFieldsPretty)}
                    enabled={allFieldsPretty}
                  />
                )}
              </th>
              <th className="vs-table__column table__column_type_th">Value</th>
            </tr>
          </thead>

          <tbody>
            {fieldsSorted.map((item) => {
              const valueType = prettyFields.includes(item.id) ? "pretty" : "plain";

              return (
                <tr key={item.id.toString()} className="vs-table__row">
                  <td className="vs-table__column vs-table__column_type_td">{item.id}</td>
                  <td className="vs-table__column vs-table__column_type_td">{item.key}</td>

                  <td className="vs-table__column vs-table__column_type_td">
                    {item.value_pretty && (
                      <ToggleButton
                        onClick={() => togglePrettyRow(item.id)}
                        enabled={prettyFields.includes(item.id)}
                      />
                    )}
                  </td>

                  <td className="vs-table__column vs-table__column_type_td vs-table__column_content_value">
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
        <div className="vs-message vs-message_type_not-found">There are no meta fields for this search query.</div>
      )}
    </>
  );
}

const ToggleButton = ({onClick, enabled}) => {
  let classes = ["vs-pretty-code-button"];
  if (enabled) classes.push("vs-pretty-code-button_activated");

  return (<button type="button" className={classes.join(" ")} onClick={onClick}>{"{}"}</button>);
};

const SortingArrow = ({show, dir}) => {
  if (! show) {
    return null;
  }

  const classes = ["vs-arrow"];
  classes.push(dir === "asc" ? "vs-arrow_dir_up" : "vs-arrow_dir_down")

  return (
    <span className={classes.join(" ")} />
  );
}
