<template>
    <q-layout view="hHh lpR fFf">
        <Toolbar :background="heroBannerImage" />
        <q-page-container class="flex flex-center">
            <div v-if="loadingCharacters">
                <div class="loading-spinner"></div>
            </div>
            <div v-else>
                <Pagination :totalItems="characters.length" :itemsPerPage="itemsPerPage" :currentPage="currentPage"
                    :loading="loadingCharacters" @page-changed="changePage"
                    @items-per-page-changed="changeItemsPerPage" />
                <div class="banner-container">
                    <div class="banner" v-for="(character, index) in paginatedCharacters" :key="index"
                        @click="openCharacterModal(character)">
                        <img :src="`${character.thumbnail.path}.${character.thumbnail.extension}`"
                            :alt="character.name" />
                        <div class="favorite-container" v-if="!isFavorite(character.id)">
                            <q-icon :name="isFavorite(character.id) ? 'star' : 'star_border'" class="favorite-icon"
                                @click.stop="confirmFavorite(character)" />
                            <span class="favorite-text">{{ isFavorite(character.id) ? 'Remover Favorito' : 'Favoritar'
                                }}</span>
                        </div>
                        <div class="banner-text">{{ character.name }}</div>
                    </div>
                </div>
            </div>
        </q-page-container>
        <q-dialog v-model="isModalOpen" persistent>
            <q-card class="character-modal">
                <q-card-section class="character-modal-header">
                    <div class="text-h5">{{ selectedCharacter.name }}</div>
                    <hr class="character-modal-divider" />
                </q-card-section>
                <q-card-section class="character-modal-content">
                    <div class="character-modal-image">
                        <img :src="`${selectedCharacter.thumbnail.path}.${selectedCharacter.thumbnail.extension}`"
                            :alt="selectedCharacter.name" />
                    </div>
                    <div class="character-modal-details">
                        <div class="favorite-container" v-if="!isFavorite(selectedCharacter.id)">
                            <q-icon :name="isFavorite(selectedCharacter.id) ? 'star' : 'star_border'"
                                class="favorite-icon" @click="confirmFavorite(selectedCharacter)" />
                            <span class="favorite-text">{{ isFavorite(selectedCharacter.id) ? 'Remover Favorito' :
                                'Favoritar' }}</span>
                        </div>
                        <p><strong>Descrição:</strong> {{ selectedCharacter.description || 'Descrição não disponível.'
                            }}</p>
                        <p><strong>Quadrinhos Disponíveis:</strong> {{ selectedCharacter.comics.available }}</p>
                        <p><strong>Séries Disponíveis:</strong> {{ selectedCharacter.series.available }}</p>
                        <p><strong>Histórias Disponíveis:</strong> {{ selectedCharacter.stories.available }}</p>
                        <p><strong>Eventos Disponíveis:</strong> {{ selectedCharacter.events.available }}</p>
                    </div>
                </q-card-section>
                <q-card-actions align="right">
                    <q-btn flat label="Fechar" color="primary" v-close-popup />
                </q-card-actions>
            </q-card>
        </q-dialog>

        <!-- Diálogo de Confirmação -->
        <q-dialog v-model="dialogVisible">
            <q-card>
                <q-card-section>
                    <div class="text-h6">{{ dialogTitle }}</div>
                </q-card-section>
                <q-card-section class="dialog-text">
                    <p>{{ dialogMessage }}</p>
                </q-card-section>
                <q-card-actions align="right">
                    <q-btn flat label="Cancelar" color="primary" v-close-popup />
                    <q-btn flat label="Confirmar" color="primary" @click="toggleFavorite(selectedCharacter)" />
                </q-card-actions>
            </q-card>
        </q-dialog>
    </q-layout>
</template>

<script>
import { ref, onMounted, computed } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import Toolbar from '@/js/components/Toolbar.vue';
import Pagination from '@/js/components/Pagination.vue';
import ApiService from '@/js/components/ApiService.vue';
import axios from 'axios';

export default {
    name: 'Characters',
    components: {
        Toolbar,
        Pagination
    },
    setup () {
        const characters = ref([]);
        const loadingCharacters = ref(true);
        const heroBannerImage = ref('');
        const currentPage = ref(1);
        const itemsPerPage = ref(10);
        const isModalOpen = ref(false);
        const selectedCharacter = ref({});
        const favorites = ref([]);
        const dialogVisible = ref(false);
        const dialogTitle = ref('');
        const dialogMessage = ref('');

        const fetchCharacters = async () => {
            try {
                const data = await ApiService.fetchData('/api/marvel/characters', { limit: 100 });
                characters.value = data.data.results;
                const randomIndex = Math.floor(Math.random() * characters.value.length);
                const character = characters.value[randomIndex];
                heroBannerImage.value = `${character.thumbnail.path}.${character.thumbnail.extension}`;
                const favoritedCharacters = characters.value.filter(character => isFavorite(character.id));
            } catch (error) {
                console.error('Error fetching characters:', error);
            } finally {
                loadingCharacters.value = false;
            }
        };

        const fetchFavorites = async () => {
            try {
                const response = await ApiService.fetchData('/api/user/favorites', {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                    }
                });
                favorites.value = response.data || [];
            } catch (error) {
                console.error('Error fetching favorites:', error);
            }
        };

        const isFavorite = (marvelId) => {
            return favorites.value && favorites.value.some(favorite => favorite.marvel_id == marvelId && favorite.type === 'character');
        };

        const showDialog = (title, message) => {
            dialogTitle.value = title;
            dialogMessage.value = message;
            dialogVisible.value = true;
        };

        const confirmFavorite = (character) => {
            selectedCharacter.value = character;
            showDialog('Adicionar aos Favoritos', 'Você tem certeza que deseja adicionar este item aos favoritos?');
        };

        const toggleFavorite = async (character) => {
            try {
                const response = await axios.post('/favorites/characters', {
                    name: character.name,
                    description: character.description,
                    thumbnail: character.thumbnail,
                    marvel_id: character.id,
                    comics_available: character.comics.available,
                    series_available: character.series.available,
                    stories_available: character.stories.available,
                    events_available: character.events.available
                }, {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                    }
                });
                favorites.value.push(response.data);
                dialogVisible.value = false;
            } catch (error) {
                console.error('Error adding favorite:', error);
            }
        };

        const goToCharacter = (characterId) => {
            Inertia.visit(`/characters/${characterId}`);
        };

        const openCharacterModal = (character) => {
            selectedCharacter.value = character;
            isModalOpen.value = true;
        };

        const changePage = (page) => {
            currentPage.value = page;
        };

        const changeItemsPerPage = (newItemsPerPage) => {
            itemsPerPage.value = newItemsPerPage;
            currentPage.value = 1; // Reset to first page
        };

        const paginatedCharacters = computed(() => {
            const start = (currentPage.value - 1) * itemsPerPage.value;
            const end = start + itemsPerPage.value;
            return characters.value.slice(start, end);
        });

        onMounted(async () => {
            await fetchCharacters();
            await fetchFavorites();
        });

        return {
            characters,
            loadingCharacters,
            heroBannerImage,
            goToCharacter,
            currentPage,
            itemsPerPage,
            changePage,
            changeItemsPerPage,
            paginatedCharacters,
            isModalOpen,
            selectedCharacter,
            openCharacterModal,
            toggleFavorite,
            confirmFavorite,
            dialogVisible,
            dialogTitle,
            dialogMessage,
            favorites,
            isFavorite
        };
    }
};
</script>

<style lang="scss" scoped>
@import '@/css/styles.sass';
</style>