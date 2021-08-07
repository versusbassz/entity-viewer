import React from "react";
import Highlighter from "react-highlight-words";

export const CellContent = ({value: content, search}) => {
  // for "id" column
  const content_visible = Number.isInteger(content) ? content.toString() : content;

  return content && search ? (
    <Highlighter
      highlightClassName="vsm-highlighted"
      searchWords={[search]}
      textToHighlight={content_visible}
    />
  ) : (
    content
  );
};
