<template>
    <div class="order-container">
        <h2>Order List</h2>

        <div class="table-responsive">
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer Name</th>
                        <th>Customer Email</th>
                        <th>Customer Phone</th>
                        <th>Order Status</th>
                        <th>Total Price</th>
                        <th>Created At</th>
                        <th>Expired At</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="order in orders" :key="order.id">
                        <td>
                            <router-link :to="`/order/${order.id}`" class="order-link">
                                {{ order.id }}
                            </router-link>
                        </td>
                        <td>{{ order.customer_name }}</td>
                        <td>{{ order.customer_email }}</td>
                        <td>{{ order.customer_phone }}</td>
                        <td>{{ order.order_status_name }}</td>
                        <td>{{ order.total_price }}</td>
                        <td>{{ formatDate(order.created_at) }}</td>
                        <td>{{ formatDate(order.expired_at) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { fetchApi } from "../utils/ApiUtils.js";
import {formatDate} from "../utils/DatetimeUtils.js";

const orders = ref([]);

const loadOrders = async () => {
    try {
        const response = await fetchApi({
            method: 'GET',
            url: '/api/order',
        });
        orders.value = response.data || [];
    } catch (error) {}
};

onMounted(() => {
    loadOrders();
});
</script>

<style scoped>
.order-container {
    font-family: sans-serif;
    max-width: 1500px;
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