<?php
require_once __DIR__ . '/config.php';

// Obtener parámetros de búsqueda y categoría
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$selected_category = isset($_GET['cat']) ? trim($_GET['cat']) : '';

// Construir la consulta de productos
$query_str = "SELECT * FROM products WHERE 1=1";
$params = [];

if ($search !== '') {
    $query_str .= " AND (title LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($selected_category !== '') {
    $query_str .= " AND category = ?";
    $params[] = $selected_category;
}

$query_str .= " ORDER BY id DESC";

try {
    $stmt = $pdo->prepare($query_str);
    $stmt->execute($params);
    $products = $stmt->fetchAll();

    // Obtener categorías únicas para los filtros
    $cat_stmt = $pdo->query("SELECT DISTINCT category FROM products ORDER BY category ASC");
    $categories = $cat_stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $products = [];
    $categories = [];
    $error_msg = "Error al cargar productos: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($site_name); ?> - Tienda WISP</title>
    <meta name="description" content="Encuentra antenas, routers, cableado y accesorios WISP de alta calidad en ArriagaMarket.">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- Encabezado / Barra de Navegación -->
    <header>
        <div class="nav-container">
            <a href="index.php" class="logo-link" id="logoLink">
                <svg width="35" height="35" viewBox="0 0 24 24" fill="none" stroke="url(#logo-grad)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <defs>
                        <linearGradient id="logo-grad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#0070f3" />
                            <stop offset="100%" stop-color="#00dfd8" />
                        </linearGradient>
                    </defs>
                    <path d="M5 12.55a11 11 0 0 1 14.08 0"></path>
                    <path d="M1.42 9a16 16 0 0 1 21.16 0"></path>
                    <path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path>
                    <circle cx="12" cy="20" r="1"></circle>
                </svg>
                <div class="logo-group">
                    <span class="logo-text"><?php echo htmlspecialchars($site_name); ?></span>
                    <span class="logo-tagline"><?php echo htmlspecialchars($site_tagline); ?></span>
                </div>
            </a>

            <!-- Formulario de Búsqueda -->
            <div class="search-bar-container">
                <form action="index.php" method="GET">
                    <?php if ($selected_category !== ''): ?>
                        <input type="hidden" name="cat" value="<?php echo htmlspecialchars($selected_category); ?>">
                    <?php endif; ?>
                    <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Buscar equipos, herramientas..." class="search-input" id="searchInput">
                </form>
            </div>

            <!-- Acciones -->
            <div class="header-actions">
                <button class="btn-cart" id="btnOpenCart" title="Ver carrito">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    <span>Carrito</span>
                    <span class="cart-badge" id="cartBadge" style="display: none;">0</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Banner Hero -->
    <section class="hero">
        <h1>Conectividad de Alta Velocidad</h1>
        <p>Tu proveedor de confianza para equipos e infraestructura WISP. Compra en línea y confirma al instante por WhatsApp.</p>
    </section>

    <!-- Categorías / Filtros -->
    <div class="categories-container">
        <a href="index.php<?php echo $search !== '' ? '?q=' . urlencode($search) : ''; ?>" 
           class="category-tab <?php echo $selected_category === '' ? 'active' : ''; ?>">
            Todos
        </a>
        <?php foreach ($categories as $cat): ?>
            <a href="index.php?cat=<?php echo urlencode($cat); ?><?php echo $search !== '' ? '&q=' . urlencode($search) : ''; ?>" 
               class="category-tab <?php echo $selected_category === $cat ? 'active' : ''; ?>">
                <?php echo htmlspecialchars($cat); ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Catálogo de Productos -->
    <main class="shop-container">
        <?php if (isset($error_msg)): ?>
            <div class="alert alert-danger" style="margin-bottom: 2rem;"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <?php if (empty($products)): ?>
            <div class="cart-empty-message" style="margin: 4rem 0;">
                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="8" y1="12" x2="16" y2="12"></line>
                </svg>
                <p>No se encontraron productos en esta categoría.</p>
                <a href="index.php" class="btn-secondary" style="margin-top: 1rem;">Limpiar Filtros</a>
            </div>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach ($products as $p): ?>
                    <article class="product-card" id="product-<?php echo $p['id']; ?>">
                        <div class="product-image-wrapper">
                            <span class="product-category-badge"><?php echo htmlspecialchars($p['category']); ?></span>
                            <?php 
                            $imagePath = 'assets/uploads/' . $p['image'];
                            if (!empty($p['image']) && file_exists(__DIR__ . '/' . $imagePath)): 
                            ?>
                                <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($p['title']); ?>" class="product-image" loading="lazy">
                            <?php else: ?>
                                <!-- Fallback SVG en lugar de imagen rota -->
                                <div class="product-placeholder-svg">
                                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="2" y="2" width="20" height="20" rx="2" ry="2"></rect>
                                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                        <polyline points="21 15 16 10 5 21"></polyline>
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <h3 class="product-title"><?php echo htmlspecialchars($p['title']); ?></h3>
                            <p class="product-desc"><?php echo htmlspecialchars($p['description']); ?></p>
                            <div class="product-footer">
                                <span class="product-price"><?php echo formatPrice($p['price']); ?></span>
                                <button type="button" 
                                        class="btn-add-cart" 
                                        data-id="<?php echo $p['id']; ?>"
                                        data-title="<?php echo htmlspecialchars($p['title']); ?>"
                                        data-price="<?php echo $p['price']; ?>"
                                        data-image="<?php echo htmlspecialchars($p['image']); ?>">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                    Agregar
                                </button>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <!-- Overlay y Carrito Sidebar Deslizable -->
    <div class="cart-overlay" id="cartOverlay"></div>
    <aside class="cart-sidebar" id="cartSidebar">
        <div class="cart-header">
            <h2>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
                Tu Carrito
            </h2>
            <button class="btn-close-cart" id="btnCloseCart" aria-label="Cerrar carrito">&times;</button>
        </div>

        <div class="cart-body" id="cartItems">
            <!-- Los artículos del carrito se renderizarán dinámicamente con JS -->
        </div>

        <div class="cart-footer">
            <div class="cart-summary">
                <span class="cart-total-label">Total estimado:</span>
                <span class="cart-total-amount" id="cartTotalAmount">$0.00</span>
            </div>

            <!-- Formulario de checkout -->
            <form id="checkoutForm" class="checkout-form" style="display: none;">
                <div class="form-group">
                    <label for="clientName" class="form-label">Tu Nombre *</label>
                    <input type="text" id="clientName" required placeholder="Ej. Juan Pérez" class="form-input">
                </div>
                <div class="form-group">
                    <label for="clientNotes" class="form-label">Notas o Dirección (Opcional)</label>
                    <input type="text" id="clientNotes" placeholder="Ej. Enviar a domicilio, o Factura" class="form-input">
                </div>
                <button type="submit" class="btn-whatsapp-send" data-phone="<?php echo htmlspecialchars($whatsapp_number); ?>">
                    <!-- SVG de Icono de WhatsApp -->
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.517 2.266 2.27 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.455L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.37 9.864-9.799.002-2.63-1.023-5.101-2.885-6.97C16.388 1.95 13.916.924 11.296.924c-5.442 0-9.866 4.372-9.87 9.802 0 1.762.476 3.48 1.379 5.02L1.749 20.7l5.034-1.326c1.554.896 3.197 1.378 4.773 1.378zM17.43 14.33c-.32-.16-1.89-.93-2.18-1.04-.29-.11-.51-.17-.72.15-.21.32-.83 1.04-1.02 1.25-.19.21-.38.24-.7.08-.32-.16-1.34-.49-2.56-1.58-.95-.85-1.6-1.9-1.78-2.22-.19-.32-.02-.49.14-.65.15-.14.32-.38.48-.56.16-.18.21-.3.32-.51.11-.21.05-.4-.03-.56-.08-.16-.72-1.73-.99-2.37-.26-.63-.53-.55-.72-.56-.19-.01-.4-.01-.61-.01s-.55.08-.83.38c-.29.3-1.1 1.08-1.1 2.63s1.14 3.05 1.29 3.26c.16.21 2.24 3.43 5.42 4.8 1.21.52 2.1.84 2.82 1.07.75.24 1.43.2 1.96.12.6-.09 1.89-.77 2.15-1.52.26-.75.26-1.4.19-1.52-.07-.12-.27-.22-.59-.38z"/>
                    </svg>
                    Confirmar por WhatsApp
                </button>
            </form>
        </div>
    </aside>

    <!-- Pie de página -->
    <footer class="main-footer">
        <div class="footer-content">
            <span class="logo-text" style="font-size: 1.32rem;"><?php echo htmlspecialchars($site_name); ?></span>
            <p class="footer-text">&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($site_name); ?>. Todos los derechos reservados.</p>
            <p class="footer-text" style="font-size: 0.8rem; color: var(--text-muted);">Los precios se muestran en Moneda Nacional. Envíos y entregas a convenir.</p>
        </div>
    </footer>

    <!-- Script de Carrito -->
    <script src="assets/js/cart.js"></script>

    <!-- Acceso Administrador Oculto (Ctrl + 1) -->
    <script>
        document.addEventListener('keydown', function(event) {
            if (event.ctrlKey && event.key === '1') {
                event.preventDefault();
                window.location.href = 'login.php';
            }
        });
    </script>
</body>
</html>
