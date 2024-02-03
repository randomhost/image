<?php

namespace randomhost\Image\Tests;

/**
 * Provides accessing test data.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2024 Random-Host.tv
 * @license   https://opensource.org/licenses/BSD-3-Clause BSD License (3 Clause)
 *
 * @see https://github.random-host.tv
 */
class TestData
{
    /**
     * Path to test data directory.
     *
     * @var string
     */
    private const TEST_DATA_DIR = __DIR__.'/../data';

    /**
     * Returns the path to the given test data file.
     *
     * @param string $fileName Test data file name.
     *
     * @throws \Exception Thrown in case the test data file could not be read.
     */
    public static function getPath(string $fileName): string
    {
        if (!is_dir(self::TEST_DATA_DIR) || !is_readable(self::TEST_DATA_DIR)) {
            throw new \Exception(
                sprintf(
                    'Test data directory %s not found',
                    self::TEST_DATA_DIR
                )
            );
        }

        $path = realpath(self::TEST_DATA_DIR).'/'.$fileName;
        if (!is_file($path) || !is_readable($path)) {
            throw new \Exception(
                sprintf(
                    'Test file %s not found',
                    $path
                )
            );
        }

        return realpath($path);
    }
}
