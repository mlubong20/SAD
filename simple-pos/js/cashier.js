document.addEventListener('alpine:init', function () {
    Alpine.data('products', function (products) {
        let _this = this;
        return {
            products,
            payment: 0,
            carts: [],
            change: null,
            receiptVisible: false,
            receiptDate: '',
            receiptTime: '',

            // Computed property to calculate the total price before tax
            get totalPrice() {
                return this.carts.reduce((sum, cart) => sum + cart.quantity * cart.product.price, 0);
            },

            // Computed property to calculate the tax (12%)
            get tax() {
                return (this.totalPrice * 0.12).toFixed(2);
            },

            // Computed property to calculate the total price after tax
            get totalWithTax() {
                return (this.totalPrice + parseFloat(this.tax)).toFixed(2);
            },

            // Calculate change based on totalWithTax
            calculateChange: function () {
                const totalWithTax = parseFloat(this.totalWithTax);
                const change = this.payment - totalWithTax;

                if (change < 0) {
                    alert('Not enough payment!');
                    this.change = null; // Reset change to null if insufficient payment
                } else {
                    this.change = change.toFixed(2);
                    this.$refs.change.innerText = `${this.change} PHP`;
                }

                return change;
            },

            validate: function (e) {
                const change = this.calculateChange();

                if (change < 0 || this.carts.length === 0) {
                    e.preventDefault();
                    alert('Please ensure payment is sufficient and there are items in the cart.');
                }
            },

            addToCart: function (id) {
                const product = products.find(product => product.id == id);
                const cart = this.carts.find(cart => cart.product.id == id);

                if (product.quantity < 1) return alert('Out of stock');

                if (cart) {
                    if (cart.quantity < product.quantity) {
                        cart.quantity++;
                    }
                } else {
                    this.carts.push({
                        product,
                        quantity: 1,
                    });
                }
            },

            subtractQuantity: function (cart) {
                cart.quantity--;
                if (cart.quantity < 1) {
                    this.carts = this.carts.filter(c => c.product.id !== cart.product.id);
                }
            },

            addQuantity: function (cart) {
                if (cart.quantity < cart.product.quantity) {
                    cart.quantity++;
                }
            },

            // Print the receipt and toggle visibility
            printReceipt() {
                const now = new Date();
                this.receiptDate = now.toLocaleDateString();
                this.receiptTime = now.toLocaleTimeString();
                this.receiptVisible = true; // Make the receipt visible
            },
        };
    });
});
