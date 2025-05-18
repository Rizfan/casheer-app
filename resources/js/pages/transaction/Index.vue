<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { type Menus } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    CardFooter,
} from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { LoaderCircle } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { Input } from '@/components/ui/input';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogClose,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';

interface Props { menus: Menus; }
defineProps<Props>();

function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(angka);
}

const cartItems = ref<any[]>([]);

function addToCart(menu: any) {
    const existing = cartItems.value.find(item => item.id === menu.id);
    if (existing) {
        existing.quantity += 1;
        existing.subtotal = existing.quantity * existing.price;
    } else {
        cartItems.value.push({
            id: menu.id,
            name: menu.name,
            price: parseHarga(menu.price),
            image: menu.image,
            quantity: 1,
            subtotal: parseHarga(menu.price),
        });
    }
    console.log('cartItems:', cartItems.value);
}
function parseHarga(rupiah: string | number): number {
    if (typeof rupiah === 'number') return rupiah;
    return Number(rupiah.replace(/[^0-9]/g, ''));
}
const totalPrice = computed(() =>
    cartItems.value.reduce((total, item) => total + item.subtotal, 0)
);

const uangPembeli = ref<number | null>(null);

const kembalian = computed(() => {
    if (uangPembeli.value !== null && uangPembeli.value >= totalPrice.value) {
        return uangPembeli.value - totalPrice.value;
    }
    return 0;
});

const form = useForm({
    transaction_details: [],
    total_price: 0,
    amount_paid: 0,
    change_return: 0,
    status: 'paid',
});

function saveBill() {

    form.transaction_details = cartItems.value.map(item => ({
        menu_id: item.id,
        quantity: item.quantity,
        price: item.price,
        subtotal: item.subtotal,
    }));

    form.total_price = totalPrice.value;
    form.amount_paid = 0;
    form.change_return = 0;
    form.status = 'saved';

    form.post(route('transaction.store'), {
        forceFormData: true,
        onSuccess: () => {
            cartItems.value = [];
            uangPembeli.value = null;
            alert('Transaksi berhasil disimpan!.');
        },
        onError: () => {
            alert('Terjadi kesalahan saat menyimpan transaksi.');
        }
    });

}

function charge() {
    if (uangPembeli.value === null) {
        alert("Masukkan uang pembeli terlebih dahulu.");
        return;
    }

    if (uangPembeli.value < totalPrice.value) {
        alert("Uang pembeli tidak mencukupi.");
        return;
    }

    form.transaction_details = cartItems.value.map(item => ({
        menu_id: item.id,
        quantity: item.quantity,
        price: item.price,
        subtotal: item.subtotal,
    }));

    form.total_price = totalPrice.value;
    form.amount_paid = uangPembeli.value;
    form.change_return = uangPembeli.value - totalPrice.value;
    form.status = 'paid';

    form.post(route('transaction.store'), {
        forceFormData: true,
        onSuccess: () => {
            cartItems.value = [];
            uangPembeli.value = null;
            alert('Transaksi berhasil dilakukan!');
        },
        onError: () => {
            alert('Terjadi kesalahan saat menyimpan transaksi.');
        }
    });
}

function printBill() {
    if (cartItems.value.length === 0) {
        alert("Keranjang kosong.");
        return;
    }

    localStorage.setItem('cartItems', JSON.stringify(cartItems.value));
    localStorage.setItem('totalPrice', totalPrice.value.toString());

    window.open(route('print-preview'), '_blank');
}

</script>

<template>

    <Head title="Transaksi" />
    <AppLayout>
        <div class="flex lg:flex-row flex-col justify-between items-start gap-4 mt-4">
            <div class="w-full lg:w-3/5 grid lg:grid-cols-3 grid-cols-1 gap-8 justify-">
                <div class="bg-white shadow rounded w-full ease-in-out transform hover:scale-102 transition-all "
                    v-for="menu in menus" :key="menu.id" @click="addToCart(menu)">
                    <img :src="`/storage/${menu.image}`" :alt="menu.name"
                        class="w-full h-36 object-cover justify-self-center rounded-t" />
                    <div class="p-4 text-center dark:text-black">
                        <h5 class="text-sm mb-3">{{ menu.name }}</h5>
                        <h3>{{ formatRupiah(menu.price) }}</h3>
                    </div>
                </div>
            </div>
            <div class="w-full lg:w-2/5">
                <Card class="w-full">
                    <CardHeader>
                        <CardTitle class="text-center font-bold text-xl">
                            <h1>Pesanan</h1>
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div v-if="cartItems.length">
                            <div v-for="item in cartItems" :key="item.id"
                                class="flex justify-between items-center py-2 border-b">
                                <div class="flex items-center gap-4">
                                    <img :src="`/storage/${item.image}`" :alt="item.name"
                                        class="w-16 h-16 object-cover rounded" />
                                    <p class="font-semibold">{{ item.name }}</p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <p class="text-sm text-gray-600">x{{ item.quantity }}</p>
                                    <p>{{ formatRupiah(item.subtotal) }}</p>
                                    <!-- <button @click="removeFromCart(item.id)" class="text-red-500 text-xs">Hapus</button> -->
                                </div>
                            </div>
                            <div class="mt-4 flex justify-between font-bold">
                                <span>Total:</span>
                                <span>{{ formatRupiah(totalPrice) }}</span>
                            </div>
                        </div>
                        <div v-else class="text-center text-gray-400 py-4">
                            Keranjang kosong
                        </div>
                    </CardContent>
                    <CardFooter class="w-full flex flex-col mt-4 gap-2">
                        <Button @click="cartItems = []" variant="outline" class="w-full flex-auto">Clear Cart</Button>
                        <div class="flex flex-row gap-2 w-full">
                            <Button @click="saveBill" class="w-full flex-auto">
                                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" /> Save Bill
                            </Button>
                            <Button @click="printBill" class="w-full flex-auto">
                                Print Bill
                            </Button>
                        </div>
                        <Dialog>
                            <DialogTrigger asChild>
                                <Button variant="destructive" class="w-full flex-auto">Charge {{
                                    formatRupiah(totalPrice)
                                    }}</Button>
                            </DialogTrigger>
                            <DialogContent class="w-full max-w-4xl lg:min-w-[900px] px-6 py-4">
                                <DialogHeader>
                                    <DialogTitle>Detail Pesanan</DialogTitle>
                                </DialogHeader>
                                <DialogDescription>
                                    <div class="flex lg:flex-row flex-col gap-2">
                                        <div class="lg:w-3/5 w-full">
                                            <Table>
                                                <TableHeader>
                                                    <TableRow>
                                                        <TableHead>#</TableHead>
                                                        <TableHead>Nama</TableHead>
                                                        <TableHead>Foto</TableHead>
                                                        <TableHead>Total</TableHead>
                                                    </TableRow>
                                                </TableHeader>
                                                <TableBody>
                                                    <TableRow v-for="(item, index) in cartItems" :key="item.id">
                                                        <TableCell>{{ index + 1 }}</TableCell>
                                                        <TableCell>{{ item.name }} (x{{ item.quantity }})</TableCell>
                                                        <TableCell>
                                                            <img :src="`/storage/${item.image}`" :alt="item.name"
                                                                class="w-16 h-16 object-cover rounded" />
                                                        </TableCell>
                                                        <TableCell>{{ formatRupiah(item.subtotal) }}</TableCell>
                                                    </TableRow>
                                                </TableBody>
                                            </Table>
                                        </div>
                                        <div class="lg:w-2/5 w-full">
                                            <div class="text-center">
                                                <h3 class="font-bold text-xl">Uang Pembeli (Rp)</h3>
                                            </div>
                                            <Input type="number" class="w-full mt-4" name="uangPembeli" id="uangPembeli"
                                                placeholder="Masukkan Uang Pembeli" v-model.number="uangPembeli" />
                                            <div class="flex flex-row gap-4 mt-4">
                                                <div class="w-full flex-auto">
                                                    <DialogClose as-child>
                                                        <Button type="button" variant="secondary" class="w-full">
                                                            Close
                                                        </Button>
                                                    </DialogClose>
                                                </div>
                                                <div class="w-full flex-auto">
                                                    <Button type="button" variant="destructive" @click="charge"
                                                        class="w-full">
                                                        <LoaderCircle v-if="form.processing"
                                                            class="h-4 w-4 animate-spin" />
                                                        Charge
                                                    </Button>
                                                </div>
                                            </div>
                                            <div class="text-center mt-4">
                                                <h3 class="font-bold">Kembalian</h3>
                                                <p v-if="uangPembeli === null" class="text-gray-400">
                                                    Masukkan uang pembeli terlebih dahulu
                                                </p>
                                                <p v-else-if="uangPembeli < totalPrice" class="text-red-500 text-sm">
                                                    Uang pembeli tidak mencukupi
                                                </p>
                                                <p v-else>
                                                    {{ formatRupiah(kembalian) }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </DialogDescription>
                            </DialogContent>
                        </Dialog>
                    </CardFooter>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>