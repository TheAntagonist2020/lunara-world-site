# Lunara Deploy Checklist

Use this checklist for every production deploy.

## 1) Pre-deploy

- [ ] Confirm branch is up to date with `main`
- [ ] Confirm PR is approved/ready and CI (`Lint and Validate`) is green
- [ ] Review changed files are limited to intended theme/plugin/docs scope
- [ ] Confirm protected assets were not deleted/structurally altered:
  - media files
  - written reviews
  - posts
  - Oscars database records
- [ ] Snapshot/backup current production theme/plugin files

## 2) Deploy

- [ ] Deploy only changed files from:
  - `wordpress/wp-content/themes/lunara-film-premium`
  - `wordpress/wp-content/plugins/academy-awards-table-optimized`
- [ ] Keep `Code Snippets Pro` out of critical-path recovery unless explicitly needed

## 3) Smoke Test (Live)

- [ ] `/`
- [ ] `/reviews/`
- [ ] `/news/`
- [ ] `/oscars/`
- [ ] One review single (`/reviews/{slug}/`)
- [ ] One Oscars detail route (`/oscars/title/{id}/` or `/oscars/ceremony/{id}/`)
- [ ] Check header/footer + mobile nav sanity on at least one page

## 4) If Something Breaks

- [ ] Roll back to previous known-good theme/plugin snapshot
- [ ] Re-test the same smoke routes
- [ ] Open a follow-up fix PR with root-cause notes

