<?php
ini_set('memory_limit','2048M');

require __DIR__ . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

$generator = new \LeadGenerator\Generator();

$queue = [];
$generator->generateLeads(10000, function (\LeadGenerator\Lead $lead) use (&$queue) {
    $queue[] = $lead;
});

define('LOG_FILE_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'log.log');

function asyncCall($lead){
    sleep(2);
    file_put_contents(LOG_FILE_PATH, implode('|', [$lead->id, $lead->categoryName, time()]) . "\n", FILE_APPEND);
    //echo "Done {$lead->id}\n";
    return true;
}

@unlink(LOG_FILE_PATH);

$start = microtime(true);
while(count($queue) > 0) {
    $threads = new \tests\threads\Pcntl($queue);
    $threads->runAsync(500);
}
$finish = microtime(true);
$delta = $finish - $start;
echo $delta / 60 . ' min.';
