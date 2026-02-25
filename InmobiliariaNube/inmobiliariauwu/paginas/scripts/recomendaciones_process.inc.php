<?php
function addViewToInmueble($con,$inmueble) {
    if (isset($_SESSION['user']['id']) && $inmueble) {
    $usuarioId = $_SESSION['user']['id']; // Usar la estructura de sesión correcta
    $inmuebleId = $inmueble['id'];

    try {
        $sqlIns = "
          INSERT INTO eventos_usuario (usuario_id, inmueble_id, tipo_evento)
          VALUES (:uid, :id, :tipo)
        ";
        $stmtIns = $con->prepare($sqlIns); // Usar $con, que es tu objeto PDO existente
        $stmtIns->execute([
          ':uid'  => $usuarioId,
          ':id'   => $inmuebleId,
          ':tipo' => 'vista'
        ]);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    }
}


function getTopXPropiedades($con, $x, $offset = 0) {
    try {
        $sql = "
          SELECT i.*, COUNT(e.id) AS total_vistas
          FROM inmuebles i
          LEFT JOIN eventos_usuario e ON i.id = e.inmueble_id
          GROUP BY i.id
          ORDER BY total_vistas DESC
          LIMIT :x OFFSET :offset
        ";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':x', $x, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return [];
    }
}


function getTopXPropiedadesPorTipo($con, $x, $tipo, $offset = 0) {
    try {
        $sql = "
          SELECT i.*, COUNT(e.id) AS total_vistas
          FROM inmuebles i
          LEFT JOIN eventos_usuario e ON i.id = e.inmueble_id
          WHERE i.tipo = :tipo
          GROUP BY i.id
          ORDER BY total_vistas DESC
          LIMIT :x OFFSET :offset
        ";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':x', $x, PDO::PARAM_INT);
        $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return [];
    }
}

function getTopXPropiedadesPorEstado($con, $x, $estado, $offset = 0) {
    try {
        $sql = "
          SELECT i.*, COUNT(e.id) AS total_vistas
          FROM inmuebles i
          LEFT JOIN eventos_usuario e ON i.id = e.inmueble_id
          WHERE i.estado = :estado
          GROUP BY i.id
          ORDER BY total_vistas DESC
          LIMIT :x OFFSET :offset
        ";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':x', $x, PDO::PARAM_INT);
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return [];
    }
}


function getXTotalViewHistory($con, $x, $offset = 0) {
    try {
        $sql = "
          SELECT i.*, COUNT(e.id) AS total_vistas, MAX(e.fecha) AS ultima_fecha_evento
          FROM inmuebles i
          LEFT JOIN eventos_usuario e ON i.id = e.inmueble_id
          GROUP BY i.id
          ORDER BY ultima_fecha_evento DESC
          LIMIT :x OFFSET :offset
        ";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':x', $x, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return [];
    }
}

function getXFavorites($con, $user_id, $x, $offset = 0){
    try {
        $sql = "
          SELECT i.*, f.fecha_guardado
          FROM inmuebles i
          JOIN favoritos f ON i.id = f.id_inmueble
          WHERE f.id_usuario = :user_id
          ORDER BY f.fecha_guardado DESC
          LIMIT :x OFFSET :offset
        ";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':x', $x, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return [];
    }
}

function getUserMostViewedProperties($con, $userId, $x, $offset = 0) {
    try {
        $sql = "
            SELECT i.*, COUNT(e.id) AS user_total_vistas
            FROM inmuebles i
            JOIN eventos_usuario e ON i.id = e.inmueble_id
            WHERE e.usuario_id = :userId AND e.tipo_evento = 'vista'
            GROUP BY i.id
            ORDER BY user_total_vistas DESC
            LIMIT :x OFFSET :offset
        ";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':x', $x, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return [];
    }
}
function getUserMostRecurringTipoInViews($con, $userId) {
    try {
        $sql = "
            SELECT i.tipo
            FROM inmuebles i
            JOIN eventos_usuario e ON i.id = e.inmueble_id
            WHERE e.usuario_id = :userId AND e.tipo_evento = 'vista'
            GROUP BY i.tipo
            ORDER BY COUNT(i.tipo) DESC
            LIMIT 1
        ";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['tipo'] : null;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return null;
    }
}

// New Helper Function: Get user's most recurring estado in their view history
function getUserMostRecurringEstadoInViews($con, $userId) {
    try {
        $sql = "
            SELECT i.estado
            FROM inmuebles i
            JOIN eventos_usuario e ON i.id = e.inmueble_id
            WHERE e.usuario_id = :userId AND e.tipo_evento = 'vista'
            GROUP BY i.estado
            ORDER BY COUNT(i.estado) DESC
            LIMIT 1
        ";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['estado'] : null;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return null;
    }
}


function generateSimpleRecommendations($con, $numberOfRecs = 5, $userId = null) {
    $recommendations = [];
    $recommendedIds = []; // To keep track of IDs already added

    // Helper function to add unique recommendations
    $addRecommendation = function($item) use (&$recommendations, &$recommendedIds, $numberOfRecs) {
        if ($item && !in_array($item['id'], $recommendedIds) && count($recommendations) < $numberOfRecs) {
            $recommendations[] = $item;
            $recommendedIds[] = $item['id'];
            return true;
        }
        return false;
    };

    // Helper function to add multiple unique recommendations
    $addMultipleRecommendations = function($items) use ($addRecommendation, &$recommendations, $numberOfRecs) {
        foreach ($items as $item) {
            if (count($recommendations) >= $numberOfRecs) break;
            $addRecommendation($item);
        }
    };

    if ($userId !== null) {
        // Priority 1: User's most viewed property
        if (count($recommendations) < $numberOfRecs) {
            $userMostViewed = getUserMostViewedProperties($con, $userId, 1);
            if (!empty($userMostViewed)) {
                $addRecommendation($userMostViewed[0]);
            }
        }

        // Priority 2: Most viewed property of user's most recurring type
        if (count($recommendations) < $numberOfRecs) {
            $recurringTipo = getUserMostRecurringTipoInViews($con, $userId);
            if ($recurringTipo) {
                // Fetch top property of this type, ensuring it's not already recommended
                $offset = 0;
                do {
                    $propPorTipo = getTopXPropiedadesPorTipo($con, 1, $recurringTipo, $offset);
                    if (empty($propPorTipo)) break; // No more properties of this type
                    $added = $addRecommendation($propPorTipo[0]);
                    $offset++;
                } while (!$added && $offset < 5 && count($recommendations) < $numberOfRecs); // Try a few times to find a new one
            }
        }

        // Priority 3: Most viewed property of user's most recurring estado
        if (count($recommendations) < $numberOfRecs) {
            $recurringEstado = getUserMostRecurringEstadoInViews($con, $userId);
            if ($recurringEstado) {
                // Fetch top property of this estado, ensuring it's not already recommended
                $offset = 0;
                do {
                    $propPorEstado = getTopXPropiedadesPorEstado($con, 1, $recurringEstado, $offset);
                    if (empty($propPorEstado)) break; // No more properties of this estado
                    $added = $addRecommendation($propPorEstado[0]);
                    $offset++;
                } while (!$added && $offset < 5 && count($recommendations) < $numberOfRecs); // Try a few times to find a new one
            }
        }
        
        // Fallback 1 (Logged-in User): User's Favorites
        if (count($recommendations) < $numberOfRecs) {
            $needed = $numberOfRecs - count($recommendations);
            $userFavorites = getXFavorites($con, $userId, $needed + count($recommendations) /* fetch more to filter */, 0);
            $addMultipleRecommendations($userFavorites);
        }
    }

    // Fallback 2: General Top Properties (also for non-logged-in users)
    if (count($recommendations) < $numberOfRecs) {
        $needed = $numberOfRecs - count($recommendations);
        // Fetch a bit more than strictly needed to allow for filtering out duplicates
        $fetchLimit = $needed + count($recommendedIds); 
        $topProperties = getTopXPropiedades($con, $fetchLimit, 0);
        $addMultipleRecommendations($topProperties);
    }

    // Fallback 3: General Recent View History (also for non-logged-in users)
    if (count($recommendations) < $numberOfRecs) {
        $needed = $numberOfRecs - count($recommendations);
        $fetchLimit = $needed + count($recommendedIds);
        $recentHistory = getXTotalViewHistory($con, $fetchLimit, 0);
        $addMultipleRecommendations($recentHistory);
    }
    
    return array_slice($recommendations, 0, $numberOfRecs); // Ensure exactly $numberOfRecs or fewer
}

?>