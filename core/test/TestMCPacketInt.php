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

// test integer
$packet = new MCPacket();
$packet->addVarInt(125);
$value = $packet->readInt();
$result = $result && assert($value == 125/*, "Value is not equal to 125"*/);
$result = $result && assert(strlen($packet->getData()) == 4/*, "Data length is not 4"*/);


// test negative integer
$packet = new MCPacket();
$packet->addVarInt(-125);
$value = $packet->readInt();
//$result = $result && assert($value != -125, "Value is equal to -125");
$result = $result && assert(strlen($packet->getData()) == 4/*, "Data length is not 4"*/);

// test two integers
$packet = new MCPacket();
$packet->addVarInt(55);
$packet->addVarInt(1111);
$value = $packet->readInt();
$result = $result && assert($value == 55/*, "Value is not equal to 55"*/);
$value = $packet->readInt();
$result = $result && assert($value == 1111/*, "Value is not equal to 1111"*/);
$result = $result && assert(strlen($packet->getData()) == 8/*, "Data length is not 8"*/);

// test rewind
$packet = new MCPacket();
$packet->addVarInt(55);
$packet->addVarInt(1111);
$value = $packet->readInt();
$packet->rewind();
$value = $packet->readInt();
$result = $result && assert($value == 55/*, "Value is not equal to 55"*/);


// test reading nonexistent value
$packet = new MCPacket();
$packet->addVarInt(55);
$value = $packet->readInt();
$result = $result && assert($value == 55/*, "Value is not equal to 55"*/);
$value = $packet->readInt();
$result = $result && assert($value == NULL/*, "Value is not NULL"*/);

// test maximum value
$packet = new MCPacket();
$maxval = PHP_INT_MAX; //4294967294; // 0xFF FF FF FF
$packet->addVarInt($maxval);
$value = $packet->readInt();
echo "value: " . $value . "<br/>\n";
$result = $result && assert($value == $maxval/*, "Value ". $value . " is not equal to PHP_INT_MAX (" . $maxval . ")"*/);

//$packet->addVarInt(5);

echo strtoupper(bin2hex($packet->getData())) . "<br />\n";
echo strlen($packet->getData()) . "<br />\n";

if ($result) {
    echo "MCPacket integer tests OK<br/>\n";
} else {
    echo "MCPacket integer tests failed<br/>\n";
}