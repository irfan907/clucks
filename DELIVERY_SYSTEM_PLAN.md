# Delivery & Stock Receiving System - Implementation Plan

## Overview
A comprehensive stock delivery management system for a takeaway business built on Laravel 12 + Livewire + Flux UI.

## Key Features Summary

✅ **Auto-generated Delivery Identifiers** - Format: DEL-YYYY-NNNN  
✅ **Manual Delivery Finalization** - User manually marks as delivered  
✅ **Soft Delete** - Categories and products use soft deletes  
✅ **Pre-filled Receiving** - Received quantity pre-filled with remaining ordered quantity  
✅ **Select Delivery First** - Two-step receiving process (select delivery → create receiving)  
✅ **Current Stock Tracking** - Real-time stock levels calculated from all receivings  
✅ **One Draft Per User** - Automatic draft management  
✅ **Product Snapshots** - Product details preserved in deliveries  
✅ **Multiple Receivings** - Support partial receivings with sequence tracking  
✅ **Category Colors** - Consistent color coding throughout the system

---

## Phase 1: Database Schema & Models

### 1.1 Migrations
- **categories** table
  - `id` (bigint, primary)
  - `name` (string, required)
  - `description` (text, nullable)
  - `display_order` (integer, default 0)
  - `colour` (string, hex color code, default '#3B82F6')
  - `is_active` (boolean, default true)
  - `deleted_at` (timestamp, nullable, soft delete)
  - `timestamps`

- **products** table
  - `id` (bigint, primary)
  - `category_id` (foreign key to categories)
  - `name` (string, required)
  - `unit` (string, e.g., 'kg', 'litre', 'piece', 'box')
  - `minimum_quantity` (decimal 10,2, nullable)
  - `default_quantity` (decimal 10,2, nullable)
  - `display_order` (integer, default 0)
  - `is_active` (boolean, default true)
  - `deleted_at` (timestamp, nullable, soft delete)
  - `timestamps`

- **deliveries** table
  - `id` (bigint, primary)
  - `identifier` (string, unique, auto-generated: DEL-YYYY-NNNN format)
  - `created_by` (foreign key to users)
  - `status` (enum: 'draft', 'submitted', 'approved', 'delivered')
  - `notes` (text, nullable)
  - `created_at`, `updated_at`
  - `submitted_at` (timestamp, nullable)
  - `approved_at` (timestamp, nullable)
  - `finalized_at` (timestamp, nullable, manually set)

- **delivery_items** table
  - `id` (bigint, primary)
  - `delivery_id` (foreign key to deliveries)
  - `product_id` (foreign key to products, nullable - for reference)
  - `product_name_snapshot` (string, required)
  - `unit_snapshot` (string, required)
  - `ordered_quantity` (decimal 10,2, required)
  - `minimum_quantity_snapshot` (decimal 10,2, nullable)
  - `item_note` (text, nullable)
  - `last_edited_at` (timestamp, nullable)
  - `timestamps`

- **receivings** table
  - `id` (bigint, primary)
  - `delivery_id` (foreign key to deliveries)
  - `received_by` (foreign key to users)
  - `receiving_date` (date, required)
  - `receiving_note` (text, nullable)
  - `receiving_sequence` (integer, default 1)
  - `timestamps`

- **received_items** table
  - `id` (bigint, primary)
  - `receiving_id` (foreign key to receivings)
  - `delivery_item_id` (foreign key to delivery_items)
  - `received_quantity` (decimal 10,2, required)
  - `item_note` (text, nullable)
  - `recorded_date` (date, required)
  - `timestamps`

### 1.2 Models
- `Category` - with relationships, scopes, and SoftDeletes trait
- `Product` - with relationships, scopes, and SoftDeletes trait
- `Delivery` - with relationships, status enum, auto-generate identifier, and business logic
- `DeliveryItem` - with relationships
- `Receiving` - with relationships
- `ReceivedItem` - with relationships

### 1.3 Stock Calculation
- Current stock calculated from sum of all received quantities per product
- Stock displayed in product lists and dedicated stock view
- Stock levels shown with units

### 1.4 Enums
- `DeliveryStatus` enum class (draft, submitted, approved, delivered)

---

## Phase 2: Core Business Logic & Services

### 2.1 Services
- **DeliveryService**
  - `getOrCreateDraft(User $user)` - Get existing draft or create new
  - `generateIdentifier()` - Auto-generate delivery identifier (DEL-YYYY-NNNN)
  - `submitDelivery(Delivery $delivery)` - Submit for review
  - `approveDelivery(Delivery $delivery, User $user)` - Approve delivery
  - `finalizeDelivery(Delivery $delivery, User $user)` - Manually mark as delivered

- **ReceivingService**
  - `createReceiving(Delivery $delivery, array $data, User $user)` - Create receiving record
  - `calculateReceivedQuantities(Delivery $delivery)` - Calculate totals per item
  - `getRemainingQuantities(Delivery $delivery)` - Get remaining quantities to receive per item
  - `getNextSequence(Delivery $delivery)` - Get next receiving sequence number
  - `isDeliveryComplete(Delivery $delivery)` - Check if all items fully received

- **StockService**
  - `getCurrentStock(Product $product)` - Calculate current stock for a product
  - `getCurrentStockForAllProducts()` - Get stock levels for all products
  - `getStockByCategory()` - Get stock grouped by category

### 2.2 Policies (Future-ready)
- Basic structure for future permission system

---

## Phase 3: Livewire Components

### 3.1 Category Management
- **`Categories/Index.php`** - List all categories
- **`Categories/Create.php`** - Create new category
- **`Categories/Edit.php`** - Edit category
- **`Categories/Delete.php`** - Delete confirmation

### 3.2 Product Management
- **`Products/Index.php`** - List all products (grouped by category)
- **`Products/Create.php`** - Create new product
- **`Products/Edit.php`** - Edit product
- **`Products/Delete.php`** - Delete confirmation

### 3.3 Delivery Management
- **`Deliveries/MyDeliveries.php`** - User's deliveries (draft, submitted, approved, delivered)
  - Shows current draft prominently
  - Action buttons: Continue, Delete (draft only)
  
- **`Deliveries/Create.php`** - Create/edit draft delivery
  - Category-wise product display
  - Autosave functionality
  - Save & Next Category button
  - Manual Save button
  - Submit button
  
- **`Deliveries/Edit.php`** - Edit submitted delivery (all users can edit)
  - Similar to Create but for submitted deliveries
  
- **`Deliveries/Show.php`** - View delivery details
  - Read-only for approved/delivered
  - Approve button for submitted deliveries
  
- **`Deliveries/AllDeliveries.php`** - All submitted deliveries from all users
  - Excludes drafts
  - Filter by status
  - Edit/View/Approve actions based on status

### 3.4 Receiving Management
- **`Receivings/SelectDelivery.php`** - Select delivery for receiving
  - List all approved deliveries
  - Filter/search functionality
  - Show delivery details and status
  
- **`Receivings/Create.php`** - Create receiving record
  - Delivery pre-selected (from SelectDelivery)
  - Pre-fill received quantity with ordered quantity (remaining)
  - Show ordered quantity and already received quantity
  - Show remaining quantity to receive
  - Enter received quantities per item
  - Item-level notes
  - Receiving-level note
  - Auto-calculate sequence
  
- **`Receivings/Show.php`** - View receiving details
- **`Receivings/List.php`** - List all receivings for a delivery

### 3.5 Stock Management
- **`Stock/Index.php`** - Current stock view
  - Show all products with current stock levels
  - Grouped by category with category colours
  - Show stock quantity with units
  - Highlight low stock (below minimum quantity)
  - Filter by category
  - Search functionality

---

## Phase 4: Views & UI Components

### 4.1 Layout Updates
- Add navigation items to sidebar/header:
  - Categories
  - Products
  - Current Stock
  - My Deliveries
  - All Deliveries
  - Receive Stock

### 4.2 Category Views
- Index, Create, Edit forms with colour picker
- Category colour displayed consistently

### 4.3 Product Views
- Index with category grouping and colours
- Create/Edit forms with category dropdown
- Unit dropdown/input

### 4.4 Delivery Views
- **My Deliveries**: Card/list view with status badges
- **Create/Edit**: 
  - Category tabs or accordion
  - Product list with category colours
  - Quantity inputs with unit display
  - Item note fields
  - Autosave indicator
- **Show**: Read-only view with all details
- **All Deliveries**: Table/list with filters

### 4.5 Receiving Views
- **Select Delivery**: List of approved deliveries with details
- **Create**: 
  - Delivery information header
  - Item list with:
    - Ordered quantity
    - Already received quantity
    - Remaining quantity
    - Pre-filled received quantity input (with remaining)
    - Item notes
  - Receiving-level note
  - Progress indicators for partial receivings
  - Summary of received vs ordered quantities

### 4.6 Stock Views
- **Index**: 
  - Product list grouped by category
  - Category colours as indicators
  - Stock quantity with units
  - Low stock warnings (below minimum)
  - Category filter
  - Search bar
  - Sortable columns

---

## Phase 5: Features & Functionality

### 5.1 Autosave
- Livewire polling or debounced wire:model
- Visual indicator (saving/saved)
- Save on category change, quantity/note edit

### 5.2 Draft Management
- One draft per user enforcement
- Auto-load existing draft on "New Delivery"
- Delete draft functionality

### 5.3 Status Workflow
- Draft → Submitted (user action)
- Submitted → Approved (any user)
- Approved → Delivered (after receiving)

### 5.4 Snapshot System
- Product name and unit snapshotted at delivery creation
- Minimum quantity snapshotted (optional)
- Prevents changes to original product affecting deliveries

### 5.5 Multiple Receivings
- Track sequence number (auto-increment per delivery)
- Calculate total received per item
- Pre-fill received quantity with remaining ordered quantity
- Show progress (received / ordered)
- Manual mark delivery as delivered (not automatic)

### 5.7 Current Stock Display
- Calculate stock from sum of all received quantities per product
- Display in product lists
- Dedicated stock view
- Low stock warnings (below minimum quantity)
- Stock shown with product units

### 5.6 Ordering & Display
- Categories sorted by display_order
- Products sorted by display_order within category
- Category colours used consistently throughout

---

## Phase 6: Routes

```php
// Categories
Route::get('/categories', Categories\Index::class)->name('categories.index');
Route::get('/categories/create', Categories\Create::class)->name('categories.create');
Route::get('/categories/{category}/edit', Categories\Edit::class)->name('categories.edit');

// Products
Route::get('/products', Products\Index::class)->name('products.index');
Route::get('/products/create', Products\Create::class)->name('products.create');
Route::get('/products/{product}/edit', Products\Edit::class)->name('products.edit');

// Deliveries
Route::get('/deliveries/my', Deliveries\MyDeliveries::class)->name('deliveries.my');
Route::get('/deliveries/all', Deliveries\AllDeliveries::class)->name('deliveries.all');
Route::get('/deliveries/create', Deliveries\Create::class)->name('deliveries.create');
Route::get('/deliveries/{delivery}', Deliveries\Show::class)->name('deliveries.show');
Route::get('/deliveries/{delivery}/edit', Deliveries\Edit::class)->name('deliveries.edit');

// Receivings
Route::get('/receivings/select-delivery', Receivings\SelectDelivery::class)->name('receivings.select-delivery');
Route::get('/receivings/create/{delivery}', Receivings\Create::class)->name('receivings.create');
Route::get('/receivings/{receiving}', Receivings\Show::class)->name('receivings.show');
Route::get('/deliveries/{delivery}/receivings', Receivings\List::class)->name('receivings.list');

// Stock
Route::get('/stock', Stock\Index::class)->name('stock.index');
```

---

## Phase 7: Additional Considerations

### 7.1 Validation
- Category name uniqueness
- Product name uniqueness within category
- Delivery identifier uniqueness
- Quantity validations (positive numbers)
- Status transition validations

### 7.2 UI/UX Enhancements
- Status badges with colours
- Category colour indicators
- Progress bars for receiving
- Toast notifications for actions
- Confirmation modals for destructive actions

### 7.3 Data Integrity
- Soft deletes for categories/products (implemented)
- Prevent deletion of categories/products with active deliveries (validation)
- Cascade deletes for delivery items when delivery deleted
- Soft-deleted categories/products still visible in historical deliveries

### 7.4 Performance
- Eager loading relationships
- Indexes on foreign keys and status fields
- Pagination for lists

---

## Implementation Order

1. **Database & Models** (Phase 1)
   - Add soft deletes to categories/products
   - Add delivery identifier auto-generation
2. **Category Management** (Phase 3.1, 4.2)
   - Soft delete support
3. **Product Management** (Phase 3.2, 4.3)
   - Soft delete support
   - Show current stock in product list
4. **Stock View** (Phase 3.5, 4.6)
   - Current stock calculation
   - Stock display by category
5. **Delivery Creation** (Phase 3.3 Create, 4.4)
   - Auto-generate identifier
   - Autosave functionality
6. **My Deliveries** (Phase 3.3 MyDeliveries)
7. **Submission & Approval** (Phase 2.1, Phase 3.3 Edit/Show)
   - Manual finalization
8. **All Deliveries** (Phase 3.3 AllDeliveries)
9. **Receiving System** (Phase 3.4, 4.5)
   - Select delivery flow
   - Pre-fill quantities
   - Auto-sequence
10. **Polish & Testing** (Phase 5.1, 7.2)

---

## Clarifications Received ✓

1. **Delivery Identifier**: ✅ Auto-generated (DEL-YYYY-NNNN format)
2. **Finalized Date**: ✅ Manually set (not automatic)
3. **Complete Delivery**: ✅ Manual mark as delivered (not automatic)
4. **Category/Product Deletion**: ✅ Soft delete (with deleted_at column)
5. **Receiving Sequence**: ✅ Auto-increment per delivery
6. **Pre-fill Receiving**: ✅ Pre-fill received quantity with remaining ordered quantity
7. **Receiving Flow**: ✅ Select delivery first, then create receiving
8. **Current Stock**: ✅ Show current stock calculated from all receivings

---

## Estimated File Structure

```
app/
├── Models/
│   ├── Category.php
│   ├── Product.php
│   ├── Delivery.php
│   ├── DeliveryItem.php
│   ├── Receiving.php
│   └── ReceivedItem.php
├── Livewire/
│   ├── Categories/
│   │   ├── Index.php
│   │   ├── Create.php
│   │   ├── Edit.php
│   │   └── Delete.php
│   ├── Products/
│   │   ├── Index.php
│   │   ├── Create.php
│   │   ├── Edit.php
│   │   └── Delete.php
│   ├── Deliveries/
│   │   ├── MyDeliveries.php
│   │   ├── AllDeliveries.php
│   │   ├── Create.php
│   │   ├── Edit.php
│   │   └── Show.php
│   ├── Receivings/
│   │   ├── SelectDelivery.php
│   │   ├── Create.php
│   │   ├── Show.php
│   │   └── List.php
│   └── Stock/
│       └── Index.php
└── Services/
    ├── DeliveryService.php
    ├── ReceivingService.php
    └── StockService.php

database/migrations/
├── YYYY_MM_DD_create_categories_table.php
├── YYYY_MM_DD_create_products_table.php
├── YYYY_MM_DD_create_deliveries_table.php
├── YYYY_MM_DD_create_delivery_items_table.php
├── YYYY_MM_DD_create_receivings_table.php
└── YYYY_MM_DD_create_received_items_table.php

resources/views/livewire/
├── categories/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
├── products/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
├── deliveries/
│   ├── my-deliveries.blade.php
│   ├── all-deliveries.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
├── receivings/
│   ├── select-delivery.blade.php
│   ├── create.blade.php
│   ├── show.blade.php
│   └── list.blade.php
└── stock/
    └── index.blade.php
```

---

## Notes

- All components will use Flux UI components for consistency
- Follow existing Livewire patterns in the codebase
- Use Laravel's validation and form request patterns
- Implement proper error handling and user feedback
- Ensure responsive design for mobile devices
- Follow Laravel naming conventions and best practices

