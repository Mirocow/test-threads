# Tests using the asynchronous save to file method

<p align="center"><a href="#" target="_blank"><img width="400" src="https://github.com/Mirocow/test-threads/raw/master/forks.png"></a></p>

```php
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
$commands->add(new \tests\commands\NoThreadsComponentCommand($test)); // only 1 threads
$commands->runTests($event);
```

## Tests

* PCNTL
* Swoole

## Run

```bash
$ cd docker
$ docker-compose up -d
$ docker-compose php php test.php

Begin tests 10000 leads write with threads: 500
Test class tests\commands\PcntlComponentCommand  it`s took 0.8094783504804 min.
Test class tests\commands\SwooleComponentCommand  it`s took 2.934555431207 min.
```
