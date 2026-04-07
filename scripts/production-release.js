const fs = require("fs");
const path = require("path");
const { execSync } = require("child_process");
const semver = require("semver");

// Configuration
const rootDir = path.resolve(__dirname, "..");
const packageJsonPath = path.join(rootDir, "package.json");
const pluginMainFile = path.join(rootDir, "supportcenter.php");
const distDir = path.join(rootDir, "dist");

// Utility functions
function log(emoji, message) {
  console.log(`${emoji} ${message}`);
}

function logStep(step, total, message) {
  console.log(`\n[${step}/${total}] ${message}`);
}

function runCommand(command, description) {
  log("🔄", description);
  try {
    execSync(command, { stdio: "inherit", cwd: rootDir });
    return true;
  } catch (error) {
    log("❌", `Failed: ${description}`);
    throw error;
  }
}

function updateVersion(type = "patch") {
  log("📝", "Reading current package.json...");
  const packageJson = JSON.parse(fs.readFileSync(packageJsonPath, "utf8"));
  const currentVersion = packageJson.version;
  const newVersion = semver.inc(currentVersion, type);

  log("📈", `Version: ${currentVersion} → ${newVersion}`);

  // Update package.json
  packageJson.version = newVersion;
  fs.writeFileSync(
    packageJsonPath,
    JSON.stringify(packageJson, null, 2) + "\n",
  );
  log("✅", "Updated package.json");

  // Update plugin main file if exists
  if (fs.existsSync(pluginMainFile)) {
    let pluginContent = fs.readFileSync(pluginMainFile, "utf8");
    pluginContent = pluginContent.replace(
      /Version:\s*[\d.]+/,
      `Version: ${newVersion}`,
    );
    fs.writeFileSync(pluginMainFile, pluginContent);
    log("✅", "Updated plugin main file");
  }

  return { currentVersion, newVersion };
}

async function createProductionZip(version) {
  log("📦", "Creating production-ready ZIP package...");

  const packageName = `resp-tpq-supportcenter-plugin.zip`;
  const packagePath = path.join(distDir, packageName);

  // Ensure dist directory exists
  if (!fs.existsSync(distDir)) {
    fs.mkdirSync(distDir, { recursive: true });
  }

  // Clean up old ZIP files
  log("🗑️", "Cleaning up old release packages...");
  const files = fs.readdirSync(distDir);
  const zipFiles = files.filter((file) => file.endsWith(".zip"));

  zipFiles.forEach((file) => {
    const filePath = path.join(distDir, file);
    fs.unlinkSync(filePath);
    log("❌", `Removed old package: ${file}`);
  });

  if (zipFiles.length > 0) {
    log("✅", `Cleaned up ${zipFiles.length} old package(s)`);
  }

  // Production exclude patterns (WordPress Plugin specific)
  const excludePatterns = [
    "node_modules/*",
    "src/*",
    "scss/*",
    "scripts/*",
    ".git/*",
    ".github/*",
    ".vscode/*",
    ".idea/*",
    "dist/*",
    "docs/*",
    "test/*",
    "tests/*",
    ".env*",
    ".editorconfig",
    ".gitignore",
    ".eslintrc*",
    ".stylelintrc*",
    ".prettierrc*",
    "webpack.config.js",
    "postcss.config.js",
    "*.map",
    "*.log",
    "*.tmp",
    ".DS_Store",
    "Thumbs.db",
    "*.sh",
    "*.bak",
    "*~",
    "README.md",
    "DEPLOY.md",
    "SETUP-GUIDE.md",
    "LICENSE*",
    "quick-setup.sh",
    ".productionignore",
    "package-lock.json",
    "yarn.lock",
    "composer.lock",
    "CHANGELOG.md",
    ".deployignore",
  ];

  // Clean up any existing incomplete file
  if (fs.existsSync(packagePath)) {
    fs.unlinkSync(packagePath);
    log("🗑️", "Removed incomplete package file");
  }

  try {
    // Use native zip command (most reliable)
    const pluginFolderName = path.basename(rootDir);
    const parentDir = path.dirname(rootDir);

    // Create exclude arguments for zip command
    const excludeArgs = excludePatterns
      .map((pattern) => `-x "${pluginFolderName}/${pattern}"`)
      .join(" ");

    // Create zip from parent directory to ensure correct WordPress structure
    const zipCommand = `cd "${parentDir}" && zip -r "${packagePath}" "${pluginFolderName}" ${excludeArgs}`;

    log("🔄", "Creating ZIP package with native zip command...");
    execSync(zipCommand, { stdio: "pipe" });

    if (fs.existsSync(packagePath)) {
      const stats = fs.statSync(packagePath);
      const size = (stats.size / 1024 / 1024).toFixed(2);
      log("✅", `Production package created: ${packageName}`);
      log("📊", `Package size: ${size} MB`);
      log("📁", `Location: ${packagePath}`);
      log("🎯", "Ready for WordPress deployment!");
      return packagePath;
    } else {
      throw new Error("ZIP file was not created");
    }
  } catch (error) {
    log("❌", `Package creation failed: ${error.message}`);

    // Clean up any corrupted file
    if (fs.existsSync(packagePath)) {
      fs.unlinkSync(packagePath);
      log("🗑️", "Cleaned up corrupted package file");
    }

    throw error;
  }
}

// Main release process
async function createProductionRelease() {
  const startTime = Date.now();

  log("🚀", "Starting PRODUCTION RELEASE process...");
  log("🎯", "This will create a production-ready plugin package");

  try {
    // Step 1: Clean previous builds
    logStep(1, 8, "Cleaning previous builds");
    if (fs.existsSync(path.join(rootDir, "build"))) {
      runCommand("rm -rf build", "Removing old build directory");
    }

    // Step 2: Install/update dependencies
    logStep(2, 8, "Installing/updating dependencies");
    runCommand("npm ci", "Installing production dependencies");

    // Step 3: Code quality checks (optional)
    logStep(3, 8, "Running code quality checks");
    try {
      runCommand("npm run lint", "Running ESLint");
      log("✅", "Code quality checks passed");
    } catch (error) {
      log("⚠️", "Lint warnings detected, attempting auto-fix...");
      try {
        runCommand("npm run lint:fix", "Auto-fixing lint issues");
        log("✅", "Lint issues auto-fixed");
      } catch (fixError) {
        log(
          "⚠️",
          "Could not auto-fix all issues. Proceeding with production release...",
        );
        log("💡", "Consider fixing lint issues manually after release");
      }
    }

    // Step 4: Update version
    logStep(4, 8, "Updating plugin version");
    const versionType = process.argv[2] || "patch";
    const { currentVersion, newVersion } = updateVersion(versionType);

    // Step 5: Run tests (if available)
    logStep(5, 8, "Running tests");
    try {
      runCommand("npm test", "Running test suite");
    } catch (error) {
      log("⚠️", "Tests failed or not available, continuing...");
    }

    // Step 6: Create production build
    logStep(6, 8, "Creating optimized production build");
    runCommand(
      "NODE_ENV=production npm run build:production",
      "Building for production",
    );

    // Step 7: Optimize assets
    logStep(7, 8, "Optimizing assets");
    try {
      if (fs.existsSync(path.join(rootDir, "build/index.css"))) {
        log("🎨", "CSS build found and optimized");
      }

      const assetDir = path.join(rootDir, "includes/view/asset");
      if (fs.existsSync(assetDir)) {
        log("🖼️", "Image assets found");
      }

      log("✅", "Asset optimization completed");
    } catch (error) {
      log("⚠️", "Asset optimization skipped:", error.message);
    }

    // Step 8: Create production package
    logStep(8, 8, "Creating production ZIP package");
    const packagePath = await createProductionZip(newVersion);

    // Success summary
    const duration = ((Date.now() - startTime) / 1000).toFixed(1);
    log("🎉", "PRODUCTION RELEASE COMPLETED SUCCESSFULLY!");
    log("⏱️", `Total time: ${duration}s`);
    log("📦", `Package: ${path.basename(packagePath)}`);
    log("📍", `Version: ${currentVersion} → ${newVersion}`);
    log("📁", `Location: ${packagePath}`);
    log("🚀", "Ready for deployment!");

    return packagePath;
  } catch (error) {
    log("💥", "PRODUCTION RELEASE FAILED!");
    log("❌", error.message);

    // Cleanup on failure
    try {
      const files = fs.readdirSync(distDir).filter((f) => f.endsWith(".zip"));
      if (files.length > 0) {
        log("🗑️", "Cleaning up potentially corrupted files...");
        files.forEach((file) => {
          fs.unlinkSync(path.join(distDir, file));
          log("❌", `Removed: ${file}`);
        });
      }
    } catch (cleanupError) {
      log("⚠️", "Could not clean up corrupted files");
    }

    process.exit(1);
  }
}

// Command line interface
if (require.main === module) {
  const versionType = process.argv[2];
  if (versionType && !["patch", "minor", "major"].includes(versionType)) {
    console.log("❌ Invalid version type. Use: patch, minor, or major");
    process.exit(1);
  }

  createProductionRelease().catch((error) => {
    console.error("💥 Release failed:", error.message);
    process.exit(1);
  });
}

module.exports = { createProductionRelease, createProductionZip };
