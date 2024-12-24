<template>
    <q-layout view="hHh lpR fFf">
        <q-page-container class="flex flex-center background-image"
            :style="{ backgroundImage: `url(${backgroundImage})` }">
            <div v-if="loading">
                <Loading />
            </div>
            <div v-else>
                <q-card class="q-pa-md card" style="max-width: 400px; width: 100%;">
                    <q-card-section>
                        <div class="text-h6 text-center">Login</div>
                    </q-card-section>
                    <q-card-section>
                        <form @submit.prevent="login">
                            <q-input v-model="email" label="Email" type="email" class="q-mb-md input" />
                            <q-input v-model="password" label="Password" type="password" class="q-mb-md input" />
                            <q-card-actions align="right">
                                <q-btn type="submit" label="Login" color="primary" class="button" />
                            </q-card-actions>
                        </form>
                    </q-card-section>
                </q-card>
            </div>
        </q-page-container>
        <q-dialog v-model="dialogVisible">
            <q-card>
                <q-card-section class="dialog-text">
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
import Loading from '../components/Loading.vue';

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
        const router = useRouter()
        const $q = useQuasar()

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
                const response = await axios.get('/api/marvel/comics', {
                    params: {
                        limit: 25
                    }
                })
                const comics = response.data.data.results
                if (comics.length > 0) {
                    let validImageFound = false
                    while (!validImageFound && comics.length > 0) {
                        const randomIndex = Math.floor(Math.random() * comics.length)
                        const imageUrl = comics[randomIndex].thumbnail.path + '.' + comics[randomIndex].thumbnail.extension
                        if (imageUrl.includes('image_not_available')) {
                            comics.splice(randomIndex, 1) // Remove a imagem inválida da lista
                            continue
                        }
                        const isValid = await isImageValid(imageUrl)
                        if (isValid) {
                            backgroundImage.value = imageUrl
                            validImageFound = true
                        } else {
                            comics.splice(randomIndex, 1) // Remove a imagem inválida da lista
                        }
                    }
                }
            } catch (error) {
                console.error('Error fetching background image:', error)
            } finally {
                loading.value = false
            }
        }

        const login = async () => {
            if (!email.value || !password.value) {
                dialogTitle.value = 'Falha ao Logar'
                dialogMessage.value = 'Campos de Email e senha são obrigatórios!'
                dialogVisible.value = true
                return
            }

            try {
                const response = await axios.post('/login', { email: email.value, password: password.value })
                localStorage.setItem('authToken', response.data.token) // Armazene o token de autenticação
                window.location.href = '/' // Redireciona para a página Home
            } catch (error) {
                    dialogTitle.value = 'Falha na Autenticação'
                    dialogMessage.value = 'Email ou senha inválidos!'
                    dialogVisible.value = true
                    return
                }
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
            dialogMessage
        }
    }
}
</script>

<style lang="scss" scoped>
@import '../../css/styles.sass';
</style>