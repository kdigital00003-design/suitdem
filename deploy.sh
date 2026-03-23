#!/bin/bash
# ============================================================
#  SUITDEM ERP — Deploy suitdem_CC → GitHub → Clever Cloud
#  Usage: bash deploy.sh "description du changement"
# ============================================================

MSG=${1:-"sync $(date '+%Y-%m-%d %H:%M')"}
CC_DIR="$(cd "$(dirname "$0")" && pwd)"

echo "🚀 Déploiement depuis $CC_DIR"
echo "📝 Commit: $MSG"

cd "$CC_DIR"
git add .
git commit -m "$MSG"
git push origin main

echo ""
echo "✅ Déployé — en ligne dans ~2 min"
open "https://app-7e2ea449-02b1-4e81-9af2-f1ad732d06aa.cleverapps.io"
