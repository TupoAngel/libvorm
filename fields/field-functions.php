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

/* Types */
function Integer () {
  return new IntegerField ('integer');
}

function UnsignedInteger () {
  return new UnsignedIntegerField ('unsigned integer');
}

function AutoIncrement () {
  return new AutoIncrementField ('unsigned integer auto_increment');
}

function String ($size) {
  return new StringField ('varchar', $size);
}

function Date_ () {
  return new DateField ('date');
}

function Decimal () {
  return new DecimalField ('real');
}

function Boolean () {
  $args = func_get_args ();
  if (array_empty ($args))
    $args = array ('True', 'False');
  return new BooleanField ($args);
}

/* Constraints */
function PrimaryKey () {
  return new PrimaryKeyField ();
}

function ForeignKey ($table, $field = null) {
  return new ForeignKeyField ($table, $field);
}

function Unique () {
  return new UniqueField ();
}

function Required () {
  return new RequiredField ();
}