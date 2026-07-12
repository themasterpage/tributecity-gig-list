# TributeCity Gig List — WordPress.org Submission Guide

**Plugin:** TributeCity Gig List  
**Version:** 2.5.0  
**Slug (proposed):** `tributecity-gig-list`  
**GitHub:** https://github.com/themasterpage/tributecity-gig-list  
**Release zip:** `../tributecity-gig-list-dist/tributecity-gig-list-2.5.0.zip`

This document is the checklist for submitting the plugin to the official WordPress.org Plugin Directory.

---

## What you already have

| Item | Status |
|------|--------|
| Modern plugin (v2.5.0) | Done |
| GPL-2.0-or-later license header + `license.txt` | Done |
| `readme.txt` (WordPress.org format) | Done |
| Security: sanitize/escape, capabilities, Settings API | Done |
| i18n text domain `tributecity-gig-list` | Done |
| SaaS/external service documented in readme | Done |
| Optional credit link (off by default) | Done |
| SEO: semantic markup + JSON-LD MusicEvent | Done |
| Local test on led-zepplica.local | Done |
| Release zip built | Done (see path above) |
| Git commit on `master` | Done |

---

## Package locations

```text
Source repo:
  /Users/themasterpage/Documents/Projects/Grok/tributecity-gig-list

Release zip (upload this to WordPress.org when requested):
  /Users/themasterpage/Documents/Projects/Grok/tributecity-gig-list-dist/tributecity-gig-list-2.5.0.zip
```

### Zip contents rules (already followed)

- Top-level folder is **`tributecity-gig-list/`** (must match the plugin slug).
- Includes main file `tributecity-gig-list.php`, `readme.txt`, `license.txt`, `inc/`, `templates/`, `assets/`, `vendor/` (Composer autoload only).
- Excludes `.git`, debug images, and OS junk.

**Do not** submit a zip of the raw git repo root without the nested folder, or a zip that unpacks as loose files.

---

## Pre-submission checklist (do these yourself)

### 1. WordPress.org account

1. Create or log in: https://login.wordpress.org/  
2. Confirm your display name / username (used in `readme.txt` **Contributors:**).  
3. Update `readme.txt` if your WP.org username is **not** `themasterpage`:

```text
Contributors: your-wporg-username
```

### 2. Final local QA

On a clean WordPress install (or led-zepplica.local):

1. Upload/activate the **zip** (not only the symlink).  
2. Enter API token + Band ID under **TributeCity Gigs → Settings**.  
3. Confirm:
   - Current shows (cards/table/list per Styling).  
   - Detail view (Event Page / Facebook Event Page / Venue Website).  
   - Archive table: search, pagination, rows 10/25/50/All.  
   - Font size setting affects both current + archive.  
4. Deactivate / uninstall and confirm options/transients are removed.

### 3. Plugin Check (strongly recommended)

1. Install [Plugin Check](https://wordpress.org/plugins/plugin-check/) from the directory.  
2. Run it against **TributeCity Gig List**.  
3. Fix any **Errors** (and preferably Warnings) before submission.  
4. Optional PHPCS with WordPress Coding Standards if you want a cleaner review.

### 4. Assets for the directory (SVN `assets/`, not in the plugin zip)

Prepare and keep ready (PNG or JPG):

| File | Size |
|------|------|
| `icon-128x128.png` | 128×128 |
| `icon-256x256.png` | 256×256 |
| `banner-772x250.png` | 772×250 |
| `banner-1544x500.png` | 1544×500 (optional HiDPI) |
| `screenshot-1.png` … | Match `readme.txt` Screenshots section |

These go into the plugin’s **SVN `assets/`** folder after approval—not inside the PHP plugin package.

### 5. Double-check guidelines (high risk items)

You already addressed most; re-confirm before submit:

1. **GPL** — header + `license.txt` present.  
2. **No trialware** — all free features work without a paid gate (SaaS account for TributeCity Pro is OK if documented).  
3. **External service** — documented in `readme.txt` (API + media).  
4. **No forced credit** — credit is opt-in.  
5. **No remote executable JS/CSS** — assets are local.  
6. **Human-readable code** — no obfuscation.  
7. **Naming** — “TributeCity” is your product; avoid trademark abuse of “WordPress”.  
8. **readme.txt** — Stable tag matches version (`2.5.0`), Tested up to is current.

---

## Submit to WordPress.org

### Step A — Add your plugin

1. Go to: https://wordpress.org/plugins/developers/add/  
2. Complete the form (name, description, URL, etc.).  
3. Upload the zip when the form asks for it, **or** wait until after the initial review email (process can vary slightly).  
4. Agree to guidelines.  

### Step B — Wait for review

- First reviews often take **days to a few weeks**.  
- Watch the email on your WordPress.org account.  
- Respond promptly to any reviewer requests (common: prefixes, escaping, readme tweaks, assets).

### Step C — After approval (SVN)

You will receive:

- Plugin slug (hopefully `tributecity-gig-list`)  
- SVN URL, typically:

```text
https://plugins.svn.wordpress.org/tributecity-gig-list/
```

#### Checkout

```bash
svn checkout https://plugins.svn.wordpress.org/tributecity-gig-list/ tributecity-gig-list-svn
cd tributecity-gig-list-svn
```

#### Put code in trunk

```bash
# Copy plugin files into trunk/ (not the outer zip wrapper twice)
# trunk/ should contain tributecity-gig-list.php, readme.txt, etc.
rsync -av --delete \
  --exclude '.git' \
  --exclude 'debug-*' \
  /path/to/tributecity-gig-list/ \
  trunk/
```

#### Tag the release

```bash
svn cp trunk tags/2.5.0
```

#### Assets

```bash
# Place icons/banners/screenshots in assets/
# e.g. assets/icon-256x256.png, assets/banner-772x250.png, assets/screenshot-1.png
```

#### Commit

```bash
svn status
svn add trunk/* --force
svn add tags/2.5.0 --force
svn add assets/* --force
svn commit -m "Initial release 2.5.0"
```

### Step D — Verify on WordPress.org

1. Plugin page appears: `https://wordpress.org/plugins/tributecity-gig-list/`  
2. Install from a WP admin **Plugins → Add New** search.  
3. Confirm Stable tag / version match.

---

## Future releases

1. Bump version in:
   - `tributecity-gig-list.php` header + `TRIBUTECITY_GIG_LIST_VERSION`
   - `readme.txt` Stable tag + Changelog  
2. Commit & push GitHub.  
3. Rebuild zip.  
4. Update SVN:

```bash
# Update trunk files, then:
svn cp trunk tags/X.Y.Z
svn commit -m "Release X.Y.Z"
```

---

## GitHub

### Commit (already done)

```text
Modernize TributeCity Gig List for WordPress.org (v2.5.0).
```

Branch: `master`  
Remote: `https://github.com/themasterpage/tributecity-gig-list.git`

### Push (if not already pushed)

SSH may fail without the right key. Prefer HTTPS + GitHub CLI login:

```bash
cd /Users/themasterpage/Documents/Projects/Grok/tributecity-gig-list
git remote set-url origin https://github.com/themasterpage/tributecity-gig-list.git
git push -u origin master
```

Or:

```bash
gh repo sync
# or
gh auth setup-git
git push origin master
```

---

## Feature recap (what this release includes)

- **Settings** — API token, Band ID, hide band name, optional credit  
- **Styling** — use site styles, theme presets, list layouts (table/cards/list), font size (current + archive)  
- **Current shows** — responsive cards (or other layouts), detail view, event/venue/FB links  
- **Archive** — searchable, paginated table (10/25/50/All), accent show names, row hover  
- **Security / standards** — nonces via Settings API, `manage_options`, sanitize/escape, i18n  
- **SEO** — semantic HTML, MusicEvent microdata, JSON-LD ItemList/Event  

---

## Common review feedback (be ready)

| Feedback | Response |
|----------|----------|
| Document external HTTP requests | Already in readme “External service” |
| Prefix options/functions | Prefixed `tributecity_` / namespace `TributeCity\GigList` |
| Escape all output | Templates use `esc_*` / `wp_kses` |
| Don’t load assets globally | Conditional enqueue + shortcode load |
| Screenshots / icons missing | Add to SVN `assets/` after approval |

---

## Quick links

- Add plugin: https://wordpress.org/plugins/developers/add/  
- Detailed guidelines: https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/  
- readme validator: https://wordpress.org/plugins/developers/readme-validator/  
- Plugin Check: https://wordpress.org/plugins/plugin-check/  
- SVN handbook: https://developer.wordpress.org/plugins/wordpress-org/how-to-use-subversion/  

---

## Your action list (short)

1. [ ] Confirm WP.org username in `readme.txt` Contributors  
2. [ ] Install zip on a clean WP site and re-test  
3. [ ] Run Plugin Check; fix blockers  
4. [ ] Create icons, banner, screenshots  
5. [ ] Push GitHub if not already pushed  
6. [ ] Submit at https://wordpress.org/plugins/developers/add/  
7. [ ] After approval: SVN trunk + `tags/2.5.0` + assets  
8. [ ] Verify the public plugin page and install from directory  

---

*Generated for TributeCity Gig List v2.5.0.*
