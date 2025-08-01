# API Test Cases untuk SCM Backend

## Test Case 1: Login User
**URL:** `POST /api/login`
**Method:** POST
**Headers:**
```
Content-Type: application/json
Accept: application/json
```
**Request Body:**
```json
{
    "email": "user@example.com",
    "password": "password123"
}
```
**Expected Response (200):**
```json
{
    "status": true,
    "message": "Login berhasil",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "user@example.com",
            "role": "supplier-marketing",
            "bp_code": "BP001"
        },
        "token": "1|abc123def456...",
        "token_type": "Bearer"
    }
}
```
**Test Scenario:** User berhasil login dengan kredensial yang valid

---

## Test Case 2: Get Purchase Order List (Supplier Marketing)
**URL:** `GET /api/supplier-marketing/po/index`
**Method:** GET
**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```
**Expected Response (200):**
```json
{
    "status": true,
    "message": "Data berhasil diambil",
    "data": {
        "current_page": 1,
        "data": [
            {
                "po_no": "PO-2024-001",
                "po_date": "2024-01-15",
                "supplier_code": "BP001",
                "supplier_name": "PT Supplier ABC",
                "total_amount": 50000000,
                "status": "active",
                "response_status": "pending"
            }
        ],
        "total": 1,
        "per_page": 10
    }
}
```
**Test Scenario:** Supplier marketing berhasil mengambil daftar Purchase Order

---

## Test Case 3: Update Delivery Note Detail
**URL:** `PUT /api/supplier-marketing/dn/update`
**Method:** PUT
**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```
**Request Body:**
```json
{
    "dn_detail_no": "DN-DET-001",
    "qty_confirm": 100,
    "qty_outstanding": 0,
    "notes": "Barang sudah diterima dengan baik"
}
```
**Expected Response (200):**
```json
{
    "status": true,
    "message": "Data berhasil diupdate",
    "data": {
        "dn_detail_no": "DN-DET-001",
        "qty_confirm": 100,
        "qty_outstanding": 0,
        "notes": "Barang sudah diterima dengan baik",
        "updated_at": "2024-01-15T10:30:00Z"
    }
}
```
**Test Scenario:** Supplier berhasil mengupdate detail Delivery Note

---

## Test Case 4: Get Dashboard Data (Super Admin)
**URL:** `GET /api/super-admin/dashboard`
**Method:** GET
**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```
**Expected Response (200):**
```json
{
    "status": true,
    "message": "Data dashboard berhasil diambil",
    "data": {
        "total_users": 150,
        "active_users": 45,
        "total_po": 1250,
        "total_dn": 980,
        "monthly_stats": {
            "january": 120,
            "february": 135,
            "march": 110
        },
        "recent_activities": [
            {
                "user": "John Doe",
                "action": "Login",
                "timestamp": "2024-01-15T10:30:00Z"
            }
        ]
    }
}
```
**Test Scenario:** Super admin berhasil mengambil data dashboard

---

## Test Case 5: Create User (Super Admin)
**URL:** `POST /api/super-admin/user/store`
**Method:** POST
**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```
**Request Body:**
```json
{
    "name": "Jane Smith",
    "email": "jane.smith@company.com",
    "password": "securepassword123",
    "role": 5,
    "bp_code": "BP002",
    "status": "active"
}
```
**Expected Response (201):**
```json
{
    "status": true,
    "message": "User berhasil dibuat",
    "data": {
        "id": 151,
        "name": "Jane Smith",
        "email": "jane.smith@company.com",
        "role": "supplier-marketing",
        "bp_code": "BP002",
        "status": "active",
        "created_at": "2024-01-15T10:30:00Z"
    }
}
```
**Test Scenario:** Super admin berhasil membuat user baru

---

## Test Case 6: Get Performance Report (Admin Purchasing)
**URL:** `GET /api/admin-purchasing/performance-report/index/BP001`
**Method:** GET
**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```
**Expected Response (200):**
```json
{
    "status": true,
    "message": "Data performance report berhasil diambil",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "filename": "performance_report_jan_2024.pdf",
                "file_size": "2.5MB",
                "upload_date": "2024-01-15",
                "supplier_code": "BP001",
                "status": "active"
            }
        ],
        "total": 1,
        "per_page": 10
    }
}
```
**Test Scenario:** Admin purchasing berhasil mengambil daftar performance report

---

## Test Case 7: Sync Data (Admin Warehouse)
**URL:** `GET /api/admin-warehouse/sync`
**Method:** GET
**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```
**Expected Response (200):**
```json
{
    "status": true,
    "message": "Sinkronisasi data berhasil",
    "data": {
        "po_header_synced": 25,
        "po_detail_synced": 150,
        "dn_header_synced": 30,
        "dn_detail_synced": 180,
        "sync_time": "2024-01-15T10:30:00Z"
    }
}
```
**Test Scenario:** Admin warehouse berhasil melakukan sinkronisasi data

---

## Test Case 8: Change Password
**URL:** `POST /api/supplier-marketing/change-password`
**Method:** POST
**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```
**Request Body:**
```json
{
    "current_password": "oldpassword123",
    "new_password": "newsecurepassword456",
    "confirm_password": "newsecurepassword456"
}
```
**Expected Response (200):**
```json
{
    "status": true,
    "message": "Password berhasil diubah",
    "data": {
        "user_id": 1,
        "password_changed_at": "2024-01-15T10:30:00Z"
    }
}
```
**Test Scenario:** User berhasil mengubah password

---

## Test Case 9: Logout
**URL:** `POST /api/supplier-marketing/logout`
**Method:** POST
**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```
**Expected Response (200):**
```json
{
    "status": true,
    "message": "Logout berhasil",
    "data": {
        "user_id": 1,
        "logged_out_at": "2024-01-15T10:30:00Z"
    }
}
```
**Test Scenario:** User berhasil logout

---

## Test Case 10: Get Business Partner List
**URL:** `GET /api/super-admin/partner/list`
**Method:** GET
**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```
**Expected Response (200):**
```json
{
    "status": true,
    "message": "Data business partner berhasil diambil",
    "data": {
        "current_page": 1,
        "data": [
            {
                "bp_code": "BP001",
                "bp_name": "PT Supplier ABC",
                "email": "contact@supplierabc.com",
                "phone": "+62-21-1234567",
                "address": "Jl. Supplier No. 123, Jakarta",
                "status": "active"
            }
        ],
        "total": 1,
        "per_page": 10
    }
}
```
**Test Scenario:** Super admin berhasil mengambil daftar business partner

---

## Catatan Penting untuk Testing:

1. **Authentication:** Semua endpoint kecuali login memerlukan token Bearer
2. **Role-based Access:** Setiap endpoint memiliki role tertentu yang diizinkan
3. **Validation:** Pastikan request body sesuai dengan validation rules
4. **Error Handling:** Test juga skenario error (400, 401, 403, 404, 500)
5. **Data Consistency:** Pastikan data yang digunakan konsisten dengan database

## Role Mapping:
- 1: Super Admin
- 2: Admin Purchasing  
- 3: Admin Warehouse
- 4: Admin Subcont
- 5: Supplier Marketing
- 6: Supplier Subcont Marketing
- 7: Supplier Warehouse
- 8: Supplier Subcont
- 9: Super User 