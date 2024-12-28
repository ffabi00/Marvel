<template>
    <q-layout view="hHh lpR fFf">
        <q-page-container class="flex flex-center background-image"
            :style="{ backgroundImage: `url(${backgroundImage})` }">
            <div v-if="loading">
                <Loading />
            </div>
            <div v-else>
                <q-card class="login-card">
                    <q-card-section>
                        <div class="text-h6">Login</div>
                    </q-card-section>
                    <q-card-section>
                        <form @submit.prevent="login">
                            <q-input v-model="email" label="Email" type="email" class="q-mb-md input" />
                            <q-input v-model="password" label="Senha" type="password" class="q-mb-md input" />
                            <q-card-actions align="right">
                                <q-btn flat label="Registrar" @click="showRegisterModal" color="primary" />
                                <q-btn type="submit" label="Entrar" color="primary" class="button" />
                            </q-card-actions>
                        </form>
                    </q-card-section>
                </q-card>
            </div>
        </q-page-container>

        <!-- Diálogo de Registro -->
        <q-dialog v-model="registerDialogVisible">
            <q-card style="min-width: 450px">
                <q-card-section>
                    <div class="text-h6">Registro</div>
                </q-card-section>
                <q-card-section>
                    <form @submit.prevent="register">
                        <q-input v-model="registerName" label="Nome" class="q-mb-md input" />
                        <q-input v-model="registerEmail" label="Email" type="email" class="q-mb-md input" />
                        <q-input v-model="registerPassword" label="Senha" type="password" class="q-mb-md input" />
                        <q-input v-model="confirmPassword" label="Confirmar Senha" type="password"
                            class="q-mb-md input" />
                        <q-card-actions align="right">
                            <q-btn flat label="Cancelar" v-close-popup color="grey-7" />
                            <q-btn type="submit" label="Registrar" color="primary" />
                        </q-card-actions>
                    </form>
                </q-card-section>
            </q-card>
        </q-dialog>

        <!-- Diálogo de Mensagem -->
        <q-dialog v-model="dialogVisible">
            <q-card>
                <q-card-section>
                    <div class="text-h6">{{ dialogTitle }}</div>
                </q-card-section>
                <q-card-section class="dialog-text">
                    <p>{{ dialogMessage }}</p>
                </q-card-section>
                <q-card-actions align="right">
                    <q-btn flat label="OK" color="primary" v-close-popup />
                </q-card-actions>
            </q-card>
        </q-dialog>
    </q-layout>
</template>

<script>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import Loading from '../components/Loading.vue'

export default {
    components: {
        Loading
    },
    setup () {
        const email = ref('')
        const password = ref('')
        const backgroundImage = ref('')
        const loading = ref(true)
        const dialogVisible = ref(false)
        const dialogTitle = ref('')
        const dialogMessage = ref('')
        const registerDialogVisible = ref(false)
        const registerEmail = ref('')
        const registerPassword = ref('')
        const confirmPassword = ref('')
        const registerName = ref('')
        const router = useRouter()
        const $q = useQuasar()
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content')

        const isImageValid = (url) => {
            return new Promise((resolve) => {
                const img = new Image()
                img.onload = () => resolve(true)
                img.onerror = () => resolve(false)
                img.src = url
            })
        }

        const fetchBackgroundImage = async () => {
            try {
                const response = await axios.get('/api/marvel/comics')
                const comics = response.data.data.results
                let validImage = false
                let selectedImage = ''

                while (!validImage && comics.length > 0) {
                    const randomIndex = Math.floor(Math.random() * comics.length)
                    const comic = comics[randomIndex]
                    const imageUrl = `${comic.thumbnail.path}.${comic.thumbnail.extension}`
                    validImage = await isImageValid(imageUrl)
                    if (validImage) {
                        selectedImage = imageUrl
                    }
                    comics.splice(randomIndex, 1)
                }

                backgroundImage.value = selectedImage
            } catch (error) {
                console.error('Error fetching background image:', error)
            } finally {
                loading.value = false
            }
        }

        const showDialog = (title, message) => {
            dialogTitle.value = title
            dialogMessage.value = message
            dialogVisible.value = true
        }

        const login = async () => {
            try {
                if (!email.value || !password.value) {
                    showDialog('Falha ao Logar', 'Campos de Email e senha são obrigatórios!')
                    return
                }

                const response = await axios.post('/login', {
                    email: email.value,
                    password: password.value,
                    _token: csrfToken
                })
                if (response.data.token) {
                    showDialog('Sucesso', 'Login realizado com sucesso!')
                    localStorage.setItem('token', response.data.token)
                    window.location.href = '/'
                } else {
                    showDialog('Falha ao Logar', 'Token inválido')
                }
            } catch (error) {
                if (error.response?.status === 401) {
                    showDialog('Falha ao Logar', 'Email ou senha incorretos')
                } else {
                    showDialog('Falha ao Logar', 'Erro ao realizar login')
                }
            }
        }

        const register = async () => {
            try {
                if (!registerEmail.value || !registerPassword.value || !registerName.value || !confirmPassword.value) {
                    showDialog('Falha ao Registrar', 'Todos os campos são obrigatórios!')
                    return
                }

                if (registerPassword.value !== confirmPassword.value) {
                    showDialog('Falha ao Registrar', 'As senhas não coincidem!')
                    return
                }

                if (registerPassword.value.length < 8) {
                    showDialog('Falha ao Registrar', 'A senha deve ter no mínimo 8 caracteres!')
                    return
                }

                const response = await axios.post('/register', {
                    name: registerName.value,
                    email: registerEmail.value,
                    password: registerPassword.value,
                    password_confirmation: confirmPassword.value,
                    _token: csrfToken
                })

                if (response.status === 201) {
                    showDialog('Sucesso', 'Registro realizado com sucesso!')
                    registerDialogVisible.value = false
                    registerEmail.value = ''
                    registerPassword.value = ''
                    registerName.value = ''
                    confirmPassword.value = ''
                }
            } catch (error) {
                const errors = error.response?.data?.errors
                if (errors) {
                    Object.keys(errors).forEach(key => {
                        showDialog('Falha ao Registrar', errors[key][0])
                    })
                } else {
                    showDialog('Falha ao Registrar', 'Erro ao registrar usuário!')
                }
            }
        }

        const showRegisterModal = () => {
            registerDialogVisible.value = true
        }

        onMounted(() => {
            fetchBackgroundImage()
        })

        return {
            email,
            password,
            backgroundImage,
            loading,
            login,
            dialogVisible,
            dialogTitle,
            dialogMessage,
            showRegisterModal,
            registerDialogVisible,
            registerEmail,
            registerPassword,
            confirmPassword,
            registerName,
            register,
            csrfToken
        }
    }
}
</script>

<style lang="scss" scoped>
@import '../../css/styles.sass';
</style>