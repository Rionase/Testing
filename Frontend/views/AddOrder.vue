<template>
    <div class="form-container">
        <h2>Buat Pesanan Baru</h2>

        <form @submit.prevent="handleSubmit">
            <div class="form-group">
                <label for="gross_ammount">Gross Amount</label>
                <input
                    type="number"
                    id="gross_ammount"
                    v-model="form.gross_ammount"
                    min="1"
                    required
                />
            </div>

            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea
                    id="keterangan"
                    v-model="form.keterangan"
                    rows="4"
                    maxlength="256"
                ></textarea>
            </div>

            <button type="submit" class="btn-submit">Buat Pesanan</button>
        </form>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import {fetchApi} from "../utils/ApiUtils.js";

const router = useRouter();

// State Form
const form = ref({
    gross_ammount: '',
    keterangan: ''
});

// Fungsi Submit Data
const handleSubmit = async () => {
    try {
        await fetchApi({
            method: 'POST',
            url: '/api/order',
            data: {
                gross_ammount: form.value.gross_ammount,
                keterangan: form.value.keterangan
            }
        });
        router.push('/order');
    } catch (error) {
        console.error('Gagal membuat pesanan:', error);
    }
};
</script>

<style scoped>
.form-container {
    font-family: sans-serif;
    max-width: 500px;
    margin: 3rem auto;
    padding: 2rem;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    background-color: #ffffff;
}

h2 {
    color: #333;
    margin-bottom: 1.5rem;
    font-size: 1.25rem;
}

.form-group {
    margin-bottom: 1.25rem;
    position: relative;
}

label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #555;
    font-size: 0.9rem;
}

input[type="number"],
textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    font-size: 0.95rem;
}

input[type="number"]:focus,
textarea:focus {
    border-color: #4f46e5;
    outline: none;
}

.char-counter {
    display: block;
    text-align: right;
    color: #888;
    font-size: 0.8rem;
    margin-top: 4px;
}

.btn-submit {
    width: 100%;
    background-color: #4f46e5;
    color: white;
    border: none;
    padding: 12px;
    border-radius: 4px;
    font-size: 0.95rem;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.2s;
}

.btn-submit:hover {
    background-color: #4338ca;
}
</style>