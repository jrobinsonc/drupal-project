<?php

namespace DrupalProject\composer;

use Composer\IO\IOInterface;

/**
 * Log class.
 *
 * @package DrupalProject\composer
 * @author Jose Robinson <hi@joserobinson.com>
 * @copyright 2019 Jose Robinson
 * @license GPL-2.0
 * @since 1.0.0
 */
class Log {
  const LOG_FORMAT = '[%s] %s';
  const ERROR_FORMAT = '    → %s';

  /**
   * Composer IOInterface.
   *
   * @var Composer\IO\IOInterface
   */
  protected $io;

  /**
   * Message to display.
   *
   * @var string
   */
  protected $msg;

  /**
   * Creates a new instance of the Log.
   *
   * @param Composer\IO\IOInterface $io
   *   IOInterface.
   * @param string $msg
   *   The message to show.
   */
  public function __construct(IOInterface $io, string $msg) {
    $this->io = $io;
    $this->msg = $msg;

    $log = sprintf(self::LOG_FORMAT, ' ', $this->msg);

    $io->write($log, FALSE);
  }

  /**
   * Indicates the task is done and the log should be ended.
   *
   * @param array $errors
   *   Array with errors to be displayed.
   */
  public function done(array $errors = []) {
    $status = empty($errors) ? '✓' : '✗';
    $log = sprintf(self::LOG_FORMAT, $status, $this->msg);

    $this->io->overwrite($log);

    if (!empty($errors)) {
      foreach ($errors as $error) {
        $this->io->write(sprintf(self::ERROR_FORMAT, $error));
      }
    }
  }

}
