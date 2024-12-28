<template>
    <q-layout view="hHh lpR fFf">
        <Toolbar />
        <q-page-container class="banner-favorites flex flex-center">
            <div v-if="loadingFavorites">
                <div style="margin-top: 40vh;" class="loading-spinner"></div>
            </div>
            <div v-else>
                <div v-if="favorites.length === 0" class="bg-body">
                    <div class="flex flex-center column bg-red" style="border-radius: 10px; margin-top: 40vh;">
                        <q-icon name="star_border" size="5rem" color="primary" />
                        <span class="text-h2 q-mt-md">Nenhum favorito encontrado.</span>
                        <span class="text-subtitle1 q-mt-sm">Adicione favoritos para vê-los aqui.</span>
                    </div>
                </div>
                <div v-else>
                    <Pagination :totalItems="favorites.length" :itemsPerPage="itemsPerPage" :currentPage="currentPage"
                        :loading="loadingFavorites" @page-changed="changePage"
                        @items-per-page-changed="changeItemsPerPage" />
                    <div class="favorites-banner-container">
                        <div class="favorites-banner" v-for="(favorite, index) in paginatedFavorites" :key="index"
                            @click="openModal(favorite)">
                            <img v-if="favorite.character || favorite.comic"
                                :src="`${(favorite.character || favorite.comic).thumbnail.path}.${(favorite.character || favorite.comic).thumbnail.extension}`"
                                :alt="favorite.character ? favorite.character.name : favorite.comic.title" />
                            <div class="favorite-container" v-if="favorite.character || favorite.comic">
                                <q-icon name="star" class="favorite-icon"
                                    @click.stop="confirmDeleteFavorite((favorite.character || favorite.comic).id, favorite.character ? 'character' : 'comic')" />
                                <span class="favorite-text">Remover</span>
                            </div>
                            <div class="favorites-banner-text" v-if="favorite.character || favorite.comic">
                                {{ favorite.character ? favorite.character.name : favorite.comic.title }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </q-page-container>
        <q-dialog v-model="isModalOpen" persistent>
            <q-card class="geral-modal">
                <q-card-section class="geral-modal-header">
                    <div class="text-h5">{{ isComicModal ? selectedComic.title : selectedCharacter.name }}</div>
                    <hr class="geral-modal-divider" />
                </q-card-section>
                <q-card-section class="geral-modal-content">
                    <div class="geral-modal-image">
                        <img :src="`${isComicModal ? selectedComic.thumbnail.path : selectedCharacter.thumbnail.path}.${isComicModal ? selectedComic.thumbnail.extension : selectedCharacter.thumbnail.extension}`"
                            :alt="isComicModal ? selectedComic.title : selectedCharacter.name" />
                    </div>
                    <div class="geral-modal-details">
                        <div class="favorite-container">
                            <q-icon name="star" class="favorite-icon"
                                @click="confirmDeleteFavorite(isComicModal ? selectedComic.id : selectedCharacter.id, isComicModal ? 'comic' : 'character')" />
                            <span class="favorite-text">Remover</span>
                        </div>
                        <p style="margin-bottom: 1rem; font-size: 16px;"><strong>Descrição:</strong> {{ isComicModal ?
                            selectedComic.description || 'Descrição não disponível.' :
                            selectedCharacter.description || 'Descrição não disponível.' }}</p>
                        <p v-if="isComicModal" style="line-height: 2; font-size: 14px;">
                            <strong>Data de Publicação:</strong> {{ selectedComic.publication_date ?
                            formatDate(selectedComic.publication_date) : 'Data não disponível' }}<br>
                            <strong>Preço:</strong> {{ selectedComic.price ? formatPrice(selectedComic.price) : 'Preço não disponível' }}<br>
                            <strong>Páginas:</strong> {{ selectedComic.pages || 'Número de páginas não disponível' }}<br>
                            <strong>Personagens: </strong>
                            <span v-if="selectedComic.characters && selectedComic.characters.length > 0">
                                <span v-for="(character, index) in selectedComic.characters" :key="index">
                                    {{ character.name || 'Nome não disponível' }}<span
                                        v-if="index < selectedComic.characters.length - 1">, </span>
                                </span>
                            </span>
                            <span v-else>Não disponível</span><br>
                            <strong>Criadores: </strong>
                            <span v-if="selectedComic.creators && selectedComic.creators.length > 0">
                                <span v-for="(creator, index) in selectedComic.creators" :key="index">
                                    {{ creator.name }} ({{ creator.role }}){{ index < selectedComic.creators.length - 1
                                        ? ', ' : '' }} </span>
                                </span>
                                <span v-else>Não disponível</span><br>
                                <strong>Histórias: </strong>
                                <span v-if="selectedComic.stories && selectedComic.stories.length > 0">
                                    <template v-for="(story, index) in selectedComic.stories" :key="index">
                                        {{ story.name }}{{ index < selectedComic.stories.length - 1 ? ', ' : '' }}
                                            </template>
                                </span>
                                <span v-else>Não disponível</span>
                        </p>
                        <p v-else style="line-height: 2;">
                            <strong>Quadrinhos Disponíveis:</strong> {{ selectedCharacter.comics_available || 'Não disponível' }}<br>
                            <strong>Séries Disponíveis:</strong> {{ selectedCharacter.series_available || 'Não disponível' }}<br>
                            <strong>Histórias Disponíveis:</strong> {{ selectedCharacter.stories_available || 'Não disponível' }}<br>
                            <strong>Eventos Disponíveis:</strong> {{ selectedCharacter.events_available || 'Não disponível' }}
                        </p>
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
                    <q-btn flat label="Confirmar" color="primary" @click="deleteFavorite(selectedFavoriteId, selectedFavoriteType)" />
                </q-card-actions>
            </q-card>
        </q-dialog>
    </q-layout>
</template>

<script>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import Toolbar from '../components/Toolbar.vue';
import Pagination from '../components/Pagination.vue';

export default {
    components: {
        Toolbar,
        Pagination
    },
    setup () {
        const favorites = ref([]);
        const loadingFavorites = ref(true);
        const heroBannerImage = ref('');
        const currentPage = ref(1);
        const itemsPerPage = ref(10);
        const isModalOpen = ref(false);
        const selectedCharacter = ref({
            name: '',
            description: '',
            thumbnail: { path: '', extension: '' },
            comics_available: '',
            series_available: '',
            stories_available: '',
            events_available: ''
        });
        const selectedComic = ref({
            title: '',
            description: '',
            thumbnail: { path: '', extension: '' },
            publication_date: '',
            price: '',
            pages: '',
            characters: [],
            creators: [],
            stories: []
        });
        const isComicModal = ref(false);
        const dialogVisible = ref(false);
        const dialogTitle = ref('');
        const dialogMessage = ref('');
        const selectedFavoriteId = ref(null);
        const selectedFavoriteType = ref('');

        const fetchFavorites = async () => {
            try {
                const response = await axios.get('/api/favorites');
                favorites.value = response.data;
                if (favorites.value.length > 0) {
                    const firstFavorite = favorites.value[0];
                    heroBannerImage.value = firstFavorite.character
                        ? `${firstFavorite.character.thumbnail.path}.${firstFavorite.character.thumbnail.extension}`
                        : `${firstFavorite.comic.thumbnail.path}.${firstFavorite.comic.thumbnail.extension}`;
                }
            } catch (error) {
                console.error('Error fetching favorites:', error);
            } finally {
                loadingFavorites.value = false;
            }
        };

        const changePage = (page) => {
            currentPage.value = page;
        };

        const changeItemsPerPage = (newItemsPerPage) => {
            itemsPerPage.value = newItemsPerPage;
            currentPage.value = 1; // Reset to first page
        };

        const paginatedFavorites = computed(() => {
            const start = (currentPage.value - 1) * itemsPerPage.value;
            const end = start + itemsPerPage.value;
            return favorites.value.slice(start, end);
        });

        const openModal = (favorite) => {
            if (favorite.character) {
                selectedCharacter.value = favorite.character;
                isComicModal.value = false;
            } else if (favorite.comic) {
                selectedComic.value = favorite.comic;
                isComicModal.value = true;
            }
            isModalOpen.value = true;
        };

        const showDialog = (title, message) => {
            dialogTitle.value = title;
            dialogMessage.value = message;
            dialogVisible.value = true;
        };

        const confirmDeleteFavorite = (id, type) => {
            selectedFavoriteId.value = id;
            selectedFavoriteType.value = type;
            showDialog('Remover dos Favoritos', 'Você tem certeza que deseja remover este item dos favoritos?');
        };

        const deleteFavorite = async (id, type) => {
            try {
                await axios.delete(`/api/favorites/${type}/${id}`);
                favorites.value = favorites.value.filter(fav => (fav.character && fav.character.id !== id) || (fav.comic && fav.comic.id !== id));
                dialogVisible.value = false; // Fechar o diálogo após remover dos favoritos
            } catch (error) {
                console.error('Error deleting favorite:', error);
            }
        };

        const formatDate = (date) => {
            if (!date) return 'Data não disponível';
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(date).toLocaleDateString(undefined, options);
        };

        const formatPrice = (price) => {
            if (typeof price === 'number' || !isNaN(price)) {
                return `$${parseFloat(price).toFixed(2)}`;
            }
            return 'Preço não disponível';
        };

        onMounted(async () => {
            await fetchFavorites();
        });

        return {
            favorites,
            loadingFavorites,
            heroBannerImage,
            currentPage,
            itemsPerPage,
            changePage,
            changeItemsPerPage,
            paginatedFavorites,
            isModalOpen,
            selectedCharacter,
            selectedComic,
            isComicModal,
            openModal,
            deleteFavorite,
            confirmDeleteFavorite,
            formatDate,
            formatPrice,
            dialogVisible,
            dialogTitle,
            dialogMessage,
            selectedFavoriteId,
            selectedFavoriteType
        };
    }
};
</script>

<style scoped>
@import '../../css/styles.sass';
</style>