<template>
    <div>
        <q-header>
            <q-toolbar class="bg-red">
                <div v-if="isSmallScreen">
                    <ResponsiveMenu />
                </div>
                <div v-else>
                    <q-btn flat label="Home" @click="goToPage('/')" />
                    <q-btn flat label="Quadrinhos" @click="goToPage('/comics')" />
                    <q-btn flat label="Heróis" @click="goToPage('/characters')" />
                    <q-btn flat label="Favoritos" @click="goToPage('/favorites')" />
                </div>
                <q-toolbar-title :class="{ 'marvel-app-title': true, 'large-screen-title': !isSmallScreen }">
                    Marvel
                </q-toolbar-title>
                <Dropdown :user="user" :menu="menu" @toggle-menu="toggleMenu" @logout="logout" />
            </q-toolbar>
        </q-header>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import axios from 'axios';
import Dropdown from '@/js/components/Dropdown.vue';
import ResponsiveMenu from '@/js/components/ResponsiveMenu.vue';

export default {
    name: 'Toolbar',
    components: {
        Dropdown,
        ResponsiveMenu
    },
    setup () {
        const menu = ref(false);
        const user = ref({
            name: '',
            email: ''
        });
        const isSmallScreen = ref(window.innerWidth < 870);
        const backgroundImage = ref('');
        const loading = ref(true);

        const isImageValid = (url) => {
            return new Promise((resolve) => {
                const img = new Image();
                img.onload = () => resolve(true);
                img.onerror = () => resolve(false);
                img.src = url;
            });
        };

        const fetchBackgroundImage = async () => {
            try {
                const response = await axios.get('/api/marvel/comics');
                const comics = response.data.data.results;
                let validImage = false;
                let selectedImage = '';

                while (!validImage && comics.length > 0) {
                    const randomIndex = Math.floor(Math.random() * comics.length);
                    const comic = comics[randomIndex];
                    const imageUrl = `${comic.thumbnail.path}.${comic.thumbnail.extension}`;
                    validImage = await isImageValid(imageUrl);
                    if (validImage) {
                        selectedImage = imageUrl;
                    }
                    comics.splice(randomIndex, 1);
                }

                backgroundImage.value = selectedImage;
            } catch (error) {
                console.error('Error fetching background image:', error);
            } finally {
                loading.value = false;
            }
        };

        const fetchUser = async () => {
            try {
                const response = await axios.get('/api/user');
                user.value = response.data;
            } catch (error) {
                console.error('Error fetching user:', error);
            }
        };

        const goToPage = (page) => {
            Inertia.visit(page);
        };

        const logout = async () => {
            try {
                await axios.post('/logout');
                Inertia.visit('/login');
            } catch (error) {
                console.error('Error logging out:', error);
            }
        };

        const toggleMenu = () => {
            menu.value = !menu.value;
        };

        const handleResize = () => {
            isSmallScreen.value = window.innerWidth < 870;
        };

        onMounted(() => {
            fetchUser();
            fetchBackgroundImage();
            window.addEventListener('resize', handleResize);
        });

        return {
            menu,
            user,
            goToPage,
            logout,
            toggleMenu,
            isSmallScreen,
            backgroundImage,
            loading
        };
    }
};
</script>

<style lang="scss" scoped>
@import '@/css/styles.sass';
</style>