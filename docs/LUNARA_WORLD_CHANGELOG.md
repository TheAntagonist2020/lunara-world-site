# Lunara World Changelog

## 2026-03-29

### Review archive unification
- Rewired `/reviews/` to use the shared archive shell instead of the older bespoke `archive-review.php` path.
- Standardized the archive lead, rail, and grid onto one criticism system.
- Removed visible real-excerpt usage from review cards and replaced it with the controlled placeholder line:
  - `Open the review and enter the full argument.`

### Review single composition
- Preserved the approved review hero structure:
  - title with poster directly underneath on the left
  - excerpt, meta, `Where to Watch`, and `Review Details` in the right-hand sidebar
- Preserved `Lunara Debrief` as the signature closing module.
- Preserved paired-film behavior:
  - title click -> Letterboxd
  - `IMDb` chip -> IMDb
  - `Oscar Ledger` pill -> internal Lunara ledger route

### Mobile criticism pass
- Tightened the mobile review single so the poster remains directly under the title with cleaner spacing through the hero stack.
- Reduced the mobile archive lead-card mass so it reads less like a towering slab.
- Improved compact archive-card fallback styling so no-poster states feel intentional instead of empty.
- Kept poster sizes more uniform across the review grid/archive language.

### Deployment / safety
- Continued using direct SSH deployment as the primary live path.
- Kept `Code Snippets Pro` out of the critical path.
- No reviews, posts, media files, or Oscars data were deleted or structurally altered.

## 2026-03-30

### Homepage Oscars consolidation
- Merged the homepage `Oscar Ledger Highlights` and `Oscar Lore` lanes into one stronger `Oscar Lore` section.
- Kept the homepage Oscars storytelling to one premium poster-led lane instead of two adjacent lanes competing for the same conceptual space.
- Repositioned the database/stat side of that material into a smaller supporting strip labeled `Inside the Ledger` beneath the merged lore lane.
- Normalized the merged cards so homepage Oscar stories can mix:
  - curated lore cards
  - ledger-derived records
  - one shared poster-led card grammar

### Carousel behavior
- Added a slow homepage-only autoplay drift for the merged Oscars lane on desktop-sized viewports.
- Autoplay respects `prefers-reduced-motion`.
- Autoplay pauses naturally on hover/focus and loops back to the beginning instead of running off the end.
- Mobile remains swipe/manual rather than auto-drifting.

### Verification
- Live homepage verified with the merged Oscars lane active.
- Smoke-tested:
  - `/`
  - `/reviews/`
  - `/reviews/weapons-2025/`
  - `/oscars/`
- No reviews, posts, media files, or Oscars data were deleted or structurally altered.

### Front-door polish pass
- Tightened the homepage shell rhythm so sections read more like deliberate movements instead of stacked modules.
- Added stronger section separators and cleaner section-head spacing on the homepage.
- Improved header utility styling:
  - search trigger feels more intentional
  - dropdowns/readout surfaces feel less default
- Improved footer finish:
  - stronger closing atmosphere
  - better spacing through the footer grid and utility row
  - privacy/legal controls read more like part of the Lunara system

### Front-door verification
- Rechecked:
  - `/`
  - `/reviews/`
  - `/oscars/`
- No reviews, posts, media files, or Oscars data were deleted or structurally altered.

### Hero and pulse composition pass
- Tightened the homepage opening spread so the hero copy and Oscar pulse card read more like one composed editorial band.
- Increased the hero copy breathing room and gave the pulse card a more deliberate relationship to the lead copy and CTA row.
- Kept the homepage Oscars carousel behavior intact while improving the opening visual hierarchy.

### Top-band convenience pass
- Added a primary-nav title fallback for icon-only menu items so the `More` dropdown cannot silently surface blank labels.
- Improved the dropdown panel treatment with a darker glassier shell, stronger hover states, and clearer icon/text rhythm.
- Continued refining the homepage top band so the front door feels more authored operationally, not just visually.

### Front-door control and hero shell follow-through
- Closed the last blank `More` dropdown seam by restoring a readable `About the Ledger` label for the icon-driven submenu item.
- Gave the homepage hero copy panel a stronger Lunara shell:
  - inset panel treatment
  - subtle grain/light texture
  - more deliberate lower rule
- Tightened the relationship between the hero and `Latest Oscar Pulse` card so the opening spread feels more composed and premium.
- Nudged the pulse poster and card balance upward so the right rail reads less like a utility stack and more like a designed editorial companion.

### Follow-through verification
- Rechecked the live homepage front door and `More` dropdown.
- Confirmed the formerly blank submenu item now reads as `About the Ledger`.
- No reviews, posts, media files, or Oscars data were deleted or structurally altered.

### Editorial route cleanup pass
- Added a safe archive-summary helper so shortcode-only page scaffolding can no longer leak into Lunara archive intros.
- Repaired the `/news/` route with a dedicated `page-news.php` template:
  - `News` now renders as a real Lunara editorial archive
  - the raw `[lunara_posts ...]` artifact is gone
  - the page now uses the shared editorial archive shell instead of inherited page content
- Repaired footer editorial fallback routing so the broken `/journal/` path no longer leaks through the fallback menu.
- Live smoke checks after deploy were clean for:
  - `/`
  - `/news/`
  - `/reviews/`
  - `/oscars/`

### News archive premium pass
- Upgraded the repaired `News` route from a merely fixed archive into a true Lunara editorial surface.
- Added a dedicated premium `News` shell with:
  - `Signal Check` orientation panel
  - authored `Lead / Bulletin Rail / Grid` cadence when posts exist
  - stronger standby state when coverage is light
- Added a richer empty-state treatment so `/news/` still feels like part of the same premium world as reviews and Oscars even before the lane fills out.
- Confirmed live route markers on `/news/`:
  - `Signal Check`
  - `Desk Standby`
  - `What Lives Here`
### Homepage hero shell pass
- Gave the homepage hero a stronger authored shell instead of leaving the left side as a looser text block.
- Added a compact Lunara overview strip under the hero CTAs so readers immediately understand the criticism/archive/ledger split.
- Tightened the opening spread between the left hero panel and the `Latest Oscar Pulse` card so the front door reads like one composed editorial marquee.
- Rebalanced the pulse card hierarchy:
  - stronger poster anchor
  - cleaner internal spacing
  - more deliberate relationship between summary, metrics, and title chips
- Live smoke checks remained clean on:
  - `/`
  - `/reviews/`
  - `/news/`
  - `/oscars/`
### Homepage latest-reviews follow-through
- Carried the homepage hero polish one section lower so the front door does not flatten out after the opening band.
- Added a controlled review-card kicker and teaser line to the homepage `Latest Reviews` strip.
- Tightened poster/card uniformity and strengthened the card shell so the homepage criticism lane feels closer to the review archive quality bar.
### Homepage dispatch signal-desk pass
- Upgraded the homepage editorial lane so it reads more like a curated signal desk and less like a simple post row.
- Added a compact overview strip above the dispatch lead/rail stack.
- Strengthened the lead card hierarchy and gave the supporting rail a firmer premium shell.
- Kept the lane flexible for news, reactions, essays, and audio while making its homepage identity more deliberate.
### Homepage lower-card uniformity pass
- Tightened the featured-reviews carousel so it feels less bare and more in-family with the rest of the homepage.
- Added controlled review-card teaser language inside the featured shelf.
- Pulled the homepage winner, database, and lore cards closer to the same shell language with richer surface/light treatment and more consistent interior rhythm.

### Shared shell clarity pass
- Tightened the live header controls so the search trigger and utility buttons feel more like deliberate Lunara tools instead of default chrome.
- Added clearer hover, active, and focus-visible states for the top-band controls and submenu links.
- Highlighted current submenu items so dropdown navigation feels more predictable while working inside the site.
- Upgraded the Lunara footer columns into stronger panel-like shells with better structure, cleaner legal-row rhythm, and more intentional utility-chip behavior.
- Live smoke checks remained clean on:
  - `/`
  - `/reviews/`
  - `/news/`
  - `/oscars/`
- No reviews, posts, media files, or Oscars data were deleted or structurally altered.

### Oscars seam-removal pass
- Tightened the Oscars hub/entity language so the pages read less like raw plugin surfaces and more like the rest of Lunara.
- Replaced live review excerpts inside Oscars-side review bridges with the controlled teaser line:
  - `Open the review and enter the full argument.`
- Updated title-page and ceremony/category review bridges to use stronger Lunara wording and a cleaner `Review Archive` action.
- Refined the surrounding module copy for:
  - Oscar history
  - nominated films / category highlights
  - On Lunara review bridges
  - research / explorer callouts
  - winner-circle framing
- Added a CSS follow-through so the controlled review-bridge line and related-card rhythm stay visually intentional.
- Live smoke checks remained clean on:
  - `/oscars/`
  - `/oscars/ceremonies-page/`
  - `/oscars/ceremony/98/`
  - `/oscars/title/tt30144839/`
- No reviews, posts, media files, or Oscars data were deleted or structurally altered.

### Oscars detail-page rhythm pass
- Tightened the ceremony/category/detail-page pacing so those modules feel more like one composed Lunara system and less like adjacent plugin sections.
- Strengthened the rhythm and presence of:
  - ceremony/category marquee blocks
  - spotlight stacks
  - explorer/research callouts
  - winner-circle spacing
  - shared hub-section cadence
- This was a CSS-only follow-through on the Oscars product, keeping the routes stable while improving the sense of editorial composition.
- Live smoke checks remained clean on:
  - `/oscars/`
  - `/oscars/ceremony/98/`
  - `/oscars/categories-page/`
  - `/oscars/title/tt30144839/`
- No reviews, posts, media files, or Oscars data were deleted or structurally altered.

### Oscars table-shell pass
- Gave the explorer/data mode a stronger Lunara shell so switching into raw table view no longer feels like stepping out of the product.
- Tightened:
  - research callout shell
  - filters disclosure treatment
  - embedded table wrapper shell
  - filter/control styling inside poster-first and table-view routes
- This was a CSS-only database/explorer refinement pass; no query logic or data handling changed.
- Live smoke checks remained clean on:
  - `/oscars/?view=table`
  - `/oscars/ceremony/98/?view=table`
  - `/oscars/category/actor-in-a-leading-role/?view=table`
- No reviews, posts, media files, or Oscars data were deleted or structurally altered.

### Editorial single flagship pass
- Upgraded the standard editorial single template so non-review articles now carry the same authored confidence as the criticism side.
- Added the missing dispatch-type classes to the live article template, which finally activates the color-accent system that was already prepared in CSS for:
  - news
  - reactions
  - essays
  - podcast/audio
  - interviews
  - ink
- Strengthened the standard post hero and right rail with:
  - a premium copy shell
  - a short signal note
  - a clearer `Signal Context` card
  - better archive return language
  - a more deliberate `Continue Reading` handoff
- Live checks remained clean on:
  - `/`
  - `/news/`
  - `/reviews/`
  - `/oscars/`
  - `/weapons-2025/`
- No reviews, posts, media files, or Oscars data were deleted or structurally altered.

### Oscars detail-shell polish pass
- Tightened the last major Oscars detail-page seam through a CSS-led refinement focused on composition rather than logic.
- Strengthened:
  - entity status banner presence
  - timeline card shell and spacing
  - history-item hierarchy and badge/status alignment
  - ceremony/category marquee copy rhythm
  - spotlight/chip/explorer handoff
- This was intentionally a presentation-only pass:
  - no query logic changed
  - no importer logic changed
  - no Oscars data changed
- Live smoke checks remained clean on:
  - `/oscars/`
  - `/oscars/ceremony/98/`
  - `/oscars/category/actor-in-a-leading-role/`
  - `/oscars/title/tt30144839/`
- No reviews, posts, media files, or Oscars data were deleted or structurally altered.

### Final coherence sweep
- Tightened the shared control surface between the theme side and the Oscars product so the site behaves more like one authored system at the CTA/action level, not just the card/module level.
- Theme-side gains:
  - stronger section-link pill treatment
  - tighter section-head rhythm
  - more unified button hover/focus behavior
- Oscars-side gains:
  - action-row/button rhythm aligned with the main theme
  - stronger CTA shell and hover states
  - section-head spacing and title rhythm brought closer to the theme language
- This was a finish-layer CSS pass only.
- Live smoke checks remained clean on:
  - `/`
  - `/news/`
  - `/reviews/`
  - `/oscars/`
- No reviews, posts, media files, or Oscars data were deleted or structurally altered.

### Editorial archive parity sweep
- Promoted the last generic editorial archive surfaces into the same premium hero-plus-orientation system that `News` already uses.
- Strengthened the shared editorial archive renderer so posts index, category archives, tag archives, author archives, and date archives now carry:
  - a Lunara hero shell
  - a `Signal Check` orientation panel
  - a lead/support/grid composition
  - stronger standby language when an archive is empty
- Added smarter archive-specific voice in template routing:
  - `Tagged Signal`
  - `Byline Archive`
  - `Calendar File`
  - category-specific rail/run copy
- This was a theme-only pass touching:
  - `functions.php`
  - `category.php`
  - `archive.php`
  - `style.css`
- Live smoke checks remained clean on:
  - `/`
  - `/news/`
  - `/reviews/`
  - `/oscars/`
  - `/category/news/`
  - `/tag/2025/`
- `/author/lunarafilm/`
- No reviews, posts, media files, or Oscars data were deleted or structurally altered.

### Utility shell pass
- Search results and 404 are no longer falling back to generic theme behavior.
- Added dedicated theme templates for:
  - `search.php`
  - `404.php`
- Search now keeps users inside a Lunara-authored shell with:
  - a `Search Desk` hero
  - a `Signal Check` summary
  - mixed results rendered through the existing review/editorial card systems
- 404 now keeps recovery inside the publication world with:
  - a `Lost Signal` hero
  - direct routes back to Home / Reviews / Oscars
  - a built-in search form
- Live checks remained clean on:
  - `/?s=weapons`
  - a deliberate nonexistent route returning a proper `404`
  - `/`
  - `/news/`
  - `/oscars/`
- No reviews, posts, media files, or Oscars data were deleted or structurally altered.

### Search functionality pass
- Search moved from a styled page into a real Lunara gateway.
- The main search query now deliberately includes:
  - `review`
  - `post`
  - `page`
- The search desk now also surfaces direct Oscars-side matches from the live ledger table:
  - title routes
  - person routes
  - company routes
- Result rendering now behaves more like the site itself:
  - reviews stay in review-card form
  - editorial posts stay in dispatch-card form
  - direct Oscars entities get a `Direct Ledger Matches` section
- Confirmed live on:
  - `/?s=weapons`
  - `/?s=mikey+madison`
  - `/?s=sinners`
- No reviews, posts, media files, or Oscars data were deleted or structurally altered.

### Live search suggestion pass
- Added lightweight live suggestions to front-end search inputs so users get useful routes before they ever hit the full results page.
- Suggestions now blend:
  - reviews
  - editorial posts/pages
  - direct Oscars entity hits
- Confirmed the AJAX endpoint is returning live suggestions for:
  - `we`
  - `weapons`
  - `mikey madison`
- Confirmed the live search script is present on the front-end search desk.
- No reviews, posts, media files, or Oscars data were deleted or structurally altered.

### Search suggestion pruning pass
- Tightened Oscars-side suggestion scoring so winner bonuses only apply to real text matches, not unrelated long-title edge cases.
- This removed the noisy `Deadly Deception: General Electric, Nuclear Weapons and Our Environment` hit from `weapons` suggestions while keeping the useful cross-product routes intact.
- Confirmed live suggestion output after deploy on:
  - `weapons`
  - `we`
  - `mikey madison`
- The current live suggestion lane now behaves more like an impatient-user shortcut than a broad keyword dump.
- No reviews, posts, media files, or Oscars data were deleted or structurally altered.

### Search suggestion ordering and context pass
- Mixed live suggestions now sort by confidence across sources instead of always listing WordPress results first and Oscars results second.
- The dropdown now includes small context lines for ledger hits so people can see why a result matters before they click.
- Lunara-owned criticism and editorial surfaces now get a small ranking edge when the site has a strong native answer, which keeps review-first searches feeling like Lunara before ledger.
- Confirmed live suggestion output after deploy on:
  - `weapons`
  - `sinners`
  - `mikey madison`
- No reviews, posts, media files, or Oscars data were deleted or structurally altered.

### Search recovery and search-desk follow-through pass
- Added a typo-tolerant recovery layer so weak queries can surface closest Lunara and ledger routes instead of dropping directly into a cold empty state.
- Search results pages now show a `Closest Routes` lane when the exact query misses but the intended destination is still obvious enough to recover.
- Live suggestions now fall back to those recovery routes when the direct suggestion set is empty.
- The search dropdown also now ends with an explicit `See all results on the record` action so users can jump straight to the full search desk.
- Confirmed live on:
  - `/?s=weapns`
  - live suggestions for `weapns`
  - live suggestions for `weapons`
- No reviews, posts, media files, or Oscars data were deleted or structurally altered.

### Search entry-point polish pass
- Added a dedicated full-desk escape hatch to the live suggestion dropdown so users can always jump from fast suggestions into the complete search experience.
- Kept the search-box behavior aligned with the stronger search desk language rather than leaving the dropdown as a dead-end suggestion list.
- Confirmed live on:
  - `/?s=weapons`
  - `/?s=weapns`
  - live suggestions for `weapons`
- No reviews, posts, media files, or Oscars data were deleted or structurally altered.

### Oscars ledger-language seam removal pass
- Replaced the remaining generic `Academy Awards Database` language on live Oscars routes with Lunara-owned ledger branding.
- Updated document titles, route labels, and action text so entity, hub, and table-mode pages now read as `The Lunara Oscar Ledger` instead of a generic plugin product.
- Confirmed live on:
  - title pages
  - ceremony pages
  - category pages
  - table mode
- No reviews, posts, media files, or Oscars data were deleted or structurally altered.

### News standby-state acceptance pass
- Strengthened the empty `/news/` route so it now behaves like an intentional editorial holding pattern instead of a page that visually dies after two cards.
- Added a `Stay On Signal` standby deck with guided routes into Reviews, the Oscar Ledger, and the homepage while the next dispatch is still pending.
- Refined the empty-state copy so the desk reads as live-but-waiting rather than inactive.
- Confirmed live on:
  - `/?codexcheck=20260331-news-standby-pass`
- No reviews, posts, media files, or Oscars data were deleted or structurally altered.

### Homepage ledger-language alignment pass
- Removed the last visible homepage `Database` phrasing from the Oscar front-door modules so the homepage now agrees with the live Oscars product branding.
- Updated the homepage CTA, spotlight kicker, and ledger-entry links to use `Oscar Ledger` language consistently.
- Added compatibility handling so legacy saved homepage button text that still says `Explore the Oscar Database` is normalized into `Explore the Oscar Ledger` without manual cleanup.
- Confirmed live on:
  - `/?codexcheck=20260331-home-ledger-language-4`
- No reviews, posts, media files, or Oscars data were deleted or structurally altered.

### Footer and Oscars portal ledger-language cleanup pass
- Finished the remaining reader-facing `Database` wording drift in the footer and Oscars portal shortcut layer.
- Updated the footer Oscar column link from `Database` to `Ledger`, the footer fallback from `Oscar Database` to `Oscar Ledger`, and the Oscars portal shortcut language from `Open Full Database` / `Use the full database` to ledger-owned phrasing.
- Confirmed live on:
  - homepage footer
  - Oscars portal
- No reviews, posts, media files, or Oscars data were deleted or structurally altered.

### Empty-search recovery deck pass
- Strengthened the true no-results search state so it now behaves like a recovery surface instead of a cold form-and-dead-end page.
- Added a `Stay In The Record` route deck that sends users directly into Reviews, the Oscar Ledger, or the News desk when a query misses completely.
- Kept the existing search form in place, but added guided next moves for impatient users who would otherwise bounce.
- Confirmed live on:
  - `/?s=zzzxqv&codexcheck=20260331-search-empty-routes-2`
- No reviews, posts, media files, or Oscars data were deleted or structurally altered.

### 404 ledger-route wording pass
- Tightened the 404 recovery language so the route now points people back toward the `Oscar Ledger` instead of generic `Oscars` wording.
- Updated the stable-hubs line, the recovery copy, and the primary ledger action label so the not-found experience stays in the same Lunara language as the rest of the site.
- Confirmed live on:
  - `/this-page-definitely-does-not-exist?codexcheck=20260331-404-ledger-pass`
- No reviews, posts, media files, or Oscars data were deleted or structurally altered.

### Final public Ledger-language seam removal pass
- Closed the last reader-facing `Database` wording leak across the dedicated Oscars portal template and the plugin hub/index template.
- Updated the remaining portal and hub CTA/copy language to `Ledger`, including `Open Full Ledger` on the categories and ceremonies index surfaces.
- Confirmed live on:
  - `/oscars/?codexcheck=20260331-ledger-wording-scan-2`
  - `/oscars/?view=table&codexcheck=20260331-ledger-wording-scan-2`
  - `/oscars/categories-page/?codexcheck=20260331-ledger-wording-scan-2`
  - `/oscars/ceremonies-page/?codexcheck=20260331-ledger-wording-scan-2`
- No reviews, posts, media files, or Oscars data were deleted or structurally altered.

### Public archive/search jargon cleanup pass
- Removed the last obviously backstage archive-language from the public review, news, search, and editorial archive shells.
- Reframed those summary panels around `At A Glance`, `Page Shape`, and `Desk State` so the structure stays clear without exposing internal planning labels like `Archive Orientation` or `Lead / Rail / Grid`.
- Confirmed live on:
  - `/reviews/?codexcheck=20260331-acceptance-scan-2`
  - `/news/?codexcheck=20260331-acceptance-scan-2`
  - `/?s=zzzxqv&codexcheck=20260331-acceptance-scan-2`
  - `/category/news/?codexcheck=20260331-acceptance-scan-2`
- No reviews, posts, media files, or Oscars data were deleted or structurally altered.

### Journal label consolidation pass
- Promoted `Journal` to the canonical public-facing umbrella label for the editorial lane while keeping the `/news/` route stable.
- Updated the `/news/` route fallback title, the empty-search recovery route, the 404 recovery language, and the footer fallback so the publication triad now reads `Reviews / Journal / Oscar Ledger`.
- Confirmed live on:
  - `/news/?codexcheck=20260331-journal-pass`
  - `/?s=zzzxqv&codexcheck=20260331-journal-pass`
  - `/this-page-definitely-does-not-exist?codexcheck=20260331-journal-pass`
  - `/?codexcheck=20260331-journal-pass`
- No reviews, posts, media files, or Oscars data were deleted or structurally altered.

### Homepage review-front swap pass
- Replaced the left homepage hero panel with a compact `Latest Reviews` front so the criticism is immediately visible on first load instead of hiding behind a generic brand card.
- Kept the `Latest Oscar Pulse` card anchored on the right and preserved the hero CTA row underneath the review-card stack.
- Used the existing homepage slot-order system to elevate the `Oscar Ledger Spotlight` block ahead of the older Oscar spotlight module, giving the stronger ledger card the more prominent position without rewriting large homepage markup.
- Added responsive rules so the new review-front hero stays readable on tablet and collapses cleanly on phones.
- Follow-up typography decision:
  - pushed Georgia into the new homepage review-front display moments
  - kept `LUNARA FILM` as a selective all-caps imprint treatment rather than applying all caps broadly to editorial titles
- Follow-up homepage cleanup:
  - initially removed too much from the homepage Oscar lane
  - corrected that by restoring the `Oscar Lore` carousel and removing only the supporting `Oscars deep dive` / deep-cut strip underneath it
  - the final homepage state keeps the fun lore carousel while dropping the inaccurate supporting category/deep-dive block
- Confirmed live on:
  - `/?codexcheck=20260331-home-review-hero-swap`
  - `/?codexcheck=20260331-home-lore-not-deep-dive`
- Verified in the rendered HTML:
  - `Latest Reviews` hero title present
  - `Latest Oscar Pulse` card present
  - `4` hero review cards rendered
  - `Oscar Lore` / homepage Oscar story section present
  - no homepage Oscar supporting-head / deep-dive strip present
- Follow-up lore-shell polish:
  - added a stronger standalone shell around the restored `Oscar Lore` carousel so it feels intentional after the supporting strip was removed
  - tightened the header-to-carousel spacing and added mobile/tablet padding adjustments so the lane reads cleanly across breakpoints
- Public language convergence pass:
  - promoted `Journal` more clearly on the homepage editorial lane and added compatibility normalization so old saved homepage labels lift away from `Dispatches & Audio`
  - normalized the homepage ledger heading away from `Oscar Ledger Highlights`
  - closed more public Oscars wording leaks in the hub, entity, table, and tracker templates so visible reader-facing copy keeps choosing `ledger` over `database`
- No reviews, posts, media files, or Oscars data were deleted or structurally altered.
