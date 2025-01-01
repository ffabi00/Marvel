<template>
    <q-layout view="hHh lpR fFf">
        <Toolbar />
        <q-page-container class="flex flex-center">
            <q-card class="user-card">
                <q-card-section class="user-card-header">
                    <div class="text-h5 text-black">Editar Perfil</div>
                </q-card-section>
                <q-card-section class="user-card-section">
                    <q-form @submit="updateUser" class="user-form">
                        <q-input v-model="user.name" label="Nome" outlined class="q-mb-md input-white" />
                        <q-input v-model="user.email" label="Email" outlined class="q-mb-md input-white" />
                        <q-input v-model="user.password" label="Nova senha" type="password" outlined
                            class="q-mb-md input-white" />
                        <q-input v-model="user.password_confirmation" label="Confirmar nova Senha" type="password" outlined class="q-mb-md input-white" />
                        <div class="form-actions">
                            <q-btn label="Excluir Conta" color="negative" @click="confirmDeleteAccount"
                                class="q-mr-sm" />
                            <q-btn type="submit" label="Salvar" color="primary" />
                        </div>
                    </q-form>
                </q-card-section>
            </q-card>
        </q-page-container>

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
                    <q-btn flat label="OK" color="primary" @click="handleDialogClose" />
                </q-card-actions>
            </q-card>
        </q-dialog>

        <!-- Diálogo de Confirmação para Exclusão -->
        <q-dialog v-model="deleteDialogVisible">
            <q-card>
                <q-card-section>
                    <div class="text-h6">Confirmar Exclusão</div>
                </q-card-section>
                <q-card-section class="dialog-text">
                    <p>Digite sua senha para confirmar a exclusão da conta:</p>
                    <q-input v-model="deletePassword" label="Senha" type="password" outlined
                        class="q-mb-md input-white" />
                </q-card-section>
                <q-card-actions align="right">
                    <q-btn flat label="Cancelar" color="primary" v-close-popup />
                    <q-btn flat label="Confirmar" color="primary" @click="confirmDeleteAccountWithPassword" />
                </q-card-actions>
            </q-card>
        </q-dialog>
    </q-layout>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { Inertia } from '@inertiajs/inertia';
import Toolbar from '@/js/components/Toolbar.vue';

export default {
    name: 'User',
    components: {
        Toolbar
    },
    setup () {
        const user = ref({
            name: '',
            email: '',
            password: '',
            password_confirmation: ''
        });
        const dialogVisible = ref(false);
        const dialogTitle = ref('');
        const dialogMessage = ref('');
        const deleteDialogVisible = ref(false);
        const deletePassword = ref('');

        const fetchUser = async () => {
            try {
                const response = await axios.get('/api/user');
                user.value.name = response.data.name;
                user.value.email = response.data.email;
                user.value.password = '';
                user.value.password_confirmation = '';
            } catch (error) {
                console.error('Error fetching user:', error);
            }
        };

        const updateUser = async () => {
            if (user.value.password !== user.value.password_confirmation) {
                showDialog('Erro', 'As senhas não coincidem.');
                return;
            }

            if (user.value.password && user.value.password.length < 8) {
                showDialog('Erro', 'A senha deve ter pelo menos 8 caracteres.');
                return;
            }

            try {
                await axios.put('/api/user', {
                    name: user.value.name,
                    email: user.value.email,
                    password: user.value.password,
                    password_confirmation: user.value.password_confirmation
                });
                showDialog('Sucesso', 'Dados atualizados com sucesso!');
                handleDialogCloseReload();
            } catch (error) {
                console.error('Error updating user:', error);
                showDialog('Erro', 'Erro ao atualizar os dados.');
            }
        };

        const showDialog = (title, message) => {
            dialogTitle.value = title;
            dialogMessage.value = message;
            dialogVisible.value = true;
        };

        const handleDialogClose = () => {
            dialogVisible.value = false;
        };

        const handleDialogCloseReload = () => {
            dialogVisible.value = false;
            Inertia.visit(window.location.href, { replace: true });
        };

        const confirmDeleteAccount = () => {
            deleteDialogVisible.value = true;
        };

        const confirmDeleteAccountWithPassword = async () => {
            try {
                await axios.post('/api/user/verify-password', { password: deletePassword.value });
                await deleteAccount();
            } catch (error) {
                console.error('Error verifying password:', error);
                showDialog('Erro', 'Senha incorreta.');
            }
        };

        const deleteAccount = async () => {
            try {
                await axios.delete('/api/user');
                Inertia.visit('/login');
            } catch (error) {
                console.error('Error deleting account:', error);
                showDialog('Erro', 'Erro ao excluir a conta.');
            }
        };

        onMounted(() => {
            fetchUser();
        });

        return {
            user,
            dialogVisible,
            dialogTitle,
            dialogMessage,
            updateUser,
            confirmDeleteAccount,
            confirmDeleteAccountWithPassword,
            deleteAccount,
            deleteDialogVisible,
            deletePassword,
            handleDialogClose,
            handleDialogCloseReload
        };
    }
};
</script>

<style lang="scss" scoped>
@import '@/css/styles.sass';
</style>