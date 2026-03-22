#!/bin/bash
# ============================================================
#  SUITDEM ERP — Sync MAMP → suitdem_CC + Deploy
#  Copies files from MAMP local folder to CC folder,
#  keeps the Clever Cloud config.php intact, then deploys.
#  Usage: ./sync_and_deploy.sh "description du changement"
# ============================================================

MSG=${1:-"sync $(date '+%Y-%m-%d %H:%M')"}

MAMP_DIR="/Applications/MAMP/htdocs/suitdem"
CC_DIR="$HOME/Desktop/suitdem_CC"

echo "🔄 Sync MAMP → suitdem_CC..."

# Copy everything except api/config.php (keep CC credentials)
rsync -av --exclude='.git' --exclude='api/config.php' \
    "$MAMP_DIR/" "$CC_DIR/"

echo "✅ Sync OK — config.php Clever Cloud préservé"

# Deploy to Clever Cloud
echo ""
echo "🚀 Déploiement Clever Cloud..."
echo "📝 Commit: $MSG"

cd "$CC_DIR"
git add .
git commit -m "$MSG"
git push origin main

echo ""
echo "✅ Déployé — en ligne dans ~2 min"
echo "👉 App: https://app-7e2ea449-02b1-4e81-9af2-f1ad732d06aa.cleverapps.io"
