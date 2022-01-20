import React, { useState } from "react";

import { Spoiler } from "./Spoiler";
import { SortingArrow } from "./SortingArrow";
import { ToggleButton } from "./ToggleButton";
import { CellContent } from "./CellContent";
import { Quote } from "./Quote";
import { str } from "../../utils/i18n";
import { searchString } from "../../utils/strings";
import { dynamicSort } from "../../utils/sorting";
import { SPOILER_MIN_LENGTH } from "../../constants";

export const MetaTable = ({ fieldsData, search }) => {
  const [ ui, setUI ] = useState({
    showPrettifyAllButton: true,
    sorting: {
      column: "id",
      dir: "asc",
    },
  });

  const [ prettyFields, setPrettyFields ] = useState([]);

  const fields = fieldsData?.fields ?? [];

  if (! fields.length) {
    return <div className="vsm-message vsm-message_type_not-exists">{str("fields_not_found")}</div>;
  }

  // Pretty-view logic
  const serializedFields = fields.filter((item) => item.value_pretty);


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

  // Sorting
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

  const orderSign = ui.sorting.dir.toLowerCase() === "asc" ? "" : "-";
  let fieldsSorted = [...fields].sort(dynamicSort(`${orderSign}${ui.sorting.column}`));

  // Search logic
  if (search) {
    fieldsSorted = fieldsSorted.filter((field) => {
      let contains = false;

      ['id', 'key', 'value'].forEach((field_name) => {
        let value = Number.isInteger(field[field_name]) ? field[field_name].toString(10) : String(field[field_name]);

        if (! contains && searchString(search, value)) {
          contains = true;
        }
      });

      return contains;
    });
  }

  if (! fieldsSorted.length) {
    return <div className="vsm-message vsm-message_type_not-found">{str("fields_not_found_for_search_query")}</div>;
  }

  return (
    <table className="vsm-table">
      <thead>
      <tr>
        <th
          className="vsm-table__column vsm-table__column_sortable vsm-table__column_content_umeta-id"
          onClick={() => sortFields("id")}
        >
          {str("th_id")}
          <SortingArrow show={ui.sorting.column === "id"} dir={ui.sorting.dir} />
        </th>

        <th
          className="vsm-table__column vsm-table__column_sortable"
          onClick={() => sortFields("key")}
        >
          {str("th_key")}
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
        <th className="vsm-table__column table__column_type_th">{str("th_value")}</th>
      </tr>
      </thead>

      <tbody>
      {fieldsSorted.map((item) => {
        const valueType = prettyFields.includes(item.id) ? "pretty" : "plain";

        return (
          <tr key={item.id.toString()} className="vsm-table__row">
            <td className="vsm-table__column vsm-table__column_type_td">
              <CellContent value={item.id} search={search} />
            </td>

            <td className="vsm-table__column vsm-table__column_type_td">
              <CellContent value={item.key} search={search} />
            </td>

            <td className="vsm-table__column vsm-table__column_type_td">
              {item.value_pretty && (
                <ToggleButton
                  onClick={() => togglePrettyRow(item.id)}
                  enabled={prettyFields.includes(item.id)}
                />
              )}
            </td>

            <td className="vsm-table__column vsm-table__column_type_td vsm-table__column_content_value">
              <Spoiler enabled={item?.value?.length >= SPOILER_MIN_LENGTH}>
                {valueType === "pretty" ? (
                  <>
                    <div className="vsm-value_type_pretty">
                      <CellContent value={item.value_pretty} search={search} />
                    </div>
                    {search && (searchString(search, item.value) && ! searchString(search, item.value_pretty)) && (
                      <span className="vsm-value-note">* {str("see_raw_value")}</span>
                    )}
                  </>
                ) : (
                  <div className="vsm-value_type_plain">
                    {item.value === null ? (
                      <span className="vsm-null">null</span>
                    ) : (
                      <>
                        <Quote />
                        <CellContent value={item.value} search={search} />
                        <Quote />
                      </>
                    )}
                  </div>
                )}
              </Spoiler>
            </td>
          </tr>
        );
      })}
      </tbody>
    </table>
  );
};
