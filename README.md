# Bricks Content Toolkit

Custom WordPress functionality and Bricks Builder elements for article-content workflows.

## Development approach

This repository contains the plugin source code and project tooling only. It does **not** include WordPress core, a database, the licensed Bricks theme, uploads, or a Docker Compose WordPress stack.

Recommended local development uses:

- **Laravel Herd** to serve WordPress locally with PHP 8.3 and optional local HTTPS
- **DBngin** to run a local MariaDB database
- **NVM** to manage Node.js versions
- **Node.js 24 LTS** and npm for JavaScript/CSS build tooling
- **Composer** and **WP-CLI** when PHP dependencies, quality tools, or WordPress automation are needed
- **GitHub Actions** for repeatable CI checks

The plugin repository is symlinked into a separate local WordPress installation. This gives immediate access to plugin changes without copying files or uploading ZIP archives.

## Requirements

Install the following on the development machine:

- Local WordPress installation ie. [Laravel Herd](https://herd.laravel.com/) or similar
- MySQL/MariDB Database. ie. built with [DBngin](https://dbngin.com/) or similar
- [Git](https://git-scm.com/)
- [NVM](https://github.com/nvm-sh/nvm)
- Node.js 24 LTS, installed through NVM
- PHP 8.3+
- Composer
- WP-CLI
- VS Code, PhpStorm, or another editor

### Required versions

| Tool | Project standard |
|---|---|
| PHP runtime | PHP 8.3 |
| Node.js | Node 24 LTS |
| npm | The npm version bundled with the selected Node 24 release |
| Database | MariaDB, managed locally through DBngin |
| WordPress | Use a currently supported version; document the exact tested version in release notes when needed |
| Bricks | Install and activate a licensed, compatible Bricks version in the local WordPress site |

The project pins its Node major version in `.nvmrc`.

## Repository layout

```text
bricks-content-toolkit/
├── .github/
│   └── workflows/                 # GitHub Actions CI workflows
├── includes/                      # PHP classes and plugin functionality
├── src/                           # JavaScript/CSS source assets, when needed
├── build/                         # Generated browser assets; ignored by Git initially
├── bricks-content-toolkit.php     # Plugin bootstrap file
├── package.json                   # npm scripts and JavaScript dependencies
├── package-lock.json              # Exact locked npm dependency tree
├── .nvmrc                         # Required Node.js major version
├── .editorconfig                  # Shared editor whitespace rules, when added
├── composer.json                  # PHP dependencies/tooling, when added
├── phpcs.xml.dist                 # WordPress coding standards configuration, when added
├── phpstan.neon                   # Static-analysis configuration, when added
├── tests/                         # Automated tests, when added
├── README.md
└── .gitignore
```

## Local WordPress layout

Keep the local WordPress site separate from this Git repository.

```text
/<sites-dir>/
└── bricks-local/                         # Local WordPress site; not this repository
    ├── wp-admin/
    ├── wp-content/
    │   ├── plugins/
    │   │   └── bricks-content-toolkit → symlink to this repository
    │   └── themes/
    │       └── bricks/                   # Licensed local theme installation
    |       └── bricks-child/             # recommended activated local theme
    ├── wp-includes/
    └── wp-config.php

/<path-to-repositories>/
└── bricks-content-toolkit/               # This Git repository
```

The symlink lets WordPress load the plugin directly from this repository. Saving a PHP, CSS, JavaScript, or built asset file changes what WordPress sees immediately.

## First-time setup

### 1. Install WordPress and Bricks

Install WordPress wherever you place your WordPress installations locally and connect it to a MySQL/MariaDB database, and complete the local WordPress installation. Your WordPress installation should be using a minimum of PHP 8.3.

Install and activate a licensed copy of the Bricks theme (Brick's child theme is recommended.) in that local WordPress site. Do not commit Bricks files or license material to this plugin repository.

### 2. Clone the plugin repository

`cd` into the directory where you store repositories locally.

```bash
git clone <repository-url>
cd bricks-content-toolkit
```

### 3. Set the Node.js version

Install and select the project’s Node version with NVM:

```bash
nvm install
nvm use
```

Verify the selected runtime:

```bash
node --version
npm --version
```

The Node major version should match `.nvmrc`.

### 4. Install JavaScript dependencies

Use the lockfile for reproducible dependency installation:

```bash
npm ci
```

Use `npm install` only when intentionally adding, removing, or changing dependencies in `package.json`. When `package.json` changes, commit the corresponding updated `package-lock.json` in the same change.

Do not commit `node_modules/`.

### 5. Symlink the plugin into WordPress

From the WordPress plugins directory, create a symlink to the cloned repository:

```bash
ln -s \
  /<path-to-bricks-content-toolkit> \
  /<path-to-wordpress>/wp-content/plugins/bricks-content-toolkit
```

Verify the symlink:

```bash
ls -la <path-to-wordpress>/wp-content/plugins
```

Expected shape:

```text
bricks-content-toolkit -> /<path-to-repository-directory>/bricks-content-toolkit
```

Open WordPress Admin, go to **Plugins**, and activate **Bricks Content Toolkit**.

## Daily development

### PHP-only development

For WordPress hooks, Bricks elements, settings pages, PHP classes, and directly-enqueued CSS or JavaScript:

```text
Edit → save → refresh the browser
```

No npm build command is required for PHP changes.

### Compiled JavaScript or CSS

When the project contains source assets that need compilation—such as module-based JavaScript, JSX/React, TypeScript, Sass/SCSS, or Gutenberg block code—run the watcher once per development session:

```bash
cd /<path-to-repository-directory>//bricks-content-toolkit
nvm use
npm run start
```

Leave the watcher running. It watches `src/` and writes generated browser assets to `build/` whenever you save a source file.

```text
Edit src/ file
→ save
→ npm run start recompiles build/ automatically
→ refresh the WordPress page or editor
→ see the updated asset
```

Use the production build only before packaging, release, or deployment:

```bash
npm run build
```

At the beginning of the project, `src/` may be empty. In that case, `npm run build` may report that no entry file was found; that is expected until compiled assets are added.

## npm commands

Run commands from the repository root:

| Command | Purpose |
|---|---|
| `nvm use` | Select the Node version declared in `.nvmrc` |
| `npm ci` | Install the exact versions in `package-lock.json` |
| `npm run start` | Watch and rebuild source assets during development |
| `npm run build` | Create optimized production assets |
| `npm run lint:js` | Lint JavaScript source assets |
| `npm run lint:css` | Lint CSS/Sass source assets |
| `npm run format` | Format supported project files using WordPress tooling |
| `npm audit` | Inspect dependency-security advisories without changing dependencies |

Do not run `npm audit fix` or `npm audit fix --force` blindly. Review dependency upgrades deliberately, test the project, and commit dependency changes separately.

## PHP checks

Check the main plugin bootstrap file:

```bash
php -l bricks-content-toolkit.php
```

Check every PHP file under `includes/`:

```bash
find includes -type f -name '*.php' -print0 | xargs -0 -r -n1 php -l
```

Use PHP 8.3 for local runtime behavior. Later, add Composer-based tools such as PHP_CodeSniffer with WordPress Coding Standards, PHPStan, and PHPUnit as the plugin grows.

## Local HTTPS

HTTP is adequate for ordinary local plugin work: PHP hooks, Bricks elements, admin pages, CSS, and most JavaScript behavior.

Enable HTTPS through Herd when testing features that require a secure context or production-like behavior, including:

- OAuth or social-login redirect callbacks
- Secure cookies/authentication behavior
- Payment integrations
- WebAuthn/passkeys
- Service workers/PWA behavior
- Browser APIs such as camera, microphone, or geolocation
- Mixed-content issues and HTTP-to-HTTPS redirects

## Git workflow

Commit source code and reproducibility/configuration files:

```text
bricks-content-toolkit.php
includes/
src/
.github/
.nvmrc
package.json
package-lock.json
composer.json
composer.lock
phpcs.xml.dist
phpstan.neon
phpunit.xml.dist
README.md
.gitignore
```

Do not commit machine-specific, generated, licensed, or secret material:

```text
node_modules/
vendor/
.env
wp-config.php
WordPress core
Bricks theme files or license keys
Database dumps
uploads/
Editor-local state
```

## Continuous integration

GitHub Actions is the clean-room verification environment for this plugin. CI should use the committed lockfiles and run checks on every pull request and push.

Initial CI checks should include:

```text
PHP syntax checks
Node dependency installation with npm ci
JavaScript linting
CSS linting
Production asset build
```

As the plugin grows, add:

```text
PHP_CodeSniffer with WordPress Coding Standards
PHPStan static analysis
PHPUnit unit tests
WordPress integration tests
A PHP compatibility matrix, such as PHP 8.2, 8.3, and 8.4
Browser/E2E tests when the plugin has user-facing workflows
```

CI should run `npm ci`, never `npm install`, because CI must install the exact dependency tree captured in `package-lock.json`.

## Troubleshooting

### Plugin does not appear in WordPress

Verify the symlink exists and points at the plugin repository:

```bash
ls -la /<path-to-wordpress>/wp-content/plugins
```

Verify the target contains the plugin bootstrap file:

```bash
ls -la /<path-to-repository-directory>/bricks-content-toolkit/bricks-content-toolkit.php
```

The bootstrap file must contain a valid WordPress plugin header.

### Changes do not appear

- For PHP: save the file and refresh the page.
- For directly-enqueued CSS/JS: perform a hard browser refresh if caching is involved.
- For files in `src/`: ensure `npm run start` is running and reports a successful compilation.
- Verify WordPress is loading the expected plugin copy through the symlink.

### `npm ci` fails

`npm ci` requires `package.json` and `package-lock.json` to match. If you intentionally changed dependencies in `package.json`, update the lockfile:

```bash
rm -rf node_modules package-lock.json
npm install
```

Then review and commit the new `package-lock.json` together with the `package.json` change.

### `npm run build` says no entry file was discovered

This is expected while `src/` is empty. Add a valid entry point such as `src/index.js`, or add block metadata/source files when implementing a Gutenberg block. Until then, PHP-only plugin features do not require a build step.

## License

License information will be added before the first public release.
