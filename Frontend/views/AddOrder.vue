<template>
    <div class="order-container">
        <!-- HEADER -->
        <div class="header-section">
            <h2>Create New Order</h2>
            <p class="subtitle">Manage new orders by selecting a customer and product items.</p>
        </div>

        <form @submit.prevent="handleSubmit" novalidate>
            <div class="layout-grid">

                <!-- LEFT COLUMN: Customer Info & Product Cards -->
                <div class="main-content">

                    <!-- SECTION 1: CUSTOMER INFORMATION -->
                    <div class="card">
                        <div class="card-header">
                            <h3><span class="icon">👤</span> Customer Information</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="customer_name">Customer Name <span class="required">*</span></label>
                                <input
                                    id="customer_name"
                                    v-model="form.customer_name"
                                    type="text"
                                    maxlength="256"
                                    placeholder="Enter full name"
                                    :class="{ 'is-invalid': errors.customer_name }"
                                    @input="clearError('customer_name')"
                                />
                                <span v-if="errors.customer_name" class="error-text">{{ errors.customer_name }}</span>
                            </div>

                            <div class="form-row">
                                <div class="form-group half">
                                    <label for="customer_email">Customer Email <span class="required">*</span></label>
                                    <input
                                        id="customer_email"
                                        v-model="form.customer_email"
                                        type="email"
                                        maxlength="256"
                                        placeholder="example@email.com"
                                        :class="{ 'is-invalid': errors.customer_email }"
                                        @input="clearError('customer_email')"
                                    />
                                    <span v-if="errors.customer_email" class="error-text">{{ errors.customer_email }}</span>
                                </div>

                                <div class="form-group half">
                                    <label for="customer_phone">Customer Phone <span class="required">*</span></label>
                                    <input
                                        id="customer_phone"
                                        v-model="form.customer_phone"
                                        type="text"
                                        maxlength="20"
                                        placeholder="08123456789"
                                        :class="{ 'is-invalid': errors.customer_phone }"
                                        @input="clearError('customer_phone')"
                                    />
                                    <span v-if="errors.customer_phone" class="error-text">{{ errors.customer_phone }}</span>
                                </div>
                            </div>

                            <div class="form-group mb-0">
                                <label for="notes">Notes</label>
                                <textarea
                                    id="notes"
                                    v-model="form.notes"
                                    maxlength="256"
                                    rows="2"
                                    placeholder="Additional notes (optional)"
                                    :class="{ 'is-invalid': errors.notes }"
                                    @input="clearError('notes')"
                                ></textarea>
                                <span v-if="errors.notes" class="error-text">{{ errors.notes }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: SELECTED PRODUCTS (CARD LIST) -->
                    <div class="card">
                        <div class="card-header header-with-action">
                            <h3><span class="icon">📦</span> Selected Products</h3>
                            <button type="button" class="btn btn-outline" @click="openCatalogModal">
                                + Browse & Add Product
                            </button>
                        </div>

                        <div class="card-body">
                            <span v-if="errors.list_product" class="error-text block mb-3">
                                {{ errors.list_product }}
                            </span>

                            <!-- Empty State -->
                            <div v-if="form.list_product.length === 0" class="empty-product-state">
                                <div class="empty-icon">🛍️</div>
                                <h4>No products selected yet</h4>
                                <p>Click the button above to open the catalog and select products.</p>
                            </div>

                            <!-- List Selected Product Cards -->
                            <div v-else class="selected-product-list">
                                <div
                                    v-for="(item, index) in form.list_product"
                                    :key="item.id_product"
                                    class="selected-item-card"
                                >
                                    <div class="item-info">
                                        <h4 class="item-title">{{ getProductDetails(item.id_product)?.name }}</h4>
                                        <div class="item-meta">
                                            <span class="stock-badge">
                                                Available Stock: {{ getProductDetails(item.id_product)?.quantity || 0 }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Quantity Stepper Controls -->
                                    <div class="item-qty-control">
                                        <label class="qty-label">Qty:</label>
                                        <div class="stepper">
                                            <button
                                                type="button"
                                                class="stepper-btn"
                                                :disabled="item.quantity <= 1"
                                                @click="decrementQty(index)"
                                            >-</button>
                                            <input
                                                v-model.number="item.quantity"
                                                type="number"
                                                min="1"
                                                :max="getProductDetails(item.id_product)?.quantity || 1"
                                                class="stepper-input"
                                                @input="validateQuantity(index)"
                                            />
                                            <button
                                                type="button"
                                                class="stepper-btn"
                                                :disabled="item.quantity >= (getProductDetails(item.id_product)?.quantity || 1)"
                                                @click="incrementQty(index)"
                                            >+</button>
                                        </div>
                                    </div>

                                    <!-- Remove Button -->
                                    <button
                                        type="button"
                                        class="btn-remove-card"
                                        title="Remove"
                                        @click="removeProductRow(index)"
                                    >
                                        &times;
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- RIGHT COLUMN: ORDER SUMMARY (STICKY) -->
                <div class="sidebar-content">
                    <div class="card sticky-card">
                        <div class="card-header">
                            <h3>Summary</h3>
                        </div>
                        <div class="card-body">
                            <div class="summary-row">
                                <span>Total Product Types</span>
                                <strong>{{ form.list_product.length }} Item(s)</strong>
                            </div>
                            <div class="summary-row">
                                <span>Total Quantity Units</span>
                                <strong>{{ totalQuantityCount }} Pcs</strong>
                            </div>

                            <hr class="divider" />

                            <button type="submit" class="btn btn-primary btn-block" :disabled="isSubmitting">
                                <span>{{ isSubmitting ? 'Processing...' : 'Submit Order' }}</span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </form>

        <!-- PRODUCT CATALOG MODAL -->
        <div v-if="isCatalogOpen" class="modal-overlay" @click.self="closeCatalogModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Select Product from Catalog</h3>
                    <button type="button" class="close-modal" @click="closeCatalogModal">&times;</button>
                </div>

                <div class="modal-body">
                    <!-- Search Filter -->
                    <div class="form-group mb-3">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="🔍 Search product name..."
                            class="search-input"
                        />
                    </div>

                    <!-- Catalog Grid -->
                    <div class="catalog-grid">
                        <div
                            v-for="prod in filteredCatalog"
                            :key="prod.id"
                            class="catalog-item-card"
                            :class="{ 'already-added': isProductSelected(prod.id), 'out-of-stock': prod.quantity <= 0 }"
                        >
                            <div class="catalog-item-body">
                                <h5>{{ prod.name }}</h5>
                                <p class="catalog-desc">{{ prod.description || 'No description available' }}</p>
                                <div class="catalog-stock">Stock: <strong>{{ prod.quantity }}</strong></div>
                            </div>
                            <div class="catalog-item-footer">
                                <button
                                    type="button"
                                    class="btn btn-sm"
                                    :class="isProductSelected(prod.id) ? 'btn-success' : 'btn-primary'"
                                    :disabled="prod.quantity <= 0 || isProductSelected(prod.id)"
                                    @click="addProductFromCatalog(prod)"
                                >
                                    {{ isProductSelected(prod.id) ? '✓ Added' : '+ Select' }}
                                </button>
                            </div>
                        </div>

                        <div v-if="filteredCatalog.length === 0" class="empty-search">
                            No matching products found.
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="closeCatalogModal">Done</button>
                </div>
            </div>
        </div>

    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { fetchApi } from "../utils/ApiUtils.js";
import router from "@/router.js";

const availableProducts = ref([]);
const isSubmitting = ref(false);

// Modal Catalog State
const isCatalogOpen = ref(false);
const searchQuery = ref('');

const form = reactive({
    customer_name: '',
    customer_email: '',
    customer_phone: '',
    notes: '',
    list_product: []
});

const errors = reactive({});

// Load product data
const loadProducts = async () => {
    try {
        const response = await fetchApi({
            method: 'GET',
            url: '/api/product',
        });
        availableProducts.value = response.data || [];
    } catch (error) {
        console.error('Failed to load products:', error);
    }
};

// Filter catalog based on keyword
const filteredCatalog = computed(() => {
    if (!searchQuery.value) return availableProducts.value;
    return availableProducts.value.filter(p =>
        p.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

// Calculate total quantity count
const totalQuantityCount = computed(() => {
    return form.list_product.reduce((acc, curr) => acc + (Number(curr.quantity) || 0), 0);
});

// Helper: Get product details by ID
const getProductDetails = (productId) => {
    return availableProducts.value.find(p => p.id === productId);
};

// Check if product is already added to the form
const isProductSelected = (productId) => {
    return form.list_product.some(item => item.id_product === productId);
};

// Controls Modal
const openCatalogModal = () => { isCatalogOpen.value = true; };
const closeCatalogModal = () => { isCatalogOpen.value = false; };

// Add product from modal
const addProductFromCatalog = (product) => {
    if (isProductSelected(product.id)) return;

    form.list_product.push({
        id_product: product.id,
        quantity: 1
    });
    delete errors.list_product;
};

// Remove product item
const removeProductRow = (index) => {
    form.list_product.splice(index, 1);
};

// Stepper Quantity
const incrementQty = (index) => {
    const item = form.list_product[index];
    const maxStock = getProductDetails(item.id_product)?.quantity || 1;
    if (item.quantity < maxStock) {
        item.quantity++;
    }
};

const decrementQty = (index) => {
    const item = form.list_product[index];
    if (item.quantity > 1) {
        item.quantity--;
    }
};

const validateQuantity = (index) => {
    const item = form.list_product[index];
    const maxStock = getProductDetails(item.id_product)?.quantity || 1;

    if (item.quantity > maxStock) item.quantity = maxStock;
    if (item.quantity < 1 || !item.quantity) item.quantity = 1;
};

const clearError = (field) => {
    delete errors[field];
};

// Form Validation
const validateForm = () => {
    Object.keys(errors).forEach(key => delete errors[key]);
    let isValid = true;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!form.customer_name?.trim()) {
        errors.customer_name = 'Customer name is required.';
        isValid = false;
    }

    if (!form.customer_email?.trim()) {
        errors.customer_email = 'Customer email is required.';
        isValid = false;
    } else if (!emailRegex.test(form.customer_email)) {
        errors.customer_email = 'Invalid email format.';
        isValid = false;
    }

    if (!form.customer_phone?.trim()) {
        errors.customer_phone = 'Customer phone is required.';
        isValid = false;
    }

    if (form.list_product.length === 0) {
        errors.list_product = 'Please select at least 1 product from the catalog.';
        isValid = false;
    }

    return isValid;
};

// Submit Handler
const handleSubmit = async () => {
    if (!validateForm()) return;

    isSubmitting.value = true;
    try {
        const payload = {
            customer_name: form.customer_name,
            customer_email: form.customer_email,
            customer_phone: form.customer_phone,
            notes: form.notes || null,
            list_product: form.list_product.map(p => ({
                id_product: Number(p.id_product),
                quantity: Number(p.quantity)
            }))
        };

        const response = await fetchApi({
            method: 'POST',
            url: '/api/order',
            data: payload
        });

        alert('Order submitted successfully!');

        const idOrder = response.data.id_order
        await router.push(`/orders/${idOrder}`);
    } catch (error) {
        console.error('Failed to submit order:', error);
        if (error.response && error.response.status === 422 && error.response.data.errors) {
            const bErrors = error.response.data.errors;
            if (bErrors.customer_name) errors.customer_name = bErrors.customer_name[0];
            if (bErrors.customer_email) errors.customer_email = bErrors.customer_email[0];
            if (bErrors.customer_phone) errors.customer_phone = bErrors.customer_phone[0];
            if (bErrors.notes) errors.notes = bErrors.notes[0];
            if (bErrors.list_product) errors.list_product = bErrors.list_product[0];
        } else {
            const errorMsg = error.response?.data?.message || 'A system error occurred. Please try again.';
            alert(errorMsg);
        }
    } finally {
        isSubmitting.value = false;
    }
};

onMounted(() => {
    loadProducts();
});
</script>

<style scoped>
.order-container {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    max-width: 1150px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.header-section { margin-bottom: 1.5rem; }
h2 { color: #111827; font-size: 1.75rem; margin: 0 0 0.25rem 0; font-weight: 700; }
.subtitle { color: #6b7280; margin: 0; font-size: 0.95rem; }

/* Grid Layout */
.layout-grid {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 1.5rem;
}

@media (max-width: 850px) {
    .layout-grid { grid-template-columns: 1fr; }
}

/* Card General */
.card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}

.card-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #f3f4f6;
}

.card-header h3 {
    margin: 0;
    font-size: 1.05rem;
    color: #1f2937;
    font-weight: 600;
}

.card-body { padding: 1.25rem; }

.card-header.header-with-action {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

@media (min-width: 640px) {
    .card-header.header-with-action {
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
    }
}

/* Customer Form */
.form-group { display: flex; flex-direction: column; margin-bottom: 1rem; }
.form-row { display: flex; gap: 1rem; }
.half { flex: 1; }
label { font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #374151; }
.required { color: #ef4444; }

input, textarea {
    padding: 9px 12px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 0.9rem;
    outline: none;
    transition: all 0.2s;
}

input:focus, textarea:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
}

.is-invalid { border-color: #ef4444 !important; }
.error-text { color: #ef4444; font-size: 0.8rem; margin-top: 0.25rem; }

/* Empty Product State */
.empty-product-state {
    text-align: center;
    padding: 2.5rem 1rem;
    background: #fafafa;
    border: 2px dashed #e5e7eb;
    border-radius: 10px;
}
.empty-icon { font-size: 2.5rem; margin-bottom: 0.5rem; }
.empty-product-state h4 { margin: 0 0 0.25rem 0; color: #374151; }
.empty-product-state p { margin: 0; color: #9ca3af; font-size: 0.875rem; }

/* Selected Product Cards */
.selected-product-list { display: flex; flex-direction: column; gap: 0.75rem; }

.selected-item-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    gap: 1rem;
}

.item-info { flex: 1; }
.item-title { margin: 0 0 0.25rem 0; font-size: 0.95rem; color: #111827; }
.stock-badge { font-size: 0.75rem; background: #e0f2fe; color: #0369a1; padding: 2px 8px; border-radius: 4px; font-weight: 500; }

.item-qty-control { display: flex; align-items: center; gap: 0.5rem; }
.qty-label { font-size: 0.8rem; color: #6b7280; }

.stepper { display: flex; align-items: center; border: 1px solid #d1d5db; border-radius: 6px; background: #fff; overflow: hidden; }
.stepper-btn { border: none; background: #f3f4f6; width: 30px; height: 32px; cursor: pointer; font-weight: bold; }
.stepper-btn:hover:not(:disabled) { background: #e5e7eb; }
.stepper-btn:disabled { color: #ccc; cursor: not-allowed; }
.stepper-input { width: 45px; height: 32px; border: none; text-align: center; font-size: 0.85rem; padding: 0; }

.btn-remove-card {
    background: transparent;
    border: none;
    color: #ef4444;
    font-size: 1.25rem;
    cursor: pointer;
    padding: 0 0.5rem;
}
.btn-remove-card:hover { color: #b91c1c; }

/* Sidebar Summary */
.sticky-card { position: sticky; top: 1rem; }
.summary-row { display: flex; justify-content: space-between; font-size: 0.9rem; margin-bottom: 0.75rem; color: #4b5563; }
.divider { border: 0; border-top: 1px solid #e5e7eb; margin: 1rem 0; }

/* Buttons */
.btn {
    padding: 9px 16px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.875rem;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-primary { background: #2563eb; color: white; }
.btn-primary:hover:not(:disabled) { background: #1d4ed8; }
.btn-outline { background: transparent; border: 1px solid #2563eb; color: #2563eb; }
.btn-outline:hover { background: #eff6ff; }
.btn-secondary { background: #e5e7eb; color: #374151; }
.btn-success { background: #10b981; color: white; }
.btn-block { width: 100%; }
.btn-sm { padding: 5px 10px; font-size: 0.8rem; }

/* Modal Catalog */
.modal-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex; align-items: center; justify-content: center;
    z-index: 1000;
}

.modal-content {
    background: #fff;
    width: 90%;
    max-width: 650px;
    max-height: 85vh;
    border-radius: 12px;
    display: flex;
    flex-direction: column;
}

.modal-header { padding: 1rem 1.25rem; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; }
.modal-header h3 { margin: 0; font-size: 1.1rem; }
.close-modal { background: none; border: none; font-size: 1.5rem; cursor: pointer; }

.modal-body { padding: 1.25rem; overflow-y: auto; }
.search-input { width: 100%; box-sizing: border-box; }

.catalog-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 0.75rem; }

.catalog-item-card {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 0.85rem;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    background: #fff;
}
.catalog-item-card.already-added { background: #f0fdf4; border-color: #bbf7d0; }
.catalog-item-card.out-of-stock { opacity: 0.6; }

.catalog-item-card h5 { margin: 0 0 0.25rem 0; font-size: 0.9rem; }
.catalog-desc { font-size: 0.75rem; color: #6b7280; margin: 0 0 0.5rem 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.catalog-stock { font-size: 0.75rem; color: #374151; margin-bottom: 0.5rem; }
.catalog-item-footer { text-align: right; }

.modal-footer { padding: 0.75rem 1.25rem; border-top: 1px solid #e5e7eb; text-align: right; }
.empty-search { grid-column: 1 / -1; text-align: center; color: #9ca3af; padding: 2rem; }
</style>