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

class Threads
{
    private $queue;

    public function __construct(&$queue)
    {
        $this->queue = &$queue;
        $this->forks = [];
    }

    public function runAsync ($forks)
    {
        $lead = array_shift($this->queue);
        $pid   = pcntl_fork();
        if ($pid == -1) {
            die('could not fork');
        }
        else if ($pid) {
            $forks -= 1;
            if ($forks > 0) {
                $this->runAsync($forks);
            }
            pcntl_wait($status);
        }
        else {
            asyncCall($lead);
            posix_kill(getmypid(), SIGKILL);
        }
    }

}

@unlink(LOG_FILE_PATH);

$start = microtime(true);
while(count($queue) > 0) {
    $threads = new Threads($queue);
    $threads->runAsync(500);
}
$finish = microtime(true);
$delta = $finish - $start;
echo $delta / 60 . ' мин.';