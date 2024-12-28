<template>
    <div class="btn-group">
        <div class="user-info">
            <span class="user-name" @click="toggleMenu">{{ user.name }}</span>
            <q-btn dense flat round icon="account_circle" @click="toggleMenu" class="btn" />
        </div>
        <q-menu :value="menu" @input="updateMenu">
            <q-list>
                <q-item>
                    <q-item-section>
                        <div class="dropdown-content">
                            <p style="border-bottom: 1px solid black;">{{ user.email }}</p>
                            <q-item clickable v-ripple @click="goToUserProfile" class="profile">
                                <q-item-section>Editar Perfil</q-item-section>
                            </q-item>
                            <q-item clickable v-ripple @click="logout" class="logout">
                                <q-item-section>Logout</q-item-section>
                            </q-item>
                        </div>
                    </q-item-section>
                </q-item>
            </q-list>
        </q-menu>
    </div>
</template>

<script>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

export default {
    props: {
        user: {
            type: Object,
            required: true
        }
    },
    setup (props) {
        const menu = ref(false);
        const router = useRouter();

        const toggleMenu = () => {
            menu.value = !menu.value;
        };

        const updateMenu = (value) => {
            menu.value = value;
        };

        const goToUserProfile = () => {
            window.location.href = '/user';
        };

        const logout = async () => {
            try {
                await axios.post('/logout');
                window.location.href = '/login';
            } catch (error) {
                console.error('Error logging out:', error);
            }
        };

        return {
            menu,
            toggleMenu,
            updateMenu,
            goToUserProfile,
            logout
        };
    }
};
</script>

<style scoped>
@import '../../css/styles.sass';
</style>