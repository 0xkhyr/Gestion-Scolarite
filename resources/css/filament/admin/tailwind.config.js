import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        // Error-pages plugin views (so their utility classes get compiled).
        './vendor/cmsmaxinc/filament-error-pages/resources/**/*.blade.php',
    ],
}
