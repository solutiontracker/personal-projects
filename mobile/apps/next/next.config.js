/** @type {import('next').NextConfig} */

const { withNativebase } = require('@native-base/next-adapter')

const withFonts = require('next-fonts')

const withImages = require('next-images');

const { withExpo } = require('@expo/next-adapter')

module.exports = withNativebase({
  dependencies: [
    '@expo/next-adapter',
    'react-native-vector-icons',
    'react-native-vector-icons-for-web',
    'solito',
    'application',
  ],
  plugins: [[withFonts, { projectRoot: __dirname }], [withImages, { projectRoot: __dirname }], [withExpo, { projectRoot: __dirname }]],
  nextConfig: {
    projectRoot: __dirname,
    reactStrictMode: false,
    webpack5: true,
    distDir: 'build',
    images: {
      disableStaticImages: true,
    },
    webpack: (config, options) => {
      config.resolve.alias = {
        ...(config.resolve.alias || {}),
        'react-native$': 'react-native-web',
        '@expo/vector-icons': 'react-native-vector-icons',
      }
      config.resolve.extensions = [
        '.web.js',
        '.web.ts',
        '.web.tsx',
        ...config.resolve.extensions,
      ]
      return config
    },
    env: {
      APP_API_BASE_URL: process.env.APP_API_BASE_URL,
      APP_EVENTCENTER_BASE_URL: process.env.APP_EVENTCENTER_BASE_URL,
      APP_API_GATEWAY_URL: process.env.APP_API_GATEWAY_URL,
      APP_SERVER_ENVIRONMENT: process.env.APP_SERVER_ENVIRONMENT,
      APP_SOCKET_SERVER: process.env.APP_SOCKET_SERVER,
    },
  },
})
