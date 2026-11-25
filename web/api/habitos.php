<?php
require __DIR__ . '/_common.php';

run_api(function (PDO $db, string $method): void {
    $uid = user_id($db);

    if ($method === 'GET') {
        send_json(habit_list($db, $uid));
    }

    $data = input_json();
    $pk = idcol($db, 'habitos');
    $owner = col($db, 'habitos', ['id_usuario', 'idUsuario']);
    $id = $method === 'POST' ? null : checked_id($_GET['id'] ?? null, 'id de hábito');

    if ($id !== null && !owns_habit($db, $id, $uid)) {
        fail('Hábito no encontrado.', 404);
    }

    if ($method === 'DELETE') {
        $db->beginTransaction();
        try {
            foreach (['cumplimientos', 'habitoetiquetas'] as $table) {
                $fk = col($db, $table, ['id_habito', 'idHabito']);
                $db->prepare('DELETE FROM ' . ident($table) . ' WHERE ' . ident($fk) . ' = ?')->execute([$id]);
            }

            $db->prepare(
                'DELETE FROM `habitos` WHERE ' . ident($pk) . ' = ? AND ' . ident($owner) . ' = ?'
            )->execute([$id, $uid]);

            $db->commit();
        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }

        send_json(['id' => $id], 200, 'Hábito eliminado.');
    }

    if (!in_array($method, ['POST', 'PUT'], true)) {
        fail('Método no permitido.', 405);
    }

    $name = validated_name($data['nombre'] ?? '', 150);
    $color = $data['color'] ?? '#5d71f5';
    if (!is_string($color) || !preg_match('/^#[a-fA-F0-9]{6}$/', $color)) {
        fail('Color inválido.');
    }

    $category = $data['categoria_id'] ?? null;
    if ($category !== null && $category !== '') {
        $category = checked_id($category, 'id de categoría');
        $categoryPk = idcol($db, 'categorias');
        $exists = query_all(
            $db,
            'SELECT ' . ident($categoryPk) . ' FROM `categorias` WHERE ' . ident($categoryPk) . ' = ? LIMIT 1',
            [$category]
        );
        if (!$exists) {
            fail('La categoría seleccionada no existe.', 422);
        }
    } else {
        $category = null;
    }

    // Solo se aceptan etiquetas que ya existan en el catálogo de MariaDB.
    // Evita que el formulario cree etiquetas escritas libremente por el usuario.
    $tagNames = $data['etiquetas'] ?? [];
    if (!is_array($tagNames) || count($tagNames) > 5) {
        fail('Selecciona como máximo las cinco etiquetas disponibles.');
    }

    $availableTags = array_column(tag_list($db), 'nombre');
    $validatedTags = [];

    foreach ($tagNames as $tag) {
        if (!is_string($tag)) {
            fail('La selección de etiquetas no es válida.');
        }

        $tag = trim($tag);
        if ($tag === '') {
            continue;
        }

        if (!in_array($tag, $availableTags, true)) {
            fail('La etiqueta "' . $tag . '" no existe en el catálogo. Actualiza la página y vuelve a seleccionar.');
        }

        $validatedTags[] = $tag;
    }

    $validatedTags = array_values(array_unique($validatedTags));

    $fields = [col($db, 'habitos', ['nombre']) => $name];
    if ($categoryColumn = col($db, 'habitos', ['id_categoria', 'idCategoria'], false)) {
        $fields[$categoryColumn] = $category;
    }
    if ($colorColumn = col($db, 'habitos', ['color'], false)) {
        $fields[$colorColumn] = $color;
    }

    if ($method === 'POST') {
        $fields[$owner] = $uid;
        if ($createdColumn = col($db, 'habitos', ['fecha_creacion', 'fechaCreacion'], false)) {
            $fields[$createdColumn] = date('Y-m-d');
        }
    }

    $db->beginTransaction();
    try {
        if ($method === 'POST') {
            $fieldNames = array_keys($fields);
            $sql = 'INSERT INTO `habitos` ('
                . implode(',', array_map('ident', $fieldNames))
                . ') VALUES (' . implode(',', array_fill(0, count($fieldNames), '?')) . ')';

            $db->prepare($sql)->execute(array_values($fields));
            $id = (int) $db->lastInsertId();
        } else {
            $assignments = [];
            foreach (array_keys($fields) as $field) {
                $assignments[] = ident($field) . ' = ?';
            }

            $sql = 'UPDATE `habitos` SET ' . implode(', ', $assignments)
                . ' WHERE ' . ident($pk) . ' = ? AND ' . ident($owner) . ' = ?';

            $db->prepare($sql)->execute([...array_values($fields), $id, $uid]);
        }

        // set_tags elimina relaciones anteriores y registra solamente las seleccionadas.
        // Como se validaron contra el catálogo, create_tag reutiliza los registros existentes.
        set_tags($db, $id, $validatedTags);

        $db->commit();
    } catch (Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        throw $e;
    }

    send_json(
        ['id' => $id],
        $method === 'POST' ? 201 : 200,
        $method === 'POST' ? 'Hábito creado.' : 'Hábito actualizado.'
    );
});
