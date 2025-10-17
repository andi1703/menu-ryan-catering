# Food Cost Form - Table Format Changes

## Overview

Successfully restructured the food cost ingredient input form from a card-based layout to a clean table format, matching the reference design provided.

## Changes Made

### 1. HTML Structure (V_Food_Cost.php)

- **Changed**: `#bahan-container` from a simple `<div>` to a proper HTML table with `<tbody>`
- **Added**: Table structure with headers:
  - No
  - Nama Bahan \*
  - Jumlah \*
  - Satuan
  - Harga per Unit \*
  - Total Harga
  - Aksi
- **Added**: Total keseluruhan display at the bottom of the table

### 2. JavaScript Functions (V_Food_Cost_js.php)

#### `addBahanRowManual()` Function

- **Before**: Created card-based divs with Bootstrap grid (col-md-\*)
- **After**: Creates table rows (`<tr>`) with table cells (`<td>`)
- **Removed**:
  - Pembagian Porsi field
  - Harga/Porsi field
  - Card header with badges
- **Simplified**: Direct calculation of Qty × Harga = Total

#### `addBahanRowFromDatabase()` Function

- **Before**: Card layout with info badges
- **After**: Table row with light blue background (table-info)
- **Kept**: Database info shown as small text under the input
- **Removed**: Pembagian Porsi and complex calculation

#### `calculateTotal()` Function

- **Simplified**: Now calculates `Qty × Harga per Unit = Total Harga`
- **Removed**: Pembagian Porsi division logic
- **Added**: `updateRowNumbers()` helper function

#### `updateRowNumbers()` Function

- **New**: Automatically renumbers rows after add/delete operations
- **Ensures**: Sequential numbering (1, 2, 3, etc.)

### 3. Form Data Collection

- **Updated**: Form submission to exclude `pembagian_porsi`
- **Added**: `id_satuan` to the data collection
- **Simplified**: Data structure for backend processing

### 4. Event Handlers

- **Updated**: Remove button now calls `updateRowNumbers()` after deletion
- **Removed**: `porsi-input` from change event listener
- **Kept**: Real-time calculation on `qty-input` and `harga-input` changes

## Visual Design

### Table Format

```
┌────┬───────────────────────┬────────┬────────┬────────────────┬─────────────┬──────┐
│ No │ Nama Bahan *          │ Jumlah │ Satuan │ Harga per Unit │ Total Harga │ Aksi │
├────┼───────────────────────┼────────┼────────┼────────────────┼─────────────┼──────┤
│ 1  │ [Input/Dropdown]      │ [Qty]  │ [Unit] │ [Price]        │ [Total]     │ [X]  │
│ 2  │ [Input/Dropdown]      │ [Qty]  │ [Unit] │ [Price]        │ [Total]     │ [X]  │
└────┴───────────────────────┴────────┴────────┴────────────────┴─────────────┴──────┘
                                                   TOTAL KESELURUHAN: Rp X,XXX
```

### Row Types

1. **Manual Input Rows**: White background, all fields editable
2. **Database Rows**: Light blue background (table-info), nama_bahan readonly

## Benefits

1. **Cleaner UI**: Table format is more compact and professional
2. **Simplified Logic**: Removed unnecessary pembagian porsi calculations
3. **Better UX**: Clear column headers and aligned data
4. **Responsive**: Bootstrap table-responsive wrapper for mobile
5. **Consistent**: Matches standard table design patterns

## Testing Checklist

- [ ] Click "Input Manual" button - adds new row
- [ ] Click "Pilih dari Database" - selects ingredient and adds row
- [ ] Enter Jumlah and Harga - calculates Total Harga automatically
- [ ] Delete row - renumbers remaining rows automatically
- [ ] Total Keseluruhan updates in real-time
- [ ] Form submission includes correct data structure
- [ ] Edit existing menu - loads data into table format

## Files Modified

1. `application/views/back/food_cost/V_Food_Cost.php` (lines 454-473)
2. `application/views/back/food_cost/V_Food_Cost_js.php` (multiple functions)

## Backward Compatibility

⚠️ **Backend Update Required**: The controller needs to handle the simplified data structure without `pembagian_porsi` field.

Update your backend model/controller to:

- Remove `pembagian_porsi` from database insert/update
- Or set a default value if the field is required
- Update calculation logic if needed

---

**Date**: October 16, 2025
**Status**: ✅ Completed
