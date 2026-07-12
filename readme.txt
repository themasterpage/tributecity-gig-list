=== TributeCity Gig List ===
Contributors: themasterpage
Tags: gigs, events, concerts, tribute band, shortcode
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 2.5.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display live and archived show listings from a TributeCity Pro band account on your WordPress site.

== Description ==

TributeCity Gig List connects to [TributeCity](https://tributecity.com) so Pro band accounts can show their current and archived gigs on any WordPress page via a shortcode.

Listings are managed in the TributeCity dashboard. This plugin does not create or edit shows locally — it only displays data returned by the TributeCity API using credentials you configure.

= Features =

* Shortcode-driven gig list and single-show detail views
* Optional archive view and result limit
* Settings screen for API token, Band ID, and display options
* Transient caching to reduce remote API calls
* Internationalized (translation-ready)
* Optional credit link (off by default)

= External service =

This plugin relies on the TributeCity service (https://tributecity.com):

* When a visitor opens a page that contains the shortcode, the plugin requests gig data from `https://tributecity.com/api/gig` using the API token and Band ID stored in your WordPress settings.
* Poster images may load from `https://tributecity.com/media/`.
* No WordPress site visitor personal data is sent to TributeCity; only your configured Band ID and the request parameters needed to fetch shows (for example gig ID, archive flag, or limit).
* A TributeCity Pro account is required for the API to return listings. Without valid credentials, the shortcode shows an empty-state message.

= Shortcode =

Recommended:

`[tributecity_gigs]`

Legacy tag (still supported):

`[tributecity-gigs]`

Attributes:

* `limit` — Maximum number of shows (e.g. `[tributecity_gigs limit="5"]`)
* `archive` — Show archived gigs (`[tributecity_gigs archive="1"]`)
* `gig_id` — Show a single gig (`[tributecity_gigs gig_id="123"]`)

Visitors can open a show’s detail view from the list; that uses a `gig_id` query argument on the same page.

== Installation ==

1. Upload the `tributecity-gig-list` folder to `/wp-content/plugins/`, or install the zip via **Plugins → Add New**.
2. Activate the plugin through the **Plugins** screen.
3. Go to **TributeCity Gigs** in the admin menu.
4. Enter your API token and Band ID from the TributeCity Pro dashboard (API Functionality manager).
5. Add `[tributecity_gigs]` to any page or post.

== Frequently Asked Questions ==

= Do I need a TributeCity account? =

Yes. The plugin displays data for TributeCity Pro accounts. Free or non-Pro accounts will not receive API listings.

= Does this plugin collect personal data? =

The plugin stores your API token and Band ID in the WordPress options table. It does not collect visitor analytics. Remote requests only fetch show data for the Band ID you configure. See the External service section above.

= Why is the list empty? =

Confirm your token and Band ID, that the account is Pro-enabled for API access, and that the band has shows to display. Cached responses expire after fifteen minutes (filterable for developers).

= Can I hide the band name on the detail view? =

Yes. Enable **Hide band name** under **TributeCity Gigs → Settings**.

= Is the “Powered by” link required? =

No. Credit is optional and off by default (WordPress.org Guideline 10).

== Screenshots ==

1. Settings screen for API credentials and display options.
2. Front-end gig list table.
3. Single show detail view.

== Changelog ==

= 2.5.0 =
* Mobile: horizontal padding for titles and main listings; fully responsive cards/detail.
* SEO: semantic section/article markup, MusicEvent microdata, JSON-LD ItemList/Event schema for indexing.

= 2.4.4 =
* Detail view: gig title/subtitle line is left-justified.

= 2.4.3 =
* Detail view: Venue Website and Facebook Event Page sit under Event Page (link list); Website removed from Details meta.

= 2.4.2 =
* Styling → Font size applies to the interactive archive table (search, rows, pager) as well as current listings.

= 2.4.1 =
* Archive table: rows-per-page dropdown (10 / 25 / 50 / All) under the list.

= 2.4.0 =
* Interactive archive table: search, pagination across all archived shows, accent show names, hover row states.

= 2.3.3 =
* Archive mode uses a simple table (show, date, location) — no posters or View details.

= 2.3.2 =
* Fix archive view: always show Archived/Current heading; soft-cap archive list (24) so the page does not hang on 200+ shows.
* Keep archive/current toggle when soft-cap is active; detail links work on archive.

= 2.3.1 =
* Cards: hard flex 50/50 with inline styles + object-fit:cover so poster fills its half.
* Detail view columns also forced to 50/50. Version marker data-tcgl-v for cache checks.

= 2.3.0 =
* Cards rebuilt as real HTML tables with inline 50% column widths + critical inline CSS.
* Fixes theme/image intrinsic-size fights that kept columns at ~320/456.

= 2.2.7 =
* Cards layout: table-layout:fixed 50/50 columns (immune to poster intrinsic width).

= 2.2.6 =
* Cards layout: true 50/50 poster and details columns via CSS grid.

= 2.2.5 =
* Force 800px host width against theme .lz-container / .lz-prose constraints.
* Load public CSS after theme styles; viewport-based breakout fallback.

= 2.2.4 =
* Cards layout uses equal 50/50 poster and details columns.
* Styling tab: font size selector (Small / Medium / Large / Extra large).

= 2.2.3 =
* Override theme prose/container (e.g. Led Zepplica .lz-prose 720px) to 800px when hosting the shortcode.

= 2.2.2 =
* Gig list container set to max-width 800px so card posters render larger.

= 2.2.1 =
* Cards layout: horizontal poster + details on wider screens; full poster (no crop); single cards expand to full width.

= 2.2.0 =
* List layout options: Table, Cards (with posters), and Stacked list.
* Layout works with any visual theme or site-style inheritance.
* Shortcode override: layout="table|cards|list".
* “Use suggested layout for selected theme” helper on the Styling tab.

= 2.1.0 =
* New Styling tab: inherit site/theme styles or choose a visual override theme.
* Themes: Classic, Dark Stage, Concert Poster, Clean Minimal, Soft Cards.
* Split public CSS into structural base + theme stylesheets.

= 2.0.0 =
* Full modernization for WordPress.org compliance (GPL headers, i18n, security).
* Namespace moved to `TributeCity\GigList`; PSR-4 autoloading.
* Escaped all front-end output; Settings API sanitization updated for PHP 8+.
* Admin UI uses core `nav-tab-wrapper` patterns and `manage_options` capability.
* Conditional asset loading; public CSS only when the shortcode is used.
* API client: timeouts, HTTP status checks, JSON validation, transient caching.
* Optional credit link defaulting to off (Guideline 10).
* Removed incomplete widget placeholder code.
* Added `readme.txt`, license file, and directory index guards.
* Version bump; improved shortcode attribute handling and permalink-safe detail links.

= 1.1 =
* Previous release: shortcode-based gig list and basic settings.

== Upgrade Notice ==

= 2.0.0 =
Major compliance and security update. Your existing Band ID and token options are preserved. The admin menu slug is now `tributecity-gig-list`. Prefer the `[tributecity_gigs]` shortcode; the legacy `[tributecity-gigs]` tag still works.

== Privacy Policy ==

This plugin stores the following options in your WordPress database: API token, Band ID, hide-title preference, and optional credit preference. When the shortcode renders, those credentials are used to request gig data from tributecity.com. No visitor emails or site usage analytics are collected by this plugin. For TributeCity’s own privacy practices, see their website.
