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
import axios from 'axios';

export default {
    props: {
        user: {
            type: Object,
            required: true
        },
        menu: {
            type: Boolean,
            required: true
        }
    },
    methods: {
        toggleMenu () {
            this.$emit('update:menu', !this.menu);
        },
        updateMenu (value) {
            this.$emit('update:menu', value);
        },
        async logout () {
            try {
                await axios.post('/logout');
                window.location.href = '/login';
            } catch (error) {
                console.error('Error logging out:', error);
            }
        }
    }
};
</script>

<style lang="scss" scoped>
@import '../../css/styles.sass';
</style>