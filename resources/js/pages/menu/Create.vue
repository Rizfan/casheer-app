<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { LoaderCircle } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
import Dropzone from 'dropzone';
import 'dropzone/dist/dropzone.css';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    Card,
    CardContent,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';


const form = useForm({
    name: '',
    image: null,
    price: '',
})

const dropzoneRef = ref<HTMLDivElement | null>(null);

onMounted(() => {
    Dropzone.autoDiscover = false;

    const dz = new Dropzone(dropzoneRef.value!, {
        url: '/', // URL tidak digunakan karena kita akan submit secara manual
        autoProcessQueue: false,
        maxFiles: 1,
        acceptedFiles: 'image/*',
        addRemoveLinks: true,
        dictDefaultMessage: 'Drop image here or click to upload.',
    });

    dz.on('addedfile', (file) => {
        form.image = file;
    });

    dz.on('removedfile', () => {
        form.image = null;
    });

    dz.on('maxfilesexceeded', (file) => {
        dz.removeAllFiles(); // Hapus file sebelumnya
        dz.addFile(file);    // Tambahkan file baru
    });
});


const submit = () => {
    // 
    form.post(route('menu.store'), {
        forceFormData: true, // penting untuk upload file!
        onFinish: () => form.reset('name', 'image', 'price'),
    });
}

</script>

<template>

    <Head title="Create Menu" />

    <AppLayout>
        <Card class="w-full mt-4">
            <CardHeader>
                <CardTitle>Tambahkan Menu</CardTitle>
            </CardHeader>
            <form @submit.prevent="submit" class="flex flex-col gap-6">
                <CardContent>
                    <div class="grid gap-6">
                        <div class="grid gap-2">
                            <Label for="name">Name</Label>
                            <Input id="name" type="text" required autofocus :tabindex="1" v-model="form.name" />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="image">Image</Label>
                            <div ref="dropzoneRef" class="dropzone border border-dashed rounded-md p-4"></div>
                            <InputError :message="form.errors.image" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="price">Price</Label>

                            <div class="flex rounded-md shadow-sm">
                                <span class="inline-flex items-center px-3 rounded-l-md bg-blue-500 text-white text-sm">
                                    Rp
                                </span>
                                <Input id="price" type="text" class="rounded-s-none" required :tabindex="3"
                                    v-model="form.price" />
                            </div>
                            <InputError :message="form.errors.price" />

                        </div>
                    </div>
                </CardContent>
                <CardFooter class="flex justify-end">
                    <Button type="submit">
                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                        Simpan
                    </Button>
                </CardFooter>
            </form>
        </Card>
    </AppLayout>
</template>