# Business Partner Unified Search System

## Overview
Sistem ini memungkinkan pencarian dan pengambilan data business partner yang menggabungkan sistem lama dan baru. Ketika mencari dengan `bp_code` lama (dengan akhiran `-1`, `-2`, dll.) atau `bp_code` baru (tanpa akhiran), sistem akan mengembalikan data yang sama.

## Fitur Utama

### 1. Pencarian Terpadu
- Mencari business partner berdasarkan `bp_code` lama atau baru
- Mengembalikan semua data terkait (parent dan child records)
- Mendukung pencarian berdasarkan nama perusahaan

### 2. Relasi Parent-Child
- `bp_code` tanpa akhiran = Parent record
- `bp_code` dengan akhiran (`-1`, `-2`, dll.) = Child record
- Child record terhubung ke parent melalui kolom `parent_bp_code`

## API Endpoints

### 1. Search Business Partner
```
GET /api/v1/super-admin/partner/search?search={search_term}
```

**Parameter:**
- `search` (required): bp_code atau nama perusahaan

**Contoh:**
```bash
# Mencari dengan bp_code lama
GET /api/v1/super-admin/partner/search?search=SLSDELA-1

# Mencari dengan bp_code baru
GET /api/v1/super-admin/partner/search?search=SLSDELA

# Mencari berdasarkan nama
GET /api/v1/super-admin/partner/search?search=DELA CEMARA
```

**Response:**
```json
{
    "success": true,
    "message": "Search completed successfully",
    "data": [
        {
            "bp_code": "SLSDELA",
            "bp_name": "DELA CEMARA INDAH PT.",
            "adr_line_1": "PT. DELA CEMARA INDAH",
            "parent_bp_code": null
        },
        {
            "bp_code": "SLSDELA-1",
            "bp_name": "DELA CEMARA INDAH PT.",
            "adr_line_1": "PT. DELA CEMARA INDAH",
            "parent_bp_code": "SLSDELA"
        },
        {
            "bp_code": "SLSDELA-2",
            "bp_name": "DELA CEMARA INDAH PT.",
            "adr_line_1": "PT. DELA CEMARA INDAH",
            "parent_bp_code": "SLSDELA"
        }
    ]
}
```

### 2. Get Business Partner by Code
```
GET /api/v1/super-admin/partner/{bp_code}
```

**Parameter:**
- `bp_code` (required): bp_code lama atau baru

**Contoh:**
```bash
# Menggunakan bp_code lama
GET /api/v1/super-admin/partner/SLSDELA-1

# Menggunakan bp_code baru
GET /api/v1/super-admin/partner/SLSDELA
```

**Response:**
```json
{
    "success": true,
    "message": "Business Partner found successfully",
    "data": {
        "bp_code": "SLSDELA",
        "bp_name": "DELA CEMARA INDAH PT.",
        "adr_line_1": "PT. DELA CEMARA INDAH",
        "parent_bp_code": null
    }
}
```

## Database Structure

### Tabel: business_partner
```sql
CREATE TABLE business_partner (
    bp_code VARCHAR(25) PRIMARY KEY,
    parent_bp_code VARCHAR(25) NULL,
    bp_name VARCHAR(255) NULL,
    bp_status_desc VARCHAR(25) NULL,
    bp_currency VARCHAR(25) NULL,
    country VARCHAR(25) NULL,
    adr_line_1 VARCHAR(255) NULL,
    adr_line_2 VARCHAR(255) NULL,
    adr_line_3 VARCHAR(255) NULL,
    adr_line_4 VARCHAR(255) NULL,
    bp_phone VARCHAR(255) NULL,
    bp_fax VARCHAR(25) NULL,
    INDEX idx_parent_bp_code (parent_bp_code)
);
```

### Relasi Data
- **Parent Record**: `bp_code` = "SLSDELA", `parent_bp_code` = NULL
- **Child Record 1**: `bp_code` = "SLSDELA-1", `parent_bp_code` = "SLSDELA"
- **Child Record 2**: `bp_code` = "SLSDELA-2", `parent_bp_code` = "SLSDELA"

## Setup dan Maintenance

### 1. Menjalankan Migration
```bash
php artisan migrate
```

### 2. Setup Relasi Data Existing
```bash
php artisan business-partner:setup-relations
```

### 3. Verifikasi Data
```sql
-- Cek parent records
SELECT bp_code, parent_bp_code FROM business_partner WHERE parent_bp_code IS NULL;

-- Cek child records
SELECT bp_code, parent_bp_code FROM business_partner WHERE parent_bp_code IS NOT NULL;

-- Cek relasi untuk SLSDELA
SELECT bp_code, parent_bp_code FROM business_partner 
WHERE bp_code = 'SLSDELA' OR parent_bp_code = 'SLSDELA';
```

## Service Class

### BusinessPartnerUnifiedService
```php
// Get all related business partners
$service = new BusinessPartnerUnifiedService();
$relatedPartners = $service->getRelatedBusinessPartners('SLSDELA-1');

// Get unified data
$unifiedData = $service->getUnifiedBusinessPartnerData('SLSDELA-1');

// Search unified
$searchResults = $service->searchUnifiedBusinessPartners('SLSDELA');
```

## Model Methods

### BusinessPartner Model
```php
// Get base bp_code
$baseCode = $partner->base_bp_code;

// Check if parent record
$isParent = $partner->isParentRecord();

// Check if child record
$isChild = $partner->isChildRecord();

// Scope for related bp_codes
$related = BusinessPartner::relatedBpCodes('SLSDELA-1')->get();
```

## Keuntungan Sistem

1. **Backward Compatible**: Tidak mengganggu sistem yang sudah ada
2. **Unified Search**: Satu endpoint untuk semua jenis bp_code
3. **Data Integrity**: Relasi yang jelas antara parent dan child
4. **Performance**: Menggunakan index database
5. **Scalable**: Mudah ditambahkan logika baru

## Contoh Penggunaan

### Frontend Integration
```javascript
// Search function
async function searchBusinessPartner(searchTerm) {
    const response = await fetch(`/api/v1/super-admin/partner/search?search=${searchTerm}`);
    const data = await response.json();
    return data.data;
}

// Get by code function
async function getBusinessPartnerByCode(bpCode) {
    const response = await fetch(`/api/v1/super-admin/partner/${bpCode}`);
    const data = await response.json();
    return data.data;
}

// Usage examples
const results1 = await searchBusinessPartner('SLSDELA-1'); // Returns SLSDELA, SLSDELA-1, SLSDELA-2
const results2 = await searchBusinessPartner('SLSDELA');   // Returns SLSDELA, SLSDELA-1, SLSDELA-2
const partner = await getBusinessPartnerByCode('SLSDELA-1'); // Returns SLSDELA record
```

## Troubleshooting

### 1. Data tidak muncul dalam search
- Pastikan relasi sudah disetup dengan command `business-partner:setup-relations`
- Cek apakah parent record sudah dibuat untuk child records

### 2. Performance lambat
- Pastikan index sudah dibuat pada kolom `parent_bp_code`
- Gunakan query optimization jika diperlukan

### 3. Data tidak konsisten
- Jalankan ulang command setup untuk memperbaiki relasi
- Cek apakah ada data yang tidak sesuai pattern 