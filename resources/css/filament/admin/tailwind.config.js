import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        // Error-pages plugin views (so their utility classes get compiled).
        './vendor/cmsmaxinc/filament-error-pages/resources/**/*.blade.php',
        // Language-switch plugin views (relies on Tailwind scanning, ships no CSS).
        './vendor/bezhansalleh/filament-language-switch/resources/**/*.blade.php',
    ],
}
