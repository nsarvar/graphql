# GraphQL-php and json example

## Server

run php file along with data.json on the server.

## Client
http://localhost/merchants.php

### Get all merchants
```
query {
        merchants{
                _id,
                name,
                type
        }
}
```

### Get all merchants and use pagination feature
```
query {
        merchants(page:"3") {
                _id,
                name,
                type
        }
}
```

### Find merchant by ID
```
query {
        merchant(id:"55478199d2c4830936e6c832") {
                _id,
                name,
                type
        }
}
```

