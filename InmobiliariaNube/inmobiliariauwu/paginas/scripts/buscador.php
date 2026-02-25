<?php

function buscarInmuebles($con, $searchTerm, $tipo, $estado, $precio_min, $precio_max, $limite, $inicio) { // Removed $recamaras
    $params = [];
    $baseSql = "SELECT * FROM inmuebles";
    $countSql = "SELECT COUNT(*) as total FROM inmuebles";
    
    $conditions = [];

    if (!empty($searchTerm)) {
        $conditions[] = "(titulo LIKE :searchTerm OR descripcion LIKE :searchTerm OR ubicacion LIKE :searchTerm)";
        $params[':searchTerm'] = '%' . $searchTerm . '%';
    }

    if (!empty($tipo)) {
        $conditions[] = "tipo = :tipo";
        $params[':tipo'] = $tipo;
    }

    if (!empty($estado)) {
        $conditions[] = "estado = :estado";
        $params[':estado'] = $estado;
    }

    if (!empty($precio_min)) {
        $conditions[] = "precio >= :precio_min";
        $params[':precio_min'] = $precio_min;
    }

    if (!empty($precio_max)) {
        $conditions[] = "precio <= :precio_max";
        $params[':precio_max'] = $precio_max;
    }

    // Removed recamaras condition

    $whereClause = "";
    if (count($conditions) > 0) {
        $whereClause = " WHERE " . implode(" AND ", $conditions);
    }

    $sql = $baseSql . $whereClause . " ORDER BY fecha_publicacion DESC LIMIT :limite OFFSET :inicio";
    $countSql .= $whereClause;

    $totalStmt = $con->prepare($countSql);
    foreach ($params as $key => &$val) {
        // Determine type for binding, assuming numeric for price, string for others
        if (strpos($key, 'precio') !== false) { // Removed || $key === ':recamaras'
            $totalStmt->bindParam($key, $val, PDO::PARAM_INT); // Or PDO::PARAM_STR if decimal for price
        } else {
            $totalStmt->bindParam($key, $val, PDO::PARAM_STR);
        }
    }
    unset($val); // Unset reference
    $totalStmt->execute();
    $totalInmuebles = $totalStmt->fetchColumn();

    $stmt = $con->prepare($sql);
    foreach ($params as $key => &$val) {
         if (strpos($key, 'precio') !== false) { // Removed || $key === ':recamaras'
            $stmt->bindParam($key, $val, PDO::PARAM_INT); // Or PDO::PARAM_STR if decimal for price
        } else {
            $stmt->bindParam($key, $val, PDO::PARAM_STR);
        }
    }
    unset($val); // Unset reference
    $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
    $stmt->bindParam(':inicio', $inicio, PDO::PARAM_INT);
    $stmt->execute();
    $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return ['filas' => $filas, 'totalInmuebles' => $totalInmuebles];
}
?>
