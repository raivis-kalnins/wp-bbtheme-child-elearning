## 3.8.11.52 hero pagination + exact row-31 repair
- Retires the v151 frontend CSS/JS and its one-slide hero data filter while keeping the stable v147 finder and route-scoped v141 WooCommerce ownership.
- Restores both authored Learning hero slides with autoplay, pause-on-hover and one deterministic compact pagination control.
- Increases hero desktop breathing room to a 600px slide with 76px top / 92px bottom content padding so the headline, CTA, finder and popular links do not crowd the header or pager.
- Keeps the Events-reference 1180px / 24px top-level rail but no longer freezes Swiper wrapper transforms.
- Targets `#wpbb-row-31` directly and forces its three proof cells into a true 3-column desktop grid, 2 columns on tablet and 1 column on mobile.
- Retains 4-up stats and equal process/editorial grids, removes generic course `5+1` controls after AJAX updates, and leaves DB/WooCommerce content unchanged.

## 3.8.11.51 Events-exact geometry and hero recovery
- Retires the v150 frontend CSS/JS that caused the hero copy to shift to the right and restores the exact Events 3.8.11.40 1180px / 24px geometry on the homepage.
- Reduces the homepage hero to one deterministic authored slide at render time, preserving the Learning copy while using a Learning/training hero image; autoplay, navigation and pagination are disabled for a stable first frame.
- Uses the stable Events v136 hero dimensions (520px desktop, 64px top / 74px bottom copy padding, 72% right media) and the Events v139 content rail.
- Keeps the v147 course finder internals untouched while aligning only its outer row to the common rail.
- Forces the actual proof markup (container > row > three col-md-4 cards) into a real 3/2/1 grid without runtime wrapper guessing.
- Restores 4-up stats, equal 3-up process/editorial rows, and consistent gallery media boxes on the same rail.
- Remaps course/about/gallery/blog demo media to bundled Learning/training images instead of the event-specific v150 set, and removes generic course 5+1 gallery controls after AJAX updates.
- Leaves WooCommerce routing/templates and database content unchanged.

## 3.8.11.50 deterministic Events-parity homepage repair
- Retires the v149 frontend experiment and keeps the stable v144/v147 finder base.
- Uses one 1440px / 28px top-level rail for header, homepage sections and footer; nested finder/card components never receive page gutters.
- Locks the hero on the first authored Learning slide, stops autoplay drift, applies Events-style full-bleed media geometry and cache-safe v150 hero images.
- Replaces repeated/stale demo media with uniquely named v150 workshop/classroom/conference assets for hero, course cards, about, gallery and Latest Thinking.
- Uses the supplied DB classes directly for 4-up services/industries/stats and 3-up proof/cases/process/editorial rows, with robust runtime marking for variable BBuilder wrappers.
- Restores the three authored proof cards server-side when the legacy icon-card renderer emits Card title placeholders.
- Removes generic 5+1 item-gallery controls from course cards and reapplies the correct course image after AJAX finder updates.
- Keeps WooCommerce routing/templates owned by the route-scoped v141 layer and does not alter database content.

## 3.8.11.48 canonical homepage rail and marked-area repair
- Re-aligns all front-page BBuilder section containers to the original 1440px sector/header/footer canvas, eliminating the 1320px inward drift visible against the header and hero.
- Keeps the course finder itself at native 100% width inside that rail and explicitly neutralises stale nested alignment markers without flattening its filter/result DOM.
- Adds balanced hero top/bottom spacing while aligning hero copy to the same 1440px rail.
- Strengthens the four stat values with the E-Learning purple used elsewhere in the theme.
- Renders the three authored proof cards server-side from the DB block attrs (no Card title placeholders) and forces their real common row to 3/2/1 responsive columns.
- Replaces the fragile repeated gallery imagery with four verified workshop/learning images copied from the supplied Events reference theme and enforces consistent 16:10 media boxes.
- Keeps process, cases and editorial rows on the same rail while preserving the stable finder and route-scoped v141 WooCommerce ownership.
- Does not edit the database, dequeue the maintained frontend stack, or change WooCommerce routing/templates.

## 3.8.11.45 focused live homepage repair
- Keeps the 3.8.11.44 DB-backed layout and adds only a final front-page repair layer.
- Locks the course finder to the same 1320px content rail as the maintained homepage sections without flattening its internal filter/result DOM.
- Normalises the Solutions heading to the same heading width, colour and rhythm as the other section headings.
- Detects the three real proof cards and their common row at runtime, then applies a deterministic 3/2/1 grid even when BBuilder emits an extra wrapper.
- Detects the three authored process cards, moves each numbered badge inside its matching card, and applies one clean 3/2/1 card grid; no process copy is replaced.
- Forces all four homepage gallery slides to use the valid bundled E-Learning images as both image sources and media backgrounds, preventing the blank gradient cards seen live.
- Uses the bundled 2560px hero sources and a shorter white fade for a sharper hero without changing hero copy.
- Preserves v141 WooCommerce route-scoped fixes and does not change database content, Woo templates or editor blocks.

## 3.8.11.44 DB-backed homepage recovery
- Rebuilt from the 3.8.11.41 stable frontend base; the broken v142/v143 layers are not loaded or included.
- Uses the supplied 17 Sep 2026 database backup as the source of truth for homepage block classes and structure (front page ID 135134).
- Keeps the native course finder DOM intact and restores its 4-field form plus 3/2/1 course grid without flattening nested Bootstrap rows/columns.
- Aligns hero copy to the same 1320px/24px content edge as the header and shortens the white image fade so it no longer washes across the learner's face.
- Forces the saved 4-up services/industries, 4-up stats, 3-up proof/cases/process, 3-up editorial grids directly from the classes stored in the database.
- Applies the Events theme's clean two-column media/text geometry to the exact Learning about row, without changing the Learning content rail or branding.
- Rebuilds the process presentation as clean rectangular cards with small numbered chips and resets stale decoration on both the grid cell and card; no cloud/blob clipping or DOM replacement.
- Trusts the four gallery image sources already stored in the homepage instead of replacing them at runtime.
- Preserves the 3.8.11.41 route-scoped WooCommerce support unchanged.
- Contains no frontend content mutation and no global CSS/JS ownership/dequeue switch.

## 3.8.11.41 emergency stability hotfix
- Rolls back the v140 global frontend ownership change that removed the maintained v119-v137 finishing stack and caused widespread homepage/layout regressions.
- Restores the proven v137 homepage, hero, header/footer, course-grid, process, stats and alignment behaviour unchanged.
- Keeps v140 files in the package for audit history but does not load them.
- Adds a CSS-only WooCommerce finish that is strictly gated to Woo routes; it does not mutate homepage/editor DOM or replace templates.
- Normalises Shop, related products, Cart and My Account responsive geometry without changing the global site grid.

## 3.8.11.37 final non-destructive alignment cleanup
- Restores the maintained 1320px content grid after the v136 1180px regression.
- Removes only exact duplicate compact hero finders and raw placeholder card rows such as `Card title / Add a short description`.
- Replaces the destructive v136 process-section rebuild with in-place grid styling so editor-owned process copy remains editable.
- Keeps hero image/pagination ownership while preserving the existing catalogue, advanced filters, stats and responsive card layouts.
- Normalises mobile section gutters to the core 18px edge and retains dark-mode process-card contrast.

## 3.8.11.16 reset-safe final release fixes
- Demo reset/import now re-runs the canonical managed BBuilder page rebuild and all v116 repairs automatically.
- The old migration that removed legitimate responsive BBuilder column widths is disabled; desktop multi-column layouts survive a clean demo reset.
- Desktop mega menus use the measured header bottom plus a hover bridge, matching the close Jobs positioning across all children.
- Home hero sliders use three distinct child-owned images, visible pagination, 8.5-second autoplay and pause-on-hover with sharp natural-scale rendering.
- Managed demo/editorial/catalogue/gallery/Woo media is restored from bundled files; Woo Clothes keeps the complete bundled product-image pool.
- Quote drawers use resilient trigger detection and sit flush to the right viewport edge on quote-enabled themes.
- Cookie-consent acceptance persists across reloads using a stable browser marker.
- Legal pages remain left-aligned on the normal grid; Latest Thinking media/card edges are normalized.
- Partner/brand serialization is normalized idempotently to prevent repeated wrappers and the `wpbb/column` validation warning.
- Theme Settings retain child-owned controls for disabling dark mode and keeping English-only Polylang content.

## 3.8.11.14 child-only settings, editor, legal, editorial and hero finish

- Latest Thinking card rows now use the same 1320px grid as their headings; the 1440px row override that shifted the first card left has been removed.
- Hero images use a direct child-owned native source, stronger left-edge gradient masking and no CSS blur/viewport stretching.
- Appearance > Theme Settings adds switches to disable dark mode and disable translations/keep English only. Enabling English-only moves non-English Polylang Pages and Posts to Trash and hides the language switcher.
- Privacy/Terms/Cookies content spans the normal site grid and is left-aligned instead of being forced into a centered narrow column.
- The known raw partner-heading serialization defect is repaired in imported pages and on future page saves, resolving the wpbb/column validation error.
- Child editor CSS is moved from enqueue_block_editor_assets to enqueue_block_assets for the WordPress editor iframe.

## 3.8.11.13 child-only hero and editorial grid finish

- Latest Thinking / related editorial cards use the full 1440px site grid; the legacy outer BBuilder row and list start padding can no longer create a first-card left inset.
- Homepage heroes use a native-resolution child asset without viewport-width stretching.
- A stronger white-to-transparent hero gradient crosses the photograph's left edge so the image seam is hidden.
- Existing and translated managed hero blocks are refreshed from the same child-owned asset after upgrade.

## 3.8.11.12 child-only visual/media fixes

- Latest Thinking card grid now inherits the section grid with no first-card left inset.
- Sharper child-owned hero source and late frontend override.
- Managed catalogue and editorial media are re-synchronised from bundled child assets.
- Media/text CTA buttons align to the copy edge.

## 3.8.11.11 media and WooCommerce finalisation

- Repairs missing demo media from bundled local assets, including cloned-site WooCommerce product images and Automotive vehicle finder thumbnails.
- Forces WooCommerce filters, ranges, compare controls and product actions to the child theme accent instead of the plugin blue fallback.
- Uses a two-column desktop Basket, Checkout and My Account shell with mobile stacking only below 821px.
- Uses the highest-resolution bundled hero source during managed demo rebuilds; Business uses the 1600x1000 office source.
- Requires parent WP BBTheme 3.8.10.23 for reliable My Account header URLs on cloned sites.

## 3.8.10.82 suite consistency

Requires WP BBuilder 5.6.9+ for palette inheritance and the shared hCaptcha verifier. This release keeps the sector's individual brand colour while using the same 1440px canvas, card/form rhythm, dark-mode baseline and footer/newsletter hierarchy as the rest of the 15-theme suite. The one-time cleanup is restricted to records explicitly marked as theme-managed demo content.

## 3.8.10.65

- Fixes the Theme Settings frontend-protection panel so its CSS is loaded in the admin head instead of appearing as visible text.
- Makes sector media repair load the WordPress image API safely before generating attachment metadata.
- Refines shared card, directory, gallery and responsive alignment.

## 3.8.10.47

- More compact and consistent section spacing, cards and responsive layouts.
- Smaller in-frame gallery thumbnail pagination and improved light/dark contrast.
- Reliable child-owned WooCommerce product shells where the theme includes commerce.

# WP BBTheme Child E-Learning 3.8.10.65
Child theme for WP BBTheme. Built to use the shared Gutenberg/WP BBuilder design system and demo importer.

## Included
- Course, Lesson and Quiz custom post types
- Course category and level taxonomies
- Visual curriculum manager with drag-and-drop lesson and quiz ordering
- Open, free, login-required and paid course-access modes
- User enrolment records, lesson completion and course-progress tracking
- Prerequisite courses, lesson drip delays, preview lessons and configurable pass marks
- Video URL and PDF material support per lesson
- Multiple-choice quizzes with server-side scoring and attempt history
- Student learning dashboard and course catalogue shortcodes
- Optional WooCommerce product linkage and enrolment after paid order processing/completion
- AJAX course finder and course curriculum templates

The native module provides a practical LearnDash-style course workflow while preserving the suite's existing post types and saved content. It is not presented as complete feature parity with the standalone LearnDash plugin.

## Requirements
- WordPress 6.6+
- PHP 8.0+
- Parent theme `wp-bbtheme` 3.8.10.20+
- WP BBuilder 5.6.0+

### 3.8.10.46
- Dashboard-safe, resumable sector media repair; no synchronous bulk image regeneration on `admin_init`.
- Password protection controls live under **Theme Settings → General**.
- Thumbnail navigation is overlaid inside the main gallery image.
- Active-sector Blog and directory media are repaired after child-theme switching.

## SCSS structure (3.8.10.9)

Frontend styles are split into `tokens`, `tools`, `base`, `header`, `footer`, `components`, `swiper`, `motion`, `forms`, `blog`, `quality`, `sector`, `responsive` and `features`. Fluid typography uses the suite `fluid-font()` mixin and explicit viewport guards rather than `clamp()`. The generated production CSS intentionally contains no `!important` declarations.

### Build compatibility

The child build is dependency-free and works with Yarn 1.22.x as well as newer Yarn versions. No Corepack step is required. Use:

```sh
yarn prod
```

The command runs `node tools/build.mjs` and rebuilds the hashed CSS/JS manifest directly.


### 3.8.10.45
- Consistent 80/64/52px section rhythm and explicit light/dark card contrast.
- Active-theme sector media repair for demo pages, blogs, directories and galleries.
- Top-aligned About imagery plus thumbnail and modal galleries on supported directory cards and single pages.

### 3.8.10.44
- Frontend password protection is enabled by default with password `wp@demo`.
- Administrators can disable it or set a new password in **Settings → Theme Settings** at `/wp-admin/options-general.php?page=wp-theme-settings`.
- Successful visitors receive a signed access cookie valid for 24 hours by default.
- Purge full-page/server/CDN caches after changing the protection setting.

### 3.8.10.42
- Replaced demo feature icons with Tabler Icons v3.46.0 outline SVGs, sized for normal UI use and coloured from the child-theme brand token.
- Single-column imported demo rows are repaired to 12 columns at every breakpoint.
- Dark-mode demo cards use explicit dark surfaces/readable text.
- Optional frontend-only demo password protection is available in Settings → Theme Settings (default password `wp@demo`).
### 3.8.10.43
- Shared alignment and dark-mode contrast fixes across service, solution, process, directory, blog and commerce cards.
- Current child-theme media is reapplied after child-theme switches, including optimised AVIF/WebP files.
- Visible slider/grid images are loaded deterministically and duplicate single-item summary text is removed.

## 3.8.10.65 BBuilder demo system
This release expects WP BBuilder 5.6.4+ and standardises demo editing around BBuilder Row/Column, Div, Icon Card, Swiper and selected native WordPress content blocks. Legacy Group/Columns demo markup is migrated automatically.


## 3.8.11.08
WooCommerce shop, basket, checkout and account layouts were normalised across the sector suite; theme preview artwork was refreshed and package documentation was reduced to this README.

## 3.8.11.40
- Uses the Automotive 3.8.11.39 stability model: v118 base plus one final frontend owner.
- Normalises homepage/course grids to the measured header/footer content edge.
- Removes exact duplicate compact course finders and unfinished placeholder rows without replacing editor content.
- Restores a single accessible hero pager and consistent section rhythm.
- Enables/forces the child WooCommerce legacy shells for Shop, Product, Cart, Checkout and My Account.
- Repairs WooCommerce page ownership, My Account endpoint routing, cart columns, checkout panels and responsive commerce grids.

## 3.8.11.47
- Reverts the v146 measured-rail regression by building from the v145 frontend baseline; v146 is not loaded or included.
- Keeps the native course finder full width and restores its 4-column filter / 3-column result layout.
- Excludes course cards from the generic item-gallery UI and removes any stale gallery controls.
- Forces the three DB-authored proof cards into one responsive row and restores their authored titles/copy in the browser.
- Normalises Solutions, Process and Latest Thinking heading rows without global DOM/grid ownership changes.
- Leaves the route-scoped v141 WooCommerce fixes unchanged.



## 3.8.11.49 — Events-reference hero, alignment and demo-media repair

- Uses the attached Woo Events theme's header-rail ownership pattern without applying page gutters to nested finder components.
- Measures the actual header inner row only to set left/right CSS variables; no DOM grid ownership or section tagging.
- Removes the v148 hero stretch and restores the stable 520px hero with 64px/74px desktop content padding.
- Replaces the repeated single-learner demo-photo set with varied workshop, conference and collaborative-learning imagery sourced from the supplied Events theme package.
- Refreshes existing managed demo course/blog attachments once after upgrade so catalogue and editorial cards use the new media outside the homepage too.
- Keeps v141 WooCommerce ownership, v147 finder recovery and v148 proof-card rendering intact.
