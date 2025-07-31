### Authentication Endpoints

#### POST /api/auth/register
```json
// Request
{
    "name": "John Doe",
    "email": "john@example.com", 
    "password": "password123",
    "password_confirmation": "password123",
}

// Response (201)
{
    "message": "User registered successfully",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "role": "customer"
    },
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "refresh_token": "abc123...",
    "token_type": "Bearer",
    "expires_in": 3600
}
```

#### POST /api/auth/login
```json
// Request
{
    "email": "admin@example.com",
    "password": "password"
}

// Response (200)
{
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "Admin User",
        "email": "admin@example.com",
        "role": "admin"
    },
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "refresh_token": "xyz789...",
    "token_type": "Bearer",
    "expires_in": 3600
}
```

#### POST /api/auth/refresh
```json
// Request
{
    "refresh_token": "xyz789..."
}

// Response (200)
{
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "Bearer",
    "expires_in": 3600
}
```

#### POST /api/auth/logout
```json
// Headers: Authorization: Bearer {access_token}
// Response (200)
{
    "message": "Logout successful"
}
```

### Product Endpoints (Admin Only)

#### GET /api/products
```json
// Headers: Authorization: Bearer {admin_token}
// Response (200)
{
    "message": "Products retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "iPhone 15 Pro",
            "description": "Latest iPhone",
            "price": "999.99",
            "stock": 50,
            "category": "Electronics",
            "is_active": true,
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
        }
    ]
}
```

#### POST /api/products
```json
// Headers: Authorization: Bearer {admin_token}
// Request
{
    "name": "New Product",
    "description": "Product description",
    "price": 99.99,
    "stock": 10,
    "category": "Electronics",
    "is_active": true
}

// Response (201)
{
    "message": "Product created successfully",
    "data": {
        "id": 2,
        "name": "New Product",
        "description": "Product description",
        "price": "99.99",
        "stock": 10,
        "category": "Electronics",
        "is_active": true,
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
    }
}
```

### Customer Endpoints

#### GET /api/products/catalog
```json
// Headers: Authorization: Bearer {customer_token}
// Response (200) - Only active products with stock
{
    "message": "Product catalog retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "iPhone 15 Pro",
            "description": "Latest iPhone",
            "price": "999.99",
            "stock": 50,
            "category": "Electronics",
            "is_active": true
        }
    ]
}
```

#### POST /api/orders
```json
// Headers: Authorization: Bearer {customer_token}
// Request
{
    "items": [
        {
            "product_id": 1,
            "quantity": 2
        },
        {
            "product_id": 3,
            "quantity": 1
        }
    ]
}

// Response (201)
{
    "message": "Order created successfully",
    "data": {
        "id": 1,
        "user_id": 2,
        "total_amount": "2999.97",
        "status": "pending",
        "created_at": "2024-01-01T00:00:00.000000Z",
        "order_items": [
            {
                "id": 1,
                "product_id": 1,
                "quantity": 2,
                "unit_price": "999.99",
                "subtotal": "1999.98",
                "product": {
                    "id": 1,
                    "name": "iPhone 15 Pro",
                    "price": "999.99"
                }
            }
        ]
    }
}
```

#### GET /api/orders
```json
// Headers: Authorization: Bearer {customer_token}
// Response (200) - Customer sees only their orders
{
    "message": "Orders retrieved successfully",
    "data": [
        {
            "id": 1,
            "user_id": 2,
            "total_amount": "2999.97",
            "status": "pending",
            "created_at": "2024-01-01T00:00:00.000000Z",
            "order_items": [
                {
                    "id": 1,
                    "product_id": 1,
                    "quantity": 2,
                    "unit_price": "999.99",
                    "subtotal": "1999.98",
                    "product": {
                        "id": 1,
                        "name": "iPhone 15 Pro"
                    }
                }
            ]
        }
    ]
}
```
