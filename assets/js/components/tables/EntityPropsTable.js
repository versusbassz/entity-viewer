import React, { useState } from "react";

import { SortingArrow } from "./SortingArrow";
import { CellContent } from "./CellContent";
import { str } from "../../utils/i18n";
import { dynamicSort } from "../../utils/sorting";
import { searchString } from "../../utils/strings";

export const EntityPropsTable = ({ fieldsData, search }) => {
  const [ ui, setUI ] = useState({
    sorting: {
      column: "db_order",
      dir: "asc",
    },
  });

  const [ prettyFields, setPrettyFields ] = useState([]);

  const fields = fieldsData?.fields ?? [];

  if (! fields.length) {
    return <div className="vsm-message vsm-message_type_not-exists">{str("fields_not_found")}</div>;
  }

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

      ['db_order', 'key', 'value'].forEach((field_name) => {
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
          className="vsm-table__column vsm-table__column_sortable"
          onClick={() => sortFields("db_order")}
        >
          {str("th_db_order")}
          <SortingArrow show={ui.sorting.column === "db_order"} dir={ui.sorting.dir} />
        </th>

        <th
          className="vsm-table__column vsm-table__column_sortable"
          onClick={() => sortFields("key")}
        >
          {str("th_key")}
          <SortingArrow show={ui.sorting.column === "key"} dir={ui.sorting.dir} />
        </th>

        <th className="vsm-table__column table__column_type_th">{str("th_value")}</th>
      </tr>
      </thead>

      <tbody>
      {fieldsSorted.map((item) => {
        const valueType = prettyFields.includes(item.id) ? "pretty" : "plain";

        return (
          <tr key={item.key} className="vsm-table__row">

            {/* The empty column for "DB order" sorting */}
            <td className="vsm-table__column vsm-table__column_type_td vsm-table__column_content_db-order">
              {item.db_order + 1}
            </td>

            <td className="vsm-table__column vsm-table__column_type_td">
              <CellContent value={item.key} search={search} />
            </td>

            <td className="vsm-table__column vsm-table__column_type_td vsm-table__column_content_value">
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
                      &#39;
                      <CellContent value={item.value} search={search} />
                      &#39;
                    </>
                  )}
                </div>
              )}
            </td>

          </tr>
        );
      })}
      </tbody>
    </table>
  );
}
