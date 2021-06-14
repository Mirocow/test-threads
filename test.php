<?php
ini_set('memory_limit','2048M');

require __DIR__ . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

define('LOG_FILE_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'log.log');
@unlink(LOG_FILE_PATH);

$event = new \tests\EventCommand();
$event->on('beforeTest', function(\tests\TestMultithreadCommand $command){
    echo 'Test class ' . get_class($command) . ' ';
    return microtime(true);
});
$event->on('afterTest', function(\tests\TestMultithreadCommand $command, $start){
    $finish = microtime(true);
    $delta = $finish - $start;
    echo ' it`s took ' . $delta / 60 . " min. \n";
});

$test = function ($lead){
    sleep(2);
    file_put_contents(LOG_FILE_PATH, implode('|', [$lead->id, $lead->categoryName, time()]) . "\n", FILE_APPEND);
    return true;
};

$commands = \tests\Commands::getInstance();
$commands->threads = 500;
$commands->count = 10000;
$commands->add(new \tests\commands\PcntlComponentCommand($test));
$commands->add(new \tests\commands\SwooleComponentCommand($test));
//$commands->add(new \tests\commands\NoThreadsComponentCommand($test));
$commands->runTests($event);
