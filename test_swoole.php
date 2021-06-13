<?php
ini_set('memory_limit','2048M');

require __DIR__ . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

$generator = new \LeadGenerator\Generator();

$queue = [];
$generator->generateLeads(10000, function (\LeadGenerator\Lead $lead) use (&$queue) {
    $queue[] = $lead;
});

define('LOG_FILE_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'log.log');

@unlink(LOG_FILE_PATH);

function asyncCall(Swoole\Process $process){
    $lead = unserialize($process->read());
    sleep(2);
    file_put_contents(LOG_FILE_PATH, implode('|', [$lead->id, $lead->categoryName, time()]) . "\n", FILE_APPEND);
    return true;
}

$start = microtime(true);
while (count($queue) > 0) {
    for ($i = 0; $i < 500; $i++) {
        $lead    = array_shift($queue);
        $process = new Swoole\Process('asyncCall');
        $process->write(serialize($lead));
        $pid           = $process->start();
        $workers[$pid] = $process;
    }
}
$finish = microtime(true);
$delta = $finish - $start;
echo $delta / 60 . ' min.';
