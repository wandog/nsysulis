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

unsigned short checksumCalculate(unsigned char *data,unsigned short length) {
	unsigned short checksum,index;
	for(index=0,checksum=0;index<length;index++) {
		checksum+=data[index];
	}
	return(checksum);
}