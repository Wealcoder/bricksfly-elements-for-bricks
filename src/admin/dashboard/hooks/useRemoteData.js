import { useEffect, useState } from "react";

/**
 * Returns fallback data when no endpoint is configured yet.
 * Once an endpoint is provided, it fetches from it and shows only the
 * fetched result (falling back only if that request fails).
 */
export const useRemoteData = (endpoint, fallback) => {
  const [data, setData] = useState(fallback);
  const [loading, setLoading] = useState(Boolean(endpoint));

  useEffect(() => {
    if (!endpoint) {
      setData(fallback);
      setLoading(false);
      return;
    }

    let cancelled = false;
    setLoading(true);

    fetch(endpoint, {
      headers: { "X-WP-Nonce": BRICKSFLY_ADDONS_ADMIN.nonce },
    })
      .then((res) => res.json())
      .then((json) => {
        if (!cancelled) setData(json);
      })
      .catch(() => {
        if (!cancelled) setData(fallback);
      })
      .finally(() => {
        if (!cancelled) setLoading(false);
      });

    return () => {
      cancelled = true;
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [endpoint]);

  return { data, loading };
};
