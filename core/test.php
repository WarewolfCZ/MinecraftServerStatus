<?php
/**
 * @author WarewolfCZ
 */
error_reporting(E_ALL);
ini_set("display_errors", 1);

require("test/TestMCPacketVarInt.php");
require("test/TestMCPacketString.php");
require("test/TestMCPacketShort.php");
require("test/TestMCPacketLong.php");

require_once(__DIR__.'/MCServer.php');

$server = new MCServer("77.93.202.250", 25565);

echo get_class($server) . "<br />\n";
try {
   printf("Ping latency: %1.3f ms<br />\n", $server->ping());
   $status = $server->status();
   printf("Slots: %d<br />\n", $status->getMaxPlayers());
   printf("Version: %s<br />\n", $status->getVersion());
   printf("Description: %s<br />\n", $status->getDescription());
} catch (MCPingException $e) {
    echo '<br/>'. $e->errorMessage();
} catch (MCConnException $e) {
    echo '<br/>'. $e->errorMessage();
}
