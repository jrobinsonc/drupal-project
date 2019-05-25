# Composer template for Drupal projects

[![Build Status](https://travis-ci.org/jrobinsonc/drupal-project.svg?branch=8.x)](https://travis-ci.org/drupal-composer/drupal-project)

This repo is based on [drupal-composer/drupal-project](https://github.com/drupal-composer/drupal-project), but modified with some goodness to make it more usable. You can start by reading the [original documentation](https://github.com/drupal-composer/drupal-project/blob/8.x/README.md).

## Requires

* PHP v7.2+
* Composer

## Usage

1. Clone the repo.
2. Run `composer install`.

But remember that for production you should run:

```shell
composer install -o --no-dev
```

### Update the core and modules of Drupal

**Update the core**

⚠️ Remember to update `webflo/drupal-core-require-dev` every time you update `drupal/core` with exactly the same version.

**Updating or adding modules**

I like to specify the exact version constraint of the module to avoid having different versions between environments; doing something like `composer require drupal/devel:2.1.15`. You may be thinking that is the job of the `composer.lock` but remember that if you install a new module doing `composer require`, that will update all the packages to the latest version depending on the constraint they have, making your local environment different from the others.

### Development

Remember that any custom module should be placed inside `web/modules/custom` and any custom theme should be placed ad `web/themes/custom`.

## Feedback

If you find any bug or have an improvement that you'll like to share, please [let me know](https://github.com/jrobinsonc/drupal-project/issues/new).
