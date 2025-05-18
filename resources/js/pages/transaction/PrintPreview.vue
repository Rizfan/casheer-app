<script setup lang="ts">
import { onMounted, ref } from 'vue';

const cartItems = ref([]);
const totalPrice = ref(0);

function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(angka);
}

onMounted(() => {
    cartItems.value = JSON.parse(localStorage.getItem('cartItems') || '[]');
    totalPrice.value = parseInt(localStorage.getItem('totalPrice') || '0');

    // Langsung buka print
    window.print();
});
</script>

<template>
    <div class="p-6 text-sm font-mono">
        <h1 class="text-center font-bold text-lg">INVOICE</h1>
        <p>Tanggal: {{ new Date().toLocaleString() }}</p>

        <table class="w-full mt-4 border-collapse text-sm">
            <thead>
                <tr class="border-b">
                    <th class="text-left">Menu</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in cartItems" :key="item.id" class="border-b">
                    <td>{{ item.name }}</td>
                    <td>{{ item.quantity }}</td>
                    <td>{{ formatRupiah(item.price) }}</td>
                    <td>{{ formatRupiah(item.subtotal) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="text-right mt-4 font-bold">
            Total: {{ formatRupiah(totalPrice) }}
        </div>
    </div>
</template>
