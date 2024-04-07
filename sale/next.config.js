/** @type {import('next').NextConfig} */
const nextConfig = {
    experimental: {
        appDir: true,
    },
    env: {
        serverHost: process.env.SERVER_HOST,
        serverImageHost: process.env.SERVER_IMAGE_HOST,
        regSiteHost: process.env.REG_SITE_HOST,
    },
    eslint: {
        // Warning: This allows production builds to successfully complete even if
        // your project has ESLint errors.
        ignoreDuringBuilds: true,
    },
    images: {
        remotePatterns: [
            {
                protocol: 'https',
                hostname: 'my.eventbuizz.com',
                port: '',
            },
            {
                protocol: 'https',
                hostname: 'stage.eventbuizz.com',
                port: '',
            },
            {
                protocol: 'https',
                hostname: 'dev.eventbuizz.com',
                port: '',
            },
            {
                protocol: 'http',
                hostname: 'localhost',
                port: '8200',
            },
        ],
        domains: ['https://my.eventbuizz.com','https://stage.eventbuizz.com', 'https://dev.eventbuizz.com', 'http:/localhost:8200']
    },
}

module.exports = nextConfig

