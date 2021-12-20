class Seller {

    constructor(ordersUrl, setCourierUrl, couriersListUrl) {
        this.ordersUrl = ordersUrl;
        this.setCourierUrl = setCourierUrl;
        this.couriersListUrl = couriersListUrl;
        this.couriers = [];
        this.courierInterface = null;
        this.ordersTable = null;
    }

    setCourier(orderId, courier) {
        const url = this.setCourierUrl.replace('{1}', orderId).replace('{2}', courier.id);
        return $.ajax({
            url: url,
            method: 'post',
            dataType: 'json'
        }).then(response => {
            if (!response.success) {
                throw new Error(response.message);
            }
            return response;
        });
    }

    getCouriers() {
        return $.ajax({
            url: this.couriersListUrl,
            method: 'get',
            dataType: 'json'
        }).then(response => {
            if (!response.success) {
                throw new Error(response.message);
            }
            return response;
        });
    }

    setCourierInterface(courierInterface) {
        this.courierInterface = courierInterface;
    }

    reload() {
        if (this.ordersTable) {
            this.ordersTable.ajax.reload();
        }
    }

    init() {
        this.ordersTable = $('#orders').DataTable({
            "processing": true,
            "ajax": {
                "url": this.ordersUrl,
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
                {"data": "productsTotal"},
                {"data": "deliveryTotal"},
                {"data": "grandTotal"},
                {
                    "data": "set",
                    "mRender": function (value, action, row) {
                        return '<button action-set-courier="' + row.id + '" class="btn btn-sm btn-success">Set Random Courier</button>';
                    }
                }
            ],
            "order": [[0, "desc"]]
        }).on('draw', () => {
            const seller = this;
            $('#orders').find('[action-set-courier]').click(
                function () {
                    const min = Math.ceil(0);
                    const max = Math.floor(seller.couriers.length);
                    const index = Math.floor(Math.random() * (max - min) + min);
                    const courier = seller.couriers.length ? seller.couriers[index] : null;
                    const orderId = $(this).attr('action-set-courier');

                    if (courier) {
                        seller.setCourier(orderId, courier).then(response => {
                            seller.ordersTable.ajax.reload();
                            if (seller.courierInterface) {
                                seller.courierInterface.reload();
                            }
                            alert("Order set to the courier " + courier.name);
                        }).catch(e => alert(e.responseJSON ? e.responseJSON.message : e.message));
                    } else {
                        alert('No courier to select');
                    }
                }
            );
        });
        this.getCouriers().then(response => this.couriers = response.value);
    }
}