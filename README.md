# OtherCommerce Composer Plugin

This plugin provides an easy way to configure OtherCommerce dependency
using composer in the projects. It solves an issue of using a local repository
as a dependency when working on the OtherCommerce core features, and using
a proper remote repository in production without having to manually changing
your `composer.json` file every time.

## How does it work

This plugin simply manages composer.json repositories to provide a proper
version of OtherCommerce core package into your project. You can configure
path where your local repo is to load a local dependency, or let the plugin
use a standard remote repository.

## Installation

Simply install this plugin using composer as a dependency

```bash
composer require othercommerce/composer-plugin
```

You will need to configure this as allowed composer plugin in your `composer.json`
file to enable it:

```json
{
    "config": {
        "allow-plugins": {
            "othercommerce/composer-plugin": true
        }
    }
}
```

## Configuration

This plugin accepts some option to manage how, and where it gets things from.
You can provide configuration as a JSON file within your project. The plugin
looks for file with names:

- `oc.json`
- `othercommerce.json`
- `other_commerce.json`

The full JSON configuration example:

```json
{
    "remote": {
        "url": "git@github.com:othercommerce/framework.git",
        "force": false
    },
    "local": {
        "path": "/home/acme/othercommerce/framework"
    }
}
```

### Keeping configuration file in repository

Basically you should not commit your config file into the repository and keep it
in your `.gitignore` file. This file is meant to configure the plugin on your
local machine only, and anyone else would have a different options here.

However, since this plugin is also used in production, you can use this file
to replace the core package repository by example. This should never happen
actually, but there is such option possible.

### Available options

#### `remote.url`

This option provides a URL to a Git repository with OtherCommerce core package.
Usually you won't need to change that setting, unless some experiments, or going
into a full overwrite mode of the core within your project.

#### `remote.force`

When this option is set to `true` the plugin will always resolve dependency
from the remote repository, even if you had configured a local path. This can be
helpful in the future, when you would be working on multiple major versions of
the core within your projects.

#### `local.path`

This option is to provide a per-project path to your local repository with
the core package. You might have multiple copies of core repository on your
computer, for example, separate copies for v1, v2, etc. Then in your project
you can set a proper path for the version a project uses, for example v1,
while keeping a v2 in your other project.

### Using environment variable

The plugin by default automatically looks for `OTHER_COMMERCE_PATH` environment
variable, to resolve your local copy of core repository to use. It is the default
behaviour and can be overwritten by `local.path` option per-project.

This is recommended approach when working with mostly the same major version of
the core, as you won't even need to provide a config file for the plugin.

#### Setting in Unix

Find your bash profile file. Usually it might be `~/.bash_profile` or `~/.zshrc`
depending on what you use for your terminal, and define an environment variable
there like so, replacing the path with your location:

```bash
export OTHER_COMMERCE_PATH = "/home/acme/othercommerce/framework"
```

#### Setting in Windows

1. Right-click on **Computer** icon and choose **Properties**, or in
   Windows Control Panel, choose **System**.
2. Choose **Advanced system settings**.
3. On the Advanced tab, click **Environment Variables**
4. Click **New** to set up a new variable with name `OTHER_COMMERCE_PATH` and
   your core package path as value.

### Note on the paths

Either setting your path in configuration file, or in environment variable,
always put an absolute path to your core package local copy. Thanks to that
you'll avoid any issues with paths resolution.
