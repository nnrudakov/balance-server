<?php

declare(strict_types = 1);

namespace App\Console\Commands;

use Illuminate\Console\Command as BaseCommand;

/**
 * Commands base class.
 *
 * @package    App\Console\Commands
 * @author     Nikolaj Rudakov <nnrudakov@gmail.com>
 * @copyright  2018
 */
abstract class Command extends BaseCommand
{
    /**
     * Command start time.
     *
     * @var float
     */
    protected $startTime;


    /**
     * Actions before command start.
     *
     * @return void
     */
    protected function before(): void
    {
        $this->startTime = microtime(true);
        $this->info(sprintf('Start time: %s.', date('d.m.Y H:i:s')));
    }

    /**
     * Actions after command end.
     *
     * @return void
     */
    protected function after(): void
    {
        $this->info("\n" . 'Task finished.');
        $durationTime = gmdate('H:i:s', (int) (microtime(true) - $this->startTime));
        $this->info(sprintf('Execution time: %s.', $durationTime));
    }
}
