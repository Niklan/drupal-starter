# Drupal Starter

Drupal Starter is a composer template designed for creating new Drupal projects.
It is very similar to the `drupal/recommended-project` template, but with a few
modifications that alter the project structure.

## Installation

```
composer -n create-project niklan/drupal-starter my-new-project
```

## About template

The main idea of this project is to move all custom and project-related code,
files, assets, and other files outside the public directory `web/`. This will
make the project more secure because nothing will be accessible on the public
web unless you explicitly make it so. The new structure will also make it easier
to navigate through the project and configure the IDE.

### local/

This folder is used to store environment-specific settings. By default, it will
create a file called 'settings.php' for local settings, which are related to the
project's current environment. If no 'settings.php' file already exists, it will
be created only once and will never be overwritten.

This folder is included in the gitignore list because it contains information
that should only be available in the specific environment where the project is
running.

### app/

This folder is used to store Drupal and PHP code. You can give it any name you
like, for example:

* `./app/modules/my_module`
* `./app/themes/my_theme`
* `./app/profiles/my_profile`

For example, you can also create a **Drupal** folder and name it according to
the namespace. For instance, code with the namespace `Drupal/foo/Bar` will be
located at:

* `./app/Drupal/foo/src/Bar/Baz.php`

The logic behind this naming scheme is that it helps you easily identify the
source code for a specific part of your project.

### assets/

The `assets` folder is where you can store all the files that need to be in the
project repository but are not part of any custom code or third-party libraries.

This is a great place to keep local patches, files used by the 'drupal:scaffold'
composer plugin, as well as any third-party libraries you want to use in your
project.

### config/

The `config` directory is where you keep all the settings for your project. This
includes configuration files for Drupal, PHPCS, PHPStan, PHPUnit, CSPell,
ESlint, Stylelint, and other tools.

### var/

The `var` directory is a storage space for any content you need. Since it is
excluded from git, it is used by default for the `public://`, `private://`, and
`temporary://` stream wrappers.

It is important to note that all these folders, when properly used, will not be
accessible to the public because they are located outside the `web/` directory.
The `public://` option is added through a symbolic link, which means you can
safely delete the entire `web/` directory, run `composer install` again, and
everything will continue to work without any data loss.

This approach also makes it easier and more efficient to make backups by
excluding the entire `web/` from them. All its contents can be downloaded via
composer or stored in the `assets` or `var` folders.

## web/

The `web/` directory is the public directory where Drupal and other public code
is located. The idea behind this template is that if you want to make something
public by placing it in the `web/` directory, you should do it explicitly.

Custom modules, themes, and profiles are installed using Composer by creating a
symbolic link. Custom libraries and other static assets are installed using the
`drupal:scaffold` composer plugin. Shared directories such as `public://` are
also linked to the `var/` directory.

## FAQ

### How to install my modules/themes/profiles?

All Drupal extensions, including custom ones, must have a valid `composer.json`
file. If you have it, everything is easy.

For instance, you have the `example` module located in the
`./app/Drupal/example` path. This module contains the following `composer.json`
file:

```json
{
    "name": "myproject/example",
    "type": "drupal-custom-module",
    "version": "1.0.0-dev"
}
```

Everything you need to do is to require it as a proper dependency:

```
composer require myproject/example:^1.0@dev
```

It will be installed into the following directory:
`./web/modules/custom/example`, using a symbolic link.

The same process applies to other types of extensions, just use the appropriate
type:

* `drupal-custom-module` for modules
* `drupal-custom-theme` for themes
* `drupal-custom-profile` for profiles.

### How to install a third-party library?

For example, you want to use the [quicklink][3] module. It can attach a library
via a CDN, but you also have the option to provide a local copy. Drupal will
serve and aggregate this local copy.

Most Drupal modules expect third-party libraries to be located under
`./web/libraries`. This is great for us because it makes it easy to use the
`drupal:scaffold` composer plugin and composer in general.

There are two ways to solve this problem. Choose whichever one you prefer.

#### Using `drupal:scaffold`

1. Download and save library (`quicklink.umd.js`) at
   `./assets/vendor/quicklink/quicklink.umd.js`.
2. Update `composer.json`: `extra.drupal-scaffold.file-mapping`:
    ```json
    {
      "extra": {
        "drupal-scaffold": {
          "file-mapping": {
            "[libraries-root]/quicklink/dist/quicklink.umd.js": "assets/vendor/quicklink/quicklink.umd.js"
          }
        }
      }
    }
    ```
3. Run the command `composer drupal:scaffold`. That's all! The code will also be
   copied during the composer install process, so you don't need to worry
   anymore — just update the vendor file.

The only drawback to this approach is that the `drupal:scaffold` plugin doesn't
allow you to copy directories. This can make it a bit frustrating to use when a
library uses multiple files, for example:

```json
"[libraries-root]/photoswipe/dist/default-skin/default-skin.css": "assets/vendor/photoswipe/dist/default-skin/default-skin.css",
"[libraries-root]/photoswipe/dist/default-skin/default-skin.png": "assets/vendor/photoswipe/dist/default-skin/default-skin.png",
"[libraries-root]/photoswipe/dist/default-skin/default-skin.svg": "assets/vendor/photoswipe/dist/default-skin/default-skin.svg",
"[libraries-root]/photoswipe/dist/default-skin/preloader.gif": "assets/vendor/photoswipe/dist/default-skin/preloader.gif",
"[libraries-root]/photoswipe/dist/photoswipe-ui-default.min.js": "assets/vendor/photoswipe/dist/photoswipe-ui-default.min.js",
"[libraries-root]/photoswipe/dist/photoswipe.css": "assets/vendor/photoswipe/dist/photoswipe.css",
"[libraries-root]/photoswipe/dist/photoswipe.min.js": "assets/vendor/photoswipe/dist/photoswipe.min.js",
"[libraries-root]/photoswipe/photoswipe.json": "assets/vendor/photoswipe/photoswipe.json",
```

#### Using composer

This approach is similar to the one used for modules, themes, and profiles. The
only difference is that you need to make some small changes to your
`composer.json` file and define the library using that file.

First, you need to allow composer to search for packages in the `./asset/vendor`
directory. To do this, add a new repository to your root `composer.json` file:

```json
    "repositories": [
        {
            "type": "path",
            "url": "app/**/*"
        },
        {
            "type": "path",
            "url": "assets/vendor/*"
        },
        {
            "type": "composer",
            "url": "https://packages.drupal.org/8"
        }
    ],
```

Then, for instance, just add a `composer.json` file to the library folder:
`./assets/vendor/photoswipe/composer.json`:

```json
{
    "name": "myproject/photoswipe-asset",
    "type": "drupal-library",
    "version": "1.0.0-dev"
}
```

Then just require it as any other package:

```
composer require myproject/photoswipe-asset:^1.0@dev
````

That's it! It will copy all the files to the `./web/libraries/photoswipe`
folder.

### How to run tool X?

Since the configurations have been moved to the `./config` directory, it can be
confusing to run these tools now:

* PHPCS: `phpcs --standard=config/phpcs.xml`
* PHPCBF: `phpcbf --standard=config/phpcs.xml`
* PHPStan: `phpstan --configuration=config/phpstan.neon`
* PHPUnit: `phpunit --configuration=config/phpunit.xml`
* ESLint: `eslint -c config/.eslintrc.json`
* Stylelint: `stylelint -c config/.stylelintrc.json`
* CSPell: `cspell --config config/.cspell.json`

A bit unusual, isn't it? But there are ways to make it easier. You can use
composer, yarn, or npm scripts. Or you can try a great tool called
[Taskfile][4]. Check out the dedicated section for a drop-in solution.

### Configuring PHPStorm

1. Go to **PHP | Composer** and disable the option to "Add packaged as
   libraries".
2. Go to **PHP | Include path** and remove everything that is currently added.
3. Click on "Add include path" (the plus icon), then select only the
   `./vendor` and `./web` directories.
   ![](https://i.imgur.com/94RWgH3.png)
4. Identify your custom modules, themes, and profiles, under `.web/` directory,
   select their folders, and click "Exclude". You should exclude the following
   paths (if exists):
* `./web/modules/custom`
* `./web/themes/custom`
* `./web/profiles/custom`
5. Save the settings and close the panel.
6. In the project structure, exclude the following directories from the index:
* `./local`
* `./var`
* `./vendor`
* `./web`
  ![](https://i.imgur.com/CPRFZPS.png)

This approach has several advantages:

* Your custom code will be intensively indexed in the `./app/*` directory. This
  not only reduces the load on your system and PHPStorm's resource consumption
  but also speeds up autocompletion.
* The `./vendor` and `./web` directories will still be indexed, but less
  frequently. Changes will be found instantly, though.
* All suggestions, searches, and other features will continue to work as usual.
* Code from Drupal and vendors will be highlighted with a different background,
  which can be helpful in some cases to distinguish your code from others.

By default, the search will only look in the project files. To search
everywhere, you need to double the last keybind: <kbd>Ctrl</kbd> +
<kbd>Shift</kbd> + <kbd>F</kbd> + <kbd>F</kbd> or
<kbd>Ctrl</kbd> + <kbd>N</kbd> + <kbd>N</kbd>. Yellow rows indicate third-party
files.

### What else?

To avoid making the template too complex, some aspects have been intentionally
simplified.

#### Monolog

If you're concerned about log files, there's a great opportunity to start using
the [Monolog module][4]. It allows you to store all logs in the `./var/log`
directory.

1. Require module `composer require drupal/monolog`.
2. Create `./assets/scaffold/monolog.services.yml`.
   ```yaml
    parameters:
      monolog.channel_handlers:
        # Drupal's core channels.
        default: ['rotating_file.default']
        php: ['rotating_file.php']
        image: ['rotating_file.image']
        cron: ['rotating_file.cron']
        file: ['rotating_file.file']
        security: ['rotating_file.security']
        mail: ['rotating_file.mail']
        system: ['rotating_file.system']
   ```
3. Add it into scaffold:
    ```json
    {
        "extra": {
            "drupal-scaffold": {
                "file-mapping": {
                    "[web-root]/sites/monolog.services.yml": "assets/scaffold/monolog.services.yml"
                }
            }
        }
    }
    ```
4. Update global settings: `./assets/scaffold/settings.php`:
   ```php
   $settings['container_yamls'][] = DRUPAL_ROOT . '/sites/monolog.services.yml';
   ```
5. `composer drupal:scaffold`
6. `drush en monolog`

That's it! Now you'll find your logs in the `./var/log` directory.

#### Taskfile

[Taskfile][5] is a fantastic tool that can be used even on shared hosting
platforms! It simplifies many tasks, which is why it's worth mentioning. You can
use Taskfile to easily call PHPUnit, PHPCS, or any other tool you need.

Here's a sample Taskfile.yml file you can use as a starting point:

<details>
<summary>./Taskfile.yml</summary>

```yaml
version: '3'

env:
  PHP_BIN: '{{.PHP_BIN | default "$(which php)"}}'
  COMPOSER_BIN: '{{.COMPOSER_BIN | default "$(which composer)"}}'
  NODE_BIN: '{{.NODE_BIN | default "$(which node)"}}'
  YARN_BIN: '{{.YARN_BIN | default "$(which yarn)"}}'

vars:
  CONFIG_DIR: '{{.TASKFILE_DIR}}/config'
  COMPOSER_BIN_DIR: '{{.TASKFILE_DIR}}/vendor/bin'
  NODEJS_BIN_DIR: '{{.TASKFILE_DIR}}/node_modules/.bin'

tasks:
  default:
    cmd: 'task --list-all'

  composer:
    label: Composer
    desc: Runs 'composer' command.
    cmd: '{{.PHP_BIN}} {{.COMPOSER_BIN}} {{.CLI_ARGS}}'

  drush:
    label: Drush
    desc: Runs 'drush' command.
    requires:
      vars:
        - COMPOSER_BIN_DIR
    cmd: '{{.PHP_BIN}} {{.COMPOSER_BIN_DIR}}/drush {{.CLI_ARGS}}'

  phpstorm-meta:
    label: PHPStorm metadata
    desc: Generates PHPStorm metadata.
    cmds:
      - task: drush
        vars: { CLI_ARGS: 'generate -y phpstorm-meta' }

  phpcs:
    label: PHPCS
    desc: Runs 'phpcs' command.
    cmd: '{{.PHP_BIN}} {{.COMPOSER_BIN_DIR}}/phpcs -ps --colors --standard={{.CONFIG_DIR}}/phpcs.xml {{.CLI_ARGS}}'

  phpcbf:
    label: PHPCBF
    desc: Runs 'phpcbf' command.
    # @see https://github.com/squizlabs/PHP_CodeSniffer/issues/1818
    cmd: '{{.PHP_BIN}} {{.COMPOSER_BIN_DIR}}/phpcbf -ps --colors --standard={{.CONFIG_DIR}}/phpcs.xml {{.CLI_ARGS}} || if [ $? -eq 1 ]; then exit 0; fi'

  phpstan:
    label: PHPStan
    desc: Runs 'phpstan' command.
    cmd: '{{.PHP_BIN}} {{.COMPOSER_BIN_DIR}}/phpstan --configuration={{.CONFIG_DIR}}/phpstan.neon {{.CLI_ARGS}}'

  parallel-lint:
    label: PHP Parallel lint
    desc: Runs 'parallel-lint' command.
    cmd: '{{.PHP_BIN}} {{.COMPOSER_BIN_DIR}}/parallel-lint {{.CLI_ARGS}}'

  phpunit:
    label: PHPUnit
    desc: Runs 'phpunit' command.
    vars:
      SUITE: '{{if .SUITE}}--testsuite={{.SUITE}}{{end}}'
    cmd: '{{.PHP_BIN}} {{.COMPOSER_BIN_DIR}}/phpunit --configuration={{.CONFIG_DIR}}/phpunit.xml {{.SUITE}} {{.CLI_ARGS}}'

  yarn:
    label: yarn
    desc: Runs 'yarn' command.
    cmd: '{{.YARN_BIN}} {{.CLI_ARGS}}'

  eslint:
    label: ESLint
    desc: Runs 'eslint' command.
    cmd: '{{.NODE_BIN}} {{.NODEJS_BIN_DIR}}/eslint {{.CLI_ARGS}}'

  stylelint:
    label: Stylelint
    desc: Runs 'stylelint' command.
    cmd: '{{.NODE_BIN}} {{.NODEJS_BIN_DIR}}/stylelint {{.CLI_ARGS}}'

  cspell:
    label: CSPell
    desc: Runs 'cspell' command.
    cmd: '{{.NODE_BIN}} {{.NODEJS_BIN_DIR}}/cspell {{.CLI_ARGS}}'

  install:
    desc: Install website.
    summary: Installs a website with a demo content for development and testing.
    prompt: |
      This command will delete current database and install a fresh website.
      All unsaved data will be permanently lost.
      Are you sure?
    cmds:
      - task: composer
        vars: { CLI_ARGS: 'install' }
      - task: drush
        vars: { CLI_ARGS: 'site:install -y --existing-config' }
      - task: drush
        vars: { CLI_ARGS: 'deploy:mark-complete -y' }
      - task: phpstorm-meta
      - task: drush
        vars: { CLI_ARGS: 'user:login --uid=1' }

  validate:
    label: Project validation
    desc: Validates project files.
    cmds:
      - task: validate/composer
      - task: validate/phplint
      - task: validate/phpcs
      - task: validate/phpstan
      - task: validate/js
      - task: validate/css
      - task: validate/yml
      - task: validate/spellcheck

  validate/composer:
    label: Composer validation
    desc: Validates composer.json file and checks platform requirements.
    cmds:
      - task: composer
        vars: { CLI_ARGS: 'validate --strict' }
      - task: composer
        vars: { CLI_ARGS: 'check-platform-req' }

  validate/phplint:
    label: PHP linter
    desc: Lints PHP files.
    aliases:
      - 'phplint'
    cmds:
      - task: parallel-lint
        vars: { CLI_ARGS: '-e php,module,install,inc,theme app' }

  validate/phpcs:
    label: PHPCS validation
    desc: Validate PHP for code style.
    cmds:
      - task: phpcs

  validate/phpstan:
    label: PHPStan analyze
    desc: Analyze PHP code for bugs and errors.
    cmds:
      - task: phpstan
        vars: { CLI_ARGS: 'analyze' }

  validate/js:
    label: JavaScript linter
    desc: Lints JavaScript files.
    aliases:
      - 'jslint'
    cmds:
      - task: eslint
        vars: { CLI_ARGS: '-c {{.CONFIG_DIR}}/.eslintrc.json --ext .js . {{.CLI_ARGS}}' }

  validate/css:
    label: CSS linter
    desc: Lints CSS files.
    aliases:
      - 'csslint'
    cmds:
      - task: stylelint
        vars: { CLI_ARGS: '-c {{.CONFIG_DIR}}/.stylelintrc.json **/*.css {{.CLI_ARGS}}' }

  validate/yml:
    label: YML linter
    desc: Lints Y(A)ML files.
    aliases:
      - 'ymllint'
      - 'yamllint'
    cmds:
      - task: eslint
        vars: { CLI_ARGS: '-c {{.CONFIG_DIR}}/.eslintrc.json --ext .yml --ext .yaml . {{.CLI_ARGS}}' }

  validate/spellcheck:
    label: Spellcheck
    desc: Checks for common spelling issues.
    aliases:
      - 'spellcheck'
    cmds:
      - task: cspell
        vars: { CLI_ARGS: '--config {{.CONFIG_DIR}}/.cspell.json --quiet --no-progress "**" {{.CLI_ARGS}}' }

  fix:
    label: Fixing found issues
    desc: Trying for automated fixes for found problems.
    cmds:
      - task: fix/phpcs
      - task: fix/js
      - task: fix/css
      - task: fix/yml

  fix/phpcs:
    label: PHPCS
    desc: Fix PHPCS issues.
    cmds:
      - task: phpcbf

  fix/js:
    label: JavaScript
    desc: Fix JavaScript issues.
    cmds:
      - task: validate/js
        vars: { CLI_ARGS: '--fix' }

  fix/css:
    label: CSS
    desc: Fix CSS issues.
    cmds:
      - task: validate/css
        vars: { CLI_ARGS: '--fix' }

  fix/yml:
    label: Y(A)ML
    desc: Fix Y(A)ML issues.
    aliases:
      - 'fix/yaml'
    cmds:
      - task: validate/yml
        vars: { CLI_ARGS: '--fix' }

  test:
    label: Tests
    desc: Runs all available project tests.
    cmds:
      - task: test/unit
      - task: test/kernel
      - task: test/browser

  test/unit:
    label: Unit tests
    desc: Runs Unit test
    cmds:
      - task: phpunit
        vars: { SUITE: 'unit' }

  test/kernel:
    label: Kernel tests
    desc: Runs Kernel tests.
    cmds:
      - task: phpunit
        vars: { SUITE: 'kernel' }

  test/browser:
    label: Browser tests
    desc: Runs Browser tests.
    cmds:
      - task: phpunit
        vars: { SUITE: 'functional' }

  update:
    label: Update project
    desc: Updates project dependencies withing constraints.
    prompt: |
      This command can break website. Do it independently without any other
      active changes. Make sure you have a backup.
      Never run it on production.
    cmds:
      - task: composer
        vars: { CLI_ARGS: 'update -W' }
      - task: drush
        vars: { CLI_ARGS: 'updatedb -y' }
      - task: drush
        vars: { CLI_ARGS: 'config:export -y' }
      - task: yarn
        vars: { CLI_ARGS: 'upgrade' }

  build-dictionary:
    label: Builds dictionary
    desc: Builds a project dictionary for CSPell.
    cmds:
      - task: cspell
        vars: { CLI_ARGS: '--config {{.CONFIG_DIR}}/.cspell.json --words-only --unique "**" | sort -f > {{.CONFIG_DIR}}/cspell/dictionary.txt' }
```

</details>

Simply drop it into the root folder and remove any unnecessary files. That's it!

### Is there an open-source project that uses this approach?

Want to see a real-world example of this approach in action? No problem! You can
check out the [source code of my blog][6]. You can run it locally and play with
the structure to see how a real project uses it. This is a better way to
understand and apply the concept than just relying on theories.

## Similar projects

- [drupal/recommended-project][1]
- [xandeadx/drupal-starter][2]

[1]: https://github.com/drupal/recommended-project
[2]: https://github.com/xandeadx/drupal-starter
[3]: https://www.drupal.org/project/quicklink
[4]: https://www.drupal.org/project/monolog
[5]: https://taskfile.dev/
[6]: https://github.com/Niklan/niklan.net