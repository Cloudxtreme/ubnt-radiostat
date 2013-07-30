<?php

/* UBNT-radiostat.php
 * 
 * Cheap PHP knockoff attempt replicating what ifstat -s does,
 * while using a 15 second delay by default to deal with the 
 * fact that the counters only update every 10-12s on UBNT
 * devices.
 * 
 * Copyright (c) September 11, 2012
 * by Theo Baschak <theo@voinetworks.ca>
 * for Voi Network Solutions, Inc
 * 311-955 Portage Ave
 * Winnipeg, MB  R3G0P9
*/

snmp_set_valueretrieval(SNMP_VALUE_PLAIN);

if(isset($argv[1]))
{
  $ubnt = $argv[1];
  if(isset($argv[2]))
    $community = $argv[2];

  // initial gets
  $myname = @snmpget($ubnt, $community, '.1.3.6.1.2.1.1.5.0', 2000000, 2);
  $info = @snmpwalk($ubnt, $community, '.1.3.6.1.4.1.14988', 2000000, 2);

  $oldinfo = $info;
  printf("Stats for %s on %s (%.3F GHz)\n", $myname, $info[3], ($info[5]/1000));

  set_time_limit(0);
  for($i = 0; $i < 30000; $i++)
  {
    sleep(17);
    $info = @snmpwalk($ubnt, $community, '.1.3.6.1.4.1.14988', 2000000, 2);
    printf("Traffic: RX/TX: %.2F/%.2F(Mbps)\t\tSignal: %d\tWLAN Rate RX/TX: %d/%d(Mbps)\n", (($info[8]-$oldinfo[8])/17)*(8/1048576), (($info[7]-$oldinfo[7])/17)*(8/1048576), $info[2], ($info[1]/1000000), ($info[0]/1000000));
    $oldinfo = $info;
  }
}
else
{
  echo "Usage\n\n" . $argv[0] . " <ip> [snmpcomm]\n\tRuns stats on ubnt radio at <ip>\n\twith [snmpcomm] community\n\n";
}

?>
