# Flexible Portfolio

A WordPress plugin that provides a filterable portfolio grid for posts and pages using native WordPress categories and tags. Designed as a migration-friendly alternative to Divi's built-in Filterable Portfolio module (which only works with the `project` custom post type).

## Architecture

Two-layer design:

1. **Standalone shortcode** `[tag_portfolio]` — works with or without Divi, survives migration to Gutenberg or any other theme.
2. **Divi Extension wrapper** — registers the module in Divi Builder with full Visual Builder (VB) live preview via a React component. Disposable at migration time.

```
flexible-portfolio/
├── flexible-portfolio.php              # Plugin entry point, constants, extension init, CSS enqueue
├── loader.php                          # Auto-loaded by DiviExtension on et_builder_ready
├── includes/
│   ├── FlexiblePortfolioExtension.php  # DiviExtension subclass
│   ├── tag-portfolio-shortcode.php     # Core shortcode (migration-safe, single source of truth)
│   └── modules/
│       └── TagPortfolio/
│           └── TagPortfolio.php        # ET_Builder_Module subclass (Divi integration)
├── scripts/
│   ├── src/
│   │   ├── loader.js                   # Registers React module on et_builder_api_ready
│   │   ├── frontend.js                 # Frontend bundle entry (empty — Divi JS handles filtering)
│   │   └── modules/
│   │       ├── index.js                # Module exports
│   │       └── TagPortfolio/
│   │           └── TagPortfolio.jsx    # React VB preview component
│   ├── builder-bundle.min.js           # Webpack output (React component for VB)
│   └── frontend-bundle.min.js          # Webpack output (empty, placeholder)
├── styles/
│   └── style.min.css                   # Empty (we use Divi's CSS)
├── assets/css/
│   └── portfolio-standalone.css        # Fallback CSS for post-Divi migration
├── package.json
└── webpack.config.js
```

## Features

- Filter by **categories**, **tags**, or **both**
- Include specific posts/pages by ID
- Grid or fullwidth layout with configurable column count
- Client-side filter tabs (click to filter by term)
- Client-side pagination (all items loaded, JS paginates — matches Divi's approach)
- Toggle visibility of: filter tabs, titles, category labels, pagination
- Sort by date ascending/descending
- Full Divi Visual Builder live preview
- Works as standalone `[tag_portfolio]` shortcode without Divi

## Shortcode Usage

```
[tag_portfolio
  post_type="post"
  filter_by="category"
  include_categories="56,94,125"
  posts_number="12"
  show_filter="on"
  show_title="on"
  show_categories="on"
  show_pagination="on"
  fullwidth="off"
  columns="4"
  order="DESC"
]
```

### Parameters

| Parameter | Values | Default | Description |
|---|---|---|---|
| `post_type` | `post`, `page`, `post,page` | `post` | Content types to query |
| `filter_by` | `category`, `post_tag`, `both` | `category` | Taxonomy for filter tabs |
| `include_categories` | comma-separated IDs | (empty) | Category term IDs to include |
| `include_tags` | comma-separated IDs | (empty) | Tag term IDs to include |
| `include_posts` | comma-separated IDs | (empty) | Specific post/page IDs |
| `posts_number` | integer | `12` | Items per page (pagination) |
| `show_filter` | `on`/`off` | `on` | Show filter tabs |
| `show_title` | `on`/`off` | `on` | Show post titles |
| `show_categories` | `on`/`off` | `on` | Show category/tag labels |
| `show_pagination` | `on`/`off` | `on` | Show pagination |
| `fullwidth` | `on`/`off` | `off` | Fullwidth (list) or grid layout |
| `columns` | `1`-`6` | `4` | Grid columns (only when fullwidth=off) |
| `order` | `DESC`/`ASC` | `DESC` | Sort order by date |

## Build

```bash
npm install
npx webpack --mode production
```

Produces `scripts/builder-bundle.min.js` and `scripts/frontend-bundle.min.js`. The built files are committed to the repo — no build step needed for deployment.

## Deployment

Copy the `flexible-portfolio/` directory to `wp-content/plugins/` and activate in WordPress admin. The plugin auto-detects Divi: with Divi active, it registers as a Divi Extension; without Divi, the shortcode still works standalone.

## Divi Integration Notes

### Important learnings for future development

**Module base class**: Use `ET_Builder_Module`, NOT `ET_Builder_Module_Type_PostBased`. The latter caused settings dialog issues and is intended for Divi's own post-based modules with specific internal expectations.

**VB support level**: Set `$vb_support = 'on'` for full React preview. The `'partial'` mode (AJAX server-render) doesn't work reliably for third-party modules — Divi's dynamic module framework skips custom modules not in its internal `$modules_map`.

**Computed fields** are the mechanism for passing server data to the React component in the VB:
- Define `__fp_items` and `__fp_terms` as `type => 'computed'` fields with `computed_callback` and `computed_depends_on`
- Regular fields reference computed fields via `computed_affects`
- **Critical**: `computed_affects` and `computed_depends_on` must be consistent. If field X lists `__fp_foo` in its `computed_affects`, then `__fp_foo` must list X in its `computed_depends_on`. A mismatch can cause the settings dialog to hang on save.
- Callback signature must be 3 parameters: `($args, $conditional_tags, $current_page)` — Divi always passes all three.

**Field definitions**:
- Always include `option_category` on fields (e.g. `'basic_option'`, `'configuration'`, `'layout'`). Divi's built-in modules always set this.
- The `categories` field type with `'renderer_options' => array('use_terms' => false)` works for WP's built-in `category` and `post_tag` taxonomies. Without `use_terms => false`, the category picker renders empty for standard taxonomies.
- `taxonomy_name` explicitly sets which taxonomy the picker shows.

**Frontend rendering**: Divi's dynamic module framework does not call `render()` for third-party modules on the frontend. The workaround is registering the module slug (`et_pb_tag_portfolio`) as a standard WordPress shortcode that delegates to the core shortcode function. This is done in `tag-portfolio-shortcode.php`.

**CSS**: Divi's dynamic CSS loader only loads styles for known modules. The plugin enqueues Divi's own `portfolio.css`, `filterable_portfolio.css`, and `overlay.css` from Divi's assets directory. Items need `style="display:block"` inline because Divi's CSS sets `.et_pb_filterable_portfolio_grid .et_pb_portfolio_item { display: none }` by default (Divi's JS normally toggles visibility, but for our items we force them visible).

**CSS class collision**: The React component uses Divi's CSS classes for styling compatibility (`et_pb_filterable_portfolio`, `et_pb_portfolio_filter`, etc.), but Divi's JS also binds event handlers to these classes. To prevent Divi's JS from interfering:
- Use `e.stopPropagation()` on click handlers in the React component
- Add a unique wrapper class (`fp_tag_portfolio`) to distinguish our blocks from Divi's built-in ones

**Pagination**: Client-side only, matching Divi's approach. All matching posts are loaded (`posts_per_page = -1`), the wrapper gets `data-posts-number` set to the per-page count, and Divi's JS handles pagination on the frontend. The pagination container class has Divi's typo: `et_pb_portofolio_pagination` (not "portfolio") — this is intentional to match Divi's CSS/JS selectors. The React VB preview implements its own pagination via component state.

**DiviExtension paths**: `FlexiblePortfolioExtension.php` lives in `includes/` but Divi looks for JS bundles relative to `$this->plugin_dir`. Use the plugin root constants (`FLEX_PORTFOLIO_DIR`, `FLEX_PORTFOLIO_URL`), not `__FILE__`-based paths, or Divi won't find the bundles and the builder will timeout.

**Cache clearing**: After any PHP changes to module fields or callbacks, clear Divi's caches:
```php
et_fb_delete_builder_assets();
delete_transient('et_builder_module_cache');
delete_transient('et_builder_all_fields_cache');
wp_cache_flush();
```
Also delete `/wp-content/et-cache/`. Without this, the VB may use stale field definitions.

## Apache Charset Note

If hosting with Apache (including Docker), ensure UTF-8 charset is set for static files:

```apache
AddDefaultCharset UTF-8
AddCharset UTF-8 .js .css .html
```

Without this, browsers (especially Firefox) may misinterpret UTF-8 characters in JS bundles as ISO-8859-1, causing broken umlauts. Debian's default Apache config has `AddDefaultCharset UTF-8` commented out, and `AddCharset` for JS/CSS is not set at all.
