<?php

namespace App\Http\Controllers;

use App\Models\MessageLog;
use App\Models\Task;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use OpenApi\Annotations as OA;

class TaskController extends Controller
{

    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * @OA\Get(
     *     path="/tasks",
     *     tags={"Tasks"},
     *     summary="Get all tasks",
     *     description="Fetches all tasks of the authenticated user",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of tasks",
     *         
     *     )
     * )
     */
    public function index()
    {
        $tasks = Task::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get();
        return response()->json($tasks, 200);
    }

/**
 * @OA\Post(
 *     path="/tasks",
 *     tags={"Tasks"},
 *     summary="Create a new task",
 *     description="Adds a new task to the user's list",
 *     security={{"bearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title"},
 *             @OA\Property(property="title", type="string", example="Buy groceries"),
 *             @OA\Property(
 *                 property="due_date",
 *                 type="string",
 *                 format="date-time",
 *                 example="2024-11-01T14:30:00",
 *                 description="The due date and time for the task (ISO 8601 format, optional)"
 *             )
 *         )
 *     ),
 *     @OA\Response(response=201, description="Task created")
 * )
 */

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'due_date' => 'nullable|date_format:Y-m-d H:i:s',
        ]);

        $task = Task::create([
            'title' => $request->title,
            'due_date' => $request->due_date,
            'user_id' => Auth::id(),
            'is_completed' => false,
        ]);

        return response()->json($task, 201);
    }

    /**
     * @OA\Put(
     *     path="/tasks/{id}",
     *     tags={"Tasks"},
     *     summary="Update a task",
     *     description="Modifies the details of an existing task",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Buy milk"),
     *             @OA\Property(property="due_date", type="string", format="date", example="2024-11-02")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Task updated")
     * )
     */

    public function update(Request $request, $id)
    {
        $task = Task::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $task->update($request->only('title', 'reminder', 'due_date'));

        return response()->json($task, 201);
    }

    /**
     * @OA\Patch(
     *     path="/tasks/{id}/complete",
     *     tags={"Tasks"},
     *     summary="Mark task as complete",
     *     description="Marks a specific task as completed",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(response=200, description="Task marked as complete")
     * )
     */
    public function markComplete($id)
    {
        $task = Task::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $task->update(['is_completed' => true]);

        return response()->json(['message' => 'Task marked as complete']);
    }


    /**
     * @OA\Delete(
     *     path="/tasks/{id}",
     *     tags={"Tasks"},
     *     summary="Delete a task",
     *     description="Deletes a task by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(response=200, description="Task deleted")
     * )
     */
    public function destroy($id)
    {
        $task = Task::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $task->delete();

        return response()->json(['message' => 'Task deleted']);
    }


    public function checkAndSendReminders()
    {
        Log::info('ini bukan main');
        try {
            // Get tasks due in the next hour that aren't completed
            $tasks = Task::with('user')
                ->where('is_completed', false)
                ->where('due_date', '>', Carbon::now())
                ->where('due_date', '<=', Carbon::now()->addHour())
                ->whereDoesntHave('messageLogs', function ($query) {
                    $query->where('status', 'sent');
                })
                ->get();

            $results = [];


            foreach ($tasks as $task) {
                if (!$task->user->no_telp) {
                    continue;
                }

                $message = $this->formatReminderMessage($task);
                
                // Log the attempt
                $messageLog = MessageLog::create([
                    'task_id' => $task->id,
                    'status' => 'pending',
                    'message_content' => $message,
                ]);

                // Send the message
                $response = $this->whatsAppService->sendMessage(
                    $task->user->no_telp,
                    $message
                );

                if ($response['success']) {
                    $messageLog->update([
                        'status' => 'sent',
                        'sent_at' => now(),
                    ]);
                    $results[] = [
                        'task_id' => $task->id,
                        'status' => 'success'
                    ];
                } else {
                    $messageLog->update([
                        'status' => 'failed',
                        'error_message' => $response['error'],
                        'retry_count' => $messageLog->retry_count + 1,
                    ]);
                    $results[] = [
                        'task_id' => $task->id,
                        'status' => 'failed',
                        'error' => $response['error']
                    ];
                }
            }
            Log::info($results);
            return response()->json([
                'message' => 'Reminder check completed',
                'results' => $results
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error processing reminders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function formatReminderMessage($task)
    {
        $quotes = [
            "The only way to do great work is to love what you do. - Steve Jobs",
            "Don't watch the clock; do what it does. Keep going. - Sam Levenson",
            "The future depends on what you do today. - Mahatma Gandhi",
        ];

        $randomQuote = $quotes[array_rand($quotes)];
        
        return "Task Reminder!\n" .
               "Task: {$task->title}\n" .
               "Due: " . Carbon::parse($task->due_date)->format('M j, Y g:i A') . "\n\n" .
               "Remember: {$randomQuote}\n\n" .
               "Semangat!";
    }
}
