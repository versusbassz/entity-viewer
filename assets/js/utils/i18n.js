/**
 * Outputs a localized string by its id.
 * If a string is not found by its id, the id is displayed as a fallback.
 */
export function str(str_id) {
  return window?.vsm?.i18n?.[str_id] ?? str_id;
}
