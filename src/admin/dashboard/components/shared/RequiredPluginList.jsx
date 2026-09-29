import { Badge } from "@/components/ui/badge";
import { Checkbox } from "@/components/ui/checkbox";

// Admin URL built from ajaxurl (…/wp-admin/admin-ajax.php).
const adminUrl = (path) =>
  String(BRICKSFLY_ADDONS_ADMIN?.ajaxurl || "").replace(
    /admin-ajax\.php.*$/,
    path,
  );

/**
 * Required plugins of a starter template / page.
 *
 * The free plugin never installs or activates plugins. Without an add-on
 * that handles them (`import_plugins`), each plugin shows its status and a
 * link to the WordPress Plugins screen so the user can install / activate
 * it themselves. With the add-on, the user ticks which plugins it should set up.
 */
const RequiredPluginList = ({
  plugins = [],
  selectedPlugins,
  setSelectedPlugins,
  onRefresh,
}) => {
  const managed = !!BRICKSFLY_ADDONS_ADMIN?.import_plugins;

  return (
    <div className="space-y-4">
      <p className="text-sm text-text-secondary">
        {managed
          ? "Selected plugins will be installed and activated during import."
          : "Install and activate these plugins from the Plugins screen for the template to work as shown. The import itself does not install or activate plugins."}
      </p>
      {plugins.map((plugin, i) => {
        const notInstalled = plugin?.status === "Not Installed";
        const active = plugin?.status === "Active";

        return (
          <div className="flex items-center space-x-2.5" key={plugin.slug + i}>
            {managed ? (
              <Checkbox
                id={`plugin-${plugin.slug}`}
                checked={selectedPlugins.includes(plugin?.slug)}
                disabled={plugin?.required}
                onCheckedChange={(value) =>
                  setSelectedPlugins((prev) =>
                    value
                      ? [...prev, plugin?.slug]
                      : prev.filter((p) => p !== plugin?.slug),
                  )
                }
              />
            ) : null}
            <label
              htmlFor={`plugin-${plugin.slug}`}
              className="text-base font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
            >
              {plugin.name}
            </label>
            <Badge variant={notInstalled ? "inProgress" : "installed"}>
              {plugin?.status}
            </Badge>
            {!managed && !active ? (
              <a
                href={
                  notInstalled
                    ? adminUrl(
                        `plugin-install.php?s=${encodeURIComponent(plugin.slug)}&tab=search&type=term`,
                      )
                    : adminUrl("plugins.php?plugin_status=inactive")
                }
                target="_blank"
                rel="noopener noreferrer"
                className="text-sm font-medium text-[#5453FD] underline"
              >
                {notInstalled ? "Install" : "Activate"}
              </a>
            ) : null}
          </div>
        );
      })}
      {!managed && onRefresh ? (
        <button
          type="button"
          onClick={onRefresh}
          className="text-sm font-medium text-[#5453FD] underline"
        >
          Check status again
        </button>
      ) : null}
    </div>
  );
};

export default RequiredPluginList;
