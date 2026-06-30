@extends('partials.app', ['title' => 'New Sale'])

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Side: Product Selection & Cart -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Products Selection -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Select Products</h3>

                    <!-- Search Products -->
                    <div class="mb-4">
                        <input type="text" placeholder="Search products..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                    </div>

                    <!-- Products Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Product Card 1 -->
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-lg hover:border-amber-300 transition cursor-pointer" onclick="addToCart('Chicken Breast', 450)">
                            <div class="bg-gradient-to-br from-green-100 to-green-50 h-32 rounded-lg flex items-center justify-center mb-3">
                                <i class="fas fa-drumstick-bite text-4xl text-green-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-800">Chicken Breast</h4>
                            <p class="text-sm text-gray-600 mb-2">Per KG</p>
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-bold text-amber-600">₨450</span>
                                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">In Stock</span>
                            </div>
                        </div>

                        <!-- Product Card 2 -->
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-lg hover:border-amber-300 transition cursor-pointer" onclick="addToCart('Chicken Legs', 380)">
                            <div class="bg-gradient-to-br from-blue-100 to-blue-50 h-32 rounded-lg flex items-center justify-center mb-3">
                                <i class="fas fa-drumstick-bite text-4xl text-blue-600 transform -rotate-45"></i>
                            </div>
                            <h4 class="font-bold text-gray-800">Chicken Legs</h4>
                            <p class="text-sm text-gray-600 mb-2">Per KG</p>
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-bold text-amber-600">₨380</span>
                                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">In Stock</span>
                            </div>
                        </div>

                        <!-- Product Card 3 -->
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-lg hover:border-amber-300 transition cursor-pointer" onclick="addToCart('Chicken Wings', 320)">
                            <div class="bg-gradient-to-br from-amber-100 to-amber-50 h-32 rounded-lg flex items-center justify-center mb-3">
                                <i class="fas fa-feather text-4xl text-amber-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-800">Chicken Wings</h4>
                            <p class="text-sm text-gray-600 mb-2">Per KG</p>
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-bold text-amber-600">₨320</span>
                                <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Low Stock</span>
                            </div>
                        </div>

                        <!-- Product Card 4 -->
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-lg hover:border-amber-300 transition cursor-pointer" onclick="addToCart('Whole Chicken', 550)">
                            <div class="bg-gradient-to-br from-red-100 to-red-50 h-32 rounded-lg flex items-center justify-center mb-3">
                                <i class="fas fa-egg text-4xl text-red-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-800">Whole Chicken</h4>
                            <p class="text-sm text-gray-600 mb-2">Per KG</p>
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-bold text-amber-600">₨550</span>
                                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">In Stock</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Cart & Checkout -->
            <div>
                <!-- Shopping Cart -->
                <div class="bg-white rounded-lg shadow-lg p-6 sticky top-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-shopping-cart text-amber-600"></i>
                        Shopping Cart
                    </h3>

                    <!-- Cart Items -->
                    <div id="cartItems" class="space-y-3 mb-4 max-h-96 overflow-y-auto">
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-shopping-bag text-4xl mb-2"></i>
                            <p>Cart is empty</p>
                        </div>
                    </div>

                    <!-- Cart Summary -->
                    <div class="border-t border-gray-200 pt-4 space-y-3">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal:</span>
                            <span id="subtotal">₨0</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Discount (5%):</span>
                            <span id="discount">-₨0</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Tax (17%):</span>
                            <span id="tax">₨0</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold text-gray-800 border-t border-gray-200 pt-3">
                            <span>Total:</span>
                            <span id="total" class="text-amber-600">₨0</span>
                        </div>
                    </div>

                    <!-- Customer Selection -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Customer</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                            <option>Walk-in Customer</option>
                            <option>Ahmed Hassan</option>
                            <option>Fatima Khan</option>
                            <option>Ali Muhammad</option>
                        </select>
                    </div>

                    <!-- Payment Method -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="payment" value="cash" checked class="mr-2">
                                <span class="text-sm text-gray-700">Cash</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="payment" value="card" class="mr-2">
                                <span class="text-sm text-gray-700">Card</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="payment" value="cheque" class="mr-2">
                                <span class="text-sm text-gray-700">Cheque</span>
                            </label>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-2 mt-6">
                        <button class="btn-primary w-full">
                            <i class="fas fa-check mr-2"></i>
                            Complete Sale
                        </button>
                        <button class="btn-secondary w-full">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let cart = [];

        function addToCart(product, price) {
            const existingItem = cart.find(item => item.product === product);

            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({ product, price, quantity: 1 });
            }

            updateCart();
        }

        function removeFromCart(index) {
            cart.splice(index, 1);
            updateCart();
        }

        function updateQuantity(index, quantity) {
            if (quantity <= 0) {
                removeFromCart(index);
            } else {
                cart[index].quantity = quantity;
                updateCart();
            }
        }

        function updateCart() {
            const cartItemsDiv = document.getElementById('cartItems');

            if (cart.length === 0) {
                cartItemsDiv.innerHTML = `
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-shopping-bag text-4xl mb-2"></i>
                        <p>Cart is empty</p>
                    </div>
                `;
            } else {
                cartItemsDiv.innerHTML = cart.map((item, index) => `
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <p class="font-medium text-gray-800">${item.product}</p>
                            <p class="text-sm text-gray-600">₨${item.price} x ${item.quantity}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="number" value="${item.quantity}" min="1" onchange="updateQuantity(${index}, this.value)" class="w-12 px-2 py-1 border border-gray-300 rounded text-center">
                            <button onclick="removeFromCart(${index})" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `).join('');
            }

            calculateTotals();
        }

        function calculateTotals() {
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const discount = subtotal * 0.05;
            const taxBase = subtotal - discount;
            const tax = taxBase * 0.17;
            const total = taxBase + tax;

            document.getElementById('subtotal').textContent = '₨' + subtotal.toFixed(2);
            document.getElementById('discount').textContent = '-₨' + discount.toFixed(2);
            document.getElementById('tax').textContent = '₨' + tax.toFixed(2);
            document.getElementById('total').textContent = '₨' + total.toFixed(2);
        }
    </script>
@endsection
