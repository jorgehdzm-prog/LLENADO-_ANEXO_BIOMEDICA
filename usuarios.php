<!DOCTYPE html>
<!--de dashboard rediriguir a usuarios es la lista de usuario la cual permite añadir eliminar crear
usuarios nuevos solo tiene acceso admin-->
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 20px 0;
            margin-bottom: 30px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            margin-bottom: 20px;
            border: none;
        }
        .card-header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            border-radius: 10px 10px 0 0 !important;
        }
        .btn-primary {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #4e0ca8 0%, #1a63d6 100%);
        }
        .table-hover tbody tr:hover {
            background-color: rgba(38, 117, 252, 0.1);
        }
        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            background-color: #6a11cb;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header text-center">
        <h1><i class="fas fa-users me-2"></i>Gestión de Usuarios</h1>
        <p class="lead">Sistema de administración de usuarios del proyecto</p>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Lista de Usuarios</h5>
                        <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="fas fa-plus me-1"></i> Agregar Usuario
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Avatar</th>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Rol</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Simulación de datos - En una aplicación real, estos datos vendrían de una base de datos
                                    $usuarios = [
                                        ['id' => 1, 'nombre' => 'Juan Pérez', 'email' => 'juan@example.com', 'rol' => 'Administrador', 'activo' => true],
                                        ['id' => 2, 'nombre' => 'María García', 'email' => 'maria@example.com', 'rol' => 'Editor', 'activo' => true],
                                        ['id' => 3, 'nombre' => 'Carlos López', 'email' => 'carlos@example.com', 'rol' => 'Usuario', 'activo' => false],
                                        ['id' => 4, 'nombre' => 'Ana Martínez', 'email' => 'ana@example.com', 'rol' => 'Editor', 'activo' => true],
                                        ['id' => 5, 'nombre' => 'Pedro Rodríguez', 'email' => 'pedro@example.com', 'rol' => 'Usuario', 'activo' => true]
                                    ];
                                    
                                    foreach ($usuarios as $usuario) {
                                        $iniciales = "";
                                        $nombres = explode(" ", $usuario['nombre']);
                                        if (count($nombres) > 0) {
                                            $iniciales = substr($nombres[0], 0, 1);
                                            if (count($nombres) > 1) {
                                                $iniciales .= substr($nombres[1], 0, 1);
                                            }
                                        }
                                        
                                        echo "<tr>";
                                        echo "<td>{$usuario['id']}</td>";
                                        echo "<td><div class='user-avatar'>{$iniciales}</div></td>";
                                        echo "<td>{$usuario['nombre']}</td>";
                                        echo "<td>{$usuario['email']}</td>";
                                        echo "<td>{$usuario['rol']}</td>";
                                        echo "<td><span class='badge " . ($usuario['activo'] ? "bg-success" : "bg-danger") . "'>" . ($usuario['activo'] ? "Activo" : "Inactivo") . "</span></td>";
                                        echo "<td class='action-buttons'>";
                                        echo "<button class='btn btn-sm btn-info text-white'><i class='fas fa-eye'></i></button> ";
                                        echo "<button class='btn btn-sm btn-warning text-white'><i class='fas fa-edit'></i></button> ";
                                        echo "<button class='btn btn-sm btn-danger'><i class='fas fa-trash'></i></button>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para agregar usuario -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Agregar Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre completo</label>
                            <input type="text" class="form-control" id="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="rol" class="form-label">Rol</label>
                            <select class="form-select" id="rol">
                                <option value="Administrador">Administrador</option>
                                <option value="Editor">Editor</option>
                                <option value="Usuario" selected>Usuario</option>
                            </select>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="activo" checked>
                            <label class="form-check-label" for="activo">Usuario activo</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary">Guardar Usuario</button>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-4 mt-5">
        <p class="mb-0">Sistema de Gestión de Usuarios - Proyecto Anexo 7</p>
        <p class="mb-0">localhost/dashboard/proyecto_anexo7/usuarios.php</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>