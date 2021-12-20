# eleving-rest
REST delivery service with Rate Limit 10 requests per minute.

## Stack
PHP 8, Symfony 6, Postgres
Symfony components: Doctrine, Annotations, Validation, Forms, Twig, RateLimit

## Setup

Clone project from GIT
```
$ git clone https://github.com/olvinize/eleving-rest.git
```

Update **.env** file if necessary (see Docker configuration) and start docker containers
```
$ docker-compose -f eleving-rest/docker-compose.yml up -d
```

To test service, open in browser (**First container launch installs composer dependencies and can take 30 to 60 seconds**)

```
http://localhost:81
```

## Service interface
Demo interface contains three users interfaces:
* Client interface with following functionality
  * products list with ability to add to cart
  * shipping cost calculation. Cost calculated as sum of all products shipping. Each product shipping calculated as sum of characters in Seller un Customer addresses.
  * place order - places order to system
* Seller interface
  * list of orders made
  * set random courier - sets random courier to order and initiates delivery
  * show order - shows full order information
* Courier interface
  * orders list in Delivering or Delivered status
  * set delivered - marks order as Delivered

## API
All api responses has structure below:
```
{
success: true|false
code: int - response code
value: mixed - actual response, depends on call
message: string - optional response text message
}
```

Api calls:
* POST /api/shipping/calc - receives delivery address and products ids
```
{
deliveryAddress: Riga, Brivibas street
products: [1, 4, 6]
}
```
Response is calculated prices
```
{
  value: {
    deliveryTotal: 0,
    productsTotal: 0,
    grandTotal: 0
  }
}
```
* POST /api/order/create - places order to the system
```
{
deliveryAddress: Riga, Brivibas street
products: [1, 4, 6]
}
```
Response
```
{
  value: int - order id
}
```
* GET /api/orders - list orders

Response
```
{
  value: [] - list of orders
}
```
* GET /api/order/{id} - order information

Response
```
{
  value: {} - order object
}
```
* POST /api/order/{id}/setCourier/{courierId} - assigns courier to order and initiates delivery

Response
```
{
  success: true
}
```
* POST /api/order/{id}/delivered - completes order delivery

Response
```
{
  success: true
}
```
* GET /api/products - products list
* GET /api/couriers - couriers list


## Database configuration

With docker launched adminer to browse db on port 8081 (can be changed in docker-compose.yml)

```
http://localhost:8081
System: PostgreSQL
Server: postgres
Username: postgres
Password: example
Database: app
```

Database tables
* courier - couriers list
* order - orders list
* product - products list
* seller - sellers list
* order_product - order products list

## Docker configuration
Docker can be customized through docker/.env file. Accepts following arguments:
* PROXY_PORT - change browser port, if 80 is busy (default 81)
* TZ - container timezone (default Europe/Riga)
* ENV - deployment configuration DEV or PROD (optimizes autoloader, removes composer dev dependencies).  (default DEV)