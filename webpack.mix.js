const mix = require('laravel-mix')
const path = require('path')

const directory = path.basename(path.resolve(__dirname))
const source = `platform/plugins/${directory}`
const dist = `public/vendor/core/plugins/${directory}`

mix
    .js(`${source}/resources/js/live-chat.js`, `${dist}/js`)
    .sass(`${source}/resources/sass/live-chat.scss`, `${dist}/css`)
    .sass(`${source}/resources/sass/admin-messenger.scss`, `${dist}/css`)

if (mix.inProduction()) {
    mix.copy(`${dist}/css/live-chat.css`, `${source}/public/css`)
    mix.copy(`${dist}/css/admin-messenger.css`, `${source}/public/css`)
    mix.copy(`${dist}/js/live-chat.js`, `${source}/public/js`)
}
