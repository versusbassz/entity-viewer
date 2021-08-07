import React, { useEffect, useRef, useState } from "react";
import { str } from "../../utils/i18n";

export const RefreshButton = ({ refreshFields, fetchedInitial }) => {
  const [loading, setLoading] = useState(false);
  const [lastUpdated, setLastUpdated] = useState(0);
  const [showDone, setShowDone] = useState(false);

  // PHP provides timestamps in seconds, JS prefers milliseconds
  useEffect(() => setLastUpdated(fetchedInitial * 1000), []);

  // tracking saving state of Gutenberg
  useEffect(() => {
    if (! window.wp || ! window.wp.data || ! document.body.classList.contains('block-editor-page')) {
      return;
    }
    const wp = window.wp;

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

  const buttonText = loading ? str("loading") : str("refresh_data");

  const visualLastUpdated = lastUpdated
    ? (new Date(lastUpdated)).toLocaleTimeString() // doesn't need i18n (done by a browser itself)
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
        {! loading && visualLastUpdated ? `${str("last_updated")}: ${visualLastUpdated}` : ""}
        {! loading && showDone && (<span className="vsm-refresh__success">{str("done")}</span>)}
      </span>
    </div>
  );
};
