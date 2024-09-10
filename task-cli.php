<?php

// Ruta al archivo JSON
$file = 'tasks.json';


if (file_exists($file)) {
    $tasks = json_decode(file_get_contents($file), true);

    if (!empty($tasks)) {
        // Obtener el último ID usado
        $lastTask = end($tasks); // Obtener la última tarea del array
        $id = $lastTask['id'] + 1;
    } else {
        $id = 0;
    }
} else {
    echo "El archivo JSON no existe.\n";
}

// Función para obtener la fecha y hora actual en un formato legible
function getCurrentDateTime()
{
    return date('Y-m-d H:i:s');
}



// Leer el archivo JSON o crear uno vacío si no existe
$tasks = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

// Obtener el comando y argumentos desde la línea de comandos
$command = $argv[1] ?? null;
$argument = $argv[2] ?? null;

// Procesar el comando
switch ($command) {
    case 'add':
        // Crear una nueva tarea con las propiedades requeridas
        $newTask = [
            'id' => $id,
            'description' => $argument,
            'status' => 'todo',
            'createdAt' => getCurrentDateTime(),
            'updatedAt' => getCurrentDateTime(),
        ];

        $tasks[] = $newTask;
        file_put_contents($file, json_encode($tasks, JSON_PRETTY_PRINT));
        echo "Tarea agregada: " . $newTask['description'] . "\n";
        break;

    case 'update':
        $taskId = $argv[2] ?? null;
        $newDescription = $argv[3] ?? null;

        // Buscar la tarea por su ID y actualizar su estado
        foreach ($tasks as &$task) {
            if ($task['id'] == $taskId) {
                $task['description'] = $newDescription;
                $task['updatedAt'] = getCurrentDateTime();
                echo "Tarea actualizada: " . $task['description'] . "\n";

                break;
            }
        }

        file_put_contents($file, json_encode($tasks, JSON_PRETTY_PRINT));
        break;
    case 'mark-in-progress':
        $taskId = $argv[2];

        foreach ($tasks as &$task) {
            if ($taskId == $task['id']) {
                $task['status'] = "in-progress";
                echo 'Tarea ' . $task['description'] . " En progreso";
            }
        }

        file_put_contents($file, json_encode($tasks, JSON_PRETTY_PRINT));
        break;

    case 'mark-done':
        $taskId = $argv[2];

        foreach ($tasks as &$task) {
            if ($taskId == $task['id']) {
                $task['status'] = "done";
                echo 'Tarea ' . $task['description'] . " terminada";
            }
        }

        file_put_contents($file, json_encode($tasks, JSON_PRETTY_PRINT));
        break;

    case 'list-in-progress':
        foreach ($tasks as $task) {
            if ($task['status'] == 'in-progress') {
                echo $task['id'] . ". " . $task['description'] . " [" . $task['status'] . "] - Creado: " . $task['createdAt'] . " - Actualizado: " . $task['updatedAt'] . "\n";
            }
        }
        break;

    case 'list-done':
        foreach ($tasks as $task) {
            if ($task['status'] == 'done') {
                echo $task['id'] . ". " . $task['description'] . " [" . $task['status'] . "] - Creado: " . $task['createdAt'] . " - Actualizado: " . $task['updatedAt'] . "\n";
            }
        }
        break;

    case 'list-todo':
        foreach ($tasks as $task) {
            if ($task['status'] == 'todo') {
                echo $task['id'] . ". " . $task['description'] . " [" . $task['status'] . "] - Creado: " . $task['createdAt'] . " - Actualizado: " . $task['updatedAt'] . "\n";
            }
        }
        break;

    case 'list':
        foreach ($tasks as $task) {
            echo $task['id'] . ". " . $task['description'] . " [" . $task['status'] . "] - Creado: " . $task['createdAt'] . " - Actualizado: " . $task['updatedAt'] . "\n";
        }
        break;

    case 'delete':
        $taskId = $argv[2];

        $tasks = array_filter($tasks, function ($task) use ($taskId) {
            return $task['id'] != $taskId;
        });

        $tasks = array_values($tasks);

        file_put_contents($file, json_encode($tasks, JSON_PRETTY_PRINT));
        echo "Tarea eliminada correctamente.\n";
        break;

    default:
        echo "Comando no reconocido.\n";
        break;
}
