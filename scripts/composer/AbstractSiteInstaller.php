<?php

namespace DrupalProject\composer;

use Composer\Script\Event;
use DrupalFinder\DrupalFinder;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;
use Webmozart\PathUtil\Path;

/**
 * Abstract site installer.
 *
 * @package DrupalProject\composer
 * @author Jose Robinson <hi@joserobinson.com>
 * @copyright 2019 Jose Robinson
 * @license GPL-2.0
 * @since 1.0.0
 */
abstract class AbstractSiteInstaller {

  /**
   * Drupal root directory path.
   *
   * @var string
   */
  protected static $drupalRoot;

  /**
   * Directory path of composer.json file.
   *
   * @var string
   */
  private static $composerRoot;

  /**
   * Composer event.
   *
   * @var Composer\Script\Event
   */
  protected static $event;

  /**
   * Returns an instance of the Filesystem class.
   *
   * @return Symfony\Component\Filesystem\Filesystem
   *   Filesystem instance.
   */
  protected function getFs() {
    static $filesystem = NULL;

    if (NULL === $filesystem) {
      $filesystem = new Filesystem();
    }

    return $filesystem;
  }

  /**
   * Entrypoint for installer.
   */
  public static function run(Event $event) {
    self::$event = $event;

    // Locate root directories.
    $drupalFinder = new DrupalFinder();

    if (!$drupalFinder->locateRoot(getcwd())) {
      throw new Exception('Project root could not be located.');
    }

    self::$drupalRoot = $drupalFinder->getDrupalRoot();
    self::$composerRoot = $drupalFinder->getComposerRoot();

    // Start the installer.
    try {
      new static();
    }
    catch (Throwable $th) {
      self::$event->getIO()->writeError('<error>✗ Error: ' . $th->getMessage() . '</error>');
    }
  }

  /**
   * Returns a Log instance.
   *
   * @param string $msg
   *   The message to show.
   *
   * @return DrupalProject\composer\Log
   *   Log instance.
   */
  protected function log($msg) {
    return new Log(self::$event->getIO(), $msg);
  }

  /**
   * Check required directories and creates them if they don't exist.
   *
   * Note that this script assumes that the user running composer is the
   * same user used by the web server (Apache, NGINX, etc). Default
   * directories are checked by default:
   * - themes/custom
   * - modules/custom
   * - sites/default/files.
   *
   * @param array $extra
   *   Extra directories to create.
   * @param int $dirPerms
   *   Permissions to be applied to each directory.
   */
  protected function checkRequiredDirs(array $extra = [], int $dirPerms = 0755) {
    $log = $this->log('Checking required directories.');
    $fs = $this->getFs();
    $errors = [];
    $requiredDirs = array_merge([
      'themes/custom',
      'modules/custom',
      'sites/default/files',
    ], $extra);
    $oldmask = umask(0);

    foreach ($requiredDirs as $dir) {
      $dirPath = self::$drupalRoot . '/' . $dir;

      // Creates the directory if it doesn't exist.
      if (!$fs->exists($dirPath)) {
        try {
          $fs->mkdir($dirPath, $dirPerms);
        }
        catch (Throwable $th) {
          $errors[] = sprintf(
            'Directory "%s" cannot be created.',
            $dirPath
          );
        }
      }
    }

    umask($oldmask);

    $log->done($errors);
  }

  /**
   * Generates the settings file if it doesn't exist.
   */
  protected function generateSettings() {
    $log = $this->log('Generating settings.');
    $fs = $this->getFs();
    $errors = [];

    $settingsFile = self::$drupalRoot . '/sites/default/settings.php';
    $settingsDefaultFile = self::$drupalRoot . '/sites/default/default.settings.php';

    if (!$fs->exists($settingsDefaultFile)) {
      $errors[] = 'File "default.settings.php" does not exist.';
    }

    if (!$fs->exists($settingsFile) && $fs->exists($settingsDefaultFile)) {
      $fs->copy($settingsDefaultFile, $settingsFile);

      require_once self::$drupalRoot . '/core/includes/bootstrap.inc';
      require_once self::$drupalRoot . '/core/includes/install.inc';

      $settings = [];
      $settings['config_directories'] = [
        CONFIG_SYNC_DIRECTORY => (object) [
          'value' => Path::makeRelative(
            self::$composerRoot . '/config/sync',
            self::$drupalRoot
          ),
          'required' => TRUE,
        ],
      ];

      drupal_rewrite_settings($settings, $settingsFile);
    }

    $log->done($errors);
  }

}
