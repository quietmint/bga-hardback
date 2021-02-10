export default {
    base: '',
    outDir: 'build',
    assetsDir: '',
    emitIndex: false,
    sourcemap: true,
    rollupOutputOptions: {
        entryFileNames: `[name].js`,
        chunkFileNames: `[name].js`,
        assetFileNames: `[name].[ext]`
    },
}