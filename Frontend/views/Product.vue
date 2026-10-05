<template>
    <div class="order-container">
        <h2>Product List</h2>

        <div class="table-responsive">
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="product in products" :key="product.id">
                        <td>{{ product.id }}</td>
                        <td>{{ product.name }}</td>
                        <td>{{ product.description }}</td>
                        <td>{{ product.quantity }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { fetchApi } from "../utils/ApiUtils.js";

const products = ref([]);

const loadProducts = async () => {
    try {
        const response = await fetchApi({
            method: 'GET',
            url: '/api/product',
        });
        products.value = response.data || [];
    } catch (error) {}
};

onMounted(() => {
    loadProducts();
});
</script>

<style scoped>
.order-container {
    font-family: sans-serif;
    max-width: 1100px;
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

</style>