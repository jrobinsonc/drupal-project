<?php

namespace DrupalProject\composer;

use Composer\Script\Event;
use DrupalFinder\DrupalFinder;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Site installer.
 *
 * @package DrupalProject\composer
 * @author Jose Robinson <hi@joserobinson.com>
 * @copyright 2019 Jose Robinson
 * @license GPL-2.0
 * @since 1.0.0
 */
class SiteInstaller {
  /**
   * Drupal root directory path.
   *
   * @var string
   */
  private static $drupalRoot;

  /**
   * Composer event.
   *
   * @var Composer\Script\Event
   */
  private static $event;

  /**
   * Required directories.
   */
  const REQUIRED_DIRS = [
    'web/themes/custom',
    'web/modules/custom',
    'sites/default/files',
  ];

  /**
   * Permissions for required directories.
   */
  const REQUIRED_DIRS_PERMS = 0775;

  /**
   * @return Symfony\Component\Filesystem\Filesystem
   */
  private static function getFs() {
    static $filesystem = NULL;

    if (NULL === $filesystem) {
      $filesystem = new Filesystem();
    }

    return $filesystem;
  }

  /**
   * Check required directories and creates them if they don't exist.
   *
   * @return null
   */
  private static function checkRequiredDirs() {
    self::$event->getIO()->write('  - Checking required directories...', FALSE);

    $filesystem = self::getFs();

    foreach (self::REQUIRED_DIRS as $requiredDir) {
      $requiredDirPath = self::$drupalRoot . '/' . $requiredDir;

      // Creates the directory if it doesn't exist.
      if (!$filesystem->exists($requiredDirPath)) {
        $filesystem->mkdir($requiredDirPath, self::REQUIRED_DIRS_PERMS);
      }
    }

    self::$event->getIO()->overwrite('  ✓ Checking required directories.  ');
  }

  /**
   * Entrypoint for installer.
   *
   * @return null
   */
  public static function init(Event $event) {
    $drupalFinder = new DrupalFinder();
    $drupalFinder->locateRoot(getcwd());

    self::$drupalRoot = $drupalFinder->getDrupalRoot();
    self::$event = $event;

    self::$event->getIO()->write('');
    self::$event->getIO()->write('★ Running installer:');

    try {
      self::checkRequiredDirs();
    }
    catch (\Throwable $th) {
      $event->getIO()->writeError('<error>✗ Error: ' . $th->getMessage() . '</error>');
    }

    self::$event->getIO()->write('');
  }

}
