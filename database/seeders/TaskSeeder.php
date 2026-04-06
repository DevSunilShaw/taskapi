<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = ['pending', 'in-progress', 'completed'];

        // Ensure at least 1 user exists
        if (User::count() == 0) {
            User::factory()->count(5)->create();
        }

        $users = User::all();

        foreach ($users as $user) {
            for ($i = 0; $i < 5; $i++) {
                try {
                    Task::create([
                        'user_id'     => $user->id,
                        'title'       => 'Task ' . Str::random(5),
                        'description' => 'This is a seeded task description.',
                        'status'      => $statuses[array_rand($statuses)],
                        'due_date'    => now()->addDays(rand(1, 30)),
                    ]);
                    Log::info("Task created for user {$user->id}");
                } catch (\Exception $e) {
                    Log::error("Failed to create task for user {$user->id}: " . $e->getMessage());
                }
            }
        }
    }
}