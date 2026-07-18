<?php
// Evitar el acceso directo al archivo si no es necesario, pero permitir inclusiones.
$db_file = __DIR__ . '/database.sqlite';

try {
    $pdo = new PDO("sqlite:" . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Crear tabla de productos
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        description TEXT,
        price REAL NOT NULL,
        category TEXT NOT NULL,
        image TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Crear tabla de configuraciones
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        key TEXT PRIMARY KEY,
        value TEXT
    )");

    // Inicializar configuración por defecto si está vacía
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM settings");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        // Guardar teléfono de WhatsApp y hash de contraseña
        $stmt_insert = $pdo->prepare("INSERT INTO settings (key, value) VALUES (?, ?)");
        
        // Número de WhatsApp provisto por el usuario
        $stmt_insert->execute(['whatsapp_number', '526121368478']);
        
        // Contraseña admin provista: Arriaga1501#
        $password_hash = password_hash('Arriaga1501#', PASSWORD_DEFAULT);
        $stmt_insert->execute(['admin_password', $password_hash]);
    }

    // Insertar algunos productos WISP iniciales para demostración si la tabla está vacía
    $stmt_p = $pdo->prepare("SELECT COUNT(*) FROM products");
    $stmt_p->execute();
    if ($stmt_p->fetchColumn() == 0) {
        $insert_product = $pdo->prepare("INSERT INTO products (title, description, price, category, image) VALUES (?, ?, ?, ?, ?)");
        
        $initial_products = [
            [
                'Ubiquiti LiteBeam 5AC Gen2',
                'Antena CPE de exterior para enlaces inalámbricos de larga distancia a 5GHz, ganancia de 23 dBi.',
                1690.00,
                'Antenas',
                'litebeam_5ac.png'
            ],
            [
                'MikroTik hEX S (RB760iGS)',
                'Router Gigabit ethernet de 5 puertos con puerto SFP, procesador de doble núcleo y soporte IPsec.',
                1599.00,
                'Routers',
                'mikrotik_hex_s.png'
            ],
            [
                'Bobina UTP Cat6 100% Cobre Exterior',
                'Cable de red blindado para exteriores con doble chaqueta, ideal para instalaciones WISP de 305 metros.',
                2450.00,
                'Cableado',
                'bobina_utp.png'
            ],
            [
                'Inyector PoE Pasivo 24V DC 0.5A',
                'Fuente de alimentación PoE para equipos inalámbricos Ubiquiti y MikroTik, protección contra descargas.',
                320.00,
                'Accesorios',
                'poe_24v.png'
            ]
        ];

        foreach ($initial_products as $p) {
            $insert_product->execute($p);
        }
    }

} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>
