<template>
    <div class="pagination-container" v-if="!loading">
        <div class="pagination-items-per-page">
            <span class="pagination-label" :class="{ 'disabled': currentPage === totalPages }">Exibir:</span>
            <select v-model="selectedItemsPerPage" @change="changeItemsPerPage" class="pagination-select">
                <option v-for="option in itemsPerPageOptions" :key="option" :value="option">{{ option }}</option>
            </select>
            <span class="pagination-label">por página</span>
        </div>
        <div class="pagination">
            <div class="pagination-background"></div>
            <button :disabled="currentPage === 1" @click="changePage(currentPage - 1)" class="pagination-button">
                <span v-if="!isMobile">Anterior</span>
                <span v-else>&laquo;</span>
            </button>

            <button v-for="page in visiblePages" :key="page"
                :class="['pagination-button', { active: currentPage === page }]" @click="changePage(page)">
                {{ page }}
            </button>

            <button :disabled="currentPage === totalPages" @click="changePage(currentPage + 1)"
                class="pagination-button">
                <span v-if="!isMobile">Próximo</span>
                <span v-else>&raquo;</span>
            </button>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Pagination',
    props: {
        totalItems: {
            type: Number,
            required: true,
        },
        itemsPerPage: {
            type: Number,
            default: 10,
        },
        currentPage: {
            type: Number,
            default: 1,
        },
        loading: {
            type: Boolean,
            default: false,
        }
    },
    emits: ["page-changed", "items-per-page-changed"],
    data () {
        return {
            selectedItemsPerPage: this.itemsPerPage,
            itemsPerPageOptions: [5, 10, 20, 50],
            isMobile: window.innerWidth <= 470
        };
    },
    computed: {
        totalPages () {
            return Math.ceil(this.totalItems / this.selectedItemsPerPage);
        },
        visiblePages () {
            const pagesArray = [];
            const startPage = Math.max(1, this.currentPage - 1);
            const endPage = Math.min(this.totalPages, startPage + (this.isMobile ? 2 : 4));
            for (let i = startPage; i <= endPage; i++) {
                pagesArray.push(i);
            }
            return pagesArray;
        },
    },
    methods: {
        changePage (page) {
            if (page >= 1 && page <= this.totalPages) {
                this.$emit("page-changed", page);
            }
        },
        changeItemsPerPage () {
            this.$emit("items-per-page-changed", this.selectedItemsPerPage);
        },
        handleResize () {
            this.isMobile = window.innerWidth <= 470;
        }
    },
    mounted () {
        window.addEventListener('resize', this.handleResize);
    },
    beforeUnmount () {
        window.removeEventListener('resize', this.handleResize);
    }
};
</script>

<style lang="scss" scoped>
@import '@/css/styles.sass';
</style>