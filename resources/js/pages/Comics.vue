<template>
    <q-layout view="hHh lpR fFf">
        <Toolbar :background="comicBannerImage" />
        <q-page-container class="flex flex-center">
            <div v-if="loadingComics">
                <div class="loading-spinner"></div>
            </div>
            <div v-else class="banner-container">
                <Pagination :totalItems="comics.length" :itemsPerPage="itemsPerPage" :currentPage="currentPage"
                    :loading="loadingComics" @page-changed="changePage" @items-per-page-changed="changeItemsPerPage" />
                <div class="banner" v-for="(comic, index) in paginatedComics" :key="index"
                    @click="openComicModal(comic)">
                    <img :src="`${comic.thumbnail.path}.${comic.thumbnail.extension}`" :alt="comic.title" />
                    <div class="favorite-container">
                        <q-icon name="star" class="favorite-icon" @click.stop="confirmFavorite(comic)" />
                        <span class="favorite-text">Favoritar</span>
                    </div>
                    <div class="banner-text">{{ comic.title }}</div>
                </div>
            </div>
        </q-page-container>

        <q-dialog v-model="isModalOpen" persistent>
            <q-card class="comic-modal">
                <q-card-section class="comic-modal-header">
                    <div class="text-h5">{{ selectedComic.title }}</div>
                    <hr class="comic-modal-divider" />
                </q-card-section>

                <q-card-section class="comic-modal-content">
                    <div class="comic-modal-image">
                        <img :src="`${selectedComic.thumbnail.path}.${selectedComic.thumbnail.extension}`"
                            :alt="selectedComic.title" />
                    </div>
                    <div class="comic-modal-details">
                        <div class="favorite-container">
                            <q-icon name="star" class="favorite-icon" @click="confirmFavorite(selectedComic)" />
                            <span class="favorite-text">Favoritar</span>
                        </div>
                        <p><strong>Descrição:</strong> {{ selectedComic.description || 'Descrição não disponível.' }}
                        </p>
                        <p><strong>Data de Publicação:</strong> {{ selectedComic.dates?.find(date => date.type ===
                            'onsaleDate')?.date ? formatDate(selectedComic.dates.find(date => date.type ===
                                'onsaleDate').date) : 'Data não disponível' }}</p>
                        <p><strong>Preço:</strong> {{ selectedComic.prices?.find(price => price.type ===
                            'printPrice')?.price ? formatPrice(selectedComic.prices.find(price => price.type ===
                                'printPrice').price) : 'Preço não disponível' }}</p>
                        <p><strong>Páginas:</strong> {{ selectedComic.pageCount || 'Número de páginas não disponível' }}
                        </p>
                        <p><strong>Personagens: </strong>
                            <span v-if="selectedComic.characters?.items?.length > 0">
                                <span v-for="(character, index) in selectedComic.characters.items" :key="index">
                                    {{ character.name || 'Nome não disponível' }}<span
                                        v-if="index < selectedComic.characters.items.length - 1">, </span>
                                </span>
                            </span>
                            <span v-else>Não disponível</span>
                        </p>
                        <p><strong>Criadores: </strong>
                            <span v-if="selectedComic.creators?.items?.length > 0">
                                <span v-for="(creator, index) in selectedComic.creators.items" :key="index">
                                    {{ creator.name || 'Nome não disponível' }} ({{ creator.role || 'Função não disponível' }})<span v-if="index < selectedComic.creators.items.length - 1">,
                                    </span>
                                </span>
                            </span>
                            <span v-else>Não disponível</span>
                        </p>
                        <p><strong>Histórias: </strong>
                            <span v-if="selectedComic.stories?.items?.length > 0">
                                <span v-for="(story, index) in selectedComic.stories.items" :key="index">
                                    {{ story.name || 'Nome não disponível' }} ({{ story.type || 'Tipo não disponível' }})<span v-if="index < selectedComic.stories.items.length - 1">,
                                    </span>
                                </span>
                            </span>
                            <span v-else>Não disponível</span>
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
                    <q-btn flat label="Confirmar" color="primary" @click="toggleFavorite(selectedComic)" />
                </q-card-actions>
            </q-card>
        </q-dialog>
    </q-layout>
</template>

<script>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import Toolbar from '../components/Toolbar.vue';
import Pagination from '../components/Pagination.vue';
import ApiService from '../components/ApiService.vue';
import axios from 'axios';

export default {
    components: {
        Toolbar,
        Pagination
    },
    setup () {
        const comics = ref([]);
        const loadingComics = ref(true);
        const comicBannerImage = ref('');
        const router = useRouter();
        const currentPage = ref(1);
        const itemsPerPage = ref(10);
        const isModalOpen = ref(false);
        const selectedComic = ref({});
        const favorites = ref([]);
        const dialogVisible = ref(false);
        const dialogTitle = ref('');
        const dialogMessage = ref('');

        const fetchComics = async () => {
            try {
                const data = await ApiService.fetchData('/api/marvel/comics', { limit: 100 });
                comics.value = data.data.results;
                const randomIndex = Math.floor(Math.random() * comics.value.length);
                const comic = comics.value[randomIndex];
                comicBannerImage.value = `${comic.thumbnail.path}.${comic.thumbnail.extension}`;
            } catch (error) {
                console.error('Error fetching comics:', error);
            } finally {
                loadingComics.value = false;
            }
        };

        const showDialog = (title, message) => {
            dialogTitle.value = title;
            dialogMessage.value = message;
            dialogVisible.value = true;
        };

        const confirmFavorite = (comic) => {
            selectedComic.value = comic;
            showDialog('Adicionar aos Favoritos', 'Você tem certeza que deseja adicionar este item aos favoritos?');
        };

        const toggleFavorite = async (comic) => {
            try {
                const response = await axios.post('/favorites/comics', {
                    title: comic.title,
                    description: comic.description,
                    thumbnail: comic.thumbnail,
                    marvel_id: comic.id,
                    page_count: comic.pageCount,
                    onsale_date: comic.dates?.find(date => date.type === 'onsaleDate')?.date.split('T')[0] || null, // Ajuste para enviar apenas a data no formato YYYY-MM-DD
                    price: comic.prices.find(price => price.type === 'printPrice').price,
                    creators: comic.creators.items || [],
                    characters: comic.characters.items || [],
                    stories: comic.stories.items || [],
                    events: comic.events.items || []
                }, {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                    }
                });
                favorites.value.push(response.data);
                dialogVisible.value = false; // Fechar o diálogo após adicionar aos favoritos
            } catch (error) {
                console.error('Error adding favorite:', error);
            }
        };

        const goToComic = (comicId) => {
            router.push(`/comics/${comicId}`);
        };

        const openComicModal = (comic) => {
            selectedComic.value = comic;
            isModalOpen.value = true;
        };

        const changePage = (page) => {
            currentPage.value = page;
        };

        const changeItemsPerPage = (newItemsPerPage) => {
            itemsPerPage.value = newItemsPerPage;
            currentPage.value = 1;
        };

        const paginatedComics = computed(() => {
            const start = (currentPage.value - 1) * itemsPerPage.value;
            const end = start + itemsPerPage.value;
            return comics.value.slice(start, end);
        });

        const formatDate = (date) => {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(date).toLocaleDateString(undefined, options);
        };

        const formatPrice = (price) => {
            return `$${price.toFixed(2)}`;
        };

        onMounted(async () => {
            await fetchComics();
        });

        return {
            comics,
            loadingComics,
            comicBannerImage,
            goToComic,
            currentPage,
            itemsPerPage,
            changePage,
            changeItemsPerPage,
            paginatedComics,
            isModalOpen,
            selectedComic,
            openComicModal,
            formatDate,
            formatPrice,
            toggleFavorite,
            confirmFavorite,
            dialogVisible,
            dialogTitle,
            dialogMessage
        };
    }
};
</script>

<style lang="scss" scoped>
@import '../../css/styles.sass';
</style>