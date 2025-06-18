<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateScheduleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:generate {milestone_id}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Генерация расписания по рубежному контролю';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $milestoneId = $this->argument('milestone_id');
        
        $milestone = \App\Models\Milestone::find($milestoneId);

        if (!$milestone) {
            $this->error("Milestone с ID {$milestoneId} не найден.");
            return;
        }

        $this->info("Генерация расписания для РК '{$milestone->name}' с {$milestone->from} по {$milestone->deadline}...");

        $generator = new \App\Services\ScheduleGeneratorService();
        $generator->generateForMilestone($milestone);

        $this->info("Расписание сгенерировано.");
    }

}
