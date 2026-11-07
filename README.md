# Hide Generator Meta Tag

An Open Journal Systems (OJS) 3.5 generic plugin that removes the `<meta name="generator">` tag (the OJS version fingerprint) from the HTML output, making it harder to fingerprint the exact OJS version. No core files are modified, so the plugin survives upgrades.

It should also work on OMP and OPS 3.5, which share the PKP codebase.

## Installation

### Option A: upload via the UI

1. As an administrator, go to **Settings > Website > Plugins**.
2. Choose **Upload A New Plugin** and upload the release archive `hideGenerator.tar.gz`.
3. Enable **Hide Generator Meta Tag** under **Generic Plugins**.

### Option B: manual file install

1. Copy the plugin folder into `plugins/generic/` so that `plugins/generic/hideGenerator/version.xml` exists.
2. Register it in the database:
   ```
   php lib/pkp/tools/installPluginVersion.php plugins/generic/hideGenerator/version.xml
   ```
3. Enable it under **Settings > Website > Plugins > Generic Plugins**.

## Verify

```
curl -s https://your-ojs-site.example/ | grep -i generator
```

The output should be empty. If the tag is still present, clear the template cache (`cache/t_compile/` and `cache/t_cache/`) and reload.

## How it works

The plugin hooks into `TemplateManager::display` and registers a Smarty output filter. The filter strips every `<meta name="generator" ...>` from the fully rendered page using a regular expression. Both quote styles and self-closing tags are matched, and the match is case-insensitive.

## Releases / packaging

The repository root holds the plugin files directly. A release archive for OJS must wrap them in a top-level `hideGenerator/` folder:

```
tar -czf hideGenerator.tar.gz hideGenerator/
```

Attach that archive to the GitHub Release and reference it from the PKP Plugin Gallery entry.

## License

GNU GPL v3. See [LICENSE](LICENSE).
