<template>
    <div class="order-container">
        <!-- HEADER SECTION -->
        <div class="header-section">
            <div class="header-left">
                <button type="button" class="btn-back" @click="goBack">
                    &larr; Back to Orders
                </button>
                <div class="title-with-badge">
                    <h2>Order #{{ order.id }}</h2>
                    <OrderStatusBadge :status="order.order_status_name" />
                </div>
                <p class="subtitle">Created on {{ formatDate(order.created_at) }}</p>
            </div>
        </div>

        <!-- LOADING STATE -->
        <div v-if="isLoading" class="state-card">
            <div class="spinner"></div>
            <p>Loading order details...</p>
        </div>

        <!-- MAIN CONTENT GRID -->
        <div v-else class="layout-grid">

            <!-- LEFT COLUMN: Customer Info & Product List -->
            <div class="main-content">

                <!-- SECTION 1: CUSTOMER INFORMATION -->
                <div class="card">
                    <div class="card-header">
                        <h3><span class="icon">👤</span> Customer Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Customer Name</label>
                                <p>{{ order.customer_name || '-' }}</p>
                            </div>
                            <div class="info-item">
                                <label>Email Address</label>
                                <p>{{ order.customer_email || '-' }}</p>
                            </div>
                            <div class="info-item">
                                <label>Phone Number</label>
                                <p>{{ order.customer_phone || '-' }}</p>
                            </div>
                            <div class="info-item full-width">
                                <label>Notes</label>
                                <p class="notes-text">{{ order.notes || 'No notes provided' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: ORDERED PRODUCTS TABLE -->
                <div class="card">
                    <div class="card-header">
                        <h3><span class="icon">📦</span> Ordered Products</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="detail-table">
                                <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th class="text-right">Price</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-right">Subtotal</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="item in order.list_order_detail" :key="item.id">
                                    <td>
                                        <div class="product-info">
                                            <span class="product-name">{{ item.name }}</span>
                                            <span class="product-desc">{{ item.description || '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="text-right">{{ formatCurrency(item.price) }}</td>
                                    <td class="text-center font-medium">{{ item.quantity }}</td>
                                    <td class="text-right font-semibold">
                                        {{ formatCurrency(item.price * item.quantity) }}
                                    </td>
                                </tr>
                                <tr v-if="!order.list_order_detail || order.list_order_detail.length === 0">
                                    <td colspan="4" class="text-center text-muted py-4">
                                        No products found in this order.
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: PAYMENT & ORDER SUMMARY (STICKY) -->
            <div class="sidebar-content">

                <!-- SUMMARY CARD -->
                <div class="card sticky-card">
                    <div class="card-header">
                        <h3>Payment Summary</h3>
                    </div>
                    <div class="card-body">
                        <div class="summary-row">
                            <span>Total Items</span>
                            <strong>{{ totalItemUnits }} Pcs</strong>
                        </div>
                        <div class="summary-row">
                            <span>Expired At</span>
                            <span class="text-sm">{{ formatDate(order.expired_at) }}</span>
                        </div>
                        <div class="summary-row">
                            <span>Payment Time</span>
                            <span class="text-sm">{{ formatDate(order.payment_time) }}</span>
                        </div>

                        <hr class="divider" />

                        <div class="summary-row total-row">
                            <span>Total Price</span>
                            <strong class="total-amount">{{ formatCurrency(order.total_price) }}</strong>
                        </div>

                        <!-- PAY BUTTON SECTION -->
                        <div v-if="canPay" class="pay-action-wrapper">
                            <button
                                type="button"
                                class="btn-pay"
                                :disabled="isProcessingPayment"
                                @click="handlePayment"
                            >
                                <span v-if="isProcessingPayment" class="btn-spinner"></span>
                                <span v-else>Pay Now</span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { fetchApi } from "../utils/ApiUtils.js";
import { formatDate } from "../utils/DatetimeUtils.js";
import OrderStatusBadge from "../components/OrderStatusBadge.vue";

const route = useRoute();
const router = useRouter();

const orderId = route.params.id;
const order = ref({});
const isLoading = ref(true);
const isProcessingPayment = ref(false);

// Cek apakah order berstatus INITIATED atau PENDING
const canPay = computed(() => {
    const status = order.value.order_status_name?.toUpperCase();
    return status === 'INITIATED' || status === 'PENDING';
});

// Fetch Detail Data
const loadOrderDetail = async () => {
    isLoading.value = true;
    try {
        const response = await fetchApi({
            method: 'GET',
            url: `/api/order/${orderId}`
        });
        order.value = response.data || {};
    } catch (error) {
        // Silent catch
    } finally {
        isLoading.value = false;
    }
};

// Process Payment Action
const handlePayment = async () => {
    if (isProcessingPayment.value) return;
    isProcessingPayment.value = true;

    try {
        const response = await fetchApi({
            method: 'POST',
            url: `/api/midtrans/payment`,
            data: {
                'id_order': orderId,
            }
        });

        const snapToken = response.data?.snap_token;
        const redirectUrl = response.data?.snap_redirect_url;

        // Opsi 1: Jika Midtrans Snap JS terpasang di window
        if (snapToken) {
            window.snap.pay(snapToken, {
                onSuccess: () => loadOrderDetail(),
                onPending: () => loadOrderDetail(),
                onError: () => loadOrderDetail(),
                onClose: () => loadOrderDetail()
            });
        } else {
            alert('Payment Not Found!');
        }
    } catch (error) {
        // Silent catch
    } finally {
        isProcessingPayment.value = false;
    }
};

// Total quantity count helper
const totalItemUnits = computed(() => {
    if (!order.value.list_order_detail) return 0;
    return order.value.list_order_detail.reduce((acc, item) => acc + (item.quantity || 0), 0);
});

// Format Rupiah
const formatCurrency = (val) => {
    if (val === null || val === undefined) return 'Rp 0';
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0
    }).format(val);
};

const goBack = () => {
    router.push('/order');
};

onMounted(() => {
    loadOrderDetail();
});
</script>

<style scoped>
.order-container {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    max-width: 1150px;
    margin: 2rem auto;
    padding: 0 1rem;
}

/* Header Section */
.header-section { margin-bottom: 1.5rem; }

.btn-back {
    background: transparent;
    border: none;
    color: #2563eb;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    padding: 0;
    margin-bottom: 0.5rem;
    display: inline-block;
}
.btn-back:hover { text-decoration: underline; }

.title-with-badge {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

h2 { color: #111827; font-size: 1.75rem; margin: 0; font-weight: 700; }
.subtitle { color: #6b7280; margin: 0.25rem 0 0 0; font-size: 0.95rem; }

/* Grid Layout */
.layout-grid {
    display: grid;
    grid-template-columns: 1fr 320px;
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
    overflow: hidden;
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
.p-0 { padding: 0 !important; }

/* Info Grid Customer */
.info-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.25rem;
}

@media (max-width: 640px) {
    .info-grid { grid-template-columns: 1fr; }
}

.info-item label {
    display: block;
    font-size: 0.75rem;
    color: #6b7280;
    text-transform: uppercase;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.info-item p {
    margin: 0;
    font-size: 0.95rem;
    color: #111827;
    font-weight: 500;
}

.info-item.full-width {
    grid-column: 1 / -1;
}

.notes-text {
    background: #f9fafb;
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #f3f4f6;
    color: #4b5563 !important;
}

/* Products Table */
.table-responsive { width: 100%; overflow-x: auto; }

.detail-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
    font-size: 0.9rem;
}

.detail-table th {
    background: #f9fafb;
    color: #4b5563;
    padding: 12px 16px;
    font-weight: 600;
    border-bottom: 1px solid #e5e7eb;
}

.detail-table td {
    padding: 12px 16px;
    border-bottom: 1px solid #e5e7eb;
    vertical-align: middle;
}

.detail-table tr:last-child td { border-bottom: none; }

.product-info { display: flex; flex-direction: column; }
.product-name { font-weight: 600; color: #111827; }
.product-desc { font-size: 0.8rem; color: #6b7280; }

.text-right { text-align: right; }
.text-center { text-align: center; }
.font-medium { font-weight: 500; }
.font-semibold { font-weight: 600; }
.text-muted { color: #9ca3af; }
.text-sm { font-size: 0.825rem; }
.py-4 { padding-top: 1rem; padding-bottom: 1rem; }

/* Sidebar & Sticky Summary */
.sticky-card { position: sticky; top: 1rem; }

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.9rem;
    margin-bottom: 0.75rem;
    color: #4b5563;
}

.divider { border: 0; border-top: 1px solid #e5e7eb; margin: 1rem 0; }

.total-row {
    font-size: 1rem;
    color: #111827;
}

.total-amount {
    color: #2563eb;
    font-size: 1.2rem;
}

/* Pay Button Styles */
.pay-action-wrapper {
    margin-top: 1.25rem;
}

.btn-pay {
    width: 100%;
    padding: 0.75rem 1rem;
    background-color: #2563eb;
    color: #ffffff;
    font-weight: 600;
    font-size: 0.95rem;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.2s ease;
}

.btn-pay:hover:not(:disabled) {
    background-color: #1d4ed8;
}

.btn-pay:disabled {
    background-color: #93c5fd;
    cursor: not-allowed;
}

.btn-spinner {
    width: 18px;
    height: 18px;
    border: 2px solid #ffffff;
    border-top-color: transparent;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

/* State Cards (Loading) */
.state-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 3rem;
    text-align: center;
    color: #6b7280;
}

.spinner {
    width: 32px;
    height: 32px;
    border: 3px solid #e5e7eb;
    border-top-color: #2563eb;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    margin: 0 auto 1rem auto;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>