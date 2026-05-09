param(
    [Parameter(Mandatory = $true)]
    [string]$Message,

    [string]$WorkBranch = "dev/theme-work",
    [string]$BaseBranch = "main",

    [switch]$OpenPR
)

$ErrorActionPreference = "Stop"

function Run-Git {
    param([string[]]$GitArgs)
    Write-Host ("> git " + ($GitArgs -join " ")) -ForegroundColor Cyan
    & git @GitArgs
    if ($LASTEXITCODE -ne 0) {
        throw "Git command failed: git $($GitArgs -join ' ')"
    }
}

# Verify repository context.
& git rev-parse --is-inside-work-tree | Out-Null
if ($LASTEXITCODE -ne 0) {
    throw "Run this script inside the repository."
}

$current = (& git rev-parse --abbrev-ref HEAD).Trim()
if ($current -ne $WorkBranch) {
    Run-Git @("checkout", $WorkBranch)
}

# Keep the work branch synchronized before committing.
Run-Git @("pull", "--ff-only", "origin", $WorkBranch)

Run-Git @("add", "-A")

$status = (& git status --porcelain)
if (-not $status) {
    Write-Host "No changes to commit." -ForegroundColor Yellow
    $prUrl = "https://github.com/TheAntagonist2020/lunara-world-site/compare/$BaseBranch...$WorkBranch?expand=1"
    Write-Host "PR compare URL: $prUrl" -ForegroundColor Green
    exit 0
}

Run-Git @("commit", "-m", $Message)
Run-Git @("push", "origin", $WorkBranch)

if ($OpenPR -and (Get-Command gh -ErrorAction SilentlyContinue)) {
    Write-Host "Opening/creating PR with GitHub CLI..." -ForegroundColor Green
    & gh pr create --base $BaseBranch --head $WorkBranch --fill
    if ($LASTEXITCODE -ne 0) {
        Write-Host "Could not auto-create PR. Use compare URL below." -ForegroundColor Yellow
    }
}

$compareUrl = "https://github.com/TheAntagonist2020/lunara-world-site/compare/$BaseBranch...$WorkBranch?expand=1"
Write-Host "Done. Open PR here: $compareUrl" -ForegroundColor Green
