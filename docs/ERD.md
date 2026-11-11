# Entity Relationship Diagram (ERD)

## Diagram
```mermaid
erDiagram
    USERS ||--o{ STOCK_INS : creates
    USERS ||--o{ STOCK_OUTS : creates
    USERS ||--o{ ACTIVITY_LOGS : performs
    CATEGORIES ||--o{ PRODUCTS : groups
    LOCATIONS ||--o{ PRODUCTS : stores
    STOCK_INS ||--|{ STOCK_IN_ITEMS : contains
    STOCK_OUTS ||--|{ STOCK_OUT_ITEMS : contains
    PRODUCTS ||--o{ STOCK_IN_ITEMS : received
    PRODUCTS ||--o{ STOCK_OUT_ITEMS : issued

    USERS {
        bigint id PK
        varchar name
        varchar email UK
        varchar password
        varchar role
        boolean is_active
        timestamp created_at
        timestamp updated_at
    }
    CATEGORIES {
        bigint id PK
        varchar name UK
        text description
        timestamp created_at
        timestamp updated_at
    }
    LOCATIONS {
        bigint id PK
        varchar name UK
        text description
        timestamp created_at
        timestamp updated_at
    }
    PRODUCTS {
        bigint id PK
        varchar code UK
        varchar name
        bigint category_id FK
        bigint location_id FK
        varchar brand
        varchar item_type
        varchar unit
        decimal purchase_price
        decimal selling_price
        int stock
        int minimum_stock
        text description
        boolean is_active
        timestamp created_at
        timestamp updated_at
    }
    STOCK_INS {
        bigint id PK
        varchar transaction_code UK
        date transaction_date
        varchar source
        text notes
        bigint user_id FK
        timestamp created_at
        timestamp updated_at
    }
    STOCK_IN_ITEMS {
        bigint id PK
        bigint stock_in_id FK
        bigint product_id FK
        int quantity
        decimal purchase_price
        timestamp created_at
        timestamp updated_at
    }
    STOCK_OUTS {
        bigint id PK
        varchar transaction_code UK
        date transaction_date
        varchar purpose
        varchar recipient
        text notes
        bigint user_id FK
        timestamp created_at
        timestamp updated_at
    }
    STOCK_OUT_ITEMS {
        bigint id PK
        bigint stock_out_id FK
        bigint product_id FK
        int quantity
        timestamp created_at
        timestamp updated_at
    }
    ACTIVITY_LOGS {
        bigint id PK
        bigint user_id FK
        varchar action
        text description
        timestamp created_at
    }
```

## Penjelasan relasi
- Satu kategori dapat memiliki banyak produk.
- Satu lokasi dapat menjadi lokasi banyak produk.
- Satu pengguna dapat membuat banyak transaksi masuk dan keluar.
- Satu transaksi masuk/keluar memiliki satu atau lebih detail.
- Satu produk dapat muncul pada banyak detail transaksi.
- Satu pengguna dapat menghasilkan banyak catatan aktivitas.

## Catatan
`products.location_id` berarti satu produk hanya mempunyai satu lokasi dalam rancangan MVP. Jika kelak satu SKU perlu berada di beberapa gudang, desain harus diubah menjadi tabel saldo per lokasi, bukan menambahkan kolom lokasi kedua.