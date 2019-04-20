<?php

/**
 * @file
 * Contains \DrupalProject\composer\SiteInstaller.
 */

namespace DrupalProject\composer;

use Composer\Script\Event;
use Composer\Semver\Comparator;
use DrupalFinder\DrupalFinder;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\PathUtil\Path;

class SiteInstaller
{
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

  private static function getFs()
  {
    static $filesystem = null;

    if (null === $filesystem) {
      $filesystem = new Filesystem();
    }

    return $filesystem;
  }

  private static function checkRequiredDirs()
  {
    self::$event->getIO()->write('  - Checking required directories...', false);

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

  public static function init(Event $event)
  {
    $drupalFinder = new DrupalFinder();
    $drupalFinder->locateRoot(getcwd());

    self::$drupalRoot = $drupalFinder->getDrupalRoot();
    self::$event = $event;

    self::$event->getIO()->write('');
    self::$event->getIO()->write('★ Running installer:');

    try {
        self::checkRequiredDirs();
    } catch (\Throwable $th) {
        $event->getIO()->writeError('<error>✗ Error: ' . $th->getMessage() . '</error>');
    }

    self::$event->getIO()->write('');
  }
}
