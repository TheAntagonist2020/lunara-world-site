# Lunara World Site Repository

This repository tracks the custom WordPress code for the Lunara Film website.

## What is in this repo

- `wordpress/wp-content/themes/lunara-film-premium`
  - Active Lunara child theme (presentation/editorial layer)
- `wordpress/wp-content/plugins/academy-awards-table-optimized`
  - Active Oscars plugin (data/query/presentation layer)
- `docs/`
  - Continuity and handoff documents for safe recovery and collaboration

## What is intentionally NOT in this repo

- WordPress core files
- Uploads/media library
- Database dumps
- Local recovery/temp folders
- Release zips and backups

## Why this structure is proper

- Keeps version control focused on custom code only.
- Makes deployments safer (theme/plugin scoped).
- Keeps history readable when multiple agents/people contribute.

## Suggested workflow

1. Create a feature branch for each change.
2. Modify only theme/plugin files in this repo.
3. Commit with clear messages.
4. Push and open a Pull Request.
5. Deploy theme/plugin updates from known-good commits.

## Safety rails in this repo

- PR template: `.github/pull_request_template.md`
- CI check: `.github/workflows/lint-and-validate.yml`
- Deploy runbook: `docs/DEPLOY_CHECKLIST.md`

## Protected assets (project rule)

Do not delete or structurally alter:

- Media files
- Written reviews
- Posts
- Oscars database records
