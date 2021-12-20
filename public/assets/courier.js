class Courier {
    constructor(ordersUrl, setDeliveredUrl) {
        this.ordersUrl = ordersUrl;
        this.setDeliveredUrl = setDeliveredUrl;
        this.sellerInterface = null;
        this.ordersTable = null;
    }

    setDelivered(orderId) {
        return $.ajax({
            url: this.setDeliveredUrl.replace(1, orderId),
            method: 'post',
            dataType: 'json'
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

    reload() {
        if (this.ordersTable) {
            this.ordersTable.ajax.reload();
        }
    }

    init() {
        this.ordersTable = $('#courierOrders').DataTable({
            "processing": true,
            "ajax": {
                "url": this.ordersUrl,
                "data": {
                    status: ['delivering', 'delivered']
                },
                "dataSrc": (json) => {
                    //json.value.forEach(value => this.products[value.id] = value);
                    return json.value;
                }
            },
            "aoColumns": [
                {"data": "id"},
                {"data": "deliveryAddress"},
                {"data": "status"},
                {"data": "created"},
                {"data": "delivered"},
                {"data": "courier"},
                {"data": "grandTotal"},
                {
                    "data": "set",
                    "mRender": function (value, action, row) {
                        return '<button action-set-delivered="' + row.id + '" class="btn btn-sm btn-success">Set Delivered</button>';
                    }
                }
            ],
            "order": [[0, "desc"]]
        }).on('draw', () => {
            const courier = this;
            $('#courierOrders').find('[action-set-delivered]').click(
                function () {
                    const orderId = $(this).attr('action-set-delivered');
                    courier.setDelivered(orderId).then(response => {
                        courier.ordersTable.ajax.reload();
                        if (courier.sellerInterface) {
                            courier.sellerInterface.reload();
                        }
                        alert(response.message ? response.message : "Order marked as delivered");
                    }).catch(e => alert(e.responseJSON ? e.responseJSON.message : e.message));

                }
            );
        });
    }
}