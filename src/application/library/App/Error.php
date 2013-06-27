<?php

/**
 * \App\Error.php
 * Error
 *
 * Handles error displaying and logging
 *
 * @author Brandon Lamb
 * @since 2013-06-26
 * @category Library
 *
 */

namespace App;

use Phalcon\DI\FactoryDefault as Di,
	Phalcon\Exception as PhException;

class Error
{
	/**
	 * Handle normal errors
	 *
	 * @param int $type
	 * @param string $message
	 * @param string $file
	 * @param int $line
	 */
	public static function normal($type, $message, $file, $line)
	{
		// Log it
		self::logError(
			$type,
			$message,
			$file,
			$line
		);

		// Display it under regular circumstances
	}

	/**
	 * Handle throw exceptions
	 *
	 * @param \Exception $exception
	 */
	public static function exception($exception)
	{
		// Log the error
		self::logError(
			'Exception',
			$exception->getMessage(),
			$exception->getFile(),
			$exception->getLine(),
			$exception->getTraceAsString()
		);
		// Display it
	}

	/**
	 * Log an error message
	 *
     * @param int $type
     * @param string $message
     * @param string $file
     * @param int $line
     * @param string $trace
	 */
	protected static function logError($type, $message, $file, $line, $trace = '')
	{
		$di = Di::getDefault();
		$template = '[%s] %s (File: %s Line: [%s])';

		if ($trace) {
			$template . PHP_EOL . $trace;
		}

		$logMessage = sprintf($template, $type, $message, $file, $line);

		if ($di->has('logger')) {
			$logger = $di->get('logger');
			if ($logger) {
				$logger->error($logMessage);
			} else {
				throw new PhException($logMessage);
			}
		} else {
			throw new PhException($logMessage);
		}
	}
}
