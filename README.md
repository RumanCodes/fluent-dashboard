# Fluent Dashboard

Follow the steps below to set up and start using this WordPress plugin boilerplate for: `fluent-dashboard`

## Requirements

- **Node.js**: Version `18`
- **Composer**: Installed globally
- **NPM**: Installed globally

## Installation Steps

1. **Install PHP dependencies**  
   Run:
   ```terminal
   composer install
   ```

2. **Dump autoload files**  
   Run:
   ```terminal
   composer dump-autoload
   ```

3. **Install Node modules**  
   Run:
   ```terminal
   npm install
   ```

4. **Update Plugin Name and Namespace**  
   Replace the default plugin name and PHP namespace with your custom one:
    - In all PHP files (classes, autoloaders, etc.)
    - In the plugin header commentl
    - before activating plugins, run `composer dump-autoload` && `composer install`

5. **Configure Vite Development URL**
    - Open `vite.config.js` and update the `root` and `devServer` URL as needed.
    - Update the dev server URL in `app/Hooks/Handlers/AdminMenuHandlers.php` to match your local Vite server (e.g., `http://localhost:5173`).
    - This will run the vue without reloading the page.

# `fluent-dashboard`
# fluent-dashboard
