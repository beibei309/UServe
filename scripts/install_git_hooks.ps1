$ErrorActionPreference = "Stop"
Set-Location (Join-Path $PSScriptRoot "..")

git config core.hooksPath scripts/git-hooks
Write-Host "Git hooks installed with core.hooksPath=scripts/git-hooks"
