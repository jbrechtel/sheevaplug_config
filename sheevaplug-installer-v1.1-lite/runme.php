<?

$WIN32=0;
$openocd_cmd="";

/* find out if we run on Linux/Windows*/
if (strstr($_ENV["OS"], "Windows"))
    $WIN32=1;

if ($argc != 2){
    print_help_message();
    exit(1);
}

$target= $argv[1];

if ($target != "nand" && $target != "mmc")
    die("installer supports installing kernel and filesystem to: mmc, nand\n");

function print_help_message() {
    global $WIN32;

    if ($WIN32==1)
        echo "usage:  runme.exe [ nand | mmc ]\n";
    else
        echo "usage:  php runme.php [ nand | mmc ]\n";
}

$fw_setenv= "uboot/uboot-env/fw_setenv";
if ($WIN32==1){
    /* executable for windows is with exe extention */
    $fw_setenv= str_replace("/","\\",$fw_setenv);
    $fw_setenv.= ".exe";

    /* add the win32 to the process PATH for Cygwin dll */
    $path=getenv("PATH");
    $dllpath= ";".getcwd()."\win32";
    $path.= $dllpath;
    putenv("PATH={$path}");
}
else
{
    if ($_ENV["USER"] != 'root')
        die("You must run this as root\n");

    echo "\n ****	exec(modprobe ftdi_sio vendor=0x9e88 product=0x9e8f)";
    exec("modprobe ftdi_sio vendor=0x9e88 product=0x9e8f", $out, $rc);

    if ($rc)
        die("modprobe ftdi_sio vendor=0x9e88 product=0x9e8f FAILED\n");
}

/* this routine adds uboot environment to uboot-env.bin that resides in CWD
 * according to text file, skipping # and empty lines
 */
function set_uboot_env($infile) {
    global $fw_setenv, $WIN32;

    if ($WIN32==1)
        $infile= str_replace("/","\\",$infile);

    if (!file_exists($infile))
        die("input file is missing: $infile");

    $lines = file($infile, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);

    echo "reading $infile\n";

    for ($i=0; $i<count($lines); $i++) {
        $line=trim($lines[$i]);
        if (strlen($line) == 0)
            continue;

        if (substr($line, 0, 1) == '#')
            continue;

        $cmd= $fw_setenv." ";
        $cmd.= ($WIN32==1) ? $line : escapeshellcmd($line);
        echo "$cmd\n";
        exec($cmd, $out, $rc);
    }
    return 0;
}

function beep ($int_beeps = 1) {
    for ($i = 0; $i < $int_beeps; $i++): $string_beeps .= "\x07"; endfor;
    print $string_beeps; 
}

/* remove the last uboot-env.bin and create an empty one */
if (file_exists("uboot-env.bin")){
    unlink("uboot-env.bin");
    if (file_exists("uboot-env.bin"))
        die("could not delete uboot-env.bin");
}

touch("uboot-env.bin");

echo "\n ****	Preparing environment variables file ...\n";

/* Update environment variable file image */
set_uboot_env("uboot/uboot-env/uboot-dflt.txt");
set_uboot_env("uboot/uboot-env/uboot-{$target}-custom.txt");

/* move the uboot-env.bin to the uboot directory */
if (file_exists("uboot/uboot-env.bin")){
    unlink("uboot/uboot-env.bin");
    if (file_exists("uboot/uboot-env.bin"))
        die("could not delete uboot/uboot-env.bin");
}

rename("uboot-env.bin", "uboot/uboot-env.bin");

echo "\n ****	Burning uboot and environment variables ... This will take few minutes ...\n";
if ($WIN32==1)
    echo " ****	When uboot.bin is written to NAND/SD, Disconnect and connect your mini USB cable\n";

chdir("uboot");
$openocd_cmd= "openocd/openocd";
$openocd_cmd.= ($WIN32==1) ? ".exe" : "";
$openocd_cmd.= " -f openocd/config/board/sheevaplug.cfg";
$openocd_cmd.= " -s openocd/config/";
$openocd_cmd.= " -c init";
$openocd_cmd.= " -c sheevaplug_reflash_uboot_env";
$openocd_cmd.= " -c sheevaplug_reflash_uboot";
$openocd_cmd.= ($WIN32==1) ? " -c init" : " -c exit";

/* replace slash to backslash for windows */
if ($WIN32==1)
    $openocd_cmd= str_replace("/","\\",$openocd_cmd);

unset($rc);
/* Burn u-boot environment variables first, then u-boot */
exec($openocd_cmd, $out, $rc);

/* remove the uboot-env.bin file */
unlink("uboot-env.bin");
chdir("..");

if ($rc) {
    echo <<<EOT
 ****	openocd FAILED
 ****	Is the mini USB cable connected?
 ****	Try powering down, then replugging the Sheevaplug
EOT;
    exit(1);
}

if ($WIN32==1)
    echo "\n ****	Disconnect and connect your mini USB cable";
echo "\n ****	U-boot should be up and running now. Open your console ...\n";
beep(1);

?>
