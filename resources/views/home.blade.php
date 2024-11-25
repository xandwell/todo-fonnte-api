@extends('layout')

@section('title', 'To-Do List')

@section('content')
    <h2>Your Tasks</h2>
    <form id="new-task-form">
        <input type="text" name="title" placeholder="New Task" required>
        <input type="date" name="due_date">
        <button type="submit">Add Task</button>
    </form>

    <ul id="task-list"></ul>

    <script>
        const token = localStorage.getItem('token');

        async function fetchTasks() {
            const response = await fetch('/api/tasks', {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const tasks = await response.json();
            renderTasks(tasks);
        }

        function renderTasks(tasks) {
            const taskList = document.getElementById('task-list');
            taskList.innerHTML = '';

            tasks.forEach(task => {
                const li = document.createElement('li');
                li.className = task.is_completed ? 'completed' : '';
                li.innerHTML = `
                    ${task.title} (Due: ${task.due_date || 'N/A'})
                    <button onclick="completeTask(${task.id})">Complete</button>
                    <button onclick="deleteTask(${task.id})">Delete</button>
                `;
                taskList.appendChild(li);
            });
        }

        document.getElementById('new-task-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            await fetch('/api/tasks', {
                method: 'POST',
                headers: { 'Authorization': `Bearer ${token}` },
                body: formData
            });
            fetchTasks();
        });

        async function completeTask(id) {
            await fetch(`/api/tasks/${id}/complete`, {
                method: 'PATCH',
                headers: { 'Authorization': `Bearer ${token}` }
            });
            fetchTasks();
        }

        async function deleteTask(id) {
            await fetch(`/api/tasks/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${token}` }
            });
            fetchTasks();
        }

        fetchTasks();
    </script>
@endsection
