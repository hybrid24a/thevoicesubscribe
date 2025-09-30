import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { viteStaticCopy } from 'vite-plugin-static-copy';

export default defineConfig({
  resolve: {
    alias: {
    },
  },
  server: {
    cors: {
      origin: ['http://checkout.the.voice'],
      methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
      credentials: true
    },
    hmr: {
      host: 'localhost'
    }
  },
  plugins: [
    viteStaticCopy({
      targets: [
        {
          src: 'resources/images',
          dest: '',
        },
      ]
    }),
    laravel({
      input: [
        'resources/scss/checkout.scss',
        'resources/scss/admin.scss',
      ],
      refresh: true,
    })
  ],
});
