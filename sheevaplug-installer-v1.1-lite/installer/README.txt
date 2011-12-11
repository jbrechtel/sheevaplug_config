Ubuntu installer
----------------

This is an installer for Ubuntu image to:
1) /dev/mtdblock1 on the internal NAND flash as UBIFS.
2) /dev/mmcblk0p1 on the attached SD card (formatted to EXT2) used for uImage
3) /dev/mmcblk0p2 on the attached SD card (formatted to EXT3) used for the root file system

The files that are included -
README	-
	This file
initrd	-
	Small busybox based initrd that has Ubuntu mtd-utils.
	The initrd perfoems the following:
	1) format target:
		a) nand - formats /dev/mtdblock1 with a ubifs filesystem
		b) mmc - delets all partition on SD card and creates two partitions on it: EXT2 amd EXT3
	2) takes rootfs.tar.gz from the USB stick (either /dev/sda or /dev/sda1) and untars that to the ubifs volume
	3) untars modules.tar.gz which are the corresponding modules for the uImage being installed
	4) mmc- copies the uImage from USB to the EXT2 partition on the SD card
rootfs.tar.gz -
	Tarball that includes the root filesystem that will be installed.
	The rootfs here is Ubuntu 9.04 root filesystem but potentially can be replaced
	by any rootfs tarball.
	Note that the directory structure in the archive should be flat meaning when
	untarring the archive, the directories /usr /etc and all others are on first
	level.
uImage -
	The kernel image for the plug. This is written to the internal NAND flash
	in the plug at address 1MB (when installing on the nand).
modules.tar.gz -
	The corresponding modules for the above uImage. Note that when untarring this
	archive on the target rootfs, then first directory should be /lib/modules as
	ubuntu distro (and most distros) expects.
