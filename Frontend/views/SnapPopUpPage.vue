<template>
    <form>
        <h1 class="mb-10">Snap Pop Up Page</h1>

        <div class="mb-10">
            <label>ORDER ID : </label>
            <input v-model="order_id" required />
        </div>
        <div class="mb-10">
            <label>GROSS AMMOUNT : </label>
            <input v-model="gross_ammount" type="number" required />
        </div>

        <button type="submit" @click="handleCreateMitransTransaction">CREATE MITRANS TRANSACTION</button>
    </form>
</template>

<script setup>
    import { ref } from 'vue';
    import { fetchApi } from '../utils/ApiUtils';

    const order_id = ref('');
    const gross_ammount = ref();

    const handleCreateMitransTransaction = async (event) => {
        event.preventDefault();
        
        const response = await fetchApi({
            method: 'POST',
            url: '/api/mitrans/transaction',
            data: {
                'order_id': order_id.value,
                'gross_ammount': gross_ammount.value
            }
        })

        const token = response.data.token;

        window.snap.pay(token);

    }
    
</script>

<style scoped>

    .mb-10 {
        margin-bottom: 10px;
    }

</style>