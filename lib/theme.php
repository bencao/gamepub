<?php
/**
 * LShai, the distributed microblogging tool
 *
 * Utilities for theme files and paths
 *
 * PHP version 5
 *
 * @category  Paths
 * @package   LShai
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Gets the full path of a file in a theme dir based on its relative name
 *
 * @param string $relative relative path within the theme directory
 * @param string $theme    name of the theme; defaults to current theme
 *
 * @return string File path to the theme file
 */

function theme_file($relative, $theme=null)
{
    if (empty($theme)) {
        $theme = common_config('site', 'theme');
    }
    $dir = common_config('theme', 'dir');
    if (empty($dir)) {
        $dir = INSTALLDIR.'/theme';
    }
    return $dir.'/'.$theme.'/'.$relative;
}

/**
 * Gets the full URL of a file in a theme dir based on its relative name
 *
 * @param string $relative relative path within the theme directory
 * @param string $theme    name of the theme; defaults to current theme
 *
 * @return string URL of the file
 */

function theme_path($relative, $theme=null)
{
    if (empty($theme)) {
        $theme = common_config('site', 'theme');
    }

    $path = common_config('theme', 'path');

    if (empty($path)) {
        $path = common_config('site', 'path') . '/theme/';
    }

    if ($path[strlen($path)-1] != '/') {
        $path .= '/';
    }

    if ($path[0] != '/') {
        $path = '/'.$path;
    }

    $server = common_config('theme', 'server');

    if (empty($server)) {
        $server = common_config('site', 'server');
    }

    // XXX: protocol

    return 'http://'.$server.$path.$theme.'/'.$relative;
}
