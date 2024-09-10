const fs = require('fs');
const path = 'tasks.json';

// Función para obtener la fecha y hora actual en un formato legible
function getCurrentDateTime() {
    return new Date().toISOString().slice(0, 19).replace('T', ' ');
}

// Leer el archivo JSON o crear uno vacío si no existe
let tasks = [];
if (fs.existsSync(path)) {
    tasks = JSON.parse(fs.readFileSync(path, 'utf8'));
}

// Obtener el comando y argumentos desde la línea de comandos
const command = process.argv[2];
const argument = process.argv[3];
const secondArgument = process.argv[4];

// Obtener el último ID usado
let id = 0;
if (tasks.length > 0) {
    id = tasks[tasks.length - 1].id + 1;
}

// Procesar el comando
switch (command) {
    case 'add':
        const newTask = {
            id: id,
            description: argument,
            status: 'todo',
            createdAt: getCurrentDateTime(),
            updatedAt: getCurrentDateTime()
        };

        tasks.push(newTask);
        fs.writeFileSync(path, JSON.stringify(tasks, null, 2));
        console.log('Tarea agregada: ' + newTask.description);
        break;

    case 'update':
        const taskIdToUpdate = parseInt(argument, 10);
        const newDescription = process.argv[4];

        tasks = tasks.map(task => {
            if (task.id === taskIdToUpdate) {
                task.description = newDescription;
                task.updatedAt = getCurrentDateTime();
                console.log('Tarea actualizada: ' + task.description);
            }
            return task;
        });

        fs.writeFileSync(path, JSON.stringify(tasks, null, 2));
        break;

    case 'mark-in-progress':
        const taskIdInProgress = parseInt(argument, 10);

        tasks = tasks.map(task => {
            if (task.id === taskIdInProgress) {
                task.status = 'in-progress';
                console.log('Tarea ' + task.description + ' en progreso');
            }
            return task;
        });

        fs.writeFileSync(path, JSON.stringify(tasks, null, 2));
        break;

    case 'mark-done':
        const taskIdDone = parseInt(argument, 10);

        tasks = tasks.map(task => {
            if (task.id === taskIdDone) {
                task.status = 'done';
                console.log('Tarea ' + task.description + ' terminada');
            }
            return task;
        });

        fs.writeFileSync(path, JSON.stringify(tasks, null, 2));
        break;

    case 'list-in-progress':
        tasks.forEach(task => {
            if (task.status === 'in-progress') {
                console.log(`${task.id}. ${task.description} [${task.status}] - Creado: ${task.createdAt} - Actualizado: ${task.updatedAt}`);
            }
        });
        break;

    case 'list-done':
        tasks.forEach(task => {
            if (task.status === 'done') {
                console.log(`${task.id}. ${task.description} [${task.status}] - Creado: ${task.createdAt} - Actualizado: ${task.updatedAt}`);
            }
        });
        break;

    case 'list-todo':
        tasks.forEach(task => {
            if (task.status === 'todo') {
                console.log(`${task.id}. ${task.description} [${task.status}] - Creado: ${task.createdAt} - Actualizado: ${task.updatedAt}`);
            }
        });
        break;

    case 'list':
        tasks.forEach(task => {
            console.log(`${task.id}. ${task.description} [${task.status}] - Creado: ${task.createdAt} - Actualizado: ${task.updatedAt}`);
        });
        break;

    case 'delete':
        const taskIdToDelete = parseInt(argument, 10);
        tasks = tasks.filter(task => task.id !== taskIdToDelete);

        fs.writeFileSync(path, JSON.stringify(tasks, null, 2));
        console.log('Tarea eliminada correctamente.');
        break;

    default:
        console.log('Comando no reconocido.');
        break;
}