<?php
include_once('./DNSToolboxInterface.php');
use RemotelyLiving\PHPDNS\Resolvers;
use RemotelyLiving\PHPDNS\Entities;
class Mx implements DNSToolboxInterface{
  public function getOutput($hostname,$sourceserver){
    $nameserver = new Entities\Hostname($sourceserver);
    $resolver = new Resolvers\Dig(null,null,$nameserver);
    $records = $resolver->getMXRecords($hostname);
    echo json_encode($records,JSON_PRETTY_PRINT);
  }
}
?>