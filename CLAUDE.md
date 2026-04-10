# Claude Code Notes

## Project

WordPress plugin: filterable portfolio grid using native WP categories/tags. Two layers: standalone shortcode + Divi Extension with React VB preview.

## Build

```bash
npx webpack --mode production
```

## Deploy to dev environment

```bash
# Copy files to Docker container
docker cp includes/modules/TagPortfolio/TagPortfolio.php rt-backup-docker-env-wordpress-tafel-oesterreich-local-web-1:/var/www/html/wp-content/plugins/flexible-portfolio/includes/modules/TagPortfolio/TagPortfolio.php
docker cp includes/tag-portfolio-shortcode.php rt-backup-docker-env-wordpress-tafel-oesterreich-local-web-1:/var/www/html/wp-content/plugins/flexible-portfolio/includes/tag-portfolio-shortcode.php
docker cp scripts/builder-bundle.min.js rt-backup-docker-env-wordpress-tafel-oesterreich-local-web-1:/var/www/html/wp-content/plugins/flexible-portfolio/scripts/builder-bundle.min.js

# Clear Divi caches (required after PHP field/callback changes)
# IMPORTANT: use chown after clearing et-cache, otherwise www-data can't regenerate CSS
docker exec rt-backup-docker-env-wordpress-tafel-oesterreich-local-web-1 bash -c "rm -rf /var/www/html/wp-content/et-cache/*; chown -R www-data:www-data /var/www/html/wp-content/et-cache/; php -r \"define('ABSPATH','/var/www/html/');require_once '/var/www/html/wp-load.php';et_fb_delete_builder_assets();delete_transient('et_builder_module_cache');delete_transient('et_builder_all_fields_cache');wp_cache_flush();\""
```

## Demo page

Post ID 38887: `https://tafel-oesterreich.localhost/tag-portfolio-demo/`

## Key gotchas

- Module extends `ET_Builder_Module` (NOT `ET_Builder_Module_Type_PostBased`)
- Computed field callbacks need 3-param signature: `($args, $conditional_tags, $current_page)`
- `computed_affects` and `computed_depends_on` must be consistent or settings dialog hangs
- Frontend rendering uses shortcode fallback (`et_pb_tag_portfolio` registered as WP shortcode)
- Items need `style="display:block"` inline (Divi CSS hides them by default)
- React component needs `e.stopPropagation()` on clicks to prevent Divi JS interference
- Pagination class has Divi's typo: `et_pb_portofolio_pagination`
- DiviExtension must use plugin root constants for paths, not `__FILE__`
