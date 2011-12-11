Sheevaplug installer README
===========================
Intro -
-------
The installer intention is to -
1. Install a new distro on the plug.
2. Reflash a bricked plug.

The base of the installer is bash scripts, openocd and few scripts for u-boot env
variables manipulation.
The installer directory has all the distro specific stuff.
The focused host PC in this readme is Linux 32, Windows is also mentioned

WARNING - WARNING - WARNING
Anything on the plug will be deleted. Starting with the onboard NAND flash that
includes u-boot, u-boot env variables, kernel, root filesystem.
Depending on the type of the installation, SD card, or USB stick might be deleted too.

Please backup your data.

Host PC Prerequisites -
-----------------------
 Win32: CDM 2.04.14 (Consol USB driver)
        LibUSB-Win32-0.1.12.1  (libusb filter driver, homepage: http://libusb-win32.sourceforge.net/)
 Linux: php5-cli

Installer procedure -
---------------------
1. On x86 PC -
   a. Install the prerequisites on your host PC (Linux or Windows according to the list above). The win32
       drivers are included in this installer in the following directory: ~/work/sheevaplug-installer/win32.
   b. Using the serial JTAG interface, burn the uboot and environment parameters
       to Plug NAND flash
2. On Sheevaplug -
   a. Load uImage and initrd to RAM from the USB stick and run from it
   b. Burn the uImage to flash
   c. Two typs of install:
      1) nand - Create a UBIFS (to create JFFS2 you need to edit the /etc/init,d/rcS in initrd
                after system boots from it and continue procedure by running /etc/init,d/rcS)
      2) mmc - format the SD card with one EXT2 partition for uImage and EXT3 partition
               for root file system and mount it
   d. Unpack the root file-system from the USB stick (rootfs.tar.gz) to flash FS
   e. Unpack the kernel modules from the USB stick (modules.tar.gz) to flash FS
   f. In case of mmc installation, copy the uImage to /boot directory

Installation -
--------------
1.  Prepare an empty USB stick that is FAT16/32 formatted.
2.  Download the installer tarball to your PC and unpack it to your working
     directory (for example: ~/work/sheevaplug-installer)
3.  Edit the ~/work/sheevaplug-installer/installer/uboot-custom.txt file to burn
     the correct MAC address according to the MAC address on the back of the
     Plug (change the ethaddr value)
4.  Populate the USB stick with a all files inside the installer directory.
    NOTE: that the files should be written to the root directory of the USB stick.
          For example if the recovery directory is sheevaplug-installer and USB
          stick mount is: /media/usb run on the host Linux PC:
          sudo cp -a ~/work/sheevaplug-installer/installer/* /media/usb/
5.  Copy the following to the USB stick root directory:
      a. Root file-system (packed into rootfs.tar.gz)
      b. Kernel (named uImage)
      c. Kernel modules (packed into modules.tar.gz)
      d. Init ramdisk (uImage.initrd)
    If you do not have all, you can download from:
      a. http://plugcomputer.org/index.php/us/resources/downloads (distro, kernel(LSP), uboot)
      b. http://plugcomputer.org/plugwiki/index.php/Main_Page     (distrio,uboot)
      c. http://sheeva.with-linux.com/sheeva                      (mainline kernel+modules)
6.  Copy the uboot image (named uboot.bin) to the ~/work/sheevaplug-installer/uboot/ directory (use prev bullet links as sources)
7.  Safely remove the USB stick from the host PC and plug it into the Plug's USB host interface (not via USB HUB!)
8.  Connect the power to the Plug
9.  Connect the mini USB connector to the Plug (not via USB HUB!)
10. On Win PC, close any connected console (tera-term, tutty, RealTerm...), since the flash burning is done via this port
12. On the host PC, change the working directory to ~/work/sheevaplug-installer
    and run the runme.php file (or the runme.exe on Win host PC). (on Linux PC you should be root in order to do that)
    When the runme finishes it will beep to indicate that the flash burning process  finished.
    On Win PC:
      a. wait for the uboot.bin to be burned (you should see: "wrote file uboot.bin to NAND flash 0 up to offset 0x00073000 in 112.746002s")
      b. exit the runme.exe program by executing Control+C
      c. disconnect the mini USB and reconnect
      d. wait for Windows to idevntify the new inserted mini USB
      e. you can open your serial (like putty) only now.
13. On a Linux PC. if the installer is unattended, watch the console and wait until the
     whole process is done.
14. If the installer is an interactive one (like Debian network installer) then
     watch the console for messages and act accordingly.

Installer directory structure -
-------------------------------
|
+-- README	- This file
+-- runme.php	- The installer script
+-- runme.exe	- The installer executable for Windows
+-- uboot	- Includes uboot images
  +-- openocd   - Scripts for openocd
  +-- uboot-env	- uboot-env manipulation
+-- installer	- Installer directory - this is specific per distro
+-- win32       - Windows 32bit dll Cygwin DLLs
+-- src         - sources and HOWTO for initrd

Troubleshooting -
-----------------
1. OpenOCD fails to burn uboot+env
   a. On Win32, make sure you installed the CDM and libusb drivers
   b. Disconnect and reconnect the mini USB
   c. Power cycle the Plug
   d. On Linux, sudo rmmod ftdi_sio;sudo modprobe ftdi_sio vendor=0x9e88 product=0x9e8f
2. Initrd installer does not find USB
   a. make sure the content was copied to the USB DOK
   b. make sure the DOK was formatted to EXT3/FAT16/FAT32 (file system type depends
      on uImage support, not in kernel modules)
3. After installation completes, kernel does not find MMC partition
   a. change the rootdelay (default value is 5) value in the uboot-mmc-custom.txt,
      this uboot kernel parameter is used to allow SDHC cards settle before Linux
      tries to search for the root
4. Host PCs does not recognize the ftdi port when connected to mini USB
   a. disconnect/connect mini USB
   b. close any open console
   c. power cycle the plug
   d. on WIN machine: restart windows
   e. make sure you you do not use a USB HUB!
5. After uboot+env is burned, device does not reboot (Win32)
   a. try to disconnect and connect mini USB cable
   b. press the reset button
   c. power cycle the plug
6. After uboot+env is burned, device does not load the initrd and user does not
   want to override uboot
   a. run in uboot: run recover1
7. After uboot+env is burned, device failes to see the /dev/mmcblk0 partition
   a. try to run the /etc/init.d/rcS again

Notes -
-------
* Do not insert SD card during install process
* Do not use USB HUB
* Use USB DOK, not USB Hard Drive, sinc HDD spin up time might fail the installation
* When using the installer, make sure only one SheevaPlug is connected to your host PC

Bugs and issues-
----------------
using minicom as terminal console -
	If you are using minicom as terminal then it might be problematic.
	Please use different (the tool was mainly tested when using putty);
	or disable initialization and reset scripts that are being used by minicom.

runme.php script exist too fast -
	the runme.sh script takes about 2 minutes to download uboot and it's env
	variables to the plug. Sometimes it exits in few seconds with error messages
	like the following -
	
	Warn : value captured during scan didn't pass the requested check:
	Warn : captured: 0x00 check_value: 0x01 check_mask: 0x0F

	or

	Error: Target not examined yet

	The best way is to either disconnect the mini USB cable and reconnect then
	try again, or completely power cycle the plug (unplug then plug back again).

Modders -
---------
Great. Now you want to have your custom installer.
Please have changes ONLY inside the installer directory. Actually it is best if
you have your own installer directory but keep all other files intact.
The reason is that we might have a Windows version of the scripts and we try
to maintain exactly same scripts for all different installers.

The scripts do the following -
1. Create a uboot-env.bin 128KB file which will have the uboot environment variables.
2. Populate uboot-env.bin with default environment variables (as if you write
   resetenv inside uboot).
3. Read installer/uboot-custom.txt and change uboot-env.bin accordingly. This
   is per installer modifications.
   You can view the env variables by running 
	./scripts-linux/uboot-env/fw_printenv
4. Start openocd (static binary) that -
	4.1 Initialize the plug
	4.2 Erase blocks 5-6 on the plug's NAND flash and write to it uboot-env.bin
	4.3 Erase blocks 0-4 on the plug's NAND flash and writes uboot/uboot.bin
	4.4 Releases the processor to start booting from a fresh new uboot.
5. uboot initializes and runs bootcmd. This can potentially be 'run recover1'
   where recover1 sets the arcNumber for the kernel, sets bootcmd to
   'run recover2', saves the env and reset. So next boot recover2 env
   variable will run.
   Using this you can potentially create a long chain of uboot boots where in
   every stage you can do something different.
   Refer to examples inside the installer directory.

Installer terminal output -
---------------------------
The following terminal print was taken from an x86 Linux PC and Sheevaplug during
MMC recovery procedure (prints for NAND intsll are followed after MMC section):

MMC installation -
------------------

1. Host Linux PC -
ubuntu:~/work/plug/sheevaplug-installer-v1.0$ sudo php runme.php mmc

 ****   exec(modprobe ftdi_sio vendor=0x9e88 product=0x9e8f)
 ****   Preparing environment variables file ...
reading uboot/uboot-env/uboot-dflt.txt
uboot/uboot-env/fw_setenv baudrate 115200
CRC read error on uboot-env.bin: Success
uboot/uboot-env/fw_setenv loads_echo 0
uboot/uboot-env/fw_setenv ipaddr 10.4.50.165
uboot/uboot-env/fw_setenv serverip 10.4.50.5
uboot/uboot-env/fw_setenv rootpath /mnt/ARM_FS/
uboot/uboot-env/fw_setenv netmask 255.255.255.0
uboot/uboot-env/fw_setenv stdin serial
uboot/uboot-env/fw_setenv stdout serial
uboot/uboot-env/fw_setenv stderr serial
uboot/uboot-env/fw_setenv console console=ttyS0,115200 mtdparts=nand_mtd:0xc0000@0\(uboot\)ro,0x1ff00000@0x100000\(root\)
uboot/uboot-env/fw_setenv mainlineLinux no
uboot/uboot-env/fw_setenv CASset min
uboot/uboot-env/fw_setenv enaMonExt no
uboot/uboot-env/fw_setenv enaCpuStream no
uboot/uboot-env/fw_setenv enaWrAllo no
uboot/uboot-env/fw_setenv pexMode RC
uboot/uboot-env/fw_setenv disL2Cache no
uboot/uboot-env/fw_setenv setL2CacheWT yes
uboot/uboot-env/fw_setenv disL2Prefetch yes
uboot/uboot-env/fw_setenv enaICPref yes
uboot/uboot-env/fw_setenv enaDCPref yes
uboot/uboot-env/fw_setenv sata_dma_mode yes
uboot/uboot-env/fw_setenv MALLOC_len 1
uboot/uboot-env/fw_setenv ethprime egiga0
uboot/uboot-env/fw_setenv netbsd_en no
uboot/uboot-env/fw_setenv vxworks_en no
uboot/uboot-env/fw_setenv bootargs_root root=/dev/nfs rw
uboot/uboot-env/fw_setenv bootargs_end :::DB88FXX81:eth0:none
uboot/uboot-env/fw_setenv image_name uImage
uboot/uboot-env/fw_setenv bootcmd tftpboot 0x2000000 \$\(image_name\)\;setenv bootargs \$\(console\) \$\(bootargs_root\) nfsroot=\$\(serverip\):\$\(rootpath\) ip=\$\(ipaddr\):\$\(serverip\)\$\(bootargs_end\) \$\(mvNetConfig\) \$\(mvPhoneConfig\)\;  bootm 0x2000000\;
uboot/uboot-env/fw_setenv standalone fsload 0x2000000 \$\(image_name\)\;setenv bootargs \$\(console\) root=/dev/mtdblock0 rw ip=\$\(ipaddr\):\$\(serverip\)\$\(bootargs_end\) \$\(mvPhoneConfig\)\; bootm 0x2000000\;
uboot/uboot-env/fw_setenv bootdelay 3
uboot/uboot-env/fw_setenv disaMvPnp no
uboot/uboot-env/fw_setenv ethaddr 00:50:43:4e:2a:14
uboot/uboot-env/fw_setenv ethmtu 1500
uboot/uboot-env/fw_setenv mvPhoneConfig mv_phone_config=dev0:fxs,dev1:fxs
uboot/uboot-env/fw_setenv mvNetConfig mv_net_config=\(00:11:88:0f:62:81,0:1:2:3\),mtu=1500
uboot/uboot-env/fw_setenv usb0Mode host
uboot/uboot-env/fw_setenv yuk_ethaddr 00:00:00:EE:51:81
uboot/uboot-env/fw_setenv nandEcc 1bit
uboot/uboot-env/fw_setenv netretry no
uboot/uboot-env/fw_setenv rcvrip 169.254.100.100
uboot/uboot-env/fw_setenv loadaddr 0x02000000
uboot/uboot-env/fw_setenv autoload no
uboot/uboot-env/fw_setenv enaAutoRecovery yes
uboot/uboot-env/fw_setenv ethact egiga0
reading uboot/uboot-env/uboot-mmc-custom.txt
uboot/uboot-env/fw_setenv bootargs_root root=/dev/mmcblk0p2 rootdelay=5
uboot/uboot-env/fw_setenv ethaddr 00:50:43:01:c1:e6
uboot/uboot-env/fw_setenv real_bootcmd setenv bootargs \$\(bootargs_console\) \$\(bootargs_root\)\; mmcinit\; ext2load mmc 0:1 0x800000 /uImage\; bootm 0x00800000
uboot/uboot-env/fw_setenv bootargs_console console=ttyS0,115200
uboot/uboot-env/fw_setenv bootcmd run recover1
uboot/uboot-env/fw_setenv recover1 setenv mainlineLinux yes\; setenv arcNumber 2097\; setenv bootcmd run recover2\; saveenv\; reset
uboot/uboot-env/fw_setenv recover2 run recover3\; setenv bootcmd \$\(real_bootcmd\)\; saveenv\; setenv bootargs \$\(bootargs_console\) root=/dev/ram0 rw ramdisk=0x01100000,8M install_type=mmc\; bootm 0x00800000 0x01100000
uboot/uboot-env/fw_setenv recover3 usb start\; fatload usb 0 0x00800000 uImage\; fatload usb 0 0x01100000 initrd

 ****   Burning uboot and environment variables ... This will take few minutes ...
Open On-Chip Debugger 0.2.0 (2009-07-26-14:56) Release
$URL: http://svn.berlios.de/svnroot/repos/openocd/tags/openocd-0.2.0/src/openocd.c $
For bug reports, read http://svn.berlios.de/svnroot/repos/openocd/trunk/BUGS
2000 kHz
jtag_nsrst_delay: 200
jtag_ntrst_delay: 200
dcc downloads are enabled
Info : JTAG tap: feroceon.cpu tap/device found: 0x20a023d3 (mfg: 0x1e9, part: 0x0a02, ver: 0x2)
Info : JTAG Tap/device matched
target state: halted
target halted in ARM state due to debug-request, current mode: Supervisor
cpsr: 0x600000d3 pc: 0x100003a0
MMU: disabled, D-Cache: disabled, I-Cache: disabled
target state: halted
target halted in ARM state due to debug-request, current mode: Supervisor
cpsr: 0x000000d3 pc: 0xffff0000
MMU: disabled, D-Cache: disabled, I-Cache: disabled
0 0 1 0: 00052078
NAND flash device 'NAND 512MiB 3,3V 8-bit' found
successfully erased blocks 5 to 6 on NAND flash device 'NAND 512MiB 3,3V 8-bit'
wrote file uboot-env.bin to NAND flash 0 up to offset 0x000c0000 in 19.379637s
target state: halted
target halted in ARM state due to debug-request, current mode: Supervisor
cpsr: 0x000000d3 pc: 0xffff0000
MMU: disabled, D-Cache: disabled, I-Cache: disabled
0 0 1 0: 00052078
NAND flash device 'NAND 512MiB 3,3V 8-bit' found
successfully erased blocks 0 to 4 on NAND flash device 'NAND 512MiB 3,3V 8-bit'
wrote file uboot.bin to NAND flash 0 up to offset 0x00074000 in 70.449287s

 ****   U-boot should be up and running now. Open your console ...

2. Sheevalug console -
  a. Burn uboot & environment params to flash
         __  __                      _ _
        |  \/  | __ _ _ ____   _____| | |
        | |\/| |/ _` | '__\ \ / / _ \ | |
        | |  | | (_| | |   \ V /  __/ | |
        |_|  |_|\__,_|_|    \_/ \___|_|_|
 _   _     ____              _
| | | |   | __ )  ___   ___ | |_
| | | |___|  _ \ / _ \ / _ \| __|
| |_| |___| |_) | (_) | (_) | |_
 \___/    |____/ \___/ \___/ \__|
 ** MARVELL BOARD: SHEEVA PLUG LE

U-Boot 1.1.4 (Jul 19 2009 - 16:03:28) Marvell version: 3.4.19

U-Boot code: 00600000 -> 0067FFF0  BSS: -> 006CFB00

Soc: 88F6281 A0 (DDR2)
CPU running @ 1200Mhz L2 running @ 400Mhz
SysClock = 400Mhz , TClock = 200Mhz

DRAM CAS Latency = 5 tRP = 5 tRAS = 18 tRCD=6
DRAM CS[0] base 0x00000000   size 256MB
DRAM CS[1] base 0x10000000   size 256MB
DRAM Total size 512MB  16bit width
Addresses 8M - 0M are saved for the U-Boot usage.
Mem malloc Initialization (8M - 7M): Done
NAND:512 MB
Flash:  0 kB

CPU : Marvell Feroceon (Rev 1)

Streaming disabled
Write allocate disabled


USB 0: host mode
PEX 0: interface detected no Link.
Net:   egiga0 [PRIME], egiga1
Hit any key to stop autoboot:  0
Saving Environment to NAND...
Erasing Nand...Writing to Nand... done
▒

  b. Load uImage and initrd from USB to RAM
         __  __                      _ _
        |  \/  | __ _ _ ____   _____| | |
        | |\/| |/ _` | '__\ \ / / _ \ | |
        | |  | | (_| | |   \ V /  __/ | |
        |_|  |_|\__,_|_|    \_/ \___|_|_|
 _   _     ____              _
| | | |   | __ )  ___   ___ | |_
| | | |___|  _ \ / _ \ / _ \| __|
| |_| |___| |_) | (_) | (_) | |_
 \___/    |____/ \___/ \___/ \__|
 ** MARVELL BOARD: SHEEVA PLUG LE

U-Boot 1.1.4 (Jul 19 2009 - 16:03:28) Marvell version: 3.4.19

U-Boot code: 00600000 -> 0067FFF0  BSS: -> 006CFB00

Soc: 88F6281 A0 (DDR2)
CPU running @ 1200Mhz L2 running @ 400Mhz
SysClock = 400Mhz , TClock = 200Mhz

DRAM CAS Latency = 5 tRP = 5 tRAS = 18 tRCD=6
DRAM CS[0] base 0x00000000   size 256MB
DRAM CS[1] base 0x10000000   size 256MB
DRAM Total size 512MB  16bit width
Addresses 8M - 0M are saved for the U-Boot usage.
Mem malloc Initialization (8M - 7M): Done
NAND:512 MB
Flash:  0 kB

CPU : Marvell Feroceon (Rev 1)

Streaming disabled
Write allocate disabled


USB 0: host mode
PEX 0: interface detected no Link.
Net:   egiga0 [PRIME], egiga1
Hit any key to stop autoboot:  0
(Re)start USB...
USB:   scanning bus for devices... 2 USB Device(s) found
       scanning bus for storage devices... 1 Storage Device(s) found
reading uImage
................................................................................                                                                  ................................................................................                                                                  ................................................................................                                                                  ..............

2601032 bytes read
reading initrd
................................................................................                                                                  ................................................................................                                                                  ................................................................................                                                                  ....................................

  c. Run from RAM based uImage+initrd

2837885 bytes read
Saving Environment to NAND...
Erasing Nand...Writing to Nand... done
## Booting image at 00800000 ...
   Image Name:   Linux-2.6.30
   Created:      2009-07-01  22:55:04 UTC
   Image Type:   ARM Linux Kernel Image (uncompressed)
   Data Size:    2600968 Bytes =  2.5 MB
   Load Address: 00008000
   Entry Point:  00008000
   Verifying Checksum ... OK
OK
## Loading Ramdisk Image at 01100000 ...
   Image Name:   initrd.ext2.gz
   Created:      2009-08-20   7:06:34 UTC
   Image Type:   ARM Linux RAMDisk Image (gzip compressed)
   Data Size:    3331618 Bytes =  3.2 MB
   Load Address: 00000000
   Entry Point:  00000000
   Verifying Checksum ... OK

Starting kernel ...

Uncompressing Linux.............................................................                                                                  ................................................................................                                                                  ............................. done, booting the kernel.
Linux version 2.6.30.2 (kelly@speedy) (gcc version 4.2.1) #11 PREEMPT Wed Jul 22                                                                           19:53:31 MDT 2009
CPU: Feroceon 88FR131 [56251311] revision 1 (ARMv5TE), cr=00053977
CPU: VIVT data cache, VIVT instruction cache
Machine: Marvell SheevaPlug Reference Board
Memory policy: ECC disabled, Data cache writeback
Built 1 zonelists in Zone order, mobility grouping on.  Total pages: 130048
Kernel command line: console=ttyS0,115200 root=/dev/ram0 rw ramdisk=0x01100000,8                                                                          M install_type=mmc
NR_IRQS:114
PID hash table entries: 2048 (order: 11, 8192 bytes)
Console: colour dummy device 80x30
Dentry cache hash table entries: 65536 (order: 6, 262144 bytes)
Inode-cache hash table entries: 32768 (order: 5, 131072 bytes)
Memory: 256MB 256MB = 512MB total
Memory: 510080KB available (4728K code, 998K data, 140K init, 0K highmem)
SLUB: Genslabs=11, HWalign=32, Order=0-3, MinObjects=0, CPUs=1, Nodes=1
Calibrating delay loop... 1192.75 BogoMIPS (lpj=5963776)
Mount-cache hash table entries: 512
CPU: Testing write buffer coherency: ok
net_namespace: 980 bytes
NET: Registered protocol family 16
Kirkwood: MV88F6281-A0, TCLK=200000000.
Feroceon L2: Enabling L2
Feroceon L2: Cache support initialised.
Kirkwood: Gating clock using mask 0x1ac224
bio: create slab <bio-0> at 0
SCSI subsystem initialized
usbcore: registered new interface driver usbfs
usbcore: registered new interface driver hub
usbcore: registered new device driver usb
cfg80211: Using static regulatory domain info
cfg80211: Regulatory domain: US
        (start_freq - end_freq @ bandwidth), (max_antenna_gain, max_eirp)
        (2402000 KHz - 2472000 KHz @ 40000 KHz), (600 mBi, 2700 mBm)
        (5170000 KHz - 5190000 KHz @ 40000 KHz), (600 mBi, 2300 mBm)
        (5190000 KHz - 5210000 KHz @ 40000 KHz), (600 mBi, 2300 mBm)
        (5210000 KHz - 5230000 KHz @ 40000 KHz), (600 mBi, 2300 mBm)
        (5230000 KHz - 5330000 KHz @ 40000 KHz), (600 mBi, 2300 mBm)
        (5735000 KHz - 5835000 KHz @ 40000 KHz), (600 mBi, 3000 mBm)
cfg80211: Calling CRDA for country: US
NET: Registered protocol family 2
IP route cache hash table entries: 16384 (order: 4, 65536 bytes)
TCP established hash table entries: 65536 (order: 7, 524288 bytes)
TCP bind hash table entries: 65536 (order: 6, 262144 bytes)
TCP: Hash tables configured (established 65536 bind 65536)
TCP reno registered
NET: Registered protocol family 1
Trying to unpack rootfs image as initramfs...
rootfs image is not initramfs (no cpio magic); looks like an initrd
Freeing initrd memory: 3252K
JFFS2 version 2.2. (NAND) © 2001-2006 Red Hat, Inc.
JFS: nTxBlock = 4012, nTxLock = 32100
msgmni has been set to 1003
alg: No test for stdrng (krng)
io scheduler noop registered
io scheduler anticipatory registered
io scheduler deadline registered
io scheduler cfq registered (default)
Serial: 8250/16550 driver, 2 ports, IRQ sharing disabled
serial8250.0: ttyS0 at MMIO 0xf1012000 (irq = 33) is a 16550A
console [ttyS0] enabled
brd: module loaded
loop: module loaded
Driver 'sd' needs updating - please use bus_type methods
MV-643xx 10/100/1000 ethernet driver version 1.4
mv643xx_eth smi: probed
net eth0: port 0 with MAC address 00:50:43:01:c1:e6
libertas_sdio: Libertas SDIO driver
libertas_sdio: Copyright Pierre Ossman
NAND device: Manufacturer ID: 0xad, Chip ID: 0xdc (Hynix NAND 512MiB 3,3V 8-bit)
Scanning device for bad blocks
Bad eraseblock 385 at 0x000003020000
Bad eraseblock 393 at 0x000003120000
Bad eraseblock 401 at 0x000003220000
Bad eraseblock 409 at 0x000003320000
Bad eraseblock 417 at 0x000003420000
Bad eraseblock 425 at 0x000003520000
Bad eraseblock 433 at 0x000003620000
Bad eraseblock 441 at 0x000003720000
Bad eraseblock 532 at 0x000004280000
Bad eraseblock 2433 at 0x000013020000
Bad eraseblock 2441 at 0x000013120000
Bad eraseblock 2449 at 0x000013220000
Bad eraseblock 2457 at 0x000013320000
Bad eraseblock 2465 at 0x000013420000
Bad eraseblock 2473 at 0x000013520000
Bad eraseblock 2481 at 0x000013620000
Bad eraseblock 2489 at 0x000013720000
Bad eraseblock 3962 at 0x00001ef40000
Bad eraseblock 3966 at 0x00001efc0000
Creating 3 MTD partitions on "orion_nand":
0x000000000000-0x000000100000 : "u-boot"
0x000000100000-0x000000500000 : "uImage"
0x000000500000-0x000020000000 : "root"
ehci_hcd: USB 2.0 'Enhanced' Host Controller (EHCI) Driver
orion-ehci orion-ehci.0: Marvell Orion EHCI
orion-ehci orion-ehci.0: new USB bus registered, assigned bus number 1
orion-ehci orion-ehci.0: irq 19, io mem 0xf1050000
orion-ehci orion-ehci.0: USB 2.0 started, EHCI 1.00
usb usb1: configuration #1 chosen from 1 choice
hub 1-0:1.0: USB hub found
hub 1-0:1.0: 1 port detected
Initializing USB Mass Storage driver...
usbcore: registered new interface driver usb-storage
USB Mass Storage support registered.
usbcore: registered new interface driver ums-datafab
usbcore: registered new interface driver ums-freecom
usbcore: registered new interface driver ums-jumpshot
usbcore: registered new interface driver ums-sddr09
usbcore: registered new interface driver ums-sddr55
mice: PS/2 mouse device common for all mice
rtc-mv rtc-mv: rtc core: registered rtc-mv as rtc0
i2c /dev entries driver
cpuidle: using governor ladder
cpuidle: using governor menu
sdhci: Secure Digital Host Controller Interface driver
sdhci: Copyright(c) Pierre Ossman
mmc0: mvsdio driver initialized, lacking card detect (fall back to polling)
Registered led device: plug:green:health
mv_xor_shared mv_xor_shared.0: Marvell shared XOR driver
mv_xor_shared mv_xor_shared.1: Marvell shared XOR driver
mv_xor mv_xor.0: Marvell XOR: ( xor cpy )
mv_xor mv_xor.1: Marvell XOR: ( xor fill cpy )
mv_xor mv_xor.2: Marvell XOR: ( xor cpy )
mmc0: host does not support reading read-only switch. assuming write-enable.
mmc0: new high speed SDHC card at address 63ae
mmcblk0: mmc0:63ae SD16G 15.1 GiB
 mmcblk0: p1 p2
mv_xor mv_xor.3: Marvell XOR: ( xor fill cpy )
usbcore: registered new interface driver usbhid
usbhid: v2.6:USB HID core driver
oprofile: using timer interrupt.
TCP cubic registered
NET: Registered protocol family 17
RPC: Registered udp transport module.
RPC: Registered tcp transport module.
lib80211: common routines for IEEE802.11 drivers
rtc-mv rtc-mv: setting system clock to 2009-08-20 08:35:12 UTC (1250757312)
RAMDISK: gzip image found at block 0
VFS: Mounted root (ext2 filesystem) on device 1:0.
Freeing init memory: 140K
usb 1-1: new high speed USB device using orion-ehci and address 2



*****************************************************************
 sheevaplug installer version 1.0

**** PRESS ENTER TO STOP THE COUNT DOWN
**** Will flash tarball from USB disk to SD card /dev/mmcblkp0
**** This will wipe out EVERYTHING on the attached SD card
**** Will wait 10 seconds before the flash.


usb 1-1: configuration #1 chosen from 1 choice
scsi0 : SCSI emulation for USB Mass Storage devices
scsi 0:0:0:0: Direct-Access     LEXAR    JUMPDRIVE SPORT  3000 PQ: 0 ANSI: 0 CCS
sd 0:0:0:0: Attached scsi generic sg0 type 0
sd 0:0:0:0: [sda] 1014784 512-byte hardware sectors: (519 MB/495 MiB)
sd 0:0:0:0: [sda] Write Protect is off
sd 0:0:0:0: [sda] Assuming drive cache: write through
sd 0:0:0:0: [sda] Assuming drive cache: write through
 sda: sda1
sd 0:0:0:0: [sda] Attached SCSI removable disk

  d. format the MMC card with EXT2/3 partitions

**** Removing MMC partitions

The number of cylinders for this disk is set to 495616.
There is nothing wrong with that, but this is larger than 1024,
and could in certain setups cause problems with:
1) software that runs at boot time (e.g., old versions of LILO)
2) booting and partitioning software from other OSs
   (e.g., DOS FDISK, OS/2 FDISK)

Command (m for help): Building a new DOS disklabel. Changes will remain in memor                                                                          y only,
until you decide to write them. After that the previous content
won't be recoverable.


The number of cylinders for this disk is set to 495616.
There is nothing wrong with that, but this is larger than 1024,
and could in certain setups cause problems with:
1) software that runs at boot time (e.g., old versions of LILO)
2) booting and partitioning software from other OSs
   (e.g., DOS FDISK, OS/2 FDISK)

Command (m for help): The partition table has been altered!

Calling ioctl() to re-read partition table
 mmcblk0:
**** Mounting /dev/sda1
**** Creating /dev/mmcblk0p1 & /dev/mmcblk0p2 partitions

The number of cylinders for this disk is set to 495616.
There is nothing wrong with that, but this is larger than 1024,
and could in certain setups cause problems with:
1) software that runs at boot time (e.g., old versions of LILO)
2) booting and partitioning software from other OSs
   (e.g., DOS FDISK, OS/2 FDISK)

Command (m for help): Command action
   e   extended
   p   primary partition (1-4)
Partition number (1-4): First cylinder (1-495616, default 1): Using default valu                                                                          e 1
Last cylinder or +size or +sizeM or +sizeK (1-495616, default 495616):
Command (m for help): Command action
   e   extended
   p   primary partition (1-4)
Partition number (1-4): First cylinder (124-495616, default 124): Using default                                                                           value 124
Last cylinder or +size or +sizeM or +sizeK (124-495616, default 495616): Using d                                                                          efault value 495616

Command (m for help): The partition table has been altered!

Calling ioctl() to re-read partition table
 mmcblk0: p1 p2
**** Making EXT2 partition on /dev/mmcblk0p1 for uImage
mke2fs 1.41.4 (27-Jan-2009)
Filesystem label=shvplug-uImage
OS type: Linux
Block size=1024 (log=0)
Fragment size=1024 (log=0)
984 inodes, 3928 blocks
196 blocks (4.99%) reserved for the super user
First data block=1
Maximum filesystem blocks=4194304
1 block group
8192 blocks per group, 8192 fragments per group
984 inodes per group

Writing inode tables: done
Writing superblocks and filesystem accounting information: done

This filesystem will be automatically checked every 21 mounts or
180 days, whichever comes first.  Use tune2fs -c or -i to override.
**** Making EXT3 partition on /dev/mmcblk0p2 for rootfs
mke2fs 1.41.4 (27-Jan-2009)
Filesystem label=shvplug-rootfs
OS type: Linux
Block size=4096 (log=2)
Fragment size=4096 (log=2)
991232 inodes, 3963944 blocks
198197 blocks (5.00%) reserved for the super user
First data block=0
Maximum filesystem blocks=4060086272
121 block groups
32768 blocks per group, 32768 fragments per group
8192 inodes per group
Superblock backups stored on blocks:
        32768, 98304, 163840, 229376, 294912, 819200, 884736, 1605632, 2654208

Writing inode tables: done
Creating journal (32768 blocks): done
Writing superblocks and filesystem accounting information: done

This filesystem will be automatically checked every 38 mounts or
180 days, whichever comes first.  Use tune2fs -c or -i to override.
**** Mounting /dev/mmcblk0p2
kjournald starting.  Commit interval 5 seconds
EXT3 FS on mmcblk0p2, internal journal
EXT3-fs: mounted filesystem with writeback data mode.

  e. Copy uImage, rootfs.tar.gz and modules.tar.gz from the USB DOK to the MMC partition

**** Copying root filesystem. This will take few minutes
**** done.
**** Copying kernel modules
**** done.
**** Doing sync. This will take some time
**** Mounting /dev/mmcblk0p1
**** Copying kernel
**** done.
**** Doing sync. This will take some time
**** done. Rebooting...
The system is going down NOW!
Sent SIGTERM to all processes
Sent SIGKILL to all processes
Requesting system reboot
Restarting system.

  f. system is ready, MMC burned with rootfs and uImage



NAND installation -
-----------------
1. Host Linux PC -

ubuntu:~/work/plug/sheevaplug-installer-v1.0$ sudo php runme.php nand

 ****   exec(modprobe ftdi_sio vendor=0x9e88 product=0x9e8f)
 ****   Preparing environment variables file ...
reading uboot/uboot-env/uboot-dflt.txt
uboot/uboot-env/fw_setenv baudrate 115200
CRC read error on uboot-env.bin: Success
uboot/uboot-env/fw_setenv loads_echo 0
uboot/uboot-env/fw_setenv ipaddr 10.4.50.165
uboot/uboot-env/fw_setenv serverip 10.4.50.5
uboot/uboot-env/fw_setenv rootpath /mnt/ARM_FS/
uboot/uboot-env/fw_setenv netmask 255.255.255.0
uboot/uboot-env/fw_setenv stdin serial
uboot/uboot-env/fw_setenv stdout serial
uboot/uboot-env/fw_setenv stderr serial
uboot/uboot-env/fw_setenv console console=ttyS0,115200 mtdparts=nand_mtd:0xc0000@0\(uboot\)ro,0x1ff00000@0x100000\(root\)
uboot/uboot-env/fw_setenv mainlineLinux no
uboot/uboot-env/fw_setenv CASset min
uboot/uboot-env/fw_setenv enaMonExt no
uboot/uboot-env/fw_setenv enaCpuStream no
uboot/uboot-env/fw_setenv enaWrAllo no
uboot/uboot-env/fw_setenv pexMode RC
uboot/uboot-env/fw_setenv disL2Cache no
uboot/uboot-env/fw_setenv setL2CacheWT yes
uboot/uboot-env/fw_setenv disL2Prefetch yes
uboot/uboot-env/fw_setenv enaICPref yes
uboot/uboot-env/fw_setenv enaDCPref yes
uboot/uboot-env/fw_setenv sata_dma_mode yes
uboot/uboot-env/fw_setenv MALLOC_len 1
uboot/uboot-env/fw_setenv ethprime egiga0
uboot/uboot-env/fw_setenv netbsd_en no
uboot/uboot-env/fw_setenv vxworks_en no
uboot/uboot-env/fw_setenv bootargs_root root=/dev/nfs rw
uboot/uboot-env/fw_setenv bootargs_end :::DB88FXX81:eth0:none
uboot/uboot-env/fw_setenv image_name uImage
uboot/uboot-env/fw_setenv bootcmd tftpboot 0x2000000 \$\(image_name\)\;setenv bootargs \$\(console\) \$\(bootargs_root\) nfsroot=\$\(serverip\):\$\(rootpath\) ip=\$\(ipaddr\):\$\(serverip\)\$\(bootargs_end\) \$\(mvNetConfig\) \$\(mvPhoneConfig\)\;  bootm 0x2000000\;
uboot/uboot-env/fw_setenv standalone fsload 0x2000000 \$\(image_name\)\;setenv bootargs \$\(console\) root=/dev/mtdblock0 rw ip=\$\(ipaddr\):\$\(serverip\)\$\(bootargs_end\) \$\(mvPhoneConfig\)\; bootm 0x2000000\;
uboot/uboot-env/fw_setenv bootdelay 3
uboot/uboot-env/fw_setenv disaMvPnp no
uboot/uboot-env/fw_setenv ethaddr 00:50:43:4e:2a:14
uboot/uboot-env/fw_setenv ethmtu 1500
uboot/uboot-env/fw_setenv mvPhoneConfig mv_phone_config=dev0:fxs,dev1:fxs
uboot/uboot-env/fw_setenv mvNetConfig mv_net_config=\(00:11:88:0f:62:81,0:1:2:3\),mtu=1500
uboot/uboot-env/fw_setenv usb0Mode host
uboot/uboot-env/fw_setenv yuk_ethaddr 00:00:00:EE:51:81
uboot/uboot-env/fw_setenv nandEcc 1bit
uboot/uboot-env/fw_setenv netretry no
uboot/uboot-env/fw_setenv rcvrip 169.254.100.100
uboot/uboot-env/fw_setenv loadaddr 0x02000000
uboot/uboot-env/fw_setenv autoload no
uboot/uboot-env/fw_setenv enaAutoRecovery yes
uboot/uboot-env/fw_setenv ethact egiga0
reading uboot/uboot-env/uboot-nand-custom.txt
uboot/uboot-env/fw_setenv bootargs_root ubi.mtd=1 root=ubi0:rootfs rootfstype=ubifs
uboot/uboot-env/fw_setenv mtdpartitions mtdparts=orion_nand:0x400000@0x100000\(uImage\),0x1fb00000@0x500000\(rootfs\)
uboot/uboot-env/fw_setenv ethaddr 00:50:43:01:c1:e6
uboot/uboot-env/fw_setenv real_bootcmd setenv bootargs \$\(bootargs_console\) \$\(mtdpartitions\) \$\(bootargs_root\)\; nand read.e 0x00800000 0x00100000 0x00400000\; bootm 0x00800000
uboot/uboot-env/fw_setenv bootargs_console console=ttyS0,115200
uboot/uboot-env/fw_setenv bootcmd run recover1
uboot/uboot-env/fw_setenv recover1 setenv mainlineLinux yes\; setenv arcNumber 2097\; setenv bootcmd run recover2\; saveenv\; reset
uboot/uboot-env/fw_setenv recover2 run recover3\; setenv bootcmd \$\(real_bootcmd\)\; saveenv\; setenv bootargs \$\(bootargs_console\) \$\(mtdpartitions\) root=/dev/ram0 rw ramdisk=0x01100000,8M install_type=nand\; bootm 0x00800000 0x01100000
uboot/uboot-env/fw_setenv recover3 run recover4\; nand erase clean 0x00100000 0x00400000\; nand write.e 0x00800000 0x00100000 0x00400000
uboot/uboot-env/fw_setenv recover4 usb start\; fatload usb 0 0x00800000 uImage\; fatload usb 0 0x01100000 initrd

 ****   Burning uboot and environment variables ... This will take few minutes ...
Open On-Chip Debugger 0.2.0 (2009-07-26-14:56) Release
$URL: http://svn.berlios.de/svnroot/repos/openocd/tags/openocd-0.2.0/src/openocd.c $
For bug reports, read http://svn.berlios.de/svnroot/repos/openocd/trunk/BUGS
2000 kHz
jtag_nsrst_delay: 200
jtag_ntrst_delay: 200
dcc downloads are enabled
Info : JTAG tap: feroceon.cpu tap/device found: 0x20a023d3 (mfg: 0x1e9, part: 0x0a02, ver: 0x2)
Info : JTAG Tap/device matched
target state: halted
target halted in ARM state due to debug-request, current mode: Supervisor
cpsr: 0x000000d3 pc: 0xffff0000
MMU: disabled, D-Cache: disabled, I-Cache: disabled
0 0 1 0: 00052078
NAND flash device 'NAND 512MiB 3,3V 8-bit' found
successfully erased blocks 5 to 6 on NAND flash device 'NAND 512MiB 3,3V 8-bit'
wrote file uboot-env.bin to NAND flash 0 up to offset 0x000c0000 in 19.372635s
target state: halted
target halted in ARM state due to debug-request, current mode: Supervisor
cpsr: 0x000000d3 pc: 0xffff0000
MMU: disabled, D-Cache: disabled, I-Cache: disabled
0 0 1 0: 00052078
NAND flash device 'NAND 512MiB 3,3V 8-bit' found
successfully erased blocks 0 to 4 on NAND flash device 'NAND 512MiB 3,3V 8-bit'
wrote file uboot.bin to NAND flash 0 up to offset 0x00074000 in 69.866272s

 ****   U-boot should be up and running now. Open your console ...

2. Sheevalug console -
  a. Burn uboot & environment params to flash
         __  __                      _ _
        |  \/  | __ _ _ ____   _____| | |
        | |\/| |/ _` | '__\ \ / / _ \ | |
        | |  | | (_| | |   \ V /  __/ | |
        |_|  |_|\__,_|_|    \_/ \___|_|_|
 _   _     ____              _
| | | |   | __ )  ___   ___ | |_
| | | |___|  _ \ / _ \ / _ \| __|
| |_| |___| |_) | (_) | (_) | |_
 \___/    |____/ \___/ \___/ \__|
 ** MARVELL BOARD: SHEEVA PLUG LE

U-Boot 1.1.4 (Jul 19 2009 - 16:03:28) Marvell version: 3.4.19

U-Boot code: 00600000 -> 0067FFF0  BSS: -> 006CFB00

Soc: 88F6281 A0 (DDR2)
CPU running @ 1200Mhz L2 running @ 400Mhz
SysClock = 400Mhz , TClock = 200Mhz

DRAM CAS Latency = 5 tRP = 5 tRAS = 18 tRCD=6
DRAM CS[0] base 0x00000000   size 256MB
DRAM CS[1] base 0x10000000   size 256MB
DRAM Total size 512MB  16bit width
Addresses 8M - 0M are saved for the U-Boot usage.
Mem malloc Initialization (8M - 7M): Done
NAND:512 MB
Flash:  0 kB

CPU : Marvell Feroceon (Rev 1)

Streaming disabled
Write allocate disabled


USB 0: host mode
PEX 0: interface detected no Link.
Net:   egiga0 [PRIME], egiga1
Hit any key to stop autoboot:  0
Saving Environment to NAND...
Erasing Nand...Writing to Nand... done

  b. Load uImage and initrd from USB to RAM
         __  __                      _ _
        |  \/  | __ _ _ ____   _____| | |
        | |\/| |/ _` | '__\ \ / / _ \ | |
        | |  | | (_| | |   \ V /  __/ | |
        |_|  |_|\__,_|_|    \_/ \___|_|_|
 _   _     ____              _
| | | |   | __ )  ___   ___ | |_
| | | |___|  _ \ / _ \ / _ \| __|
| |_| |___| |_) | (_) | (_) | |_
 \___/    |____/ \___/ \___/ \__|
 ** MARVELL BOARD: SHEEVA PLUG LE

U-Boot 1.1.4 (Jul 19 2009 - 16:03:28) Marvell version: 3.4.19

U-Boot code: 00600000 -> 0067FFF0  BSS: -> 006CFB00

Soc: 88F6281 A0 (DDR2)
CPU running @ 1200Mhz L2 running @ 400Mhz
SysClock = 400Mhz , TClock = 200Mhz

DRAM CAS Latency = 5 tRP = 5 tRAS = 18 tRCD=6
DRAM CS[0] base 0x00000000   size 256MB
DRAM CS[1] base 0x10000000   size 256MB
DRAM Total size 512MB  16bit width
Addresses 8M - 0M are saved for the U-Boot usage.
Mem malloc Initialization (8M - 7M): Done
NAND:512 MB
Flash:  0 kB

CPU : Marvell Feroceon (Rev 1)

Streaming disabled
Write allocate disabled


USB 0: host mode
PEX 0: interface detected no Link.
Net:   egiga0 [PRIME], egiga1
Hit any key to stop autoboot:  0
(Re)start USB...
USB:   scanning bus for devices... 2 USB Device(s) found
       scanning bus for storage devices... 1 Storage Device(s) found
reading uImage
...............................................................................................................................................................................................................................................................

2620504 bytes read
reading initrd
.....................................................................................................................................................................................................................................................................................................................................

3331682 bytes read

NAND erase: device 0 offset 0x100000, size 0x400000
Erasing at 0x4e0000 -- 100% complete. Cleanmarker written at 0x4e0000.
OK

NAND write: device 0 offset 0x100000, size 0x400000

Writing data at 0x4ff800 -- 100% complete.
 4194304 bytes written: OK
Saving Environment to NAND...
Erasing Nand...Writing to Nand... done

  c. Run from RAM based uImage+initrd

## Booting image at 00800000 ...
   Image Name:   Linux-2.6.30.2
   Created:      2009-07-23   1:53:36 UTC
   Image Type:   ARM Linux Kernel Image (uncompressed)
   Data Size:    2620440 Bytes =  2.5 MB
   Load Address: 00008000
   Entry Point:  00008000
   Verifying Checksum ... OK
OK
## Loading Ramdisk Image at 01100000 ...
   Image Name:   initrd.ext2.gz
   Created:      2009-08-20   7:06:34 UTC
   Image Type:   ARM Linux RAMDisk Image (gzip compressed)
   Data Size:    3331618 Bytes =  3.2 MB
   Load Address: 00000000
   Entry Point:  00000000
   Verifying Checksum ... OK

Starting kernel ...

Uncompressing Linux.......................................................................................................................................................................... done, booting the kernel.
Linux version 2.6.30.2 (kelly@speedy) (gcc version 4.2.1) #11 PREEMPT Wed Jul 22 19:53:31 MDT 2009
CPU: Feroceon 88FR131 [56251311] revision 1 (ARMv5TE), cr=00053977
CPU: VIVT data cache, VIVT instruction cache
Machine: Marvell SheevaPlug Reference Board
Memory policy: ECC disabled, Data cache writeback
Built 1 zonelists in Zone order, mobility grouping on.  Total pages: 130048
Kernel command line: console=ttyS0,115200 mtdparts=orion_nand:0x400000@0x100000(uImage),0x1fb00000@0x500000(rootfs) root=/dev/ram0 rw ramdisk=0x01100000,8M install_type=nand
NR_IRQS:114
PID hash table entries: 2048 (order: 11, 8192 bytes)
Console: colour dummy device 80x30
Dentry cache hash table entries: 65536 (order: 6, 262144 bytes)
Inode-cache hash table entries: 32768 (order: 5, 131072 bytes)
Memory: 256MB 256MB = 512MB total
Memory: 510080KB available (4728K code, 998K data, 140K init, 0K highmem)
SLUB: Genslabs=11, HWalign=32, Order=0-3, MinObjects=0, CPUs=1, Nodes=1
Calibrating delay loop... 1192.75 BogoMIPS (lpj=5963776)
Mount-cache hash table entries: 512
CPU: Testing write buffer coherency: ok
net_namespace: 980 bytes
NET: Registered protocol family 16
Kirkwood: MV88F6281-A0, TCLK=200000000.
Feroceon L2: Enabling L2
Feroceon L2: Cache support initialised.
Kirkwood: Gating clock using mask 0x1ac224
bio: create slab <bio-0> at 0
SCSI subsystem initialized
usbcore: registered new interface driver usbfs
usbcore: registered new interface driver hub
usbcore: registered new device driver usb
cfg80211: Using static regulatory domain info
cfg80211: Regulatory domain: US
        (start_freq - end_freq @ bandwidth), (max_antenna_gain, max_eirp)
        (2402000 KHz - 2472000 KHz @ 40000 KHz), (600 mBi, 2700 mBm)
        (5170000 KHz - 5190000 KHz @ 40000 KHz), (600 mBi, 2300 mBm)
        (5190000 KHz - 5210000 KHz @ 40000 KHz), (600 mBi, 2300 mBm)
        (5210000 KHz - 5230000 KHz @ 40000 KHz), (600 mBi, 2300 mBm)
        (5230000 KHz - 5330000 KHz @ 40000 KHz), (600 mBi, 2300 mBm)
        (5735000 KHz - 5835000 KHz @ 40000 KHz), (600 mBi, 3000 mBm)
cfg80211: Calling CRDA for country: US
NET: Registered protocol family 2
IP route cache hash table entries: 16384 (order: 4, 65536 bytes)
TCP established hash table entries: 65536 (order: 7, 524288 bytes)
TCP bind hash table entries: 65536 (order: 6, 262144 bytes)
TCP: Hash tables configured (established 65536 bind 65536)
TCP reno registered
NET: Registered protocol family 1
Trying to unpack rootfs image as initramfs...
rootfs image is not initramfs (no cpio magic); looks like an initrd
Freeing initrd memory: 3252K
JFFS2 version 2.2. (NAND) © 2001-2006 Red Hat, Inc.
JFS: nTxBlock = 4012, nTxLock = 32100
msgmni has been set to 1003
alg: No test for stdrng (krng)
io scheduler noop registered
io scheduler anticipatory registered
io scheduler deadline registered
io scheduler cfq registered (default)
Serial: 8250/16550 driver, 2 ports, IRQ sharing disabled
serial8250.0: ttyS0 at MMIO 0xf1012000 (irq = 33) is a 16550A
console [ttyS0] enabled
brd: module loaded
loop: module loaded
Driver 'sd' needs updating - please use bus_type methods
MV-643xx 10/100/1000 ethernet driver version 1.4
mv643xx_eth smi: probed
net eth0: port 0 with MAC address 00:50:43:01:c1:e6
libertas_sdio: Libertas SDIO driver
libertas_sdio: Copyright Pierre Ossman
NAND device: Manufacturer ID: 0xad, Chip ID: 0xdc (Hynix NAND 512MiB 3,3V 8-bit)
Scanning device for bad blocks
Bad eraseblock 385 at 0x000003020000
Bad eraseblock 393 at 0x000003120000
Bad eraseblock 401 at 0x000003220000
Bad eraseblock 409 at 0x000003320000
Bad eraseblock 417 at 0x000003420000
Bad eraseblock 425 at 0x000003520000
Bad eraseblock 433 at 0x000003620000
Bad eraseblock 441 at 0x000003720000
Bad eraseblock 532 at 0x000004280000
Bad eraseblock 2433 at 0x000013020000
Bad eraseblock 2441 at 0x000013120000
Bad eraseblock 2449 at 0x000013220000
Bad eraseblock 2457 at 0x000013320000
Bad eraseblock 2465 at 0x000013420000
Bad eraseblock 2473 at 0x000013520000
Bad eraseblock 2481 at 0x000013620000
Bad eraseblock 2489 at 0x000013720000
Bad eraseblock 3962 at 0x00001ef40000
Bad eraseblock 3966 at 0x00001efc0000
2 cmdlinepart partitions found on MTD device orion_nand
Creating 2 MTD partitions on "orion_nand":
0x000000100000-0x000000500000 : "uImage"
0x000000500000-0x000020000000 : "rootfs"
ehci_hcd: USB 2.0 'Enhanced' Host Controller (EHCI) Driver
orion-ehci orion-ehci.0: Marvell Orion EHCI
orion-ehci orion-ehci.0: new USB bus registered, assigned bus number 1
orion-ehci orion-ehci.0: irq 19, io mem 0xf1050000
orion-ehci orion-ehci.0: USB 2.0 started, EHCI 1.00
usb usb1: configuration #1 chosen from 1 choice
hub 1-0:1.0: USB hub found
hub 1-0:1.0: 1 port detected
Initializing USB Mass Storage driver...
usbcore: registered new interface driver usb-storage
USB Mass Storage support registered.
usbcore: registered new interface driver ums-datafab
usbcore: registered new interface driver ums-freecom
usbcore: registered new interface driver ums-jumpshot
usbcore: registered new interface driver ums-sddr09
usbcore: registered new interface driver ums-sddr55
mice: PS/2 mouse device common for all mice
rtc-mv rtc-mv: rtc core: registered rtc-mv as rtc0
i2c /dev entries driver
cpuidle: using governor ladder
cpuidle: using governor menu
sdhci: Secure Digital Host Controller Interface driver
sdhci: Copyright(c) Pierre Ossman
mmc0: mvsdio driver initialized, lacking card detect (fall back to polling)
Registered led device: plug:green:health
mv_xor_shared mv_xor_shared.0: Marvell shared XOR driver
mv_xor_shared mv_xor_shared.1: Marvell shared XOR driver
mv_xor mv_xor.0: Marvell XOR: ( xor cpy )
mv_xor mv_xor.1: Marvell XOR: ( xor fill cpy )
mv_xor mv_xor.2: Marvell XOR: ( xor cpy )
mmc0: host does not support reading read-only switch. assuming write-enable.
mmc0: new high speed SDHC card at address 63ae
mmcblk0: mmc0:63ae SD16G 15.1 GiB
 mmcblk0: p1 p2
mv_xor mv_xor.3: Marvell XOR: ( xor fill cpy )
usbcore: registered new interface driver usbhid
usbhid: v2.6:USB HID core driver
oprofile: using timer interrupt.
TCP cubic registered
NET: Registered protocol family 17
RPC: Registered udp transport module.
RPC: Registered tcp transport module.
lib80211: common routines for IEEE802.11 drivers
rtc-mv rtc-mv: setting system clock to 2009-08-20 09:09:49 UTC (1250759389)
RAMDISK: gzip image found at block 0
VFS: Mounted root (ext2 filesystem) on device 1:0.
Freeing init memory: 140K
usb 1-1: new high speed USB device using orion-ehci and address 2


*****************************************************************
 sheevaplug installer version 1.0

**** PRESS ENTER TO STOP THE COUNT DOWN
**** Will flash tarball from USB disk to internal NAND /dev/mtdblock1
**** This will wipe out EVERYTHING on the internal NAND flash
**** Will wait 10 seconds before the flash.


usb 1-1: configuration #1 chosen from 1 choice
scsi0 : SCSI emulation for USB Mass Storage devices
scsi 0:0:0:0: Direct-Access     LEXAR    JUMPDRIVE SPORT  3000 PQ: 0 ANSI: 0 CCS
sd 0:0:0:0: Attached scsi generic sg0 type 0
sd 0:0:0:0: [sda] 1014784 512-byte hardware sectors: (519 MB/495 MiB)
sd 0:0:0:0: [sda] Write Protect is off
sd 0:0:0:0: [sda] Assuming drive cache: write through
sd 0:0:0:0: [sda] Assuming drive cache: write through
 sda: sda1
sd 0:0:0:0: [sda] Attached SCSI removable disk

  d. format the NAND flash with UBIFS partition

**** Erasing all flash
ubiformat: mtd1 (nand), size 531628032 bytes (507.0 MiB), 4056 eraseblocks of 131072 bytes (128.0 KiB), min. I/O size 2048 bytes
libscan: scanning eraseblock 4055 -- 100 % complete
ubiformat: 4037 eraseblocks have valid erase counter, mean value is 58
ubiformat: bad eraseblocks: 345, 353, 361, 369, 377, 385, 393, 401, 492, 2393, 2401, 2409, 2417, 2425, 2433, 2441, 2449, 3922, 3926
ubUBI: attaching mtd1 to ubi0ock 4022 -- 99 % complete
iformat: formattUBI: physical eraseblock size:   131072 bytes (128 KiB)
ing eraseblock 4UBI: logical eraseblock size:    129024 bytes
023 -- 99 % compUBI: smallest flash I/O unit:    2048
ubiformatUBI: sub-page size:              512
: formatting eraUBI: VID header offset:          512 (aligned 512)
seblock 4024 -- UBI: data offset:                2048
ubiformat: formatting eraseblock 4055 -- 100 % complete
UBI: attached mtd1 to ubi0
UBI: MTD device name:            "rootfs"
UBI: MTD device size:            507 MiB
UBI: number of good PEBs:        4037
UBI: number of bad PEBs:         19
UBI: max. allowed volumes:       128
UBI: wear-leveling threshold:    4096
UBI: number of internal volumes: 1
UBI: number of user volumes:     0
UBI: available PEBs:             3993
UBI: total number of reserved PEBs: 44
UBI: number of PEBs reserved for bad PEB handling: 40
UBI: max/mean erase counter: 173/59
UBI: background thread "ubi_bgt0d" started, PID 557
UBI device number 0, total 4037 LEBs (520869888 bytes, 496.7 MiB), available 3993 LEBs (515192832 bytes, 491.3 MiB), LEB size 129024 bytes (126.0 KiB)
Set volume size to 515192832
Volume ID 0, size 3993 LEBs (515192832 bytes, 491.3 MiB), LEB size 129024 bytes (126.0 KiB), dynamic, name "rootfs", alignment 1
**** Mounting /dev/sda1
UBIFS: default file-system created
UBIFS: mounted UBI device 0, volume 0, name "rootfs"
UBIFS: file system size:   513257472 bytes (501228 KiB, 489 MiB, 3978 LEBs)
UBIFS: journal size:       25675776 bytes (25074 KiB, 24 MiB, 199 LEBs)
UBIFS: media format:       w4/r0 (latest is w4/r0)
UBIFS: default compressor: lzo
UBIFS: reserved for root:  4952683 bytes (4836 KiB)

  e. Copy uImage, rootfs.tar.gz and modules.tar.gz from the USB DOK to UBIFS

**** Copying root filesystem. This will take few minutes
**** done.
**** Copying kernel modules
**** done.
**** Copying kernel
**** done.
**** Doing sync. This will take some time
**** done. Rebooting...
UBIFS: un-mount UBI device 0, volume 0
The system is going down NOW!
Sent SIGTERM to all processes
Sent SIGKILL to all processes
Requesting system reboot
Restarting system.

  f. system is ready, NAND burned with rootfs and uImage
