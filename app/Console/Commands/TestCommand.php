<?php

namespace App\Console\Commands;

use Alphavel\Framework\Console\Command;

class TestCommand extends Command
{
    protected string $signature = 'test {argument?} {--option}';

    protected string $description = 'Command description';

    public function handle(): int
    {
        $argument = $this->argument('argument');
        $option = $this->option('option');

        $this->info('Command executed!');

        if ($argument) {
            $this->line("Argument: {$argument}");
        }

        if ($option) {
            $this->comment("Option: {$option}");
        }

        // TODO: Implement your logic here

        $this->success('Done!');

        return self::SUCCESS;
    }
}
