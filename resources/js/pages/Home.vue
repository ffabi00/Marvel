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
                <div class="banner" @click="goToPage('characters')"
                    style="height: 80vh !important; width: 45vw !important; max-width: 90vw !important;">
                    <img :src="heroBannerImage" alt="Heroes" />
                    <div class="banner-text">Ver Heróis</div>
                </div>
                <div class="banner" @click="goToPage('comics')"
                    style="height: 80vh !important; width: 45vw !important; max-width: 90vw !important;">
                    <img :src="comicBannerImage" alt="Comics" />
                    <div class="banner-text">Ver Quadrinhos</div>
                </div>
            </div>
        </q-page-container>
    </q-layout>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Toolbar from '@/js/components/Toolbar.vue';
import ApiService from '@/js/components/ApiService.vue';

export default {
    name: 'Home',
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
                const data = await ApiService.fetchData('/api/user');
                user.value = data;
            } catch (error) {
                console.error('Error fetching user:', error);
            } finally {
                loadingUser.value = false;
            }
        };

        const fetchHeroBannerImage = async () => {
            try {
                const data = await ApiService.fetchData('/api/marvel/characters', { limit: 20 });
                const characters = data.data.results;
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
                const data = await ApiService.fetchData('/api/marvel/comics', { limit: 20 });
                const comics = data.data.results;
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
@import '@/css/styles.sass';
</style>