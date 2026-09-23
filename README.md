# Bricks Content Toolkit

Custom WordPress functionality and Bricks Builder elements for article-content workflows.

## Status

This project is in early development. It includes a Docker Compose and VS Code Dev Container setup so the WordPress runtime, MariaDB database, PHP CLI, Node.js, npm, Composer, and WP-CLI run in containers rather than being installed as project dependencies on the host operating system.

## Requirements

Install these tools on the host computer:

- Docker Desktop (or Docker Engine with the Compose plugin)
- Git

Visual Studio Code and the Microsoft **Dev Containers** extension are optional. Use the Dev Container workflow below if you want the VS Code integration, or follow the [Docker-only setup](#docker-only-setup-without-vs-code) from any terminal.

You do not need to install PHP, Composer, Node.js, npm, MariaDB, or WordPress directly on the host for this project.

## Project structure

```text
bricks-content-toolkit/
├── .devcontainer/                 # VS Code Dev Container configuration
├── includes/                      # PHP classes and plugin functionality
├── src/                           # JavaScript, CSS, and other source assets
├── bricks-content-toolkit.php     # Plugin bootstrap file
├── compose.yaml                   # Workspace, WordPress, and MariaDB services
├── package.json                   # JavaScript tooling and npm scripts
├── package-lock.json              # Locked npm dependency versions
├── .env.example                   # Safe local-environment template
└── .gitignore
```

## Local development

### 1. Clone the repository

```bash
git clone <repository-url>
cd bricks-content-toolkit
```

### 2. Create local environment settings

Create a local `.env` file from the example:

```bash
cp .env.example .env
```

The default configuration uses:

```dotenv
PHP_VERSION=8.3
WORDPRESS_PORT=8080
MARIADB_DATABASE=wordpress
MARIADB_USER=wordpress
MARIADB_PASSWORD=wordpress
MARIADB_ROOT_PASSWORD=local-dev-root-password
```

The values are for local development only. Do not use these credentials in staging or production.

### 3. Open in a Dev Container

1. Open the `bricks-content-toolkit` folder in VS Code.
2. Press `Cmd+Shift+P` on macOS or `Ctrl+Shift+P` on Windows/Linux.
3. Run **Dev Containers: Reopen in Container**.
4. Wait for Docker to build the workspace image and start the services.

The Compose stack starts three containers:

| Service | Purpose |
|---|---|
| `workspace` | VS Code development environment with PHP CLI, Node.js, npm, Composer, WP-CLI, Git, and the MariaDB client |
| `wordpress` | Local WordPress site served by Apache/PHP |
| `mariadb` | Local WordPress database |

### 4. Install JavaScript dependencies

From the VS Code terminal after it reconnects to the Dev Container:

```bash
npm ci
```

Use `npm install` only when intentionally changing dependencies in `package.json`. Use `npm ci` for normal reproducible installation from `package-lock.json`.

### 5. Install WordPress

Open the local site in a browser:

```text
http://localhost:8080
```

Complete the WordPress installer using local-only credentials. Then go to:

```text
WordPress Admin → Plugins
```

Activate **Bricks Content Toolkit**.

### Docker-only setup (without VS Code)

You can run the complete development environment from a normal host terminal. This workflow uses Docker Compose directly and does not require Visual Studio Code, the Dev Containers extension, PHP, Node.js, npm, Composer, or MariaDB installed on the host.

From the repository root:

```bash
cp .env.example .env
docker compose up -d --build
```

The `workspace` container stays running so it can be used for CLI commands. The source directory is bind-mounted into both the workspace and WordPress containers, so edits made in any editor are available to WordPress immediately.

Install JavaScript dependencies in the workspace container:

```bash
docker compose exec workspace npm ci
```

Run project commands in the container with the repository as the working directory:

```bash
docker compose exec workspace npm run build
docker compose exec workspace npm run lint:js
docker compose exec workspace npm run lint:css
docker compose exec workspace php -l bricks-content-toolkit.php
```

If you are developing compiled JavaScript or CSS assets, keep the watcher running in a terminal window:

```bash
docker compose exec workspace npm run start
```

Open `http://localhost:8080` to complete the WordPress installer, then activate **Bricks Content Toolkit** under **WordPress Admin → Plugins**. If you changed `WORDPRESS_PORT` in `.env`, use that port instead.

When finished, stop the containers from the host terminal:

```bash
docker compose down
```

`docker compose down` keeps the named WordPress and MariaDB volumes. To remove the local WordPress installation and database as well, use `docker compose down --volumes`.

## Container paths

The plugin source exists in one Git repository on the host and is bind-mounted into both relevant containers:

| Location | Path |
|---|---|
| Host computer | The cloned `bricks-content-toolkit` repository |
| Workspace container | `/workspaces/bricks-content-toolkit` |
| WordPress container | `/var/www/html/wp-content/plugins/bricks-content-toolkit` |

Changes made in the host editor are immediately visible to WordPress after a browser refresh. No FTP, ZIP upload, or manual file copy is required.

## Commands

Run project commands from the VS Code terminal attached to the `workspace` container.

| Command | Purpose |
|---|---|
| `npm ci` | Install the exact JavaScript dependency versions from `package-lock.json` |
| `npm run start` | Watch source assets and rebuild them during development |
| `npm run build` | Create optimized production assets |
| `npm run lint:js` | Lint JavaScript source files |
| `npm run lint:css` | Lint CSS/Sass source files |
| `npm run format` | Format supported project source files |
| `php -l bricks-content-toolkit.php` | Check the main plugin file for PHP syntax errors |

At this stage, `src/` may be empty. Do not run `npm run start` until the project includes JavaScript/CSS assets for `@wordpress/scripts` to build.

## Managing the stack

Run Docker Compose commands from a normal host terminal in the repository root, not from the Dev Container terminal.

```bash
cd ~/Repositories/bricks-content-toolkit
```

Useful commands:

```bash
# Show running services
docker compose ps

# View recent WordPress logs
docker compose logs --tail=100 wordpress

# View recent MariaDB logs
docker compose logs --tail=100 mariadb

# Stop the local environment
docker compose down

# Start the local environment
docker compose up -d
```

To check the PHP version used by the WordPress web runtime:

```bash
docker compose exec wordpress php --version
```

## Changing PHP versions

The PHP version is controlled through `.env`:

```dotenv
PHP_VERSION=8.3
```

To test another supported version, change the value, for example:

```dotenv
PHP_VERSION=8.2
```

Then rebuild the environment:

1. In VS Code, run **Dev Containers: Rebuild Container**.
2. Verify workspace PHP:

   ```bash
   php --version
   ```

3. Verify WordPress runtime PHP from the host terminal:

   ```bash
   docker compose exec wordpress php --version
   ```

The workspace and WordPress containers should use the same PHP major/minor version.

## Git and generated files

Commit source and reproducibility files, including:

```text
bricks-content-toolkit.php
includes/
src/
.devcontainer/
compose.yaml
package.json
package-lock.json
.env.example
.gitignore
README.md
```

Do not commit local or generated state, including:

```text
.env
node_modules/
vendor/
*.log
```

The repository currently ignores `build/`, which is generated by `wp-scripts`. Before packaging or deployment, build assets with:

```bash
npm run build
```

## Troubleshooting

### `npm install` says `package.json` is missing

Run npm commands only from the repository root, where `package.json` exists:

```bash
pwd
ls -la package.json
```

Expected workspace path:

```text
/workspaces/bricks-content-toolkit
```

### `docker: command not found` in the Dev Container

This is expected. Docker runs on the host machine; the development container does not need Docker access for ordinary plugin development. Run `docker compose` commands from a host terminal or use Docker Desktop.

### WordPress does not list the plugin

Confirm that the plugin bootstrap file exists in the WordPress container:

```bash
docker compose exec wordpress ls -la \
  /var/www/html/wp-content/plugins/bricks-content-toolkit
```

The directory must contain `bricks-content-toolkit.php` with a valid WordPress plugin header.

### Changes do not appear in the browser

- Refresh the browser for PHP changes.
- Keep `npm run start` running when developing compiled JavaScript or CSS assets.
- Run `npm run build` when preparing production assets.
- Check logs from the host terminal:

  ```bash
  docker compose logs --tail=100 wordpress
  ```

## License

License information will be added before the first public release.
