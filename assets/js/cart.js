document.addEventListener('DOMContentLoaded', () => {
    // Estado del carrito
    let cart = JSON.parse(localStorage.getItem('wisp_cart')) || [];
    
    // Elementos del DOM
    const cartSidebar = document.getElementById('cartSidebar');
    const cartOverlay = document.getElementById('cartOverlay');
    const btnOpenCart = document.getElementById('btnOpenCart');
    const btnCloseCart = document.getElementById('btnCloseCart');
    const cartItemsContainer = document.getElementById('cartItems');
    const cartBadge = document.getElementById('cartBadge');
    const cartTotalAmount = document.getElementById('cartTotalAmount');
    const checkoutForm = document.getElementById('checkoutForm');
    const btnAddCartList = document.querySelectorAll('.btn-add-cart');

    // Inicializar vista del carrito
    updateCartUI();

    // Abrir Carrito
    if (btnOpenCart) {
        btnOpenCart.addEventListener('click', openCart);
    }

    // Cerrar Carrito
    if (btnCloseCart) {
        btnCloseCart.addEventListener('click', closeCart);
    }
    if (cartOverlay) {
        cartOverlay.addEventListener('click', closeCart);
    }

    function openCart() {
        cartSidebar.classList.add('open');
        cartOverlay.classList.add('open');
    }

    function closeCart() {
        cartSidebar.classList.remove('open');
        cartOverlay.classList.remove('open');
    }

    // Agregar al Carrito (delegación para que funcione con búsquedas dinámicas si las hay)
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-add-cart');
        if (btn) {
            const id = parseInt(btn.dataset.id);
            const title = btn.dataset.title;
            const price = parseFloat(btn.dataset.price);
            const image = btn.dataset.image;
            
            addToCart(id, title, price, image);
            openCart();
        }
    });

    // Funciones del Carrito
    function addToCart(id, title, price, image) {
        const existingItem = cart.find(item => item.id === id);
        
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cart.push({
                id,
                title,
                price,
                image,
                quantity: 1
            });
        }
        
        saveCart();
        updateCartUI();
    }

    function updateQuantity(id, amount) {
        const item = cart.find(item => item.id === id);
        if (item) {
            item.quantity += amount;
            if (item.quantity <= 0) {
                removeFromCart(id);
                return;
            }
            saveCart();
            updateCartUI();
        }
    }

    function removeFromCart(id) {
        cart = cart.filter(item => item.id !== id);
        saveCart();
        updateCartUI();
    }

    function saveCart() {
        localStorage.setItem('wisp_cart', JSON.stringify(cart));
    }

    function getCartTotal() {
        return cart.reduce((total, item) => total + (item.price * item.quantity), 0);
    }

    function getCartCount() {
        return cart.reduce((count, item) => count + item.quantity, 0);
    }

    function formatCurrency(amount) {
        return '$' + amount.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // Actualizar Interfaz Gráfica
    function updateCartUI() {
        // Actualizar badges e indicadores
        const count = getCartCount();
        if (cartBadge) {
            cartBadge.textContent = count;
            cartBadge.style.display = count > 0 ? 'inline-block' : 'none';
        }

        // Actualizar total
        if (cartTotalAmount) {
            cartTotalAmount.textContent = formatCurrency(getCartTotal());
        }

        // Reconstruir lista de artículos
        if (!cartItemsContainer) return;
        
        if (cart.length === 0) {
            cartItemsContainer.innerHTML = `
                <div class="cart-empty-message">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    <p>Tu carrito está vacío</p>
                </div>
            `;
            if (checkoutForm) {
                checkoutForm.style.display = 'none';
            }
        } else {
            if (checkoutForm) {
                checkoutForm.style.display = 'flex';
            }
            
            cartItemsContainer.innerHTML = cart.map(item => {
                // Si la imagen es una URL externa o local subida
                const imgPath = item.image.startsWith('http') || item.image.includes('placeholder')
                    ? item.image 
                    : 'assets/uploads/' + item.image;
                
                return `
                    <div class="cart-item">
                        <img src="${imgPath}" alt="${item.title}" class="cart-item-image" onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%236b7280%22 stroke-width=%221.5%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><rect x=%222%22 y=%222%22 width=%2220%22 height=%2220%22 rx=%2221%22 ry=%222%22></rect><path d=%22M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6%22></path></svg>'">
                        <div class="cart-item-details">
                            <h4 class="cart-item-title">${item.title}</h4>
                            <div class="cart-item-price">${formatCurrency(item.price)}</div>
                            <div class="cart-item-controls">
                                <button type="button" class="cart-qty-btn decrease-qty" data-id="${item.id}">-</button>
                                <span class="cart-qty-num">${item.quantity}</span>
                                <button type="button" class="cart-qty-btn increase-qty" data-id="${item.id}">+</button>
                            </div>
                        </div>
                        <button type="button" class="btn-remove-item" data-id="${item.id}" title="Eliminar del carrito">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                <line x1="14" y1="11" x2="14" y2="17"></line>
                            </svg>
                        </button>
                    </div>
                `;
            }).join('');

            // Agregar event listeners a botones dinámicos en el carrito
            cartItemsContainer.querySelectorAll('.decrease-qty').forEach(btn => {
                btn.addEventListener('click', () => updateQuantity(parseInt(btn.dataset.id), -1));
            });
            cartItemsContainer.querySelectorAll('.increase-qty').forEach(btn => {
                btn.addEventListener('click', () => updateQuantity(parseInt(btn.dataset.id), 1));
            });
            cartItemsContainer.querySelectorAll('.btn-remove-item').forEach(btn => {
                btn.addEventListener('click', () => removeFromCart(parseInt(btn.dataset.id)));
            });
        }
    }

    // Manejar Envío a WhatsApp
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const clientNameInput = document.getElementById('clientName');
            const clientNotesInput = document.getElementById('clientNotes');
            
            const clientName = clientNameInput ? clientNameInput.value.trim() : '';
            const clientNotes = clientNotesInput ? clientNotesInput.value.trim() : '';
            
            if (clientName === '') {
                alert('Por favor ingresa tu nombre.');
                clientNameInput.focus();
                return;
            }

            // Obtener el número de teléfono desde el atributo del botón de envío
            const btnSubmit = checkoutForm.querySelector('button[type="submit"]');
            const whatsappPhone = btnSubmit ? btnSubmit.dataset.phone : '526121368478';

            // Construir mensaje de WhatsApp
            let message = `🛒 *Nuevo Pedido - ArriagaMarket*\n`;
            message += `------------------------------------------\n`;
            message += `👤 *Cliente:* ${clientName}\n`;
            if (clientNotes) {
                message += `📝 *Nota:* ${clientNotes}\n`;
            }
            message += `------------------------------------------\n\n`;
            message += `📦 *Productos:* \n`;
            
            cart.forEach(item => {
                const itemTotal = item.price * item.quantity;
                message += `• *${item.quantity}x* ${item.title} _(${formatCurrency(item.price)} c/u)_ \n`;
            });
            
            message += `\n💵 *Total del Pedido:* ${formatCurrency(getCartTotal())}\n`;
            message += `------------------------------------------\n`;
            message += `¡Hola! Me gustaría confirmar la compra de estos equipos WISP.`;

            // Codificar el texto
            const encodedMessage = encodeURIComponent(message);
            
            // URL de la API de WhatsApp
            const whatsappUrl = `https://wa.me/${whatsappPhone}?text=${encodedMessage}`;
            
            // Vaciar carrito tras la compra y cerrar
            localStorage.removeItem('wisp_cart');
            cart = [];
            updateCartUI();
            closeCart();
            
            // Limpiar inputs del formulario
            if (clientNameInput) clientNameInput.value = '';
            if (clientNotesInput) clientNotesInput.value = '';

            // Redireccionar a WhatsApp
            window.open(whatsappUrl, '_blank');
        });
    }
});
