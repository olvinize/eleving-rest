class Customer {
    constructor(shippingUrl, orderPlaceUrl, productsUrl) {
        this.shippingUrl = shippingUrl;
        this.placeOrderUrl = orderPlaceUrl;
        this.productsUrl = productsUrl;
        this.cart = {};
        this.products = {};
        this.address = null;
        this.sellerInterface = null;
    }

    placeOrder(address, products) {
        return $.ajax({
            url: this.placeOrderUrl,
            method: 'post',
            dataType: 'json',
            data: {
                deliveryAddress: address,
                products: products
            }
        }).then(response => {
            if (!response.success) {
                throw new Error(response.message);
            }
            return response;
        });
    }

    calcShipping(address, products) {
        return $.ajax({
            url: this.shippingUrl,
            method: 'post',
            dataType: 'json',
            data: {
                deliveryAddress: address,
                products: products
            }
        }).then(response => {
            if (!response.success) {
                throw new Error(response.message);
            }
            return response;
        });
    }

    setSellerInterface(sellerInterface) {
        this.sellerInterface = sellerInterface;
    }

    init() {
        $('#products').DataTable({
            "processing": true,
            "ajax": {
                "url": this.productsUrl,
                "dataSrc": (json) => {
                    json.value.forEach(value => this.products[value.id] = value);
                    return json.value;
                }
            },
            "aoColumns": [
                {"data": "name"},
                {"data": "seller"},
                {"data": "address"},
                {"data": "price"},
                {
                    "data": "id",
                    "mRender": function (value, action, row) {
                        return '<button action-product="' + row.id + '" class="btn btn-sm btn-success">Add to Cart</button>';
                    }
                }
            ]
        }).on('draw', () => {
            const customer = this;
            $('#products').find('[action-product]').on('click',
                function () {
                    const product = customer.products[$(this).attr('action-product')];
                    if (!customer.cart[product.id]) {
                        $('#cart').dataTable().fnAddData([product.name, product.seller, product.address, product.price]);
                        customer.cart[product.id] = product;
                        let total = 0;
                        Object.values(customer.cart).forEach(item => total += item.price);
                        $('[data-total]').html(total);
                    }
                }
            );
        });
        $('#cart').DataTable({
            searching: false,
            data: []
        });
        $('[data-calc]').on('click', () => {
            const address = $('#customerAddress').val();
            const goods = [];
            Object.values(this.cart).forEach(item => goods.push(item.id));
            if (!address) {
                alert('Please provide delivery address');
            } else if (!goods.length) {
                alert('Please select some products');
            } else {
                this.calcShipping(address, goods).then(response => {
                    $('[data-shipping]').html(response.value.deliveryTotal);
                }).catch(e => alert(e.responseJSON ? e.responseJSON.message : e.message));
            }
        });
        $('[data-place-order]').on('click', () => {
            const address = $('#customerAddress').val();
            const goods = [];
            Object.values(this.cart).forEach(item => goods.push(item.id));
            if (!address) {
                alert('Please provide delivery address');
            } else if (!goods.length) {
                alert('Please select some products');
            } else {
                this.placeOrder(address, goods).then(response => {
                    if (this.sellerInterface) {
                        this.sellerInterface.reload();
                    }
                    alert('Thanks order has been placed. Order No #' + response.value);
                }).catch(e => alert(e.responseJSON ? e.responseJSON.message : e.message));
            }
        });
    }
}