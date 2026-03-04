const API_URL = 'http://localhost/task-management-api-main/api';

let currentPage = 1;
let totalPages = 1;

function checkAuth() {
    if (!localStorage.getItem('loggedIn') &&
        !window.location.pathname.includes('login.html') &&
        !window.location.pathname.includes('register.html')) {
        window.location.href = 'login.html';
    }
}

document.getElementById('loginForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const email = email.value;
    const password = password.value;

    const res = await fetch(`${API_URL}/auth/login`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password })
    });

    const data = await res.json();
    if (data.success) {
        localStorage.setItem('loggedIn', 'true');
        window.location.href = 'index.html';
    } else {
        alert(data.error);
    }
});

document.getElementById('registerForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const email = email.value;
    const password = password.value;

    const res = await fetch(`${API_URL}/auth/register`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password })
    });

    const data = await res.json();
    if (data.success) {
        alert('Registered! Please login.');
        window.location.href = 'login.html';
    } else {
        alert(data.error);
    }
});

async function loadTasks(page) {
    currentPage = page;
    const filter = document.getElementById('filter').value;

    const res = await fetch(`${API_URL}/tasks?status=${filter}&page=${page}`);
    const data = await res.json();

    if (!res.ok) return alert(data.error);

    const list = document.getElementById('taskList');
    list.innerHTML = '';

    data.tasks.forEach(task => {
        const li = document.createElement('li');
        li.innerHTML = `
            <strong>${task.title}</strong><br>
            ${task.description}<br>
            Status: ${task.status}
            <br>
            <button onclick="deleteTask(${task.id})">Delete</button>
        `;
        list.appendChild(li);
    });

    totalPages = data.total_pages;
    prevPage.disabled = page <= 1;
    nextPage.disabled = page >= totalPages;
}

async function createTask() {
    const title = document.getElementById('title').value;
    const description = document.getElementById('description').value;
    const status = document.getElementById('status').value;

    if (!title) return alert('Title required');

    const res = await fetch(`${API_URL}/tasks`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ title, description, status })
    });

    const data = await res.json();
    if (data.success) loadTasks(currentPage);
}

async function deleteTask(id) {
    if (!confirm('Delete?')) return;

    const res = await fetch(`${API_URL}/tasks/${id}`, {
        method: 'DELETE'
    });

    const data = await res.json();
    if (data.success) loadTasks(currentPage);
}

function prevPage() {
    if (currentPage > 1) loadTasks(currentPage - 1);
}

function nextPage() {
    if (currentPage < totalPages) loadTasks(currentPage + 1);
}

function logout() {
    localStorage.removeItem('loggedIn');
    window.location.href = 'login.html';
}

checkAuth();
