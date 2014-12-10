<?php

require_once("MCPacket.php");
$result = true;

// test clear
$packet = new MCPacket();
$packet->addVarInt(125);
$packet->clearData();
$value = $packet->readInt();
$result = $result && assert($value == NULL/*, "Value is not null"*/);
$result = $result && assert(strlen($packet->getData()) == 0/*, "Data length is not 0"*/);

// test single long
$packet = new MCPacket();
$packet->addLong(125);
$value = $packet->readLong();
$result = $result && assert($value == 125/*, "Value is not equal to 125"*/);
$result = $result && assert(strlen($packet->getData()) == 4/*, "Data length is not 4"*/);

echo strtoupper(bin2hex($packet->getData())) . "<br />\n";
echo strlen($packet->getData()) . "<br />\n";

if ($result) {
    echo "MCPacket long tests OK<br/>\n";
} else {
    echo "MCPacket long tests failed<br/>\n";
}