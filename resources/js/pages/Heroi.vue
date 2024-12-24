<template>
    <q-layout view="hHh lpR fFf">
        <Toolbar :background="heroBannerImage" />
        <q-page-container class="flex flex-center">
            <div v-if="loadingCharacters">
                <div class="loading-spinner"></div>
            </div>
            <div v-else class="banner-container">
                <div class="banner" v-for="(character, index) in characters" :key="index"
                    @click="goToCharacter(character.id)">
                    <img :src="`${character.thumbnail.path}.${character.thumbnail.extension}`" :alt="character.name" />
                    <div class="banner-text">{{ character.name }}</div>
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
        const characters = ref([]);
        const loadingCharacters = ref(true);
        const heroBannerImage = ref('');
        const router = useRouter();

        const fetchCharacters = async () => {
            try {
                const response = await axios.get('/api/marvel/characters', {
                    params: {
                        limit: 20
                    }
                });
                characters.value = response.data.data.results;
                const randomIndex = Math.floor(Math.random() * characters.value.length);
                const character = characters.value[randomIndex];
                heroBannerImage.value = `${character.thumbnail.path}.${character.thumbnail.extension}`;
            } catch (error) {
                console.error('Error fetching characters:', error);
            } finally {
                loadingCharacters.value = false;
            }
        };

        const goToCharacter = (characterId) => {
            router.push(`/characters/${characterId}`);
        };

        onMounted(async () => {
            await fetchCharacters();
        });

        return {
            characters,
            loadingCharacters,
            heroBannerImage,
            goToCharacter
        };
    }
};
</script>

<style lang="scss" scoped>
@import '../../css/styles.sass';
</style>