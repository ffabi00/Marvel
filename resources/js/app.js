import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { Quasar } from 'quasar'
import axios from 'axios'
import '../css/styles.sass'

// Import icon libraries
import '@quasar/extras/roboto-font/roboto-font.css'
import '@quasar/extras/material-icons/material-icons.css'

// Import Quasar css
import 'quasar/src/css/index.sass'

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

        const backgroundElements = document.querySelectorAll('.background-grid div')
        backgroundElements.forEach((element) => {
            element.style.backgroundImage = `url(${selectedImage})`
        })
    } catch (error) {
        console.error('Error fetching background image:', error)
    } finally {
        loading.value = false
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const backgroundGrid = document.createElement('div')
    backgroundGrid.className = 'background-grid'
    for (let i = 0; i < 4; i++) {
        const div = document.createElement('div')
        backgroundGrid.appendChild(div)
    }
    document.body.appendChild(backgroundGrid)
    fetchBackgroundImage()
    setInterval(fetchBackgroundImage, 30000) // 30 segundos
})

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
        return pages[`./Pages/${name}.vue`]
    },
    setup ({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(Quasar, {
                plugins: {},
            })
            .mount(el)
    },
})