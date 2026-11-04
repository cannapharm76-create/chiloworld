# CHILO EXOTICS® — Editorial Website / GitHub-ready

An original responsive website concept for `cannapharm76-create/chiloworld`. Designed in HTML, CSS and vanilla JavaScript; no framework or build step required.

## Files

- `index.html` — public-facing **design preview** with editorial imagery, quality-process narrative and navigation.
- `catalog.html` — **internal, non-transactional draft** of the Chilo historical strain index. Includes 40 records, search, cultivation filters, category tabs, sort, detail panels, browser-local saved records, CSV export, and a business introduction summary you can copy.
- `assets/styles.css` — all responsive styling.
- `assets/site.js` — responsive mobile navigation.
- `assets/catalog.js` — catalog functionality and record list.
- `assets/strain-data.json` — structured strain archive intended for future API/CMS integration.
- `assets/photo-sources.json` — photographer credits and source URLs.
- `assets/botanical.svg` — decorative illustration fallback when external photos are unavailable.
- `netlify.toml` — static Netlify deployment settings and security headers.
- `robots.txt` — blocks indexing while this is a review-only build.

## Important: public launch and compliance

**Do not publish the catalog as a public cannabis advertising or sales page in Thailand without legal review.** Government guidance in 2025 and developments in 2026 restrict cannabis to controlled medical channels, prohibit online sales and advertising, and impose licensing requirements. This package deliberately has **no prices, cart, checkout, product reservations, stock claims or live COAs**. The visual index is a demonstration. `noindex`/`robots.txt` are not access controls. Deploy it only to a restricted staging environment for review. A production trade catalog must use server-side authentication and verified partner permissions, plus legal approval.

The site has no back end and does **not** submit contact data to a server. The introduction form copies a draft (or downloads a text file if clipboard access fails). Contact via authorized channels is a separate manual step.

## Images

The design references freely usable Unsplash photos (URLs in `photo-sources.json`). They are **generic editorial cannabis flower photographs**, not photographs of the named Chilo strains. Several source photos are reused for the 40 records with different crop contexts; never label them as verified batch photography. To use actual Chilo product photography, obtain photographer permission and replace the `photo` property for each strain in **both** `assets/strain-data.json` and the embedded `PRODUCTS` array in `assets/catalog.js` (until connected to a single source-of-truth CMS). The current page says **“Illustrative”** wherever applicable.

Photographs are requested from Unsplash's image CDN at runtime. A local decorative SVG displays if blocked/offline. For fully offline images, place licensed photos under `assets/strains/` and update their paths.

## How to preview

Open `index.html` in a browser or run a local static server (`python -m http.server 8080`) and navigate to `http://localhost:8080/`. Go to `catalog.html` to test interactions. Some browsers block local clipboard calls on `file://` and will download a text draft instead.

## Deploying without replacing the existing homepage

Safest option: upload this folder to `design-preview/` inside your repository and open `/design-preview/index.html` on your staging site. The links are all relative and will work at a subpath. When visually approved and legally reviewed, move the build files to the site root on a protected preview/deployment branch. **Do not blindly overwrite the existing `main` site.**

## Updating verified records later

Store actual batch ID, source farm (with consent), COA PDF, batch-specific photography, lab values, stock snapshot time and permissions in your backend. Publish only fields validated against the correct batch, via access-controlled endpoints. Do not claim GACP certification or awards without documented evidence and express permission to display them.
