<template>
    <q-layout view="hHh lpR fFf">
        <q-header elevated class="bg-primary text-white" height-hint="98">
            <Toolbar />
        </q-header>
        <q-page-container class="flex flex-center">
            <div v-if="loadingUser || loadingHeroBanner || loadingComicBanner">
                <div class="loading-spinner"></div>
            </div>
            <div v-else class="banner-container">
                <div class="banner" @click="goToPage('heroi')">
                    <img :src="heroBannerImage" alt="Heroes" />
                    <div class="banner-text">Listar Heróis</div>
                </div>
                <div class="banner" @click="goToPage('quadrinho')">
                    <img :src="comicBannerImage" alt="Comics" />
                    <div class="banner-text">Listar Quadrinhos</div>
                </div>
            </div>
        </q-page-container>
    </q-layout>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import Toolbar from '../components/Toolbar.vue';

export default {
    components: {
        Toolbar
    },
    setup () {
        const menu = ref(false);
        const user = ref({
            name: '',
            email: ''
        });
        const heroBannerImage = ref('');
        const comicBannerImage = ref('');
        const loadingUser = ref(true);
        const loadingHeroBanner = ref(true);
        const loadingComicBanner = ref(true);
        const router = useRouter();

        const fetchUser = async () => {
            try {
                const response = await axios.get('/user');
                user.value = response.data;
            } catch (error) {
                console.error('Error fetching user:', error);
            } finally {
                loadingUser.value = false;
            }
        };

        const fetchHeroBannerImage = async () => {
            try {
                const response = await axios.get('/api/marvel/characters', {
                    params: {
                        limit: 20
                    }
                });
                const characters = response.data.data.results;
                const randomIndex = Math.floor(Math.random() * characters.length);
                const character = characters[randomIndex];
                heroBannerImage.value = `${character.thumbnail.path}.${character.thumbnail.extension}`;
            } catch (error) {
                console.error('Error fetching hero banner image:', error);
            } finally {
                loadingHeroBanner.value = false;
            }
        };

        const fetchComicBannerImage = async () => {
            try {
                const response = await axios.get('/api/marvel/comics', {
                    params: {
                        limit: 20
                    }
                });
                const comics = response.data.data.results;
                const randomIndex = Math.floor(Math.random() * comics.length);
                const comic = comics[randomIndex];
                comicBannerImage.value = `${comic.thumbnail.path}.${comic.thumbnail.extension}`;
            } catch (error) {
                console.error('Error fetching comic banner image:', error);
            } finally {
                loadingComicBanner.value = false;
            }
        };

        const goToPage = (page) => {
            window.location.href = page;
        };

        const toggleMenu = () => {
            menu.value = !menu.value;
        };

        onMounted(async () => {
            await fetchUser();
            await fetchHeroBannerImage();
            await fetchComicBannerImage();
        });

        return {
            menu,
            user,
            heroBannerImage,
            comicBannerImage,
            loadingUser,
            loadingHeroBanner,
            loadingComicBanner,
            toggleMenu,
            goToPage
        };
    }
};
</script>

<style lang="scss" scoped>
@import '../../css/styles.sass';
</style>