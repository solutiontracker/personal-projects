/** @type {import('next').NextConfig} */
const nextConfig = {
  reactStrictMode: true,
  swcMinify: true,
  env: {
    NEXT_APP_BASE_URL: process.env.NEXT_APP_BASE_URL,
    NEXT_APP_API_URL: process.env.NEXT_APP_API_URL,
    NEXT_APP_URL: process.env.NEXT_APP_URL,
    NEXT_APP_AUTH_URL: process.env.NEXT_APP_AUTH_URL,
    NEXT_APP_EVENTCENTER_URL: process.env.NEXT_APP_EVENTCENTER_URL,
    NEXT_APP_REGISTRATION_FLOW_URL: process.env.NEXT_APP_REGISTRATION_FLOW_URL,
    NEXT_APP_API_GATEWAY_URL: process.env.NEXT_APP_API_GATEWAY_URL,
    NEXT_APP_APP_ENVIRONMENT: process.env.NEXT_APP_APP_ENVIRONMENT,
    PORT: process.env.PORT,
  },
  distDir: 'build'
}

module.exports = nextConfig
