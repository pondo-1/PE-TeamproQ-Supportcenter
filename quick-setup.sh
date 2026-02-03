#!/bin/bash

# WordPress Plugin Production Release - Quick Setup Script
# Führt automatisches Setup für neue Plugin-Projekte durch

set -e

PLUGIN_NAME=${1:-"new-plugin"}
echo "🚀 Setting up Production Release for plugin: $PLUGIN_NAME"

# Schritt 1: Scripts Ordner erstellen
echo "📁 Creating scripts directory..."
mkdir -p scripts

# Schritt 2: package.json Scripts hinzufügen (manuell zu erweitern)
echo "📝 package.json Scripts die hinzugefügt werden sollten:"
cat << 'EOF'
  "scripts": {
    "build": "wp-scripts build",
    "start": "wp-scripts start",
    "build:production": "NODE_ENV=production wp-scripts build",
    "clean": "rm -rf build node_modules",
    "clean:build": "rm -rf build",
    "lint": "wp-scripts lint-js src/",
    "lint:fix": "wp-scripts lint-js src/ --fix",
    "release": "node scripts/production-release.js",
    "release:minor": "node scripts/production-release.js minor",
    "release:major": "node scripts/production-release.js major",
    "pack": "npm run package:plugin",
    "package:plugin": "node scripts/production-release.js"
  }
EOF

# Schritt 3: Dependencies installieren
echo "📦 Installing required dependencies..."
if [ -f "package.json" ]; then
    npm install --save-dev archiver semver
    echo "✅ Dependencies installed"
else
    echo "⚠️  package.json not found. Please create one first."
fi

# Schritt 4: .productionignore erstellen
echo "🗑️  Creating .productionignore..."
cat << 'EOF' > .productionignore
# Production Release - WordPress Plugin Exclude List
node_modules/
package-lock.json
yarn.lock
src/
scss/
scripts/
.git/
.github/
.vscode/
.idea/
README.md
DEPLOY.md
SETUP-GUIDE.md
LICENSE*
docs/
.env*
.editorconfig
.gitignore
.deployignore
.productionignore
.eslintrc*
.stylelintrc*
.prettierrc*
webpack.config.js
postcss.config.js
build/*.map
dist/
test/
tests/
*.log
*.tmp
.DS_Store
Thumbs.db
*.sh
*.bak
*~
EOF

# Schritt 5: .env.example erstellen
echo "⚙️  Creating .env.example..."
cat << EOF > .env.example
# Deploy Configuration for $PLUGIN_NAME

# Staging Environment
STAGING_HOST=your-staging-server.com
STAGING_USER=staging-user
STAGING_PATH=/var/www/staging/wp-content/plugins/$PLUGIN_NAME

# Production Environment  
PRODUCTION_HOST=your-production-server.com
PRODUCTION_USER=production-user
PRODUCTION_PATH=/var/www/production/wp-content/plugins/$PLUGIN_NAME

# Optional Settings
SSH_KEY_PATH=~/.ssh/id_rsa
BACKUP_ENABLED=true
NOTIFICATION_WEBHOOK_URL=
EOF

echo "✅ Basic setup completed!"
echo ""
echo "🔧 Manual steps remaining:"
echo "1. Copy production-release.js to scripts/"
echo "2. Update plugin name in production-release.js"
echo "3. Add scripts to package.json (see output above)"
echo "4. Ensure main plugin file has Version header"
echo "5. Test with: npm run release"
echo ""
echo "📚 See SETUP-GUIDE.md for detailed instructions"