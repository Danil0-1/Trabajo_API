<?php
require_once "src/db.php";

$method = $_SERVER['REQUEST_METHOD'];

$uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

$recurso = $uri[2] ?? null;
$id = $uri[3] ?? null;
header('Content-Type: application/json');

if ($recurso !== 'productos' && $recurso !== 'categorias' && $recurso !== 'promociones') {
    http_response_code(404);
    echo json_encode(['error' => 'Recurso no encontrado', 'code' => 404, 'errorUrl' => 'https://http.cat/status/404']);
    exit;
}

if ($recurso === "productos") {
    switch ($method) {
        case 'GET':
            if ($id) {
                $stmt = $pdo->prepare("
                    SELECT productos.id, productos.nombre, productos.precio, categorias.nombre AS categoria, promociones.descuento
                    FROM productos
                    LEFT JOIN categorias ON productos.categoria_id = categorias.id
                    LEFT JOIN promociones ON promociones.producto_id = productos.id
                    WHERE productos.id = ?
                ");
                $stmt->execute([$id]);
                $response = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $stmt = $pdo->query("
                    SELECT productos.id, productos.nombre, productos.precio, categorias.nombre AS categoria,
                    CASE
                        WHEN promociones.id IS NOT NULL THEN 'Sí'
                        ELSE 'No'
                    END AS tiene_promocion
                    FROM productos
                    LEFT JOIN categorias ON productos.categoria_id = categorias.id
                    LEFT JOIN promociones ON promociones.producto_id = productos.id
                ");
                $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            echo json_encode($response);
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare("INSERT INTO productos (nombre, precio, categoria_id) VALUES (?, ?, ?)");
            $stmt->execute([$data['nombre'], $data['precio'], $data['categoria_id']]);
            http_response_code(201);
            $data['id'] = $pdo->lastInsertId();
            echo json_encode($data);
            break;

        case 'PUT':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'ID no encontrado', 'code' => 400, 'errorUrl' => 'https://http.cat/status/400']);
                exit;
            }

            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare("UPDATE productos SET nombre = ?, precio = ?, categoria_id = ? WHERE id = ?");
            $stmt->execute([$data['nombre'], $data['precio'], $data['categoria_id'], $id]);
            $data['id'] = $id;
            echo json_encode($data);
            break;

        case 'DELETE':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'ID no encontrado', 'code' => 400, 'errorUrl' => 'https://http.cat/status/400']);
                exit;
            }

            $stmt = $pdo->prepare("SELECT id, nombre, precio FROM productos WHERE id = ?");
            $stmt->execute([
                $id
            ]);
            $response = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
            $stmt->execute([
                $id
            ]);

            echo json_encode($response);
            break;
    }
}

if ($recurso === "categorias") {
    switch ($method) {
        case 'GET':
            if ($id) {
                $stmt = $pdo->prepare("SELECT * FROM categorias WHERE id = ?");
                $stmt->execute([$id]);
                $response = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $stmt = $pdo->query("SELECT * FROM categorias");
                $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            echo json_encode($response);
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare("INSERT INTO categorias (nombre) VALUES (?)");
            $stmt->execute([$data['nombre']]);
            http_response_code(201);
            $data['id'] = $pdo->lastInsertId();
            echo json_encode($data);
            break;

        case 'PUT':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'ID no encontrado', 'code' => 400]);
                exit;
            }
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare("UPDATE categorias SET nombre = ? WHERE id = ?");
            $stmt->execute([$data['nombre'], $id]);
            $data['id'] = $id;
            echo json_encode($data);
            break;

        case 'DELETE':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'ID no encontrado', 'code' => 400]);
                exit;
            }
            $stmt = $pdo->prepare("SELECT * FROM categorias WHERE id = ?");
            $stmt->execute([$id]);
            $response = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $pdo->prepare("DELETE FROM categorias WHERE id = ?");
            $stmt->execute([$id]);

            echo json_encode($response);
            break;
    }
}

if ($recurso === "promociones") {
    switch ($method) {
        case 'GET':
            if ($id) {
                $stmt = $pdo->prepare("SELECT * FROM promociones WHERE id = ?");
                $stmt->execute([$id]);
                $response = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $stmt = $pdo->query("SELECT * FROM promociones");
                $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            echo json_encode($response);
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare("INSERT INTO promociones (descripcion, descuento, producto_id) VALUES (?, ?, ?)");
            $stmt->execute([$data['descripcion'], $data['descuento'], $data['producto_id']]);
            http_response_code(201);
            $data['id'] = $pdo->lastInsertId();
            echo json_encode($data);
            break;

        case 'PUT':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'ID no encontrado', 'code' => 400]);
                exit;
            }
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare("UPDATE promociones SET descripcion = ?, descuento = ?, producto_id = ? WHERE id = ?");
            $stmt->execute([$data['descripcion'], $data['descuento'], $data['producto_id'], $id]);
            $data['id'] = $id;
            echo json_encode($data);
            break;

        case 'DELETE':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'ID no encontrado', 'code' => 400]);
                exit;
            }
            $stmt = $pdo->prepare("SELECT * FROM promociones WHERE id = ?");
            $stmt->execute([$id]);
            $response = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $pdo->prepare("DELETE FROM promociones WHERE id = ?");
            $stmt->execute([$id]);

            echo json_encode($response);
            break;
    }
}
