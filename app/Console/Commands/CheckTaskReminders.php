<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\TaskController;

class CheckTaskReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:check-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check reminder untuk tiap task yang belum selesai';

    /**
     * Execute the console command.
     */
    public function handle(TaskController $taskController)
    {
        $response = $taskController->checkAndSendReminders();
        $this->info('Reminder check completed');
        return 0;
    }
}
