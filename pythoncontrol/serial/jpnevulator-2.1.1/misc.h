/* jpnevulator - serial reader/writer
 * Copyright (C) 2006-2012 Freddy Spierenburg
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

#ifndef __MISC_H
#define __MISC_H

#define max(x,y) ((x)>(y)?(x):(y))
#define min(x,y) ((x)<(y)?(x):(y))

typedef char bool_t;
#define boolTrue 1
#define boolFalse 0
#define boolSet(x) ((x)=boolTrue)
#define boolReset(x) ((x)=boolFalse)
#define boolIsSet(x) ((x)==boolTrue)
#define boolIsNotSet(x) ((x)==boolFalse)

#endif
