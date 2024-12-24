<template>
    <q-layout view="hHh lpR fFf">
        <Toolbar :background="comicBannerImage" />
        <q-page-container class="flex flex-center">
            <div v-if="loadingComics">
                <div class="loading-spinner"></div>
            </div>
            <div v-else class="banner-container">
                <div class="banner" v-for="(comic, index) in comics" :key="index" @click="goToComic(comic.id)">
                    <img :src="`${comic.thumbnail.path}.${comic.thumbnail.extension}`" :alt="comic.title" />
                    <div class="banner-text">{{ comic.title }}</div>
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
        const comics = ref([]);
        const loadingComics = ref(true);
        const comicBannerImage = ref('');
        const router = useRouter();

        const fetchComics = async () => {
            try {
                const response = await axios.get('/api/marvel/comics', {
                    params: {
                        limit: 20
                    }
                });
                comics.value = response.data.data.results;
                const randomIndex = Math.floor(Math.random() * comics.value.length);
                const comic = comics.value[randomIndex];
                comicBannerImage.value = `${comic.thumbnail.path}.${comic.thumbnail.extension}`;
            } catch (error) {
                console.error('Error fetching comics:', error);
            } finally {
                loadingComics.value = false;
            }
        };

        const goToComic = (comicId) => {
            router.push(`/comics/${comicId}`);
        };

        onMounted(async () => {
            await fetchComics();
        });

        return {
            comics,
            loadingComics,
            comicBannerImage,
            goToComic
        };
    }
};
</script>

<style lang="scss" scoped>
@import '../../css/styles.sass';
</style>