const API = "http://localhost/task-management-api-main/api";

async function register() {
    const email = email.value;
    const password = password.value;

    const res = await fetch(API + "/auth/register", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password })
    });

    const data = await res.json();
    if (data.success) window.location = "login.html";
    else alert(data.error);
}

async function login() {
    const email = email.value;
    const password = password.value;

    const res = await fetch(API + "/auth/login", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password })
    });

    const data = await res.json();
    if (data.success) window.location = "index.html";
    else alert(data.error);
}

async function addTask() {
    const title = document.getElementById("title").value;
    const description = document.getElementById("description").value;

    await fetch(API + "/tasks", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ title, description, status: "pending" })
    });

    loadTasks();
}

async function loadTasks() {
    const res = await fetch(API + "/tasks");
    const data = await res.json();

    const container = document.getElementById("tasks");
    container.innerHTML = "";

    data.tasks.forEach(task => {
        const div = document.createElement("div");
        div.className = "task";
        div.innerHTML = `
            <strong>${task.title}</strong><br>
            ${task.description}
            <button onclick="deleteTask(${task.id})">Delete</button>
        `;
        container.appendChild(div);
    });
}

async function deleteTask(id) {
    await fetch(API + "/tasks/" + id, { method: "DELETE" });
    loadTasks();
}

function logout() {
    window.location = "login.html";
}

if (window.location.pathname.includes("index.html")) {
    loadTasks();
}
