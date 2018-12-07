<?php namespace Arcanedev\LogViewer\Helpers;

use Arcanedev\LogViewer\Utilities\LogLevels;
use Illuminate\Support\Str;

/**
 * Class     LogParser
 *
 * @package  Arcanedev\LogViewer\Helpers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class LogParser
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * Parsed data.
     *
     * @var array
     */
    protected static $parsed = [];

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Parse file content.
     *
     * @param  string  $raw
     *
     * @return array
     */
    public static function parse($raw)
    {
        self::$parsed          = [];
        list($headings, $data) = self::parseRawData($raw);

        // @codeCoverageIgnoreStart
        if ( ! is_array($headings)) {
            return self::$parsed;
        }
        // @codeCoverageIgnoreEnd

        foreach ($headings as $heading) {
            for ($i = 0, $j = count($heading); $i < $j; $i++) {
                self::populateEntries($heading, $data, $i);
            }
        };

        unset($headings, $data);

        return array_reverse(self::$parsed);
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Parse raw data.
     *
     * @param  string  $raw
     *
     * @return array
     */
    private static function parseRawData($raw)
    {
        $pattern = '/\['.REGEX_DATE_PATTERN.' '.REGEX_TIME_PATTERN.'\].*/';
        preg_match_all($pattern, $raw, $headings);
        $data    = preg_split($pattern, $raw);

        if ($data[0] < 1) {
            $trash = array_shift($data);
            unset($trash);
        }

        //pidong
        if(config('log-viewer.view-other-log')) {
            $headings = [$headings[0]];
            if(isset($headings[0]) && empty($headings[0][0])) {
                if (count($data) < 1) {
                    $data[] = $raw;
                    $headings[0][] = $raw;
                } else {
                    $headings[0][] = "Click the button 'Stack' on the right to view the contents of the log";
                }
            }
        }
        //pidong

        return [$headings, $data];
    }

    /**
     * Populate entries.
     *
     * @param  array  $heading
     * @param  array  $data
     * @param  int    $key
     */
    private static function populateEntries($heading, $data, $key)
    {
        foreach (LogLevels::all() as $level) {
            if (self::hasLogLevel($heading[$key], $level)) {
                self::$parsed[] = [
                    'level'  => $level,
                    'header' => $heading[$key],
                    'stack'  => $data[$key]
                ];
            }
            //pidong
            else {
                if (config('log-viewer.view-other-log') && isset($data[$key]) && $level === 'info') {
                    self::$parsed[] = [
                        'level'  => 'info',
                        'header' => $heading[$key],
                        'stack'  => $data[$key]
                    ];
                }
            }
            //pidong
        }
    }

    /**
     * Check if header has a log level.
     *
     * @param  string  $heading
     * @param  string  $level
     *
     * @return bool
     */
    private static function hasLogLevel($heading, $level)
    {
        return Str::contains(strtolower($heading), strtolower('.' . $level));
    }
}
