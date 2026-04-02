# Lunara Agent Handoff

Updated: 2026-03-27

## Read This First

This file exists because chat handoff/context carryover is unreliable right now.

The next agent should treat this document as the authoritative restart point for the current Lunara Film build and live-site recovery state.

## Mission

Build **Lunara World**: a premium, dynamic, fully authored film publication where:

- the **theme owns presentation**
- the **Oscars plugin owns Oscars data/query logic**
- the reader never feels the seam between editorial and database surfaces

## Protected Assets

These must **not** be deleted or structurally altered:

- media files
- written reviews
- posts
- Oscars database records

Changes should target:

- theme templates
- CSS
- routing/rewrite behavior
- plugin presentation templates
- tightly scoped recovery/deployment mechanics

## Current Truth About the Live Site

The live site was partially recovered after a theme/snippet/plugin failure cascade.

Right now:

- live site is up
- Lunara child theme is active
- review routes are restored
- `/reviews/` is a real review hub again
- `/reviews/weapons-2025/` resolves to the `review` CPT again
- direct server access works and should be considered the primary deployment path
- `Code Snippets Pro` is currently disabled at the filesystem level to prevent redeclare conflicts

Verified live routes at the time of this handoff:

- `https://lunarafilm.com/`
- `https://lunarafilm.com/reviews/`
- `https://lunarafilm.com/reviews/weapons-2025/`
- `https://lunarafilm.com/oscars/`
- `https://lunarafilm.com/wp-login.php`

## Why Direct Server Access Matters

Do **not** rely on the WordPress theme uploader as the primary path right now.

Do **not** assume snippet-based recovery layers are safe to re-enable.

The cleanest current workflow is:

1. edit locally
2. lint locally
3. deploy directly over SSH/SFTP
4. smoke-test live URLs

This is the safest path until the live WordPress/plugin environment is calmer.

## Direct Server Access

Sensitive local-only note for recovery continuity:

- SSH host: `ssh.wp.com`
- Port: `22`
- SSH user: `slowlymagneticcb9c284bdc-mrckz.wordpress.com`

The password used in this workspace was recovered from local FileZilla config during emergency restoration. If this document is ever moved outside your private drive/workspace, redact the password first.

## Active Local Code Roots

Theme source of truth:

- `C:\Users\silve_i21do49\OneDrive\Desktop\New folder\lunara-film-premium-20260319-dynamic-homepage\lunara-film-premium-20260319-dynamic-homepage`

Oscars plugin source of truth:

- `C:\Users\silve_i21do49\OneDrive\Desktop\New folder\academy-awards-table-optimized`

Live-recovery working copies:

- `C:\Users\silve_i21do49\OneDrive\Desktop\New folder\12_REMOTE_RECOVERY\single.php`
- `C:\Users\silve_i21do49\OneDrive\Desktop\New folder\12_REMOTE_RECOVERY\page-reviews.php`
- `C:\Users\silve_i21do49\OneDrive\Desktop\New folder\12_REMOTE_RECOVERY\archive-review.php`
- `C:\Users\silve_i21do49\OneDrive\Desktop\New folder\12_REMOTE_RECOVERY\style.css`

Supporting continuity docs:

- `C:\Users\silve_i21do49\OneDrive\Desktop\New folder\LUNARA_WORLD_PROGRESS_DOSSIER_2026-03-25.md`
- `C:\Users\silve_i21do49\OneDrive\Desktop\New folder\WORKSPACE_INDEX.md`

## Important Live Recovery History

The next agent should know this exact sequence because it explains why the site currently behaves the way it does.

1. A bad theme/snippet/plugin interaction caused live-site failure.
2. The active theme `functions.php` on the server was discovered to be `0 bytes`.
3. The full local `functions.php` was restored to the live theme.
4. That surfaced redeclare conflicts caused by stale Code Snippets Pro code.
5. `Code Snippets Pro` was disabled by renaming the plugin folder on the server.
6. After that, the site stabilized and `wp-cli` became usable again.
7. Review routing and archive behavior were repaired directly in the theme.

## Current Live Architecture Notes

### Theme

Active stylesheet:

- `lunara-film-premium-20260319-dynamic-homepage`

Parent template:

- `blocksy`

This means live is a recovered Lunara-on-Blocksy stack, not a perfect mirror of every prior staging experiment.

### Snippets

`Code Snippets Pro` is disabled right now:

- server folder currently renamed to `code-snippets-pro.off`

This is intentional.

Do not re-enable it casually.

Reason:

- stale snippet compatibility code was redeclaring theme helpers and crashing the site

### Review System

The review system is now working again, but it is still in a transitional “recovered permanent-theme path” state rather than a polished final-state review system.

Current wins:

- `/reviews/` works as a real review hub
- `/reviews/{slug}/` resolves to `review` posts again
- review single keeps the `Lunara Debrief` signature module
- paired film titles are again routed to **Letterboxd**
- `IMDb` chip is preserved
- `Oscar Ledger` pill is preserved as the internal Lunara path

## Very Important Product Logic to Preserve

This is not cosmetic. It is intentional editorial/product strategy.

For the `Lunara Debrief` paired-film rows:

- **film title click** should go to **Letterboxd**
- **IMDb chip** should go to IMDb
- **Oscar Ledger** pill should go to Lunara’s internal Oscars/title page

Reason:

- Letterboxd is part of growth/discovery initiation
- Lunara internal ledger link is part of site retention
- both paths matter

Do not “simplify” this into one internal-only link path.

## Current Review Template Behavior

The review rendering logic currently lives in:

- `C:\Users\silve_i21do49\OneDrive\Desktop\New folder\12_REMOTE_RECOVERY\single.php`
- canonical mirrored copy:
  - `C:\Users\silve_i21do49\OneDrive\Desktop\New folder\lunara-film-premium-20260319-dynamic-homepage\lunara-film-premium-20260319-dynamic-homepage\single.php`

Current review-specific logic includes:

- extraction of `LUNARA DEBRIEF` block from review body content
- rendering that debrief as the structured end module
- stripping the duplicate legacy/raw debrief block from postscript content
- preserving Letterboxd title links for paired films
- preserving IMDb chip + Oscar Ledger pill

This logic is currently implemented at the **template layer**, not in the `review` CPT registration schema itself.

That is acceptable for now, but a future hardening step should move the parser/renderer into a named helper in `functions.php`.

## What Was Just Fixed

Most recent critical fix:

- the duplicate legacy debrief block on review singles was removed
- the real `Lunara Debrief / The Closing Ledger` module was preserved
- paired-film title links were corrected back to Letterboxd

This was done by patching the live `single.php` over direct server access.

## Known Gaps Right Now

These are the areas the next agent should assume are still unfinished.

### 1. Live vs staging difference

Live does **not** equal staging.

That is expected.

Staging has been used as a lab for experiments and preferred directions, not as a strict one-to-one release branch.

The right approach is:

- selectively promote keeper ideas from staging
- do not blindly try to clone all staging behavior

### 2. Footer duplication

The live page markup currently carries more than one footer layer.

There is still a Blocksy footer in the markup alongside the Lunara footer treatment.

That needs cleanup.

### 3. Global shell consistency

The site is stable, but not every page type is yet perfectly normalized against the homepage.

### 4. Snippet migration

Because snippets were disabled for stability, some old refinement behavior may still exist only in historical snippet logic or staging.

Those features need to be reintroduced deliberately into theme/plugin code, not by turning the snippet plugin back on blindly.

### 5. Review-system hardening

The review single is now product-correct again, but the debrief parser should eventually be promoted into a reusable helper in `functions.php`.

## Immediate Next Priorities

The next agent should pick up in roughly this order:

1. Preserve direct server deployment as the main path.
2. Keep `Code Snippets Pro` disabled until migration work is complete.
3. Audit the live footer duplication and remove the unwanted legacy footer layer.
4. Continue review/archive refinement now that the debrief logic is correct again.
5. Migrate any still-useful historical snippet behavior into permanent theme/plugin code.
6. Keep working toward full Lunara World shell parity across homepage, reviews, posts, archives, and Oscars pages.

## Safety Rules for the Next Agent

- Do not delete media.
- Do not delete reviews.
- Do not delete posts.
- Do not delete Oscars data.
- Do not re-enable `Code Snippets Pro` just to “see what happens.”
- Do not rely on wp-admin theme upload as the main deployment path.
- Prefer local edit -> lint -> SSH deploy -> live smoke test.

## Smoke Test Checklist

After any live deployment, check at minimum:

- `https://lunarafilm.com/`
- `https://lunarafilm.com/reviews/`
- one known live review route, currently:
  - `https://lunarafilm.com/reviews/weapons-2025/`
- `https://lunarafilm.com/oscars/`
- `https://lunarafilm.com/wp-login.php`

For review singles specifically, verify:

- exactly one `Lunara Debrief` module
- paired-film title links go to Letterboxd
- IMDb chip exists
- Oscar Ledger pill exists

## If a New Agent Needs a One-Paragraph Summary

This project is building Lunara World, a premium film criticism site with an integrated Oscars ledger. The live site recently had a severe theme/snippet/plugin failure and was recovered through direct SSH deployment, restoration of a zero-byte `functions.php`, and disabling `Code Snippets Pro` to stop redeclare crashes. The site is stable again, the Lunara theme is active, reviews routes work, the review archive is restored, and the signature `Lunara Debrief` module is back with the intended growth logic: paired-film title links go to Letterboxd, the IMDb chip goes to IMDb, and the Oscar Ledger pill stays internal to Lunara. Protected assets are media, reviews, posts, and Oscars data; do not delete or structurally alter them.

## Latest Implementation State (2026-03-29)

- `/reviews/` is now on one canonical archive system instead of a split between old archive templates and newer shared rendering.
- Visible real excerpts are intentionally suppressed on review cards in favor of one controlled line:
  - `Open the review and enter the full argument.`
- The approved review single composition is now the standard:
  - title + poster on the left
  - excerpt/meta/`Where to Watch`/`Review Details` on the right
- On mobile, the review single keeps the poster directly under the title and the archive cards have been tightened to avoid oversized lead slabs and dead-looking no-poster support cards.
- Current proof captures:
  - `C:\Users\silve_i21do49\OneDrive\Desktop\New folder\output\20260329-review-single-mobile-pass-2.png`
  - `C:\Users\silve_i21do49\OneDrive\Desktop\New folder\output\20260329-review-archive-mobile-pass-2.png`

## Latest Implementation State (2026-03-30)

- The homepage Oscars redundancy was intentionally reduced:
  - `Oscar Ledger Highlights` and `Oscar Lore` are no longer two competing homepage lanes
  - homepage now uses one merged `Oscar Lore` story lane
  - the data/stat side now sits underneath as a smaller `Inside the Ledger` supporting strip
- The merged homepage Oscars lane combines curated lore cards with ledger-derived title/history cards in one poster-led card grammar.
- A slow autoplay drift was added for that homepage Oscars lane on desktop:
  - desktop only
  - pauses on hover/focus
  - respects reduced-motion settings
  - mobile remains manual/swipe-first
- Smoke checks after deployment were clean for:
  - `https://lunarafilm.com/`
  - `https://lunarafilm.com/reviews/`
  - `https://lunarafilm.com/reviews/weapons-2025/`
  - `https://lunarafilm.com/oscars/`

## Latest Front-Door Polish State (2026-03-30)

- Homepage shell rhythm has been tightened with clearer section separation and cleaner head spacing.
- Header utility styling is a bit more intentional now:
  - search trigger reads more like a designed control
  - dropdown surfaces are less default-looking
- Footer finish is stronger and more atmospheric, with better spacing through the branded close, nav columns, and utility/legal row.
- This was a CSS-driven polish pass, not a data/content change.

## Latest Hero / Pulse State (2026-03-30)

- The homepage hero and Oscar pulse card have been pushed closer to one composed editorial opening band.
- Hero copy now has more breathing room and the pulse card reads as part of the same front-door movement rather than a neighboring utility block.
- The homepage opening hierarchy is stronger without changing the protected editorial/data layer.

## Latest Top-Band Convenience State (2026-03-30)

- A theme-level fallback now restores readable labels for icon-only primary-nav items.
- This specifically protects the `More` dropdown from shipping blank-text links when a menu item relies on icon markup.
- The dropdown panel also has a stronger Lunara treatment:
  - darker/glossier shell
  - better hover states
  - clearer icon/text rhythm

## Latest Front-Door Follow-Through (2026-03-30)

- The remaining blank `More` submenu hitch has been closed:
  - `About the Ledger` now renders visibly instead of appearing as an empty icon row
- The homepage hero copy panel now has a stronger Lunara shell and better connection to the Oscar pulse card.
- The right-side pulse stack now reads more like an editorial companion than a detached utility panel.
- This keeps the front door moving from “stable and styled” toward “finished and authored.”

## Latest Editorial Route State (2026-03-30)

- `/news/` has been repaired at the theme level with a dedicated `page-news.php` template.
- The old raw shortcode leak on the live News page is gone.
- A helper now strips shortcode-only scaffolding out of archive-intro sources before that copy can surface publicly.
- Footer editorial fallback routing has been cleaned so the dead `/journal/` path no longer leaks through the fallback menu.
- Current live smoke checks are clean for:
  - `https://lunarafilm.com/`
  - `https://lunarafilm.com/news/`
  - `https://lunarafilm.com/reviews/`
  - `https://lunarafilm.com/oscars/`
- This keeps the front door moving from “stable and styled” toward “finished and authored.”

## Latest News Archive State (2026-03-30)

- /news/ now has a dedicated premium shell rather than merely borrowing the generic editorial archive shell.
- It now carries:
  - Signal Check orientation panel
  - stronger spotlight/rail/grid cadence when news posts exist
  - a more intentional standby state when the lane is empty
- The editorial side is now closer in quality and authorship to the reviews side instead of feeling like a repaired but secondary route.

## Latest Homepage Hero Shell State (2026-03-30)

- The homepage hero now has a stronger left-side Lunara shell rather than a looser text-only opening.
- A compact overview strip now sits beneath the homepage CTAs so the front door clarifies the core identity faster:
  - criticism
  - ledger
  - current pulse
- The relationship between the left hero panel and the `Latest Oscar Pulse` card is tighter and more authored.
- The pulse card hierarchy is cleaner:
  - stronger poster anchor
  - better summary/metrics/title-chip rhythm
  - more convincing editorial-marquee feel at the top of the homepage
- Smoke checks remained clean on `/`, `/reviews/`, `/news/`, and `/oscars/`.

## Latest Homepage Criticism Front-Door State (2026-03-30)

- The homepage `Latest Reviews` strip now follows the same more controlled criticism language as the archive:
  - kicker present
  - teaser line controlled
  - poster/card shell stronger and more uniform
- This keeps the homepage from feeling premium at the hero and then less authored immediately underneath it.

## Latest Homepage Editorial Lane State (2026-03-30)

- The homepage dispatch lane now behaves more like a curated editorial signal desk.
- It now includes:
  - a compact overview strip
  - a stronger lead hierarchy
  - a firmer premium rail shell
- This keeps the homepage publication side from lagging behind the reviews side in finish quality.

## Latest Homepage Module Uniformity State (2026-03-30)

- The homepage modules below the hero now share a more common shell language.
- Featured reviews, winner cards, database cards, and lore cards are closer in finish quality and visual rhythm.
- This reduces the feeling of the homepage being several strong modules from different eras and moves it closer to one authored front door.

## Latest Shared Shell Clarity State (2026-03-30)

- The header utility controls have been tightened again:
  - search trigger reads more like a deliberate Lunara control
  - active/expanded states are clearer
  - focus-visible treatment is now stronger for keyboard work
- Dropdown submenu navigation is more predictable:
  - current submenu items now carry a stronger active state
  - hover/focus rhythm is cleaner and less default-looking
- The custom Lunara footer now behaves more like a composed utility surface:
  - each nav column has its own panel shell
  - the utility/legal row is more disciplined
  - the branded close is more intentional without changing protected content/data
- Current live smoke checks remain clean on:
  - `https://lunarafilm.com/`
  - `https://lunarafilm.com/reviews/`
  - `https://lunarafilm.com/news/`
  - `https://lunarafilm.com/oscars/`

## Latest Oscars Seam-Removal State (2026-03-30)

- The remaining Oscars-side seam is now less about raw layout and more about refinement; this pass addressed the language and bridge modules that were still reading too literally.
- Oscars-side review bridges now use the same controlled Lunara invitation line:
  - `Open the review and enter the full argument.`
- The primary review bridge on title pages now uses `Review Archive` instead of the blunter `All Reviews`.
- Entity, ceremony, and category pages now carry cleaner Lunara-authored language in:
  - Oscar history modules
  - nominated films / category highlights
  - On Lunara review bridges
  - research / explorer callouts
  - winner-circle framing
- CSS follow-through keeps the controlled review-bridge line and related-card rhythm visually intentional.
- Current live smoke checks remain clean on:
  - `https://lunarafilm.com/oscars/`
  - `https://lunarafilm.com/oscars/ceremonies-page/`
  - `https://lunarafilm.com/oscars/ceremony/98/`
  - `https://lunarafilm.com/oscars/title/tt30144839/`

## Latest Oscars Detail-Page Rhythm State (2026-03-30)

- The next follow-through pass stayed on the Oscars product but shifted from copy/bridge cleanup into pacing and composition.
- Ceremony, category, and detail pages now have a stronger shared rhythm across:
  - marquee blocks
  - spotlight stacks
  - explorer/research callouts
  - winner-circle spacing
  - shared hub-section cadence
- This was intentionally a CSS-only pass so the live Oscars product could keep tightening without reopening template risk.
- Current live smoke checks remain clean on:
  - `https://lunarafilm.com/oscars/`
  - `https://lunarafilm.com/oscars/ceremony/98/`
  - `https://lunarafilm.com/oscars/categories-page/`
  - `https://lunarafilm.com/oscars/title/tt30144839/`

## Latest Oscars Table-Shell State (2026-03-30)

- The Oscars explorer/data mode no longer feels quite as detached from the poster-first product.
- This pass tightened the premium shell around:
  - research/explorer callouts
  - filters disclosure
  - embedded table wrapper
  - filter/control styling in raw table mode
- It was intentionally CSS-only so the data product could become more Lunara-authored without reopening route or query risk.
- Current live smoke checks remain clean on:
  - `https://lunarafilm.com/oscars/?view=table`
  - `https://lunarafilm.com/oscars/ceremony/98/?view=table`
  - `https://lunarafilm.com/oscars/category/actor-in-a-leading-role/?view=table`

## Latest Editorial Single State (2026-03-30)

- The standard non-review article template has now received a true flagship pass.
- The important underlying fix is that editorial singles now actually receive the dispatch-type classes the CSS system was already prepared to use.
- That means the live article layer now gets type-aware accenting for:
  - news
  - reactions
  - essays
  - podcast/audio
  - interviews
  - ink
- The standard article side was also tightened with:
  - a premium copy shell in the hero
  - a short signal note under the title
  - a second `Signal Context` rail card
  - stronger archive-return wording
  - a more intentional `Continue Reading` handoff
- Current live smoke checks remain clean on:
  - `https://lunarafilm.com/`
  - `https://lunarafilm.com/news/`
  - `https://lunarafilm.com/reviews/`
  - `https://lunarafilm.com/oscars/`
  - `https://lunarafilm.com/weapons-2025/`

## Latest Oscars Detail-Shell State (2026-03-30)

- The remaining Oscars-side visual outlier was the detail stack itself more than the high-level hub shell.
- This pass tightened that final seam through CSS only, focusing on:
  - entity status banner presence
  - timeline card shell and spacing
  - history-item hierarchy and status alignment
  - ceremony/category marquee copy rhythm
  - spotlight/chip/explorer handoff
- Current live smoke checks remain clean on:
  - `https://lunarafilm.com/oscars/`
  - `https://lunarafilm.com/oscars/ceremony/98/`
  - `https://lunarafilm.com/oscars/category/actor-in-a-leading-role/`
  - `https://lunarafilm.com/oscars/title/tt30144839/`

## Latest Coherence Sweep State (2026-03-30)

- The last shared-shell pass stopped targeting modules and started targeting the control surface itself.
- Theme-side and Oscars-side CTA/button/section-head behavior is now more aligned, especially around:
  - section-link pills
  - action rows
  - hover/focus treatment
  - section-head rhythm
- Current live smoke checks remain clean on:
  - `https://lunarafilm.com/`
  - `https://lunarafilm.com/news/`
  - `https://lunarafilm.com/reviews/`
  - `https://lunarafilm.com/oscars/`

## Latest Editorial Archive Parity State (2026-03-30)

- The last meaningful editorial-family outlier was the generic archive layer:
  - category archives
  - tag archives
  - author archives
  - date archives
- Those now share a richer Lunara archive grammar closer to the `News` lane:
  - hero shell
  - `Signal Check` orientation panel
  - lead / support rail / grid composition
  - stronger empty-state note
- Template wording is also more specific now:
  - `Tagged Signal`
  - `Byline Archive`
  - `Calendar File`
- Current live smoke checks remain clean on:
  - `https://lunarafilm.com/`
  - `https://lunarafilm.com/news/`
  - `https://lunarafilm.com/reviews/`
  - `https://lunarafilm.com/oscars/`
  - `https://lunarafilm.com/category/news/`
  - `https://lunarafilm.com/tag/2025/`
  - `https://lunarafilm.com/author/lunarafilm/`

## Latest Utility Shell State (2026-03-30)

- Search and 404 are no longer generic fallback surfaces.
- `search.php` now renders a Lunara-owned search desk with:
  - a search hero
  - a summary panel
  - mixed result cards routed through the existing review/editorial systems where possible
- `404.php` now renders a Lunara-owned recovery surface with:
  - `Lost Signal`
  - Home / Reviews / Oscars recovery routes
  - integrated search
- Current live checks remain clean on:
  - `https://lunarafilm.com/?s=weapons`
  - a deliberate nonexistent route returning `404`
  - `https://lunarafilm.com/`
  - `https://lunarafilm.com/news/`
  - `https://lunarafilm.com/oscars/`

## Latest Search Functionality State (2026-03-30)

- Search is now a real Lunara gateway rather than only a styled results page.
- The main search query deliberately includes:
  - `review`
  - `post`
  - `page`
- The search desk also surfaces direct Oscars entity matches from the live ledger table, including:
  - title routes
  - person routes
  - company routes
- Confirmed live on:
  - `https://lunarafilm.com/?s=weapons`
  - `https://lunarafilm.com/?s=mikey+madison`
  - `https://lunarafilm.com/?s=sinners`

## Latest Live Search Suggestion State (2026-03-30)

- Front-end search inputs now have lightweight live suggestions.
- Suggestion sources currently include:
  - review/posts/pages from WordPress
  - direct Oscars entity matches from the live ledger table
- Confirmed live suggestion output on:
  - `we`
  - `weapons`
  - `mikey madison`
- Follow-up pruning pass completed:
  - winner bonuses no longer create false-positive Oscar title suggestions when the text match itself is invalid
  - the noisy `Deadly Deception ... Nuclear Weapons ...` hit was removed from `weapons`
  - current suggestion lane is cleaner for impatient users while keeping the useful cross-product routes
- Follow-up ordering/context pass completed:
  - mixed suggestions now sort by confidence across sources
  - ledger hits now carry a small context line in the dropdown
  - Lunara-native review/editorial answers get a small ranking edge when the site has a strong native match
- Follow-up recovery/search-desk pass completed:
  - weak typo queries can now surface `Closest Routes` instead of only an empty state
  - live suggestions can fall back to typo-tolerant recovery routes
  - the dropdown now ends with a `See all results on the record` action that links into the full search desk
- Follow-up entry-point polish pass completed:
  - the live dropdown now has a clearer full-search escape hatch
  - search entry behavior and the full search desk now feel like one guided system instead of two separate experiences
- Acceptance sweep follow-through completed:
  - Oscars detail, hub, and table surfaces now use Lunara-owned ledger branding instead of generic `Academy Awards Database` language
  - the empty `/news/` route now has a stronger standby state with a `Stay On Signal` route deck into Reviews, the Oscar Ledger, and the homepage
  - the homepage Oscar front-door language now matches the live Oscars product too, with the remaining visible `Database` phrasing normalized into `Ledger`
  - the footer and Oscars portal shortcut layer now match that same ledger wording, so the public-facing Oscar product is no longer split between `Database` and `Ledger`
  - the true no-results search state now has a `Stay In The Record` recovery deck instead of collapsing into a cold dead-end
  - the 404 page recovery language now points back into the `Oscar Ledger` and `news desk` instead of older generic wording
  - the final public-facing wording leak was then closed in the dedicated theme portal template and the plugin hub/index template, so `/oscars/`, table mode, categories, and ceremonies now all scan clean for remaining `Open Full Database` / `Database` leftovers on the public routes
  - the last obviously backstage archive/search labels were then cleaned out of the public shells too, so readers now see `At A Glance`, `Page Shape`, and `Desk State` instead of internal planning language like `Signal Check`, `Archive Orientation`, or `Lead / Rail / Grid`
  - the editorial lane now uses `Journal` as the canonical public umbrella label while keeping the `/news/` route stable, so the publication triad reads `Reviews / Journal / Oscar Ledger` in the public chrome and recovery surfaces
- Homepage hero/layout follow-through completed:
  - the generic left homepage brand card has been replaced with a compact `Latest Reviews` front built from four real review cards
  - the `Latest Oscar Pulse` card remains on the right side of the hero
  - the stronger `Oscar Ledger Spotlight` block now visually precedes the older Oscar spotlight module through the homepage slot-order system
  - responsive styling for the new review-front hero is in place for tablet and phone collapse
  - Georgia is now used for the new homepage review-front display titles, while `LUNARA FILM` remains a selective all-caps imprint treatment
  - the homepage `Oscar Lore` carousel remains live, but the supporting `Oscars deep dive` / deep-cut strip underneath it has been intentionally removed until that data is accurate enough to trust publicly
  - the restored `Oscar Lore` carousel has a stronger standalone shell and spacing treatment so the lane still feels complete without that supporting strip
  - the homepage editorial lane now visibly reads more like `Journal`, with compatibility normalization lifting older saved homepage labels away from `Dispatches & Audio`
  - more public Oscars wording leaks have been normalized from `database` to `ledger` across hub/entity/table/tracker templates
  - verified live on `/?codexcheck=20260331-home-review-hero-swap`
