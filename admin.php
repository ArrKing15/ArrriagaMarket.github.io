<?php
require_once __DIR__ . '/config.php';

// Verificar autenticación
if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

$message = '';
$message_type = '';

// Procesar acciones de formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($action === 'add' || $action === 'edit') {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $category = isset($_POST['category']) ? trim($_POST['category']) : '';
        $price = isset($_POST['price']) ? floatval($_POST['price']) : 0.0;
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        
        if (empty($title) || empty($category) || $price <= 0) {
            $message = 'El título, la categoría y el precio son requeridos y deben ser válidos.';
            $message_type = 'danger';
        } else {
            // Manejar subida de imagen
            $image_name = '';
            if ($action === 'edit' && $id > 0) {
                // Obtener imagen actual por si no se sube una nueva
                $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
                $stmt->execute([$id]);
                $image_name = $stmt->fetchColumn();
            }

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $file_tmp = $_FILES['image']['tmp_name'];
                $file_name = $_FILES['image']['name'];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                
                if (in_array($file_ext, $allowed_extensions)) {
                    // Generar un nombre único para evitar sobreescritura
                    $new_image_name = uniqid('prod_', true) . '.' . $file_ext;
                    $dest_path = __DIR__ . '/assets/uploads/' . $new_image_name;
                    
                    if (move_uploaded_file($file_tmp, $dest_path)) {
                        // Si es edición y había una imagen vieja, borrarla (opcional, solo si no es de las semillas)
                        if ($action === 'edit' && !empty($image_name) && !in_array($image_name, ['litebeam_5ac.png', 'mikrotik_hex_s.png', 'bobina_utp.png', 'poe_24v.png'])) {
                            $old_image_path = __DIR__ . '/assets/uploads/' . $image_name;
                            if (file_exists($old_image_path)) {
                                unlink($old_image_path);
                            }
                        }
                        $image_name = $new_image_name;
                    } else {
                        $message = 'Error al mover el archivo subido al directorio de destino.';
                        $message_type = 'danger';
                    }
                } else {
                    $message = 'Extensión de imagen no permitida. Use: JPG, JPEG, PNG, GIF o WEBP.';
                    $message_type = 'danger';
                }
            } elseif ($action === 'add' && empty($image_name)) {
                // Si es agregar y no se sube imagen, poner un identificador vacío o usar un SVG fallback en index
                $image_name = 'placeholder.png';
            }

            // Proceder si no hay errores previos
            if ($message_type !== 'danger') {
                try {
                    if ($action === 'add') {
                        $stmt = $pdo->prepare("INSERT INTO products (title, category, price, description, image) VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([$title, $category, $price, $description, $image_name]);
                        $message = 'Producto agregado exitosamente.';
                        $message_type = 'success';
                    } else {
                        $stmt = $pdo->prepare("UPDATE products SET title = ?, category = ?, price = ?, description = ?, image = ? WHERE id = ?");
                        $stmt->execute([$title, $category, $price, $description, $image_name, $id]);
                        $message = 'Producto actualizado exitosamente.';
                        $message_type = 'success';
                    }
                } catch (PDOException $e) {
                    $message = 'Error de base de datos: ' . $e->getMessage();
                    $message_type = 'danger';
                }
            }
        }
    } elseif ($action === 'delete') {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if ($id > 0) {
            try {
                // Obtener nombre de la imagen para borrar el archivo físico
                $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
                $stmt->execute([$id]);
                $image_name = $stmt->fetchColumn();
                
                // Eliminar de base de datos
                $stmt_del = $pdo->prepare("DELETE FROM products WHERE id = ?");
                $stmt_del->execute([$id]);
                
                // Borrar archivo físico si existe y no es una de las semillas iniciales
                if (!empty($image_name) && !in_array($image_name, ['litebeam_5ac.png', 'mikrotik_hex_s.png', 'bobina_utp.png', 'poe_24v.png'])) {
                    $image_path = __DIR__ . '/assets/uploads/' . $image_name;
                    if (file_exists($image_path)) {
                        unlink($image_path);
                    }
                }
                
                $message = 'Producto eliminado correctamente.';
                $message_type = 'success';
            } catch (PDOException $e) {
                $message = 'Error al eliminar el producto: ' . $e->getMessage();
                $message_type = 'danger';
            }
        }
    } elseif ($action === 'save_settings') {
        $new_whatsapp = isset($_POST['whatsapp_number']) ? preg_replace('/[^0-9]/', '', $_POST['whatsapp_number']) : '';
        $new_password = isset($_POST['admin_password']) ? $_POST['admin_password'] : '';
        
        if (empty($new_whatsapp)) {
            $message = 'El número de WhatsApp no puede estar vacío y debe contener solo números.';
            $message_type = 'danger';
        } else {
            try {
                // Guardar número de WhatsApp
                $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES ('whatsapp_number', ?)");
                $stmt->execute([$new_whatsapp]);
                $whatsapp_number = $new_whatsapp; // Actualizar variable local
                
                $message = 'Configuraciones guardadas.';
                $message_type = 'success';
                
                // Actualizar contraseña si se digitó una nueva
                if (!empty($new_password)) {
                    if (strlen($new_password) < 6) {
                        $message = 'Las configuraciones se guardaron, pero la contraseña debe tener al menos 6 caracteres y no se modificó.';
                        $message_type = 'warning';
                    } else {
                        $hash = password_hash($new_password, PASSWORD_DEFAULT);
                        $stmt_pw = $pdo->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES ('admin_password', ?)");
                        $stmt_pw->execute([$hash]);
                        $message = 'Configuraciones y contraseña actualizadas exitosamente.';
                        $message_type = 'success';
                    }
                }
            } catch (PDOException $e) {
                $message = 'Error al guardar configuraciones: ' . $e->getMessage();
                $message_type = 'danger';
            }
        }
    }
}

// Cargar listado de productos y categorías
try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
    $products = $stmt->fetchAll();
    
    $cat_stmt = $pdo->query("SELECT DISTINCT category FROM products ORDER BY category ASC");
    $existing_categories = $cat_stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $products = [];
    $existing_categories = [];
    $message = 'Error al cargar productos o categorías: ' . $e->getMessage();
    $message_type = 'danger';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - <?php echo htmlspecialchars($site_name); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="admin-layout">

    <!-- Encabezado Admin -->
    <header class="admin-header">
        <div class="nav-container">
            <a href="index.php" class="logo-link">
                <svg width="27" height="27" viewBox="0 0 24 24" fill="none" stroke="#0070f3" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12.55a11 11 0 0 1 14.08 0"></path>
                    <path d="M1.42 9a16 16 0 0 1 21.16 0"></path>
                    <path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path>
                    <circle cx="12" cy="20" r="1"></circle>
                </svg>
                <span class="logo-text" style="font-size: 1.43rem;"><?php echo htmlspecialchars($site_name); ?> <span style="font-weight: 300; font-size: 0.9rem; color: var(--text-secondary);">Control Panel</span></span>
            </a>
            <div class="header-actions">
                <a href="index.php" class="btn-secondary">Ver Tienda</a>
                <a href="logout.php" class="btn-admin" style="color: var(--danger);">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <div class="admin-container">
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <div class="admin-grid">
            <!-- Sección de catálogo -->
            <div>
                <div class="admin-title-bar">
                    <div class="admin-title-group">
                        <h1>Catálogo de Productos</h1>
                    </div>
                    <div class="admin-actions-menu">
                        <button type="button" class="btn-primary" id="btnOpenAddModal">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Nuevo Producto
                        </button>
                    </div>
                </div>

                <div class="admin-card" style="padding: 0; overflow: hidden;">
                    <div class="table-wrapper">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Imagen</th>
                                    <th>Título</th>
                                    <th>Categoría</th>
                                    <th>Precio</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($products)): ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 3rem 1rem;">
                                            No hay productos en el catálogo. Crea uno haciendo clic en "Nuevo Producto".
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($products as $p): ?>
                                        <tr>
                                            <td>
                                                <?php 
                                                $imagePath = 'assets/uploads/' . $p['image'];
                                                if (!empty($p['image']) && file_exists(__DIR__ . '/' . $imagePath)): 
                                                ?>
                                                    <img src="<?php echo $imagePath; ?>" alt="" class="thumb-image">
                                                <?php else: ?>
                                                    <div class="thumb-image" style="display: flex; align-items: center; justify-content: center; background-color: var(--bg-main);">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                                                            <rect x="2" y="2" width="20" height="20" rx="2" ry="2"></rect>
                                                            <polyline points="21 15 16 10 5 21"></polyline>
                                                        </svg>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div style="font-weight: 700;"><?php echo htmlspecialchars($p['title']); ?></div>
                                                <div style="font-size: 0.8rem; color: var(--text-muted); max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                    <?php echo htmlspecialchars($p['description']); ?>
                                                </div>
                                            </td>
                                            <td><span class="admin-badge"><?php echo htmlspecialchars($p['category']); ?></span></td>
                                            <td style="font-weight: 700;"><?php echo formatPrice($p['price']); ?></td>
                                            <td>
                                                <div class="action-buttons">
                                                    <button type="button" 
                                                            class="btn-edit edit-product-btn" 
                                                            data-id="<?php echo $p['id']; ?>"
                                                            data-title="<?php echo htmlspecialchars($p['title']); ?>"
                                                            data-category="<?php echo htmlspecialchars($p['category']); ?>"
                                                            data-price="<?php echo $p['price']; ?>"
                                                            data-description="<?php echo htmlspecialchars($p['description']); ?>"
                                                            data-image="<?php echo htmlspecialchars($p['image']); ?>">
                                                        Editar
                                                    </button>
                                                    <form action="admin.php" method="POST" style="display:inline;" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este producto? Esta acción no se puede deshacer.');">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                                        <button type="submit" class="btn-delete">Eliminar</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Panel lateral de configuración -->
            <div>
                <h2>Configuración</h2>
                <div class="admin-card" style="margin-top: 2rem;">
                    <form action="admin.php" method="POST" class="checkout-form">
                        <input type="hidden" name="action" value="save_settings">
                        
                        <div class="form-group">
                            <label class="form-label" for="whatsapp_number">Número de WhatsApp (con código de país, sin espacios)</label>
                            <input type="text" 
                                   name="whatsapp_number" 
                                   id="whatsapp_number" 
                                   required 
                                   value="<?php echo htmlspecialchars($whatsapp_number); ?>" 
                                   placeholder="Ej: 526121368478" 
                                   class="form-input">
                            <span style="font-size: 0.75rem; color: var(--text-muted);">
                                Ejemplo para México: 52 + 10 dígitos (Ej: 526121368478). No agregues "+" ni guiones.
                            </span>
                        </div>
                        
                        <div class="form-group" style="margin-top: 1.5rem; margin-bottom: 1.5rem;">
                            <label class="form-label" for="admin_password">Cambiar Contraseña (dejar vacío si no desea cambiarla)</label>
                            <input type="password" 
                                   name="admin_password" 
                                   id="admin_password" 
                                   placeholder="Nueva contraseña" 
                                   class="form-input">
                        </div>

                        <button type="submit" class="btn-primary" style="justify-content: center; width: 100%;">
                            Guardar Ajustes
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal para Agregar/Editar Producto -->
    <div class="modal" id="productModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Nuevo Producto</h3>
                <button type="button" class="btn-close-cart" id="btnCloseModal">&times;</button>
            </div>
            <form action="admin.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="id" id="productId" value="0">
                    
                    <div class="form-group">
                        <label for="title" class="form-label">Nombre del Producto *</label>
                        <input type="text" name="title" id="productTitle" required class="form-input" placeholder="Ej. Ubiquiti LiteBeam M5">
                    </div>

                    <div class="form-group" style="margin-top: 1rem;">
                        <label for="category" class="form-label">Categoría *</label>
                        <input type="text" name="category" id="productCategory" required class="form-input" placeholder="Ej. Antenas, Routers, Accesorios" list="categorySuggestions">
                        <datalist id="categorySuggestions">
                            <?php 
                            $default_cats = ['Antenas', 'Routers', 'Cableado', 'Herramientas', 'Accesorios'];
                            $suggested_cats = array_unique(array_merge($existing_categories, $default_cats));
                            foreach ($suggested_cats as $cat): 
                            ?>
                                <option value="<?php echo htmlspecialchars($cat); ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>

                    <div class="form-group" style="margin-top: 1rem;">
                        <label for="price" class="form-label">Precio (MXN) *</label>
                        <input type="number" name="price" id="productPrice" required step="0.01" min="0.01" class="form-input" placeholder="Ej. 1450.00">
                    </div>

                    <div class="form-group" style="margin-top: 1rem;">
                        <label for="description" class="form-label">Descripción</label>
                        <textarea name="description" id="productDescription" rows="4" class="form-input" placeholder="Descripción detallada del equipo, especificaciones técnicas..."></textarea>
                    </div>

                    <div class="form-group" style="margin-top: 1rem;">
                        <label for="image" class="form-label">Imagen del Producto</label>
                        <input type="file" name="image" id="productImageFile" class="form-input" accept="image/*">
                        <span id="imageHelp" style="font-size: 0.75rem; color: var(--text-muted); display: block; margin-top: 0.25rem;">
                            Formatos soportados: JPG, PNG, GIF, WEBP.
                        </span>
                        <div id="existingImageContainer" style="margin-top: 0.5rem; display: none;">
                            <span style="font-size: 0.8rem; color: var(--text-secondary);">Imagen actual: </span>
                            <span id="existingImageName" style="font-size: 0.8rem; font-weight: 600;"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" id="btnCancelModal">Cancelar</button>
                    <button type="submit" class="btn-primary" id="btnSubmitForm">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JS de Modal Admin -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('productModal');
            const btnOpenAddModal = document.getElementById('btnOpenAddModal');
            const btnCloseModal = document.getElementById('btnCloseModal');
            const btnCancelModal = document.getElementById('btnCancelModal');
            
            const formAction = document.getElementById('formAction');
            const productId = document.getElementById('productId');
            const productTitle = document.getElementById('productTitle');
            const productCategory = document.getElementById('productCategory');
            const productPrice = document.getElementById('productPrice');
            const productDescription = document.getElementById('productDescription');
            const modalTitle = document.getElementById('modalTitle');
            const btnSubmitForm = document.getElementById('btnSubmitForm');
            const existingImageContainer = document.getElementById('existingImageContainer');
            const existingImageName = document.getElementById('existingImageName');
            const productImageFile = document.getElementById('productImageFile');

            // Abrir modal para agregar
            btnOpenAddModal.addEventListener('click', () => {
                modalTitle.textContent = 'Nuevo Producto';
                formAction.value = 'add';
                productId.value = '0';
                productTitle.value = '';
                productCategory.value = '';
                productPrice.value = '';
                productDescription.value = '';
                productImageFile.required = true; // Imagen requerida para nuevos
                existingImageContainer.style.display = 'none';
                
                modal.classList.add('open');
            });

            // Abrir modal para editar
            document.querySelectorAll('.edit-product-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.dataset.id;
                    const title = btn.dataset.title;
                    const category = btn.dataset.category;
                    const price = btn.dataset.price;
                    const desc = btn.dataset.description;
                    const image = btn.dataset.image;

                    modalTitle.textContent = 'Editar Producto';
                    formAction.value = 'edit';
                    productId.value = id;
                    productTitle.value = title;
                    productCategory.value = category;
                    productPrice.value = price;
                    productDescription.value = desc;
                    productImageFile.required = false; // No requerida al editar
                    
                    if (image) {
                        existingImageName.textContent = image;
                        existingImageContainer.style.display = 'block';
                    } else {
                        existingImageContainer.style.display = 'none';
                    }

                    modal.classList.add('open');
                });
            });

            // Cerrar modal
            const closeModal = () => {
                modal.classList.remove('open');
            };

            btnCloseModal.addEventListener('click', closeModal);
            btnCancelModal.addEventListener('click', closeModal);
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    closeModal();
                }
            });
        });
    </script>

</body>
</html>
