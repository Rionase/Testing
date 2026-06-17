<template>
    <div class="order-container">
        <h2>Daftar Pesanan</h2>

        <div class="table-responsive">
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Gross Amount</th>
                        <th>Keterangan</th>
                        <th>Status</th>
                        <th>Payment Time</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="order in orders" :key="order.id_order">
                        <td>{{ order.id_order }}</td>
                        <td>{{ order.gross_ammount }}</td>
                        <td>{{ order.keterangan }}</td>
                        <td>{{ order.midtrans_payment_status }}</td>
                        <td>{{ order.midtrans_payment_status == 'capture' || order.midtrans_payment_status == 'settlement' ? order.midtrans_payment_time : '-' }}</td>
                        <td class="text-center">
                            <button
                                v-if="order.midtrans_payment_status === 'pending'"
                                @click="handlePay(order.id_order)"
                                class="btn-pay"
                            >
                                PAY
                            </button>
                            <span v-else class="text-muted">-</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { fetchApi } from "../utils/ApiUtils.js";

const orders = ref([]);

const loadOrders = async () => {
    try {
        const response = await fetchApi({
            method: 'GET',
            url: '/api/order',
        });
        orders.value = response.data || [];
    } catch (error) {
        console.error('Gagal memuat data:', error);
    }
};

// Fungsi untuk mengarahkan customer ke halaman pembayaran
const handlePay = async (idOrder) => {
    try {
        const response = await fetchApi({
            method: 'POST',
            url: '/api/order/payment',
            data: { 'id_order': idOrder }
        });
        const token = response.data.token;
        window.snap.pay(token);

    } catch (error) {
        console.error('Gagal memuat data:', error);
    }
};

onMounted(() => {
    loadOrders();
});
</script>

<style scoped>
.order-container {
    font-family: sans-serif;
    max-width: 900px;
    margin: 2rem auto;
    padding: 1rem;
}

h2 {
    color: #333;
    margin-bottom: 1rem;
}

.table-responsive {
    width: 100%;
    overflow-x: auto;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
}

.styled-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
}

.styled-table th {
    background-color: #f5f5f5;
    color: #555;
    padding: 12px;
    font-weight: 600;
    border-bottom: 2px solid #e0e0e0;
}

.styled-table td {
    padding: 12px;
    border-bottom: 1px solid #e0e0e0;
    color: #333;
    vertical-align: middle;
}

.styled-table tbody tr:hover {
    background-color: #f9f9f9;
}

.text-center {
    text-align: center;
}

/* Styling Tombol PAY yang simpel dan modis */
.btn-pay {
    background-color: #4f46e5;
    color: white;
    border: none;
    padding: 6px 16px;
    border-radius: 4px;
    font-size: 0.85rem;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.2s;
}

.btn-pay:hover {
    background-color: #4338ca;
}

.text-muted {
    color: #999;
    font-size: 0.9rem;
}
</style>