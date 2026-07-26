// Recursively walks a { elements: { <group>: { elements: {...} | ... } } }
// config tree (widgets or extensions) and counts leaf slugs. A node is a
// leaf when it has no `elements` key of its own; groups can nest one extra
// level (e.g. extensions' general-settings/site-settings subgroups), so the
// walk recurses until it hits a real leaf rather than assuming a fixed depth.
export function countSlugs(section) {
  let total = 0;
  let active = 0;

  const walk = (node) => {
    Object.values(node?.elements || {}).forEach((child) => {
      if (child?.elements) {
        walk(child);
        return;
      }
      if (child?.is_upcoming) return;
      total += 1;
      if (child?.is_active) active += 1;
    });
  };

  walk(section);
  return { total, active };
}
