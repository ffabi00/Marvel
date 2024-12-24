<template>
    <div class="btn-group">
        <q-btn flat round icon="menu" @click="toggleMenu" />
        <q-menu :value="menu" @input="updateMenu" anchor="bottom left" self="top left" content-class="dropdown-content">
            <q-list class="dropdown-content">
                <q-item clickable v-ripple @click="goToPage('')">
                    <q-item-section>Home</q-item-section>
                </q-item>
                <q-item clickable v-ripple @click="goToPage('quadrinho')">
                    <q-item-section>Quadrinho</q-item-section>
                </q-item>
                <q-item clickable v-ripple @click="goToPage('heroi')">
                    <q-item-section>Herói</q-item-section>
                </q-item>
                <q-item clickable v-ripple @click="goToPage('favorites')">
                    <q-item-section>Favoritos</q-item-section>
                </q-item>
            </q-list>
        </q-menu>
    </div>
</template>

<script>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

export default {
    setup () {
        const menu = ref(false);
        const router = useRouter();

        const toggleMenu = () => {
            menu.value = !menu.value;
        };

        const updateMenu = (value) => {
            menu.value = value;
        };

        const goToPage = (page) => {
            window.location.href = page;
        };

        return {
            menu,
            toggleMenu,
            updateMenu,
            goToPage
        };
    }
};
</script>

<style lang="scss" scoped>
@import '../../css/styles.sass';
</style>