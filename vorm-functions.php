<?php
/*
 *  Copyright © 2011 Estêvão Samuel Procópio
 *
 *  This file is part of libvorm.
 *
 *  Libvorm is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2, or (at your option)
 *  any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 */

if (!defined('VORM_PATH'))
  define ('VORM_PATH', dirname (__file__));

require_once (VORM_PATH.'/fields/field-functions.php');
require_once (VORM_PATH.'/query/query-functions.php');

function libvorm_autoload ($name) {
  $path = dirname (__file__);
  $file = strtolower ("$name.php");
  $folders = array ('models', 'fields', 'query', 'relationship', 'metadata', 'core');

  foreach ($folders as $f) {
    if (is_file ("{$path}/{$f}/{$file}")) {
      require_once ("{$path}/{$f}/{$file}");
      break;
    }
  }
}

if (!function_exists ('array_union')) {
  function array_union () {
    $u = array ();

    foreach (func_get_args () as $a)
      foreach ($a as $i)
        if (!in_array ($i, $u))
	  $u[] = $i;

    return $u;
  }
}
