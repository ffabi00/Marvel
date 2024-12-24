import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { Quasar } from 'quasar'
import router from './router'
import '../css/styles.sass'

// Import icon libraries
import '@quasar/extras/roboto-font/roboto-font.css'
import '@quasar/extras/material-icons/material-icons.css'

// Import Quasar css
import 'quasar/src/css/index.sass'

const backgrounds = [
    '/images/marvel_background_1.jpg',
    '/images/marvel_background_2.jpg',
    '/images/marvel_background_3.jpg',
    '/images/marvel_background_4.jpg'
]

let currentBackgroundIndex = 0

function changeBackground () {
    const backgroundElements = document.querySelectorAll('.background-grid div')
    backgroundElements.forEach((element, index) => {
        const backgroundIndex = (currentBackgroundIndex + index) % backgrounds.length
        element.style.backgroundImage = `url(${backgrounds[backgroundIndex]})`
    })
    currentBackgroundIndex = (currentBackgroundIndex + 1) % backgrounds.length
}

document.addEventListener('DOMContentLoaded', () => {
    const backgroundGrid = document.createElement('div')
    backgroundGrid.className = 'background-grid'
    for (let i = 0; i < 4; i++) {
        const div = document.createElement('div')
        backgroundGrid.appendChild(div)
    }
    document.body.appendChild(backgroundGrid)
    changeBackground()
    setInterval(changeBackground, 30000) // 30 segundos
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
            .use(router)
            .mount(el)
    },
})