<?php

include_once './_init.php';

$estados = [];

$estados[] = [
    'id' => 'todos',
    'estado' => 'Todos'
];

foreach ($estados_array_select as $id => $estado) {
    $estados[] = [
        'id' => $id,
        'estado' => $estado
    ];
}

die(json_encode($estados));

/*
$estados_array_query = [6, 1, 4, 678, 3, 2, 7];

$estados_query = implode(' OR id=', $estados_array_query);

$query = query($mysqli, "SELECT id,estado FROM estados WHERE mostrar=1  AND (id={$estados_query} )");
if (mysqli_num_rows($query) > 0) {
    $estados[] = [
        'id' => 'todos',
        'estado' => 'Todos'
    ];
    while ($estado = mysqli_fetch_assoc($query)) {
        $id = (int)$estado['id'];
        $estados[] = [
            'id' => $id,
            'estado' => estados($id)
        ];
    }
}
*/