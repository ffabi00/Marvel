<template>
    <div>
        <q-header>
            <q-toolbar class="bg-red">
                <div v-if="isSmallScreen">
                    <ResponsiveMenu />
                </div>
                <div v-else>
                    <q-btn flat label="Home" @click="goToPage('/')" />
                    <q-btn flat label="Quadrinhos" @click="goToPage('comics')" />
                    <q-btn flat label="Heróis" @click="goToPage('characters')" />
                    <q-btn flat label="Favoritos" @click="goToPage('favorites')" />
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
import { useRouter } from 'vue-router';
import axios from 'axios';
import Dropdown from './Dropdown.vue';
import ResponsiveMenu from './ResponsiveMenu.vue';

export default {
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
        const router = useRouter();
        const isSmallScreen = ref(window.innerWidth < 768);

        const fetchUser = async () => {
            try {
                const response = await axios.get('/api/user');
                user.value = response.data;
            } catch (error) {
                console.error('Error fetching user:', error);
            }
        };

        const goToPage = (page) => {
            window.location.href = page;
        };

        const logout = async () => {
            try {
                await axios.post('/logout');
                window.location.href = '/login';
            } catch (error) {
                console.error('Error logging out:', error);
            }
        };

        const toggleMenu = () => {
            menu.value = !menu.value;
        };

        const handleResize = () => {
            isSmallScreen.value = window.innerWidth < 768;
        };

        onMounted(() => {
            fetchUser();
            window.addEventListener('resize', handleResize);
        });

        return {
            menu,
            user,
            goToPage,
            logout,
            toggleMenu,
            isSmallScreen
        };
    }
};
</script>

<style lang="scss" scoped>
@import '../../css/styles.sass';
</style>