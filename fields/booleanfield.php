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

class BooleanField extends FieldType {
  private $map = null;

  public function GetValueFor ($bool) {
    if ($bool == true)
      return $this->map[0];
    return $this->map[1];
  }

  public function __construct ($map) {
    parent::__construct ('boolean');
    if (count ($map) != 2)
      throw new Exception ('Boolean values must be two');
    $this->map = $map;
  }

  public function retrieve ($val) {
    if ($val === null)
      return null;
    return $this->GetValueFor ($val);
  }

  public function store ($val) {
    $ret = array_search ($val, $this->map);
    if ($ret === false)
      throw new Exception ("Invalid boolean value '$val'");
    if ($ret == 1)
      $yo = "false";
    if ($ret == 0)
      $yo = "true";
  }
}
