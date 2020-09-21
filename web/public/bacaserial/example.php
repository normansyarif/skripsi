<?php
include "php_serial.class.php";

$serial = new PhpSerial();
//this is the port where my Arduino is. Check from the Arduino IDE to see yours!
$serial->deviceSet("/dev/ttyUSB0");
$serial->confBaudRate(9600);
$serial->confParity("none");
$serial->confCharacterLength(8);
$serial->confStopBits(1);
$serial->confFlowControl("none");
$serial->deviceOpen();
$serial->sendMessage("Hello say!");
