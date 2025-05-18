<script setup lang="ts">
import { type Menus } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow, } from '@/components/ui/table';

interface Props { menus: Menus; }

defineProps<Props>();

import { Card, CardContent, CardHeader, } from '@/components/ui/card';

import { Button } from '@/components/ui/button';


function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(angka);
}
const deleteTask = (id) => {
    if (confirm('Are you sure you want to delete this menu?')) {
        router.delete(route('menu.destroy', { id }));
    }
};
</script>

<template>

    <Head title="Menu List" />
    <AppLayout>

        <Card class="w-full mt-4">
            <CardHeader>
                <Link :href="route('menu.create')" class="flex items-center gap-x-2">
                <Button>
                    Tambah Menu
                </Button>
                </Link>
            </CardHeader>
            <CardContent>
                <Table class="w-full text-center justify-center items-center">
                    <TableHeader class="bg-gray-100 text-center">
                        <TableRow class="text-center">
                            <TableHead class="text-center">#</TableHead>
                            <TableHead class="text-center">Nama</TableHead>
                            <TableHead class="text-center">Foto</TableHead>
                            <TableHead class="text-center">Harga</TableHead>
                            <TableHead class="text-center">Opsi</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="(menu, index) in menus" :key="menu.id">
                            <TableCell class="w-[100px]">{{ index + 1 }}</TableCell>
                            <TableCell>{{ menu.name }}</TableCell>
                            <TableCell>
                                <img :src="`/storage/${menu.image}`" :alt="menu.name"
                                    class="w-20 h-20 object-cover justify-self-center" />
                            </TableCell>
                            <TableCell>{{ formatRupiah(menu.price) }}</TableCell>
                            <TableCell>
                                <Button variant="destructive" @click="deleteTask(menu.id)">Hapus</Button>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>
    </AppLayout>
</template>